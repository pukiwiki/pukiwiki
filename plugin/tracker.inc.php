<?php
// PukiWiki - Yet another WikiWikiWeb clone
// tracker.inc.php
// Copyright 2003-2021 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Issue tracker plugin (See Also bugtrack plugin)

// tracker_listで表示しないページ名(正規表現で)
// 'SubMenu'ページ および '/'を含むページを除外する
define('TRACKER_LIST_EXCLUDE_PATTERN','#^SubMenu$|/#');
// 制限しない場合はこちら
//define('TRACKER_LIST_EXCLUDE_PATTERN','#(?!)#');

// 項目の取り出しに失敗したページを一覧に表示する
define('TRACKER_LIST_SHOW_ERROR_PAGE',TRUE);

// Use cache
define('TRACKER_LIST_USE_CACHE', TRUE);

function plugin_tracker_convert()
{
	global $vars;

	$script = get_base_uri();
	if (PKWK_READONLY) return ''; // Show nothing

	$base = $refer = $vars['page'];

	$config_name = 'default';
	$form = 'form';
	$options = array();
	if (func_num_args())
	{
		$args = func_get_args();
		switch (count($args))
		{
			case 3:
				$options = array_splice($args,2);
			case 2:
				$args[1] = get_fullname($args[1],$base);
				$base = is_pagename($args[1]) ? $args[1] : $base;
			case 1:
				$config_name = ($args[0] != '') ? $args[0] : $config_name;
				list($config_name,$form) = array_pad(explode('/',$config_name,2),2,$form);
		}
	}

	$config = new Config('plugin/tracker/'.$config_name);

	if (!$config->read())
	{
		return "<p>config file '".htmlsc($config_name)."' not found.</p>";
	}

	$config->config_name = $config_name;

	$fields = plugin_tracker_get_fields($base,$refer,$config);

	$form = $config->page.'/'.$form;
	if (!is_page($form))
	{
		return "<p>config file '".make_pagelink($form)."' not found.</p>";
	}
	$retval = convert_html(plugin_tracker_get_source($form));
	$hiddens = '';

	foreach (array_keys($fields) as $name)
	{
		$replace = $fields[$name]->get_tag();
		if (is_a($fields[$name],'Tracker_field_hidden'))
		{
			$hiddens .= $replace;
			$replace = '';
		}
		$retval = str_replace("[$name]",$replace,$retval);
	}
	return <<<EOD
<form enctype="multipart/form-data" action="$script" method="post"
 class="_p_tracker_form">
<div>
$retval
$hiddens
</div>
</form>
EOD;
}
function plugin_tracker_action()
{
	global $post, $vars, $now;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');

	$config_name = array_key_exists('_config',$post) ? $post['_config'] : '';

	$config = new Config('plugin/tracker/'.$config_name);
	if (!$config->read())
	{
		return "<p>config file '".htmlsc($config_name)."' not found.</p>";
	}
	$config->config_name = $config_name;
	$source = $config->page.'/page';

	$refer = array_key_exists('_refer',$post) ? $post['_refer'] : $post['_base'];

	if (!is_pagename($refer))
	{
		return array(
			'msg'=>'cannot write',
			'body'=>'page name ('.htmlsc($refer).') is not valid.'
		);
	}
	if (!is_page($source))
	{
		return array(
			'msg'=>'cannot write',
			'body'=>'page template ('.htmlsc($source).') is not exist.'
		);
	}
	// ページ名を決定
	$base = $post['_base'];
	if (!is_pagename($base))
	{
		return array(
			'msg'=>'cannot write',
			'body'=>'page name ('.htmlsc($base).') is not valid.'
		);
	}
	$name = (array_key_exists('_name',$post)) ? $post['_name'] : '';
	$_page = (array_key_exists('_page',$post)) ? $post['_page'] : '';
	if (is_pagename($_page)) {
		// Create _page page if _page is in parameters
		$page = $real = $_page;
	} else if (is_pagename($name)) {
		// Create "$base/$name" page if _name is in parameters
		$real = $name;
		$page = get_fullname('./' . $name, $base);
	} else {
		$page = '';
	}
	if (!is_pagename($page) || is_page($page)) {
		// Need new page name => Get last article number + 1
		$page_list = plugin_tracker_get_page_list($base, false);
		usort($page_list, '_plugin_tracker_list_paganame_compare');
		if (count($page_list) === 0) {
			$num = 1;
		} else {
			$latest_page = $page_list[count($page_list) - 1]['name'];
			$num = intval(substr($latest_page, strlen($base) + 1)) + 1;
		}
		$real = '' . $num;
		$page = $base . '/' . $num;
	}
	// ページデータを生成
	$postdata = plugin_tracker_get_source($source);

	// 規定のデータ
	$_post = array_merge($post,$_FILES);
	$_post['_date'] = $now;
	$_post['_page'] = $page;
	$_post['_name'] = $name;
	$_post['_real'] = $real;
	// $_post['_refer'] = $_post['refer'];

	$fields = plugin_tracker_get_fields($page,$refer,$config);

	check_editable($page, true, true);
	// Creating an empty page, before attaching files
	touch(get_filename($page));

	foreach (array_keys($fields) as $key)
	{
		$value = array_key_exists($key,$_post) ?
			$fields[$key]->format_value($_post[$key]) : '';

		foreach (array_keys($postdata) as $num)
		{
			if (trim($postdata[$num]) == '')
			{
				continue;
			}
			$postdata[$num] = str_replace(
				"[$key]",
				($postdata[$num][0] == '|' or $postdata[$num][0] == ':') ?
					str_replace('|','&#x7c;',$value) : $value,
				$postdata[$num]
			);
		}
	}

	// Writing page data, without touch
	page_write($page, join('', $postdata));
	pkwk_headers_sent();
	header('Location: ' . get_page_uri($page, PKWK_URI_ROOT));
	exit;
}

