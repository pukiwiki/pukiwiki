<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: tracker.inc.php,v 1.4 2003/07/17 01:25:51 arino Exp $
//

function plugin_tracker_convert()
{
	global $script,$vars;
	
	$page = $vars['page'];
	
	$config_name = 'default';
	$options = array();
	if (func_num_args())
	{
		$args = func_get_args();
		switch (count($args))
		{
			case 3:
				$options = array_splice($args,2);
			case 2:
				$_page = get_fullname($args[1],$page);
				if (is_pagename($_page))
				{
					$page = $_page;
				}
			case 1:
				$config_name = $args[0];
		}
	}
	
	$config = new Config('plugin/tracker/'.$config_name);
	
	if (!$config->read())
	{
		return "<p>config file '".htmlspecialchars($config_name)."' not found.</p>";
	}
	
	$config->config_name = $config_name;
	
	$fields = plugin_tracker_get_fields($page,$config);
	
	$retval = convert_html(plugin_tracker_get_source($config->page.'/form'));
	
	foreach (array_keys($fields) as $name)
	{
		$retval = str_replace("[$name]",$fields[$name]->get_tag(),$retval);
	}
	return <<<EOD
<form action="$script" method="post">
$retval
</form>
EOD;
}
function plugin_tracker_action()
{
	global $script,$post,$vars,$now;
	
	$config_name = array_key_exists('_config',$post) ? $post['_config'] : '';
	
	$config = new Config('plugin/tracker/'.$config_name);
	if (!$config->read())
	{
		return "<p>config file '".htmlspecialchars($config_name)."' not found.</p>";
	}
	$config->config_name = $config_name;
	$source = $config->page.'/page';
	
	$refer = array_key_exists('_refer',$post) ? $post['_refer'] : '';
	
	if (!is_pagename($refer))
	{
		return array(
			'msg'=>'cannot write',
			'body'=>'page name ('.htmlspecialchars($refer).') is not valid.'
		);
	}
	if (!is_page($source))
	{
		return array(
			'msg'=>'cannot write',
			'body'=>'page template ('.htmlspecialchars($source).') is not exist.'
		);
	}
	// ページ名を決定
	$base = $post['_refer'];
	$num = 0;
	$name = (array_key_exists('_name',$post)) ? $post['_name'] : '';
	if (array_key_exists('_page',$post))
	{
		$page = $real = $post['_page'];
	}
	else
	{
		$real = is_pagename($name) ? $name : ++$num;
		$page = get_fullname('./'.$real,$base);
	}
	if (!is_pagename($page))
	{
		$page = $base;
	}
	
	while (is_page($page))
	{
		$real = ++$num;
		$page = "$base/$real";
	}
	// ページデータを生成
	$postdata = join('',plugin_tracker_get_source($source));
	
	// 規定のデータ
	$_post = $post;
	$_post['_date'] = $now;
	$_post['_page'] = $page;
	$_post['_name'] = $name;
	$_post['_real'] = $real;
	// $_post['_refer'] = $_post['refer'];
	
	$fields = plugin_tracker_get_fields($page,$config);
	
	foreach (array_keys($fields) as $key)
	{
		if (array_key_exists($key,$_post))
		{
			$postdata = str_replace("[$key]",
				$fields[$key]->format_value($_post[$key]),$postdata);
		}
	}
	
	// 書き込み
	page_write($page,$postdata);
	
	$r_page = rawurlencode($page);
	
	header("Location: $script?$r_page");
	exit;
}
// フィールドオブジェクトを構築する
function plugin_tracker_get_fields($page,&$config)
{
	global $now,$_tracker_messages;
	
	$fields = array();
	// 規定のオブジェクト
	$fields['_date']   = &new Tracker_field_text(  array('_date',  $_tracker_messages['btn_date'],  '','20',''),$page,$config);
	$fields['_update'] = &new Tracker_field_text(  array('_update',$_tracker_messages['btn_update'],'','20',''),$page,$config);
	$fields['_past']   = &new Tracker_field_text(  array('_past',  $_tracker_messages['btn_past'],  '','20',''),$page,$config);
	$fields['_page']   = &new Tracker_field_page(  array('_page',  $_tracker_messages['btn_page'],  '','20',''),$page,$config);
	$fields['_name']   = &new Tracker_field_text(  array('_name',  $_tracker_messages['btn_name'],  '','20',''),$page,$config);
	$fields['_real']   = &new Tracker_field_text(  array('_real',  $_tracker_messages['btn_real'],  '','20',''),$page,$config);
	$fields['_refer']  = &new Tracker_field_page(  array('_refer', $_tracker_messages['btn_refer'], '','20',''),$page,$config);
	$fields['_submit'] = &new Tracker_field_submit(array('_submit',$_tracker_messages['btn_submit'],'','',  ''),$page,$config);
	
	foreach ($config->get('fields') as $field)
	{
		// 0=>項目名 1=>見出し 2=>形式 3=>オプション 4=>デフォルト値
		$class = 'Tracker_field_'.$field[2];
		if (!class_exists($class))
		{ // デフォルト
			$class = 'Tracker_field_text';
			$field[2] = 'text';
			$field[3] = '20';
		}
		$fields[$field[0]] = &new $class($field,$page,$config);
	}
	return $fields;
}
// フィールドクラス
class Tracker_field
{
	var $name;
	var $title;
	var $values;
	var $default_value;
	var $page;
	var $config;
	var $data;
	
