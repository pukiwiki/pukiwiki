<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: make_link.php,v 1.9 2003/01/31 01:49:35 panda Exp $
//

// リンクを付加する
function make_link($string,$page = '')
{
	global $vars;
	static $converter;
	
	if (!isset($converter)) {
		$converter = new InlineConverter();
	}
	
	return $converter->convert($string, ($page != '') ? $page : $vars['page']);
}

class InlineConverter
{
	var $converters; // as array()
	var $pattern;
	var $pos;
	
	function InlineConverter()
	{
		$converters = array('plugin','url','mailto','interwiki','page','auto');
		$this->converters = array();
		$pattern = array();
		$start = 1;
		
		foreach ($converters as $name) { 
			$classname = "Link_$name";
			$converter = new $classname($start);
			$pattern[] = '('.$converter->get_pattern().')';
			$this->converters[$start] = $converter;
			$start += $converter->get_count();
			$start++;
		}
		$this->pattern = join('|',$pattern);
	}
	function convert($string,$page)
	{
		$this->page = $page;
		return preg_replace_callback("/{$this->pattern}/x",array(&$this,'replace'),$string);
	}
	function replace($arr)
	{
		$obj = $this->get_converter($arr);
		
		if ($obj === NULL) {
			return $arr[0];
		}
		
		$obj->set($arr,$this->page);
		return $obj->toString();
	}
	function get_objects($string,$page)
	{
		preg_match_all("/{$this->pattern}/x",$string,$matches,PREG_SET_ORDER);
		
		$arr = array();
		foreach ($matches as $match) {
			$obj = $this->get_converter($match);
			$obj->set($match,$page);
			$arr[] = $obj; // copy
		}
		return $arr;
	}
	function &get_converter(&$arr)
	{
		foreach (array_keys($this->converters) as $start) {
			if ($arr[$start] != '') {
				return $this->converters[$start];
			}
		}
		return NULL;
	}
}

class Link
{
	var $start;   // 括弧の先頭番号(0オリジン)
	var $text;    // マッチした文字列全体
	var $valid;

	var $type;
	var $page;
	var $name;
	var $alias;

	function Link($start)
	{
		$this->start = $start;
	}
	function get_pattern()
	{
	}
	function get_count()
	{
	}
	function set($arr,$page)
	{
	}
	function toString()
	{
	}
	function splice($arr)
	{
		$count = $this->get_count() + 1;
		$arr = array_splice($arr,$this->start,$count);
		while (count($arr) < $count) {
			$arr[] = '';
		}
		$this->text = $arr[0];
		return $arr;
	}
	
	//private
	function setParam($page,$name,$type = '',$alias = '')
	{
		$this->page = $page;
		$this->name = $name;
		$this->type = $type;
		$this->alias = $alias;
		
		$this->valid = TRUE;
		return TRUE;
	}
}

class Link_auto extends Link
{
	function Link_auto($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		global $WikiName,$autolink,$nowikiname;
		
		if (!$autolink) {
			return $nowikiname ? '(?!)' : $WikiName;
		}
		
		$pages = get_existpages();
		$arr = array();
		foreach ($pages as $page) {
			if (preg_match("/^$WikiName$/",$page) ? $nowikiname : strlen($page) >= $autolink) {
				$pattern = '(?:'.preg_quote($page,'/').')';
				$arr[$pattern] = strlen($pattern);
			}
		}
		arsort($arr,SORT_NUMERIC);
		$arr = array_keys($arr);
		if (!$nowikiname) {
			array_push($arr,"(?:$WikiName)");
		}
		return '('.join('|',$arr).')';
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		$arr = $this->splice($arr); 
		$name = $alias = $arr[0];
		return parent::setParam($page,$name,'pagename',$alias);
	}
	function toString($page = '')
	{
		return make_pagelink(
			$this->name,
			$this->alias,
			'',
			($page == '') ? $this->page : $page
		);
	}
}
class Link_interwiki extends Link
{
	var $r_name;
	
