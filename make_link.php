<?php
// Last-Update:2002-09-24 rev.1

// リンクを付加する
function p_make_link($name,$page = '')
{
	global $vars,$LinkPattern;

	if ($page == '')
		$page = $vars["page"];

	$obj = new link_wrapper($page);
	return $obj->make_link($name);
}
class link_wrapper
{
	var $page;
	function link_wrapper($page)
	{
		$this->page = $page; 
	}
	function &_convert($arr)
	{
		if ($arr[4]  != '')
			return new link_url($arr[4],$arr[2].$arr[5]);
		if ($arr[7]  != '')
			return new link_mailto($arr[7],$arr[6]);
		if ($arr[16] != '')
			return new link_interwiki("[[$arr[16]$arr[18]]]",$arr[10]);
		if ($arr[12] != '' or $arr[14] != '')
			return expand_bracket($arr,$this->page);
		if ($arr[19] != '')
			return new link_wikiname($arr[19],$arr[19],'',$this->page);

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
		if (!is_array($str))
			return $this->_replace($str);

		$tmp = array();

		foreach ($str as $line)
			$tmp[] = $this->_replace($line);

		return $tmp;
	}
	function &get_link($str)
	{
		global $LinkPattern;

		preg_match_all($LinkPattern,$str,$matches,PREG_SET_ORDER);

		$tmp = array();

		foreach ($matches as $arr)
			$tmp[] =& $this->_convert($arr);

		return $tmp;
	}
}
//BracketNameの処理
function &expand_bracket($name,$refer)
{
	global $WikiName,$BracketName,$LinkPattern,$defaultpage;

	if (is_array($name))
		$arr = $name;
	else if (preg_match("/^$WikiName$/",$name))
		return new link_wikiname($name);
	else if (!preg_match($LinkPattern,$name,$arr) or $arr[12] == '')
		return new link($name);

	$arr = array_slice($arr,8,7);
	$_name = array_shift($arr);

	$bracket = ($arr[0] or $arr[2]);
	$alias = $arr[1];
	$name = $arr[3];
	$anchor = $arr[5];

	if ($name != '')
	{
		if ($alias == '' and $anchor == '')
			$name = "[[$name]]";
		else if (!$bracket and preg_match("/^$WikiName$/",$name))
			return new link_wikiname($name,$alias,$anchor,$refer);
		else
			$name = "[[$name]]";
	}

	if ($alias == '')
		$alias = strip_bracket($name).$anchor;

	if ($name == '')
		return ($anchor == '') ? new link($_name) : new link_wikiname($name,$alias,$anchor,$refer);

	if ($name == '[[./]]' and preg_match("/^$WikiName$/",$refer))
		return new link_wikiname($refer,$alias,$anchor,$refer); 

	if (substr($name,0,4) == '[[./')
		$name = '[['.strip_bracket($refer).substr($name,(strlen($name) > 6) ? 3:4); // ひー ^^;)
	else if (substr($name,0,5) == '[[../')
	{
		$arrn = preg_split("/\//",strip_bracket($name),-1,PREG_SPLIT_NO_EMPTY);
		$arrp = preg_split("/\//",strip_bracket($refer),-1,PREG_SPLIT_NO_EMPTY);
		while ($arrn[0] == '..') { array_shift($arrn); array_pop($arrp); }
		$name = (count($arrp)) ? '[['.join('/',array_merge($arrp,$arrn)).']]' :
			((count($arrn)) ? "[[$defaultpage/".join('/',$arrn).']]' : $defaultpage);
	}

	if ($name == '' or preg_match("/^$WikiName$/",$name))
		return new link_wikiname($name,$alias,$anchor,$refer);
	else if (!preg_match("/^$BracketName$/",$name)) 
		return new link($_name);

	return new link_wikiname($name,$alias,$anchor,$refer);
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
			$this->image = "<img src=\"$name\" border=\"0\" alt=\"$alias\">";
		} else if (preg_match("/\.(gif|png|jpeg|jpg)$/i",$alias)) {
			$this->is_image = TRUE;
			$this->image = "<img src=\"$alias\" border=\"0\" alt=\"$name\">";
		} else {
			$this->is_image = FALSE;
			$this->image = '';
		}
	}
	function toString()
	{
		global $link_target;

		return "<a href=\"{$this->name}\" target=\"$link_target\">"
			.($this->is_image ? $this->image : $this->alias)
			.'</a>';
	}
}
class link_mailto extends link
{
	function link_mailto($name,$alias)
	{
		parent::link($name,'mailto',($alias == '') ? $name : $alias);
	}
	function toString()
	{
		return "<a href=\"mailto:$this->name\">{$this->alias}</a>";
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

		return "<a href=\"$script?$this->rawname\" target=\"$interwiki_target\">{$this->alias}</a>";
	}
}
class link_wikiname extends link
{
	var $is_bracketname; //FALSE:'WikiName' TRUE:'BracketName';
	var $anchor;
	var $strip,$special,$rawname,$rawrefer,$passage;

	function link_wikiname($name,$alias='',$anchor='',$refer='')
	{
		global $script,$vars,$related;

		$this->is_bracketname = (substr($name,0,1) == '[');
		parent::link($name,$this->is_bracketname ? 'BracketName' : 'WikiName',($alias == '') ? strip_bracket($name).$anchor : $alias);
		$this->anchor = $anchor;
		$this->strip = strip_bracket($name);
		$this->char = ((ord($this->strip) < 128) ? '0' : '1').$this->strip;
		$this->special = htmlspecialchars($this->strip);
		$this->rawname = rawurlencode($name);
		$this->rawrefer = rawurlencode($refer);

		if ($vars['page'] != $name and is_page($name))
			$related['t'.filemtime(get_filename(encode($name)))] = "<a href=\"$script?{$this->rawname}\">{$this->special}</a>".$this->passage();
	}

	function passage()
	{
		global $show_passage;
		$passage = get_pg_passage($this->name,FALSE);
		$this->passage = $show_passage ? $passage : '';
		return $passage;
	}

	function toString($refer = '')
	{
		global $script;

		if ($this->name == '' and $this->anchor != '')
			return "<a href=\"{$this->anchor}\">{$this->alias}</a>";

		if (is_page($this->name))
			return "<a href=\"$script?{$this->rawname}{$this->anchor}\" title=\"{$this->special}{$this->passage}\">{$this->alias}</a>";
		else {
			$rawrefer = ($refer != '') ? rawurlencode($refer) : $this->rawrefer;
			return "<span class=\"noexists\">$this->alias<a href=\"$script?cmd=edit&amp;page={$this->rawname}&amp;refer=$rawrefer\">?</a></span>";
		}
	}
}
?>