	function Tracker_field($field,$page,&$config)
	{
		global $post;
		
		$this->name = $field[0];
		$this->title = $field[1];
		$this->values = explode(',',$field[3]);
		$this->default_value = $field[4];
		$this->page = $page;
		$this->config = &$config;
		$this->data = array_key_exists($this->name,$post) ? $post[$this->name] : '';
	}
	function get_tag()
	{
	}
	function get_style($str)
	{
		return '%s';
	}
	function format_value($value)
	{
		return str_replace('|','&#x7c;',$value);
	}
	function format_cell($str)
	{
		return $str;
	}
	function compare($str1,$str2)
	{
		return strnatcasecmp($str1,$str2);
	}
}
class Tracker_field_text extends Tracker_field
{
	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = htmlspecialchars($this->values[0]);
		$s_value = htmlspecialchars($this->default_value);
		return "<input type=\"text\" name=\"$s_name\" size=\"$s_size\" value=\"$s_value\" />";
	}
}
class Tracker_field_page extends Tracker_field_text
{
	function format_value($value)
	{
		global $WikiName;
		
		$value = strip_bracket($value);
		if (is_pagename($value))
		{
			$value = "[[$value]]";
		}
		return parent::format_value($value);
	}
}
class Tracker_field_title extends Tracker_field_text
{
	function format_cell($str)
	{
		make_heading($str);
		return $str;
	}
}
class Tracker_field_format extends Tracker_field
{
	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = htmlspecialchars($this->values[0]);
		return "<input type=\"text\" name=\"$s_name\" size=\"$s_size\" />";
	}
	function format_value($str)
	{
		$values = $this->config->get($this->name);
		if (count($values) < 2)
		{
			return $str;
		}
		return ($str == '') ? $values[1][2] : sprintf($values[0][2],$str);
	}
	function get_style($str)
	{
		$values = $this->config->get($this->name);
		if (count($values) < 2)
		{
			return $str;
		}
		return ($str == '') ? $values[1][1] : $values[0][1];
	}
}
class Tracker_field_textarea extends Tracker_field
{
	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_cols = htmlspecialchars($this->values[0]);
		$s_rows = htmlspecialchars($this->values[1]);
		$s_value = htmlspecialchars($this->default_value);
		return "<textarea name=\"$s_name\" cols=\"$s_cols\" rows=\"$s_rows\">$s_value</textarea>";
	}
	function format_cell($str)
	{
		$str = preg_replace('/[\r\n]+/','',$str);
		if (!empty($this->values[2]) and strlen($str) > ($this->values[2] + 3))
		{
			$str = mb_substr($str,0,$this->values[2]).'...';
		}
		return $str;
	}
}
class Tracker_field_radio extends Tracker_field
{
	var $styles;
	