	function Link_interwiki($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		$s1 = $this->start + 1;
		$s3 = $this->start + 3;
		$s5 = $this->start + 5;
		return <<<EOD
\[\[                       (?#open bracket)
(?:
 (\[\[)?                  (?#<1>:open bracket)
 ([^\[\]]+)               (?#<2>:alias)
 (?:&gt;|>)               (?# '&gt;' or '>')
)?
(?:
 (\[\[)?                  (?#<3>:open bracket)
 (\[*?[^\s\]]+?\]*?)      (?#<4>InterWiki)
 (
  (?($s1)\]\]             (?#<5>:close bracket if <1>)
  |(?($s3)\]\])           (?# or <3>)
  )
 )?
 (\:.*?)                  (?#<6>param)
 (?($s5) |                (?#if !<5>)
  (?($s1)\]\]             (?# close bracket if <1>)
  |(?($s3)\]\])           (?#  or <3>)
  )
 )
)?
\]\]                       (?#close bracket)
EOD;
	}
	function get_count()
	{
		return 6;
	}
	function set($arr,$page)
	{
		$arr = $this->splice($arr);
		
		$name = '[['.$arr[4].$arr[6].']]';
		$alias = ($arr[2] != '') ? $arr[2] : strip_bracket($name);
		
		$this->r_name = rawurlencode($name);
		
		return parent::setParam($page,$name,'InterWikiName',$alias);
	}
	function toString()
	{
		global $script; //,$interwiki_target;
		
		return "<a href=\"$script?$this->r_name\">{$this->alias}</a>";
	}
}
class Link_mailto extends Link
{
	var $is_image,$image;
	
	function Link_mailto($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		$s1 = $this->start + 1;
		return <<<EOD
(?:\[\[([^\]]+)(?:&gt;|>|:))?(?#<1>:alias)
 ([\w.-]+@[\w-]+\.[\w.-]+)   (?#<2>:mailto>)
(?($s1)\]\])                 (?# close bracket if <1>)
EOD;
	}
	function get_count()
	{
		return 2;
	}
	function set($arr,$page)
	{
		$arr = $this->splice($arr);
		
		$name = $arr[2];
		$alias = $arr[1];
		
		if (preg_match("/\.(gif|png|jpeg|jpg)$/i",$alias)) {
			$this->is_image = TRUE;
			$this->image = "<img src=\"$alias\" alt=\"$name\" />";
		}
		else {
			$this->is_image = FALSE;
			$this->image = '';
		}
		return parent::setParam($page,$name,'mailto',($alias == '') ? $name : $alias);
	}
	function toString()
	{
		return "<a href=\"mailto:$this->name\">"
			.($this->is_image ? $this->image : $this->alias)
			.'</a>';
	}
}
class Link_page extends Link
{
	var $anchor,$refer;
	
	function Link_page($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		global $WikiName,$BracketName;
		
		$s1 = $this->start + 1;
		$s3 = $this->start + 3;
		$s7 = $this->start + 7;
		return <<<EOD
\[\[                     (?#open bracket)
(?:
 (\[\[)?                 (?#<1>:open bracket)
 ([^\[\]]+)              (?#<2>:alias)
 (?:&gt;|>)              (?# '&gt;' or '>')
)?
(\[\[)?                  (?#<3>:open bracket)
(                        (?#<4>PageName)
 ($WikiName)             (?#<5>WikiName)
 |
 ($BracketName)          (?#<6>BracketName)
)?
(                        (?#<7>)
 (?($s1)\]\]             (?# close bracket if <1>)
  |(?($s3)\]\])          (?#  or <3>)
 )
)
(\#(?:[a-zA-Z][\w-]*)?)? (?#<8>anchor)
(?($s7)|                 (?#if !<7>)
 (?($s1)\]\]             (?# close bracket if <1>)
  |(?($s3)\]\])          (?#  or <3>)
 )
)
\]\]                     (?#close bracket)
EOD;
	}
	function get_count()
	{
		return 8;
	}
	function set($arr,$page)
	{
		global $WikiName,$BracketName;
		
		$arr = $this->splice($arr);
		
		$alias = make_link($arr[2]);
		$name = $arr[4];
		$this->anchor = $arr[8];
		
		if ($name == '' and $this->anchor == '') {
			$this->valid = FALSE;
			return FALSE;
		}
		
		if ($name != '' and preg_match("/^$WikiName$/",$name)) {
			return parent::setParam($page,$name,'pagename',$alias);
		}
		if ($alias == '') {
			$alias = $name.$this->anchor;
		}
		if ($name == '' and $this->anchor == '') {
			$this->valid = FALSE;
			return FALSE;
		}
		$name = get_fullname($name,$page);
		
		if ($name != '' and !preg_match("/^($WikiName)|($BracketName)$/",$name)) {
			$this->valid = FALSE;
			return FALSE;
		}
		parent::setParam($page,$name,'pagename',$alias);
	}
	function toString()
	{
		global $script; //,$interwiki_target;
		
		if (!$this->valid) {
			return $this->text;
		}
		return make_pagelink(
			$this->name,
			$this->alias,
			$this->anchor,
			$this->page
		);
	}
}
class Link_plugin extends Link
{
	var $param,$body;
	
	function Link_plugin($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		return <<<EOD
&amp;(\w+) (?#<1>plugin name)
(?:
 \(
  ([^)]*)  (?#<2>parameter)
 \)
)?
(?:
 \{
  (.*)     (?#<3>body)
 \}
)?
;
EOD;
	}
	function get_count()
	{
		return 3;
	}
	function set($arr,$page)
	{
		$arr = $this->splice($arr);
		
		$name = $arr[1];
		$this->param = $arr[2];
		$this->body = $arr[3];
		
		if (!exist_plugin_inline($name)) {
			$this->valid = FALSE;
			return FALSE;
		}
		
		return parent::setParam($page,$name,'plugin','');
	}
	function toString($refer = '')
	{
		if (!$this->valid) {
			return $this->text;
		}
		return $this->make_inline($this->name,$this->param,$this->body);
	}
	function make_inline($func,$param,$body)
	{
		static $pattern = '/
			&amp;(\w+)
			(?: \( ([^)]*) \) )?
			(?: \{ (.*)    \} )?
			;
		/ex';
		
		if ($body != '') {
			$body = make_link($body);
		}
		
		//&hoge(){...}; &fuga(){...}; のbodyが'...}; &fuga(){...'となるので、前後に分ける
		$after = '';
		if (preg_match("/^ (.*) }; ( .+ &amp; \w+ (?: \( [^()]* \) )? { .+ ) $/x",$body,$matches)) {
			$body = $matches[1];
			$after = make_link($matches[2].'};');
		}
		
		// プラグイン呼び出し
		if (exist_plugin_inline($func)) {
			$str = do_plugin_inline($func,$param,$body);
			if ($str !== FALSE) { //成功
				return $str.$after;
			}
		}
		
		// プラグインが存在しないか、変換に失敗
		return $this->text;
	}
}
class Link_url extends Link
{
	var $is_image,$image;
	
	function Link_url($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		$s1 = $this->start + 1;
		$s2 = $this->start + 2;
		return <<<EOD
(?:\[\[              (?# open bracket)
 ([^\]]+)            (?#<1>:alias)
 (?:&gt;|>|:)        (?# '&gt;' or '>' or ':')
)?
(\[)?                (?#<2>:open bracket)
(                    (?#<3>:url)
 (?:https?|ftp|news)
 (?::\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+)
)
(?($s2)\s([^\]]+)\]) (?#<4>:alias, close bracket if <2>)
(?($s1)\]\])         (?# close bracket if <1>)
EOD;
	}
	function get_count()
	{
		return 4;
	}
	function set($arr,$page)
	{
		$arr = $this->splice($arr);
		
		$name = $arr[3];
		$anchor = $arr[4];
		$alias = $arr[1].$anchor;
		
		if ($alias == '' and preg_match("/\.(gif|png|jpeg|jpg)$/i",$name)) {
			$this->is_image = TRUE;
			if ($alias == '') {
				$alias = $name;
			}
			$this->image = "<img src=\"$name\" alt=\"$alias\" />";
		}
		else if (preg_match("/\.(gif|png|jpeg|jpg)$/i",$alias)) {
			$this->is_image = TRUE;
			$this->image = "<img src=\"$alias\" alt=\"$name\" />";
		}
		else {
			$this->is_image = FALSE;
			$this->image = '';
		}
		return parent::setParam($page,$name,'url',($alias == '') ? $name : $alias);
		
	}
	function toString()
	{
		global $link_target;
		
		return "<a href=\"{$this->name}\">"
			.($this->is_image ? $this->image : $this->alias)
			.'</a>';
	}
}

// ページ名のリンクを作成
function make_pagelink($page,$alias='',$anchor='',$refer='')
{
	global $script,$vars,$show_passage,$related;
	
	$r_page = rawurlencode($page);
	$s_page = htmlspecialchars(strip_bracket($page));
	$r_refer = ($refer == '') ? '' : '&amp;refer='.rawurlencode($refer);
	$s_alias = ($alias == '') ? $s_page : $alias;
	
	if (!array_key_exists($page,$related) and $page != $vars['page'] and is_page($page)) {
		$related[$page] = get_filetime($page);
	}

	
	if (is_page($page)) {
		$passage = $show_passage ? ' '.get_pg_passage($page,FALSE) : '';
		return "<a href=\"$script?$r_page$anchor\" title=\"$s_page$passage\">$s_alias</a>";
	}
	else {
		return "<span class=\"noexists\">$s_alias<a href=\"$script?cmd=edit&amp;page=$r_page$r_refer\">?</a></span>";
	}
}
// 相対参照を展開
function get_fullname($name,$refer)
{
	global $defaultpage;
	
	if ($name == './') {
		return $refer;
	}
	
	if (substr($name,0,2) == './') {
		return $refer.substr($name,1);
	}
	
	if (substr($name,0,3) == '../') {
		$arrn = preg_split('/\//',$name,-1,PREG_SPLIT_NO_EMPTY);
		$arrp = preg_split('/\//',$refer,-1,PREG_SPLIT_NO_EMPTY);
		
		while (count($arrn) > 0 and $arrn[0] == '..') {
			array_shift($arrn);
			array_pop($arrp);
		}
		$name = count($arrp) ? join('/',array_merge($arrp,$arrn)) :
			(count($arrn) ? "$defaultpage/".join('/',$arrn) : $defaultpage);
	}
	return $name;
}
?>