/**
 * Page_list comparator
 */
function _plugin_tracker_list_paganame_compare($a, $b)
{
	return strnatcmp($a['name'], $b['name']);
}

/**
 * Get page list for "$page/"
 */
function plugin_tracker_get_page_list($page, $needs_filetime) {
	$page_list = array();
	$pattern = $page . '/';
	$pattern_len = strlen($pattern);
	foreach (get_existpages() as $p) {
		if (strncmp($p, $pattern, $pattern_len) === 0 && pkwk_ctype_digit(substr($p, $pattern_len))) {
			if ($needs_filetime) {
				$page_list[] = array('name'=>$p,'filetime'=>get_filetime($p));
			} else {
				$page_list[] = array('name'=>$p);
			}
		}
	}
	return $page_list;
}

// フィールドオブジェクトを構築する
function plugin_tracker_get_fields($base,$refer,&$config)
{
	global $now,$_tracker_messages;

	$fields = array();
	// 予約語
	foreach (array(
		'_date'=>'text',    // 投稿日時
		'_update'=>'date',  // 最終更新
		'_past'=>'past',    // 経過(passage)
		'_page'=>'page',    // ページ名
		'_name'=>'text',    // 指定されたページ名
		'_real'=>'real',    // 実際のページ名
		'_refer'=>'page',   // 参照元(フォームのあるページ)
		'_base'=>'page',    // 基準ページ
		'_submit'=>'submit' // 追加ボタン
		) as $field=>$class)
	{
		$class = 'Tracker_field_'.$class;
		$fields[$field] = new $class(array($field,$_tracker_messages["btn$field"],'','20',''),$base,$refer,$config);
	}

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
		$fields[$field[0]] = new $class($field,$base,$refer,$config);
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
	var $refer;
	var $config;
	var $data;
	var $sort_type = SORT_REGULAR;
	var $id = 0;

	function Tracker_field($field,$page,$refer,&$config)
	{
		$this->__construct($field, $page, $refer, $config);
	}
	function __construct($field,$page,$refer,&$config)
	{
		global $post;
		static $id = 0;

		$this->id = ++$id;
		$this->name = $field[0];
		$this->title = $field[1];
		$this->values = explode(',',$field[3]);
		$this->default_value = $field[4];
		$this->page = $page;
		$this->refer = $refer;
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
		return $value;
	}
	function format_cell($str)
	{
		return $str;
	}
	function get_value($value)
	{
		return $value;
	}
}
class Tracker_field_text extends Tracker_field
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		$s_name = htmlsc($this->name);
		$s_size = htmlsc($this->values[0]);
		$s_value = htmlsc($this->default_value);
		return "<input type=\"text\" name=\"$s_name\" size=\"$s_size\" value=\"$s_value\" />";
	}
}
class Tracker_field_page extends Tracker_field_text
{
	var $sort_type = SORT_STRING;

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
class Tracker_field_real extends Tracker_field_text
{
	var $sort_type = SORT_REGULAR;
}
class Tracker_field_title extends Tracker_field_text
{
	var $sort_type = SORT_STRING;

