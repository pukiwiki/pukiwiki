<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: make_link.php,v 1.7 2003/01/27 05:38:44 panda Exp $
//

// リンクを付加する
function make_link($name,$page = '')
{
	global $vars,$LinkPattern;

	if ($page == '') {
		$page = $vars['page'];
	}

	$obj = new link_wrapper($page);
	return $obj->make_link($name);
}
class link_wrapper
{
	var $page;
	function link_wrapper($page='')
	{
		$this->page = $page; 
	}
	function &_convert($arr)
	{
		// url
		if ($arr[4]  != '') {
			$anchor = array_key_exists(5,$arr) ? $arr[5] : '';
			return new link_url($arr[4],$arr[2].$anchor);
		}
		// mailto
		if ($arr[7]  != '') {
			return new link_mailto($arr[7],$arr[6]);
		}
		// interwiki
		if (array_key_exists(18,$arr) and $arr[18] != '') {
			return new link_interwiki("[[$arr[18]$arr[20]]]",$arr[10]);
		}
		// BracketName
		if ($arr[12] != '' or (array_key_exists(16,$arr) and $arr[16] != '')) {
			return expand_bracket($arr,$this->page);
		}
		// WikiName
		if (array_key_exists(21,$arr) and $arr[21] != '') {
			return new link_wikiname($arr[21],$arr[21],'',$this->page);
		}
		return new link($arr[0]); //どれでもない
	}
	function &_replace_link($arr)
	{
		$obj = $this->_convert($arr);

		return $obj->toString();
	}
	function &_replace($str)
	{
		global $LinkPattern;

		return preg_replace_callback($LinkPattern,array($this,'_replace_link'), $str);
	}
	function &make_link($str)
	{
		if (!is_array($str)) {
			return $this->_replace($str);
		}
		
		$tmp = array();
		
		foreach ($str as $line) {
			$tmp[] = $this->_replace($line);
		}
		
		return $tmp;
	}
	function &get_link($str)
	{
		global $LinkPattern;
		
		preg_match_all($LinkPattern,$str,$matches,PREG_SET_ORDER);
		
		$tmp = array();
		
		foreach ($matches as $arr) {
			$tmp[] =& $this->_convert($arr);
		}
		
		return $tmp;
	}
}
//BracketNameの処理
function &expand_bracket($name,$refer)
{
	global $WikiName,$BracketName,$LinkPattern,$defaultpage;
	
	$refer = strip_bracket($refer); //fool proof
	
	if (is_array($name)) {
		$arr = $name;
	}
	else if (!preg_match($LinkPattern,$name,$arr) or $arr[12] == '') {
		return new link($name);
	}
	
//	$arr = array_slice($arr,8,9);
	$orig = $arr[8];
	$bracket = ($arr[9] or $arr[11]);
	$alias = $arr[10];
	$name = $arr[12];
	$anchor = array_key_exists(16,$arr) ? $arr[16] : '';
	
	if ($name != '' and !$bracket and preg_match("/^$WikiName$/",$name)) {
		return new link_wikiname($name,$alias,$anchor,$refer);
	}
	
	if ($alias == '') {
		$alias = $name.$anchor;
	}
	
	if ($name == '') {
		return ($anchor == '') ? new link($orig) : new link_wikiname($name,$alias,$anchor,$refer);
	}
	
	$name = get_fullname($name,$refer);
	
	if ($name == '' or preg_match("/^$WikiName$/",$name)) {
		return new link_wikiname($name,$alias,$anchor,$refer);
	}
	else if (!preg_match("/^$BracketName$/",$name)) {
		return new link($_name);
	}
	
	return new link_wikiname($name,$alias,$anchor,$refer);
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
class link
{
	var $type,$name,$char,$alias;
	
	function link($name,$type = '',$alias = '')
	{
		$this->name = $name;
		$this->type = $type;
		$this->char = '0'.$name;
		$this->alias = $alias;
	}
	function toString()
	{
		return $this->name;
	}
	function compare($a,$b)
	{
		return strnatcasecmp($a->char,$b->char);
	}
}
class link_url extends link
{
	var $is_image,$image;
	
	function link_url($name,$alias)
	{
		parent::link($name,'url',($alias == '') ? $name : $alias);
		
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
	}
	function toString()
	{
		global $link_target;
		
		return "<a href=\"{$this->name}\">"
			.($this->is_image ? $this->image : $this->alias)
			.'</a>';
	}
}
class link_mailto extends link
{
	var $is_image,$image;
	
	function link_mailto($name,$alias)
	{
		parent::link($name,'mailto',($alias == '') ? $name : $alias);
		if (preg_match("/\.(gif|png|jpeg|jpg)$/i",$alias)) {
			$this->is_image = TRUE;
			$this->image = "<img src=\"$alias\" alt=\"$name\" />";
		}
		else {
			$this->is_image = FALSE;
			$this->image = '';
		}
	}
	function toString()
	{
		return "<a href=\"mailto:$this->name\">"
			.($this->is_image ? $this->image : $this->alias)
			.'</a>';
	}
}
class link_interwiki extends link
{
	var $rawname;
	
	function link_interwiki($name,$alias)
	{
		parent::link($name,'InterWikiName',($alias == '') ? strip_bracket($name) : $alias);
		$this->rawname = rawurlencode($name);
	}
	function toString()
	{
		global $script,$interwiki_target;
		
		return "<a href=\"$script?$this->rawname\">{$this->alias}</a>";
	}
}
class link_wikiname extends link
{
	var $name,$char,$anchor,$refer;
	
	function link_wikiname($name,$alias='',$anchor='',$refer='')
	{
		global $script,$vars,$whatsnew,$related;
		
		$name = strip_bracket($name); //保険
		
		parent::link($name,'WikiName',($alias == '') ? $name.$anchor : $alias);
		$this->name = $name;
//		$this->char = ((ord($name) < 128) ? '0' : '1').$name;
		$this->anchor = $anchor;
		$this->refer = $refer;
		
		if (($name != $vars['page']) and ($name != $whatsnew) and is_page($name)) {
			$related[$name] = get_filetime($name);
		}
	}
	function toString($refer = '')
	{
		global $script;
		
		if ($this->name == '' and $this->anchor != '') {
			return "<a href=\"{$this->anchor}\">{$this->alias}</a>";
		}
		return make_pagelink(
			$this->name,
			$this->alias,
			$this->anchor,
			($refer == '') ? $this->refer : $refer
		);
	}
}
function make_pagelink($page,$alias='',$anchor='',$refer='')
{
	global $script,$show_passage;
	
	$r_page = rawurlencode($page);
	$s_page = htmlspecialchars(strip_bracket($page));
	$r_refer = ($refer == '') ? '' : '&amp;refer='.rawurlencode($refer);
	$s_alias = ($alias == '') ? $s_page : htmlspecialchars($alias);
	
	if (is_page($page)) {
		$passage = $show_passage ? ' '.get_pg_passage($page,FALSE) : '';
		return "<a href=\"$script?$r_page$anchor\" title=\"$s_page$passage\">$s_alias</a>";
	}
	else {
		return "<span class=\"noexists\">$s_alias<a href=\"$script?cmd=edit&amp;page=$r_page$r_refer\">?</a></span>";
	}
}
?>