	function Tracker_field_radio($field,$page,&$config)
	{
		parent::Tracker_field($field,$page,$config);
		
		$this->styles = array();
		foreach ($this->config->get($this->name) as $option)
		{
			$this->styles[trim($option[0])] = ($option[1] == '') ? '' : trim($option[1]);
		}
	}
	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$retval = '';
		foreach ($this->config->get($this->name) as $option)
		{
			$s_option = htmlspecialchars($option[0]);
			$checked = trim($option[0]) == trim($this->default_value) ? ' checked="checked"' : '';
			$retval .= "<input type=\"radio\" name=\"$s_name\" value=\"$s_option\"$checked />$s_option\n";
		}
		
		return $retval;
	}
	function format_cell($str)
	{
		return $str;
	}
	function get_style($str)
	{
		return array_key_exists($str,$this->styles) ? $this->styles[$str] : '%s';
	}
	function format_value($value)
	{
		if (is_array($value))
		{
			$value = join(', ',$value);
		}
		return parent::format_value($value);
	}
	function compare($str1,$str2)
	{
		static $options;
		
		if (!isset($options))
		{
			$options = array_flip(array_map(
				create_function('$arr','return $arr[0];'),
				$this->config->get($this->name)
			));
		}
		$n1 = array_key_exists($str1,$options) ? $options[$str1] : count($options);
		$n2 = array_key_exists($str2,$options) ? $options[$str2] : count($options);
		return ($n1 == $n2) ? 0 : ($n1 > $n2 ? -1 : 1);
	}
}
class Tracker_field_select extends Tracker_field_radio
{
	function get_tag($empty=FALSE)
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = (array_key_exists(0,$this->values) and is_numeric($this->values[0])) ?
			' size="'.htmlspecialchars($this->values[0]).'"' : '';
		$s_multiple = (array_key_exists(1,$this->values) and strtolower($this->values[1]) == 'multiple') ?
			' multiple="multiple"' : '';
		$retval = "<select name=\"{$s_name}[]\"$s_size$s_multiple>\n";
		if ($empty)
		{
			$retval .= " <option value=\"\"></option>\n";
		}
		$defaults = array_flip(preg_split('/\s*,\s*/',$this->default_value,-1,PREG_SPLIT_NO_EMPTY));
		foreach ($this->config->get($this->name) as $option)
		{
			$s_option = htmlspecialchars($option[0]);
			$selected = array_key_exists(trim($option[0]),$defaults) ? ' selected="selected"' : '';
			$retval .= " <option value=\"$s_option\"$selected>$s_option</option>\n";
		}
		$retval .= "</select>";
		