	function format_cell($str)
	{
		make_heading($str);
		return $str;
	}
}
class Tracker_field_textarea extends Tracker_field
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		$s_name = htmlsc($this->name);
		$s_cols = htmlsc($this->values[0]);
		$s_rows = htmlsc($this->values[1]);
		$s_value = htmlsc($this->default_value);
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

class Tracker_field_format extends Tracker_field
{
	var $sort_type = SORT_STRING;

	var $styles = array();
	var $formats = array();

	function Tracker_field_format($field,$page,$refer,&$config)
	{
		$this->__construct($field, $page, $refer, $config);
	}
	function __construct($field,$page,$refer,&$config)
	{
		parent::__construct($field,$page,$refer,$config);

		foreach ($this->config->get($this->name) as $option)
		{
			list($key,$style,$format) = array_pad(array_map('trim',$option),3,'');
			if ($style != '')
			{
				$this->styles[$key] = $style;
			}
			if ($format != '')
			{
				$this->formats[$key] = $format;
			}
		}
	}
	function get_tag()
	{
		$s_name = htmlsc($this->name);
		$s_size = htmlsc($this->values[0]);
		return "<input type=\"text\" name=\"$s_name\" size=\"$s_size\" />";
	}
	function get_key($str)
	{
		return ($str == '') ? 'IS NULL' : 'IS NOT NULL';
	}
	function format_value($str)
	{
		if (is_array($str))
		{
			return join(', ',array_map(array($this,'format_value'),$str));
		}
		$key = $this->get_key($str);
		return array_key_exists($key,$this->formats) ? str_replace('%s',$str,$this->formats[$key]) : $str;
	}
	function get_style($str)
	{
		$key = $this->get_key($str);
		return array_key_exists($key,$this->styles) ? $this->styles[$key] : '%s';
	}
}
class Tracker_field_file extends Tracker_field_format
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		$s_name = htmlsc($this->name);
		$s_size = htmlsc($this->values[0]);
		return "<input type=\"file\" name=\"$s_name\" size=\"$s_size\" />";
	}
	function format_value($str)
	{
		if (array_key_exists($this->name,$_FILES))
		{
			require_once(PLUGIN_DIR.'attach.inc.php');
			$result = attach_upload($_FILES[$this->name],$this->page);
			if ($result['result']) // アップロード成功
			{
				return parent::format_value($this->page.'/'.$_FILES[$this->name]['name']);
			}
		}
		// ファイルが指定されていないか、アップロードに失敗
		return parent::format_value('');
	}
}
class Tracker_field_radio extends Tracker_field_format
{
	var $sort_type = SORT_NUMERIC;

	function get_tag()
	{
		$s_name = htmlsc($this->name);
		$retval = '';
		$id = 0;
		foreach ($this->config->get($this->name) as $option)
		{
			$s_option = htmlsc($option[0]);
			$checked = trim($option[0]) == trim($this->default_value) ? ' checked="checked"' : '';
			++$id;
			$s_id = '_p_tracker_' . $s_name . '_' . $this->id . '_' . $id;
			$retval .= '<input type="radio" name="' .  $s_name . '" id="' . $s_id .
				'" value="' . $s_option . '"' . $checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}
	function get_key($str)
	{
		return $str;
	}
	function get_value($value)
	{
		static $options = array();
		if (!array_key_exists($this->name,$options))
		{
			// 'reset' means function($arr) { return $arr[0]; }
			$options[$this->name] = array_flip(array_map('reset',$this->config->get($this->name)));
		}
		return array_key_exists($value,$options[$this->name]) ? $options[$this->name][$value] : $value;
	}
}
class Tracker_field_select extends Tracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag($empty=FALSE)
	{
		$s_name = htmlsc($this->name);
		$s_size = (array_key_exists(0,$this->values) and is_numeric($this->values[0])) ?
			' size="'.htmlsc($this->values[0]).'"' : '';
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
			$s_option = htmlsc($option[0]);
			$selected = array_key_exists(trim($option[0]),$defaults) ? ' selected="selected"' : '';
			$retval .= " <option value=\"$s_option\"$selected>$s_option</option>\n";
		}
		$retval .= "</select>";

		return $retval;
	}
}
class Tracker_field_checkbox extends Tracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag($empty=FALSE)
	{
		$s_name = htmlsc($this->name);
		$defaults = array_flip(preg_split('/\s*,\s*/',$this->default_value,-1,PREG_SPLIT_NO_EMPTY));
		$retval = '';
		$id = 0;
		foreach ($this->config->get($this->name) as $option)
		{
			$s_option = htmlsc($option[0]);
			$checked = array_key_exists(trim($option[0]),$defaults) ?
				' checked="checked"' : '';
			++$id;
			$s_id = '_p_tracker_' . $s_name . '_' . $this->id . '_' . $id;
			$retval .= '<input type="checkbox" name="' . $s_name .
				'[]" id="' . $s_id . '" value="' . $s_option . '"' . $checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}
}
class Tracker_field_hidden extends Tracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag($empty=FALSE)
	{
		$s_name = htmlsc($this->name);
		$s_default = htmlsc($this->default_value);
		$retval = "<input type=\"hidden\" name=\"$s_name\" value=\"$s_default\" />\n";

		return $retval;
	}
}
class Tracker_field_submit extends Tracker_field
{
	function get_tag()
	{
		$s_title = htmlsc($this->title);
		$s_page = htmlsc($this->page);
		$s_refer = htmlsc($this->refer);
		$s_config = htmlsc($this->config->config_name);

		return <<<EOD
<input type="submit" value="$s_title" />
<input type="hidden" name="plugin" value="tracker" />
<input type="hidden" name="_refer" value="$s_refer" />
<input type="hidden" name="_base" value="$s_page" />
<input type="hidden" name="_config" value="$s_config" />
EOD;
	}
}
class Tracker_field_date extends Tracker_field
{
	var $sort_type = SORT_NUMERIC;

	function format_cell($timestamp)
	{
		return format_date($timestamp);
	}
}
class Tracker_field_past extends Tracker_field
{
	var $sort_type = SORT_NUMERIC;