		return $retval;
	}
}
class Tracker_field_checkbox extends Tracker_field_radio
{
	function get_tag($empty=FALSE)
	{
		$s_name = htmlspecialchars($this->name);
		$defaults = array_flip(preg_split('/\s*,\s*/',$this->default_value,-1,PREG_SPLIT_NO_EMPTY));
		$retval = '';
		foreach ($this->config->get($this->name) as $option)
		{
			$s_option = htmlspecialchars($option[0]);
			$checked = array_key_exists(trim($option[0]),$defaults) ?
				' checked="checked"' : '';
			$retval .= "<input type=\"checkbox\" name=\"{$s_name}[]\" value=\"$s_option\"$checked />$s_option\n";
		}
		
		return $retval;
	}
}
class Tracker_field_hidden extends Tracker_field_radio
{
	function get_tag($empty=FALSE)
	{
		$s_name = htmlspecialchars($this->name);
		$s_default = htmlspecialchars($this->default_value);
		$retval = "<input type=\"hidden\" name=\"$s_name\" value=\"$s_default\" />\n";
		
		return $retval;
	}
}
class Tracker_field_submit extends Tracker_field
{
	function get_tag()
	{
		$s_title = htmlspecialchars($this->title);
		$s_page = htmlspecialchars($this->page);
		$s_config = htmlspecialchars($this->config->config_name);
		
		return <<<EOD
<input type="submit" value="$s_title" />
<input type="hidden" name="plugin" value="tracker" />
<input type="hidden" name="_refer" value="$s_page" />
<input type="hidden" name="_config" value="$s_config" />
EOD;
	}
}
///////////////////////////////////////////////////////////////////////////
// 一覧表示
function plugin_tracker_list_convert()
{
	global $vars;
	
	$config = 'default';
	$page = $vars['page'];
	$field = '_page';
	$order = -1;
	$limit = NULL;
	if (func_num_args())
	{
		$args = func_get_args();
		switch (count($args))
		{
			case 5:
				$limit = is_numeric($args[4]) ? $args[4] : $limit;
			case 4:
				$order = is_numeric($args[3]) ? $args[3] : $order;
			case 3:
				$field = ($args[2] != '') ? $args[2] : $field;
			case 2:
				$page = is_pagename($args[1]) ? $args[1] : $page;
			case 1:
				$config = ($args[0] != '') ? $args[0] : $config;
		}
	}
	return plugin_tracker_getlist($page,$config,$field,$order,$limit);
}
function plugin_tracker_list_action()
{
	global $script,$vars,$_tracker_messages;
	
	$page = $vars['refer'];
	$s_page = make_pagelink($page);
	$config = $vars['config'];
	$field = array_key_exists('field',$vars) ? $vars['field'] : '_page';
	$order = (array_key_exists('order',$vars) and is_numeric($vars['order'])) ?
		$vars['order'] : -1;
		
	return array(
		'msg' => $_tracker_messages['msg_list'],
		'body'=> str_replace('$1',$s_page,$_tracker_messages['msg_back']).
			plugin_tracker_getlist($page,$config,$field,$order)
	);
}
function plugin_tracker_getlist($page,$config_name,$field=NULL,$order=1,$limit=NULL)
{
	$config = new Config('plugin/tracker/'.$config_name);
	
	if (!$config->read())
	{
		return "<p>config file '".htmlspecialchars($config_name)."' is not exist.";
	}
	$config->config_name = $config_name;
	$list = &new Tracker_list($page,$config);
	$list->sort($field,$order);
	return $list->toString($limit);
}

// 一覧クラス
class Tracker_list
{
	var $page;
	var $config;
	var $fields;
	var $pattern;
	var $pattern_fields;
	var $rows;
	var $sort_field = '_page';
	var $sort_order = -1;
	var $sort_obj = NULL;
	