	function format_cell($timestamp)
	{
		return '&passage("' . get_date_atom($timestamp + LOCALZONE) . '");';
	}
	function get_value($value)
	{
		return UTIME - $value;
	}
}
///////////////////////////////////////////////////////////////////////////
// 一覧表示
function plugin_tracker_list_convert()
{
	global $vars, $_title_cannotread;
	$config = 'default';
	$page = $refer = $vars['page'];
	$field = '_page';
	$order = '';
	$list = 'list';
	$limit = NULL;
	$start_n = NULL;
	$last_n = NULL;
	if (func_num_args())
	{
		$args = func_get_args();
		switch (count($args))
		{
			case 4:
				$range_m = null;
				if (is_numeric($args[3])) {
					$limit = $args[3];
				} else {
					if (preg_match('#^(\d+)-(\d+)$#', $args[3], $range_m)) {
						$start_n = intval($range_m[1]);
						$last_n = intval($range_m[2]);
					}
				}
			case 3:
				$order = $args[2];
			case 2:
				$args[1] = get_fullname($args[1],$page);
				$page = is_pagename($args[1]) ? $args[1] : $page;
			case 1:
				$config = ($args[0] != '') ? $args[0] : $config;
				list($config,$list) = array_pad(explode('/',$config,2),2,$list);
		}
	}
	if (!is_page_readable($page)) {
		$body = str_replace('$1', htmlsc($page), $_title_cannotread);
		return $body;
	}
	return plugin_tracker_getlist($page,$refer,$config,$list,$order,$limit,$start_n,$last_n);
}
function plugin_tracker_list_action()
{
	global $vars, $_tracker_messages, $_title_cannotread;

	$page = $refer = $vars['refer'];
	$s_page = make_pagelink($page);
	$config = $vars['config'];
	$list = array_key_exists('list',$vars) ? $vars['list'] : 'list';
	$order = array_key_exists('order',$vars) ? $vars['order'] : '_real:SORT_DESC';

	if (!is_page_readable($page)) {
		$body = str_replace('$1', htmlsc($page), $_title_cannotread);
		return array(
			'msg' => $body,
			'body' => $body
		);
	}
	return array(
		'msg' => $_tracker_messages['msg_list'],
		'body'=> str_replace('$1',$s_page,$_tracker_messages['msg_back']).
			plugin_tracker_getlist($page,$refer,$config,$list,$order)
	);
}
function plugin_tracker_getlist($page,$refer,$config_name,$list,$order='',$limit=NULL,$start_n=NULL,$last_n=NULL)
{
	global $whatsdeleted;
	$config = new Config('plugin/tracker/'.$config_name);
	if (!$config->read())
	{
		return "<p>config file '".htmlsc($config_name)."' is not exist.</p>";
	}
	$config->config_name = $config_name;

	if (!is_page($config->page.'/'.$list))
	{
		return "<p>config file '".make_pagelink($config->page.'/'.$list)."' not found.</p>";
	}
	$cache_enabled = defined('TRACKER_LIST_USE_CACHE') && TRACKER_LIST_USE_CACHE &&
		defined('JSON_UNESCAPED_UNICODE') && defined('PKWK_UTF8_ENABLE');
	if (is_null($limit) && is_null($start_n)) {
		$cache_filepath = CACHE_DIR . encode($page) . '.tracker';
	} else if (pkwk_ctype_digit($limit) && 0 < $limit && $limit <= 1000) {
		$cache_filepath = CACHE_DIR . encode($page) . '.' . $limit . '.tracker';
	} else if (!is_null($start_n) && !is_null($last_n)) {
		$cache_filepath = CACHE_DIR . encode($page) . '.' . $start_n . '-' . $last_n . '.tracker';
	} else {
		$cache_enabled = false;
	}
	$cachedata = null;
	$cache_format_version = 1;
	if ($cache_enabled) {
		$config_filetime = get_filetime($config->page);
		$config_list_filetime = get_filetime($config->page.'/'. $list);
		if (file_exists($cache_filepath)) {
			$json_cached = pkwk_file_get_contents($cache_filepath);
			if ($json_cached) {
				$wrapdata = json_decode($json_cached, true);
				if (is_array($wrapdata) && isset($wrapdata['version'],
					$wrapdata['html'], $wrapdata['refreshed_at'])) {
					$cache_time_prev = $wrapdata['refreshed_at'];
					if ($cache_format_version === $wrapdata['version']) {
						if ($config_filetime === $wrapdata['config_updated_at'] &&
							$config_list_filetime === $wrapdata['config_list_updated_at']) {
							$cachedata = $wrapdata;
						} else {
							// (Ignore) delete file
							unlink($cache_filepath);
						}
					}
				}
			}
		}
	}
	// Check recent.dat timestamp
	$recent_dat_filemtime = filemtime(CACHE_DIR . PKWK_MAXSHOW_CACHE);
	// Check RecentDeleted timestamp
	$recent_deleted_filetime = get_filetime($whatsdeleted);
	if (is_null($cachedata)) {
		$cachedata = array();
	} else {
		if ($recent_dat_filemtile !== false) {
			if ($recent_dat_filemtime === $cachedata['recent_dat_filemtime'] &&
				$recent_deleted_filetime === $cachedata['recent_deleted_filetime'] &&
				$order === $cachedata['order']) {
				// recent.dat is unchanged
				// RecentDeleted is unchanged
				// order is unchanged
				return $cachedata['html'];
			}
		}
	}
	$cache_holder = $cachedata;
	$tracker_list = new Tracker_list($page,$refer,$config,$list,$cache_holder);
	if ($order === $cache_holder['order'] &&
		empty($tracker_list->newly_deleted_pages) &&
		empty($tracker_list->newly_updated_pages) &&
		!$tracker_list->link_update_required &&
		is_null($start_n) && is_null($last_n)) {
		$result = $cache_holder['html'];
	} else {
		$tracker_list->sort($order);
		$result = $tracker_list->toString($limit,$start_n,$last_n);
	}
	if ($cache_enabled) {
		$refreshed_at = time();
		$json = array(
			'refreshed_at' => $refreshed_at,
			'rows' => $tracker_list->rows,
			'html' => $result,
			'order' => $order,
			'config_updated_at' => $config_filetime,
			'config_list_updated_at' => $config_list_filetime,
			'recent_dat_filemtime' => $recent_dat_filemtime,
			'recent_deleted_filetime' => $recent_deleted_filetime,
			'link_pages' => $tracker_list->link_pages,
			'version' => $cache_format_version);
		$cache_body = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		file_put_contents($cache_filepath, $cache_body, LOCK_EX);
	}
	return $result;
}

// 一覧クラス
class Tracker_list
{
	var $page;
	var $config;
	var $list;
	var $fields;
	var $pattern;
	var $pattern_fields;
	var $rows;
	var $order;
	var $sort_keys;
	var $newly_deleted_pages = array();
	var $newly_updated_pages = array();