	function Tracker_list($page,&$config)
	{
		$this->page = $page;
		$this->config = &$config;
		$this->fields = plugin_tracker_get_fields($page,$config);
		
		$pattern = join('',plugin_tracker_get_source($config->page.'/page'));
		// ブロックプラグインをフィールドに置換
		// #commentなどで前後に文字列の増減があった場合に、[_block_xxx]に吸い込ませるようにする
		$pattern = preg_replace('/^\#([^\(\s]+)(?:\((.*)\))?\s*$/m','[_block_$1]',$pattern);
		
		// パターンを生成
		$this->pattern = '';
		$this->pattern_fields = array();
		$pattern = preg_split('/\\\\\[(\w+)\\\\\]/',preg_quote($pattern,'/'),-1,PREG_SPLIT_DELIM_CAPTURE);
		while (count($pattern))
		{
			$this->pattern .= preg_replace('/\s+/','\\s*','(?>\\s*'.trim(array_shift($pattern)).'\\s*)');
			if (count($pattern))
			{
				$field = array_shift($pattern);
				$this->pattern_fields[] = $field;
				$this->pattern .= '(.*)';
			}
		}
		// ページの列挙と取り込み
		$this->rows = array();
		$pattern = "$page/";
		$pattern_len = strlen($pattern);
		foreach (get_existpages() as $_page)
		{
			if (strpos($_page,$pattern) === 0)
				//and is_numeric($num = substr($_page,$pattern_len)))
			{
				$name = substr($_page,$pattern_len);
				$this->add($_page,$name);
			}
		}
	}
	function add($page,$name)
	{
		global $WikiName,$BracketName;
		
		// 無限ループ防止
		if (array_key_exists($name,$this->rows))
		{
			return;
		}
		
		$source = plugin_tracker_get_source($page);
		if (preg_match("/move\s*to\s*($WikiName|\[\[$BracketName\]\])/",$source[0],$matches))
		{
			return $this->add(strip_bracket($matches[1]),$name);
		}
		$source = join('',preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/','$1$2',$source));
		
		// デフォルト値
		$this->rows[$name] = array(
			'_page'  => "[[$page]]",
			'_refer' => $this->page,
			'_real'  => $name,
			'_update'=> format_date(get_filetime($page)),
			'_past'  => get_passage(get_filetime($page),FALSE)
		);
		if (preg_match("/{$this->pattern}/s",$source,$matches))
		{
			array_shift($matches);
			foreach ($this->pattern_fields as $key=>$field)
			{
				$this->rows[$name][$field] = trim($matches[$key]);
			}
		}
	}
	function sort($field=NULL,$order=1)
	{
		$this->sort_order = $order;
		if ($field == '_page')
		{
			($order == -1) ? krsort($this->rows) : ksort($this->rows);
			return;
		}
		$fields = array_flip(array_keys($this->fields));
		
		if (!array_key_exists($field,$fields))
		{
			return;
		}
		$this->sort_field = $field;
		$this->sort_obj = &$this->fields[$field];
		usort($this->rows,array(&$this,'compare'));
	}
	function compare($arr1,$arr2)
	{
		return $this->sort_order * $this->sort_obj->compare(
			$arr1[$this->sort_field],$arr2[$this->sort_field]
		);
	}
	function replace_item($arr)
	{
		$params = explode(',',$arr[1]);
		$name = array_shift($params);
		if ($name == '')
		{
			$str = '';
		}
		else if (array_key_exists($name,$this->items))
		{
			$str = $this->items[$name];
			if (array_key_exists($name,$this->fields))
			{
				$str = $this->fields[$name]->format_cell($str);
			}
		}
		else
		{
			return $arr[0];
		}
		$style = count($params) ? $params[0] : $name;
		if (array_key_exists($style,$this->items)
			and array_key_exists($style,$this->fields))
		{
			$str = sprintf($this->fields[$style]->get_style($this->items[$style]),$str);
		}
		return $str;
	}
	function replace_title($arr)
	{
		global $script;
		
		if (!array_key_exists($arr[1],$this->fields))
		{
			return $arr[0];
		}
		
		$order = 1;
		$arrow = '';
		if ($arr[1] == $this->sort_field)
		{
			$order = -$this->sort_order;
			$arrow = ($order == -1) ? '&darr;':'&uarr;';
		}
		
		$title = $this->fields[$arr[1]]->title;
		$r_page = rawurlencode($this->page);
		$r_config = rawurlencode($this->config->config_name);
		$r_field = rawurlencode($arr[1]);
		
		return "[[$title$arrow>$script?plugin=tracker_list&refer=$r_page&config=$r_config&field=$r_field&order=$order]]";
	}
	function toString($limit=NULL)
	{
		global $_tracker_messages;
		
		$list = $body = '';
		if ($limit !== NULL and count($this->rows) > $limit)
		{
			$list .= str_replace(
				array('$1','$2'),
				array(count($this->rows),$limit),
				$_tracker_messages['msg_limit'])."\n";
			$this->rows = array_splice($this->rows,0,$limit);
		}
		if (count($this->rows) == 0)
		{
			return '';
		}
		foreach (plugin_tracker_get_source($this->config->page.'/list') as $line)
		{
			if (preg_match('/^\|(.+)\|[hHfFcC]$/',$line))
			{
				$list .= $line;
			}
			else
			{
				$body .= $line;
			}
		}
		$list = preg_replace_callback('/\[([^\[\]]+)\]/',array(&$this,'replace_title'),$list);
		foreach ($this->rows as $key=>$row)
		{
			$this->items = $row;
			$list .= preg_replace_callback('/\[([^\[\]]+)\]/',array(&$this,'replace_item'),$body);
		}
		return convert_html($list);
	}
}
function plugin_tracker_get_source($page)
{
	$source = get_source($page);
	// 見出しの固有ID部を削除
	$source = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m','$1$2',$source);
	// #freezeを削除
	return preg_replace('/^#freeze\s*$/m','',$source);
}
?>