	function Tracker_list($page,$refer,&$config,$list,&$cache_holder)
	{
		$this->__construct($page, $refer, $config, $list, $cache_holder);
	}
	function __construct($page,$refer,&$config,$list,&$cache_holder)
	{
		global $whatsdeleted, $_cached_page_filetime;
		$this->page = $page;
		$this->config = &$config;
		$this->list = $list;
		$this->fields = plugin_tracker_get_fields($page,$refer,$config);

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
				$this->pattern .= '(.*?)';
			}
		}
		if (empty($cache_holder)) {
			// List pages and get contents (non-cache behavior)
			$this->rows = array();
			$pattern = "$page/";
			$pattern_len = strlen($pattern);
			foreach (get_existpages() as $_page)
			{
				if (substr($_page, 0, $pattern_len) === $pattern)
				{
					$name = substr($_page,$pattern_len);
					if (preg_match(TRACKER_LIST_EXCLUDE_PATTERN,$name))
					{
						continue;
					}
					$this->add($_page,$name);
				}
			}
			$this->link_pages = $this->get_filetimes($this->get_all_links());
		} else {
			// Cache-available behavior
			// Check RecentDeleted timestamp
			$cached_rows = $this->decode_cached_rows($cache_holder['rows']);
			$updated_linked_pages = array();
			$newly_deleted_pages = array();
			$pattern = "$page/";
			$pattern_len = strlen($pattern);
			$recent_deleted_filetime = get_filetime($whatsdeleted);
			$deleted_page_list = array();
			if ($recent_deleted_filetime !== $cache_holder['recent_deleted_filetime']) {
				foreach (plugin_tracker_get_source($whatsdeleted) as $line) {
					$m = null;
					if (preg_match('#\[\[([^\]]+)\]\]#', $line, $m)) {
						$_page = $m[1];
						if (is_pagename($_page)) {
							$deleted_page_list[] = $m[1];
						}
					}
				}
				foreach ($deleted_page_list as $_page) {
					if (substr($_page, 0, $pattern_len) === $pattern) {
						$name = substr($_page, $pattern_len);
						if (!is_page($_page) && isset($cached_rows[$name]) &&
							!preg_match(TRACKER_LIST_EXCLUDE_PATTERN, $name)) {
							// This page was just deleted
							array_push($newly_deleted_pages, $_page);
							unset($cached_rows[$name]);
						}
					}
				}
			}
			$this->newly_deleted_pages = $newly_deleted_pages;
			$updated_pages = array();
			$this->rows = $cached_rows;
			// Check recent.dat timestamp
			$recent_dat_filemtime = filemtime(CACHE_DIR . PKWK_MAXSHOW_CACHE);
			$updated_page_list = array();
			if ($recent_dat_filemtime !== $cache_holder['recent_dat_filemtime']) {
				// recent.dat was updated. Search which page was updated.
				$target_pages = array();
				// Active page file time (1 hour before timestamp of recent.dat)
				$target_filetime = $cache_holder['recent_dat_filemtime'] - LOCALZONE - 60 * 60;
				foreach (get_recent_files() as $_page=>$time) {
					if ($time <= $target_filetime) {
						// Older updated pages
						break;
					}
					$updated_page_list[$_page] = $time;
					$name = substr($_page, $pattern_len);
					if (substr($_page, 0, $pattern_len) === $pattern) {
						$name = substr($_page, $pattern_len);
						if (preg_match(TRACKER_LIST_EXCLUDE_PATTERN, $name)) {
							continue;
						}
						// Tracker target page
						if (isset($this->rows[$name])) {
							// Existing page
							$row = $this->rows[$name];
							if ($row['_update'] === get_filetime($_page)) {
								// Same as cache
								continue;
							} else {
								// Found updated page
								$updated_pages[] = $_page;
								unset($this->rows[$name]);
								$this->add($_page, $name);
							}
						} else {
							// Add new page
							$updated_pages[] = $_page;
							$this->add($_page, $name);
						}
					}
				}
			}
			$this->newly_updated_pages = $updated_pages;
			$new_link_names = $this->get_all_links();
			$old_link_map = array();
			foreach ($cache_holder['link_pages'] as $link_page) {
				$old_link_map[$link_page['page']] = $link_page['filetime'];
			}
			$new_link_map = $old_link_map;
			$link_update_required = false;
			foreach ($deleted_page_list as $_page) {
				if (in_array($_page, $new_link_names)) {
					if (isset($old_link_map[$_page])) {
						// This link keeps existing
						if (!is_page($_page)) {
							// OK. Confirmed the page doesn't exist
							if ($old_link_map[$_page] === 0) {
								// Do nothing (From no-page to no-page)
							} else {
								// This page was just deleted
								$new_link_map[$_page] = get_filetime($_page);
								$link_update_required = true;
							}
						}
					} else {
						// This link was just added
						$new_link_map[$_page] = get_filetime($_page);
						$link_update_required = true;
					}
				}
			}
			foreach ($updated_page_list as $_page=>$time) {
				if (in_array($_page, $new_link_names)) {
					if (isset($old_link_map[$_page])) {
						// This link keeps existing
						if (is_page($_page)) {
							// OK. Confirmed the page now exists
							if ($old_link_map[$_page] === 0) {
								// This page was just added
								$new_link_map[$_page] = get_filetime($_page);
								$link_update_required = true;
							} else {
								// Do nothing (existing-page to existing-page)
							}
						}
					} else {
						// This link was just added
						$new_link_map[$_page] = get_filetime($_page);
						$link_update_required = true;
					}
				}
			}
			$new_link_pages = array();
			foreach ($new_link_map as $_page => $time) {
				$new_link_pages[] = array(
					'page' => $_page,
					'filetime' => $time,
				);
			}
			$this->link_pages = $new_link_pages;
			$this->link_update_required = $link_update_required;
			$time_map_for_cache = $new_link_map;
			foreach ($this->rows as $row) {
				$time_map_for_cache[$this->page . '/' . $row['_real']] = $row['_update'];
			}
			$_cached_page_filetime = $time_map_for_cache;
		}
	}
	function decode_cached_rows($decoded_rows)
	{
		$ar = array();
		foreach ($decoded_rows as $row) {
			$ar[$row['_real']] = $row;
		}
		return $ar;
	}
	function get_all_links() {
		$ar = array();
		foreach ($this->rows as $row) {
			foreach ($row['_links'] as $link) {
				$ar[$link] = 0;
			}
		}
		return array_keys($ar);
	}
	function get_filetimes($pages) {
		$filetimes = array();
		foreach ($pages as $page) {
			$filetimes[] = array(
				'page' => $page,
				'filetime' => get_filetime($page),
			);
		}
		return $filetimes;
	}
	function add($page,$name)
	{
		static $moved = array();

		// 無限ループ防止
		if (array_key_exists($name,$this->rows))
		{
			return;
		}

		$source = plugin_tracker_get_source($page);
		if (preg_match('/move\sto\s(.+)/',$source[0],$matches))
		{
			$page = strip_bracket(trim($matches[1]));
			if (array_key_exists($page,$moved) or !is_page($page))
			{
				return;
			}
			$moved[$page] = TRUE;
			return $this->add($page,$name);
		}
		$source = join('',preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/','$1$2',$source));

		// Default value
		$page_filetime = get_filetime($page);
		$row = array(
			'_page'  => "[[$page]]",
			'_refer' => $this->page,
			'_real'  => $name,
			'_update'=> $page_filetime,
			'_past'  => $page_filetime,
		);
		$links = array();
		if ($row['_match'] = preg_match("/{$this->pattern}/s",$source,$matches))
		{
			array_shift($matches);
			foreach ($this->pattern_fields as $key=>$field)
			{
				$row[$field] = trim($matches[$key]);
				if ($field === '_refer') {
					continue;
				}
				$lmatch = null;
				if (preg_match('/\[\[([^\]\]]+)\]/', $row[$field], $lmatch)) {
					$link = $lmatch[1];
					if (is_pagename($link) && $link !== $this->page && $link !== $page) {
						if (!in_array($link, $links)) {
							$links[] = $link;
						}
					}
				}
			}
		}
		$row['_links'] = $links;
		$this->rows[$name] = $row;
	}
	function compare($a, $b)
	{
		foreach ($this->sort_keys as $sort_key)
		{
			$field = $sort_key['field'];
			$dir = $sort_key['dir'];
			$f = $this->fields[$field];
			$sort_type = $f->sort_type;
			$aVal = isset($a[$field]) ? $f->get_value($a[$field]) : '';
			$bVal = isset($b[$field]) ? $f->get_value($b[$field]) : '';
			$c = strnatcmp($aVal, $bVal) * ($dir === SORT_ASC ? 1 : -1);
			if ($c === 0) continue;
			return $c;
		}
		return 0;
	}
	function sort($order)
	{
		if ($order == '')
		{
			return;
		}
		$names = array_flip(array_keys($this->fields));
		$this->order = array();
		foreach (explode(';',$order) as $item)
		{
			list($key,$dir) = array_pad(explode(':',$item),1,'ASC');
			if (!array_key_exists($key,$names))
			{
				continue;
			}
			switch (strtoupper($dir))
			{
				case 'SORT_ASC':
				case 'ASC':
				case SORT_ASC:
					$dir = SORT_ASC;
					break;
				case 'SORT_DESC':
				case 'DESC':
				case SORT_DESC:
					$dir = SORT_DESC;
					break;
				default:
					continue;
			}
			$this->order[$key] = $dir;
		}
		$sort_keys = array();
		foreach ($this->order as $field=>$order)
		{
			if (!array_key_exists($field,$names))
			{
				continue;
			}
			$sort_keys[] = array('field' => $field, 'dir' => $order);
		}
		$this->sort_keys = $sort_keys;
		usort($this->rows, array($this, 'compare'));
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
			return $this->pipe ? str_replace('|','&#x7c;',$arr[0]) : $arr[0];
		}
		$style = count($params) ? $params[0] : $name;
		if (array_key_exists($style,$this->items)
			and array_key_exists($style,$this->fields))
		{
			$str = sprintf($this->fields[$style]->get_style($this->items[$style]),$str);
		}
		return $this->pipe ? str_replace('|','&#x7c;',$str) : $str;
	}
	function replace_title($arr)
	{
		$field = $sort = $arr[1];
		if ($sort == '_name' or $sort == '_page')
		{
			$sort = '_real';
		}
		if (!array_key_exists($field,$this->fields))
		{
			return $arr[0];
		}
		$dir = SORT_ASC;
		$arrow = '';
		$order = $this->order;

		if (is_array($order) && isset($order[$sort]))
		{
			// BugTrack2/106: Only variables can be passed by reference from PHP 5.0.5
			$order_keys = array_keys($order); // with array_shift();

			$index = array_flip($order_keys);
			$pos = 1 + $index[$sort];
			$b_end = ($sort == array_shift($order_keys));
			$b_order = ($order[$sort] == SORT_ASC);
			$dir = ($b_end xor $b_order) ? SORT_ASC : SORT_DESC;
			$arrow = '&br;'.($b_order ? '&uarr;' : '&darr;')."($pos)";

			unset($order[$sort], $order_keys);
		}
		$title = $this->fields[$field]->title;
		$r_page = rawurlencode($this->page);
		$r_config = rawurlencode($this->config->config_name);
		$r_list = rawurlencode($this->list);
		$_order = array("$sort:$dir");
		if (is_array($order))
			foreach ($order as $key=>$value)
				$_order[] = "$key:$value";
		$r_order = rawurlencode(join(';',$_order));

		$script = get_base_uri(PKWK_URI_ABSOLUTE);
		return "[[$title$arrow>$script?plugin=tracker_list&refer=$r_page&config=$r_config&list=$r_list&order=$r_order]]";
	}
	function toString($limit=NULL,$start_n=NULL,$last_n=NULL)
	{
		global $_tracker_messages;

		$source = '';
		$body = array();

		if ($limit !== NULL and count($this->rows) > $limit)
		{
			$source = str_replace(
				array('$1','$2'),
				array(count($this->rows),$limit),
				$_tracker_messages['msg_limit'])."\n";
			$this->rows = array_splice($this->rows,0,$limit);
		} else if (!is_null($start_n) && !is_null($last_n)) {
			// sublist (range "start-last")
			$sublist = array();
			foreach ($this->rows as $row) {
				if ($start_n <= $row['_real'] && $row['_real'] <= $last_n) {
					$sublist[] = $row;
				}
			}
			$this->rows = $sublist;
		}
		if (count($this->rows) == 0)
		{
			return '';
		}
		foreach (plugin_tracker_get_source($this->config->page.'/'.$this->list) as $line)
		{
			if (preg_match('/^\|(.+)\|[hHfFcC]$/',$line))
			{
				$source .= preg_replace_callback('/\[([^\[\]]+)\]/',array(&$this,'replace_title'),$line);
			}
			else
			{
				$body[] = $line;
			}
		}
		foreach ($this->rows as $key=>$row)
		{
			if (!TRACKER_LIST_SHOW_ERROR_PAGE and !$row['_match'])
			{
				continue;
			}
			$this->items = $row;
			foreach ($body as $line)
			{
				if (trim($line) == '')
				{
					// Ignore empty line
					continue;
				}
				$this->pipe = ($line[0] == '|' or $line[0] == ':');
				$source .= preg_replace_callback('/\[([^\[\]]+)\]/',array(&$this,'replace_item'),$line);
			}
		}
		return convert_html($source);
	}
}
function plugin_tracker_get_source($page)
{
	$source = get_source($page);
	// Delete anchor part of Headings (Example: "*Heading1 [#id] AAA" to "*Heading1 AAA")
	$s2 = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m','$1$2',$source);
	// Delete #freeze
	$s3 = preg_replace('/^#freeze\s*$/im', '', $s2);
	// Delete #author line
	$s4 = preg_replace('/^#author\b[^\r\n]*$/im', '', $s3);
	return $s4;
}
