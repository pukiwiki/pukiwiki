<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: make_link.php,v 1.31 2003/03/23 08:24:19 panda Exp $
//

// リンクを付加する
function make_link($string,$page = '')
{
	global $vars;
	static $converter;
	
	if (!isset($converter))
	{
		$converter = new InlineConverter();
	}
	$_converter = $converter; // copy
	return $_converter->convert($string, ($page != '') ? $page : $vars['page']);
}
//インライン要素を置換する
class InlineConverter
{
	var $converters; // as array()
	var $pattern;
	var $pos;
	
	function InlineConverter($converters=NULL,$excludes=NULL)
	{
		if ($converters === NULL)
		{
			$converters = array(
				'plugin',        // インラインプラグイン
				'note',          // 注釈 
				'url',           // URL
				'mailto',        // mailto:
				'interwikiname', // InterWikiName
				'autolink',      // AutoLink
				'bracketname',   // BracketName
				'wikiname',      // WikiName
			);
		}
		if ($excludes !== NULL)
		{
			$converters = array_diff($converters,$excludes);
		}
		$this->converters = array();
		$patterns = array();
		$start = 1;
		
		foreach ($converters as $name)
		{
			$classname = "Link_$name";
			$converter = new $classname($start);
			$pattern = $converter->get_pattern();
			if ($pattern === FALSE)
			{
				continue;
			}
			$patterns[] = "(\n$pattern\n)";
			$this->converters[$start] = $converter;
			$start += $converter->get_count();
			$start++;
		}
		$this->pattern = join('|',$patterns);
	}
	function convert($string,$page)
	{
		$this->page = $page;
		return preg_replace_callback("/{$this->pattern}/x",array(&$this,'replace'),$string);
	}
	function replace($arr)
	{
		$obj = $this->get_converter($arr);
		
		if ($obj !== NULL and $obj->set($arr,$this->page) !== FALSE)
		{
			return $obj->toString();
		}
		return $arr[0];
	}
	function get_objects($string,$page)
	{
		preg_match_all("/{$this->pattern}/x",$string,$matches,PREG_SET_ORDER);
		
		$arr = array();
		foreach ($matches as $match)
		{
			$obj = $this->get_converter($match);
			if ($obj->set($match,$page) !== FALSE)
			{
				$arr[] = $obj; // copy
			}
		}
		return $arr;
	}
	function &get_converter(&$arr)
	{
		foreach (array_keys($this->converters) as $start)
		{
			if ($arr[$start] != '')
			{
				return $this->converters[$start];
			}
		}
		return NULL;
	}
}
//インライン要素集合のベースクラス
class Link
{
	var $start;   // 括弧の先頭番号(0オリジン)
	var $text;    // マッチした文字列全体

	var $type;
	var $page;
	var $name;
	var $alias;

	// constructor
	function Link($start)
	{
		$this->start = $start;
	}
	// マッチに使用するパターンを返す
	function get_pattern()
	{
	}
	// 使用している括弧の数を返す ((?:...)を除く)
	function get_count()
	{
	}
	// マッチしたパターンを設定する
	function set($arr,$page)
	{
	}
	// 文字列に変換する
	function toString()
	{
	}
	
	//private
	// マッチした配列から、自分に必要な部分だけを取り出す
	function splice($arr)
	{
		$count = $this->get_count() + 1;
		$arr = array_splice($arr,$this->start,$count);
		while (count($arr) < $count)
		{
			$arr[] = '';
		}
		$this->text = $arr[0];
		return $arr;
	}
	// 基本パラメータを設定する
	function setParam($page,$name,$type = '',$alias = '')
	{
		static $converter = NULL;
		
		$this->page = $page;
		$this->name = $name;
		$this->type = $type;
		if (preg_match('/\.(gif|png|jpe?g)$/i',$alias))
		{
			$alias = "<img src=\"$alias\" alt=\"$name\" />";
		}
		else if ($alias != '')
		{
			if ($converter === NULL)
			{
				$converter = new InlineConverter(array('plugin'));
			}
			$alias = make_line_rules($converter->convert($alias,$page));
		}
		$this->alias = $alias;
		
		return TRUE;
	}
}
// インラインプラグイン
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
&amp;(\w+) # (1) plugin name
(?:
 \(
  ([^)]*)  # (2) parameter
 \)
)?
(?:
 \{
  (.*)     # (3) body
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
		$this->body = ($arr[3] == '') ? '' : make_link($arr[3]);
		
		if (!exist_plugin_inline($name))
		{
			return FALSE;
		}
		
		return parent::setParam($page,$name,'plugin');
	}
	function toString()
	{
		return $this->make_inline($this->name,$this->param,$this->body);
	}
	function make_inline($func,$param,$body)
	{
		//&hoge(){...}; &fuga(){...}; のbodyが'...}; &fuga(){...'となるので、前後に分ける
		$after = '';
		if (preg_match("/^ ((?!};).*?) }; (.*?) &amp; (\w+) (?: \( ([^()]*) \) )? { (.+)$/x",$body,$matches)) {
			$body = $matches[1];
			$after = $matches[2].$this->make_inline($matches[3],$matches[4],$matches[5]);
		}
		
		// プラグイン呼び出し
		if (exist_plugin_inline($func))
		{
			$str = do_plugin_inline($func,$param,$body);
			if ($str !== FALSE) { //成功
				return $str.$after;
			}
		}
		
		// プラグインが存在しないか、変換に失敗
		return $this->text;
	}
}
// 注釈
class Link_note extends Link
{
	function Link_note($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		return <<<EOD
\(\(    # open paren
 (      # (1) note body
  (?:
   (?R)|(?!\)\)).
  )*
 )
\)\)
EOD;
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		global $foot_explain;
		static $note_id = 0;
		
		$arr = $this->splice($arr);
		
		$id = ++$note_id;
		$note = make_line_rules(make_link($arr[1]));
		
		$foot_explain[$id] = <<<EOD
<a id="notefoot_$id" href="#notetext_$id" class="note_super">*$id</a>
<span class="small">$note</span>
<br />
EOD;
		$name = "<a id=\"notetext_$id\" href=\"#notefoot_$id\" class=\"note_super\">*$id</a>";
		
		return parent::setParam($page,$name);
	}
	function toString()
	{
		return $this->name;
	}
}
// url
class Link_url extends Link
{
	function Link_url($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		$s1 = $this->start + 1;
		$s2 = $this->start + 2;
		return <<<EOD
(?:\[\[              # open bracket
 ([^\]]+)            # (1) alias
 (?:&gt;|>|:)        # '&gt;' or '>' or ':'
)?
(\[)?                # (2) open bracket
(                    # (3) url
 (?:https?|ftp|news)
 (?::\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+)
)
(?($s2)\s([^\]]+)\]) # (4) alias, close bracket if (2)
(?($s1)\]\])         # close bracket if (1)
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
		
		if ($alias == '')
		{
			$alias = $name;
		}
		return parent::setParam($page,$name,'url',$alias);
		
	}
	function toString()
	{
//		global $link_target;
		
		return "<a href=\"{$this->name}\">{$this->alias}</a>";
	}
}
//mailto:
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
(?:\[\[([^\]]+)(?:&gt;|>|:))? # (1) alias
 ([\w.-]+@[\w-]+\.[\w.-]+)    # (2) mailto
(?($s1)\]\])                  # close bracket if (1)
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
		$alias = ($arr[1] == '') ? $name : $arr[1];
		
		return parent::setParam($page,$name,'mailto',$alias);
	}
	function toString()
	{
		return "<a href=\"mailto:{$this->name}\">{$this->alias}</a>";
	}
}
//InterWikiName
class Link_interwikiname extends Link
{
	function Link_interwikiname($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		$s1 = $this->start + 1;
		$s3 = $this->start + 3;
		$s5 = $this->start + 5;
		return <<<EOD
\[\[                    # open bracket
(?:
 (\[\[)?                # (1) open bracket
 ([^\[\]]+)             # (2) alias
 (?:&gt;|>)             # '&gt;' or '>'
)?
(?:
 (\[\[)?                # (3) open bracket
 (\[*?[^\s\]]+?\]*?)    # (4) InterWiki
 (                      # (5)
  (?($s1)\]\]           #  close bracket if (1)
  |(?($s3)\]\])         #   or (3)
  )
 )?
 (?<!&gt;)
 (\:(?:(?<!&gt;|>).)*?) # (6) param
 (?($s5) |              # if !(5)
  (?($s1)\]\]           #  close bracket if (1)
  |(?($s3)\]\])         #   or (3)
  )
 )
)?
\]\]                    # close bracket
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
		
		return parent::setParam($page,$name,'InterWikiName',$alias);
	}
	function toString()
	{
		global $script; //,$interwiki_target;
		
		$r_name = rawurlencode($this->name);
		return "<a href=\"$script?$r_name\">{$this->alias}</a>";
	}
}
// BracketName
class Link_bracketname extends Link
{
	var $anchor,$refer;
	
	function Link_bracketname($start)
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
\[\[                     # open bracket
(?:
 (\[\[)?                 # (1) open bracket
 ([^\[\]]+)              # (2) alias
 (?:&gt;|>)              # '&gt;' or '>'
)?
(\[\[)?                  # (3) open bracket
(                        # (4) PageName
 ($WikiName)             # (5) WikiName
 |
 ($BracketName)          # (6) BracketName
)?
(                        # (7)
 (?($s1)\]\]             #  close bracket if (1)
  |(?($s3)\]\])          #   or (3)
 )
)
(\#(?:[a-zA-Z][\w-]*)?)? # (8) anchor
(?($s7)|                 # if !(7)
 (?($s1)\]\]             #  close bracket if (1)
  |(?($s3)\]\])          #   or (3)
 )
)
\]\]                     # close bracket
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
		
		$alias = $arr[2];
		$name = $arr[4];
		$this->anchor = $arr[8];
		
		if ($name == '' and $this->anchor == '')
		{
			return FALSE;
		}
		if ($name != '' and preg_match("/^$WikiName$/",$name))
		{
			return parent::setParam($page,$name,'pagename',$alias);
		}
		if ($alias == '')
		{
			$alias = $name.$this->anchor;
		}
		if ($name == '' and $this->anchor == '')
		{
			return FALSE;
		}
		
		$name = get_fullname($name,$page);
		
		if ($name != '' and !preg_match("/^($WikiName)|($BracketName)$/",$name))
		{
			return FALSE;
		}
		return parent::setParam($page,$name,'pagename',$alias);
	}
	function toString()
	{
		return make_pagelink(
			$this->name,
			$this->alias,
			$this->anchor,
			$this->page
		);
	}
}
// WikiName
class Link_wikiname extends Link
{
	function Link_wikiname($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		global $WikiName,$nowikiname;
		
		return $nowikiname ? FALSE : "($WikiName)";
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
	function toString()
	{
		return make_pagelink(
			$this->name,
			$this->alias,
			'',
			$this->page
		);
	}
}
// AutoLink
class Link_autolink extends Link
{
	var $forceignorepages = array();
	
	function Link_autolink($start)
	{
		parent::Link($start);
	}
	function get_pattern()
	{
		global $autolink;
		static $auto,$forceignorepages;
		
		if (!$autolink or !file_exists(CACHE_DIR.'autolink.dat'))
		{
			return FALSE;
		}
		if (!isset($auto)) // and/or !isset($forceignorepages)
		{
			@list($auto,$forceignorepages) = file(CACHE_DIR.'autolink.dat');
			$forceignorepages = explode("\t",$forceignorepages);
		}
		$this->forceignorepages = $forceignorepages;
		return "($auto)";
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		global $WikiName;
		
		$arr = $this->splice($arr);
		$name = $alias = $arr[0];
		// 無視リストに含まれている、あるいは存在しないページを捨てる
		if (in_array($name,$this->forceignorepages) or !is_page($name))
		{
			return FALSE;
		}
		return parent::setParam($page,$name,'pagename',$alias);
	}
	function toString()
	{
		return make_pagelink(
			$this->name,
			$this->alias,
			'',
			$this->page
		);
	}
}
// ページ名のリンクを作成
function make_pagelink($page,$alias='',$anchor='',$refer='')
{
	global $script,$vars,$show_title,$show_passage,$link_compact,$related;
	
	$s_page = htmlspecialchars(strip_bracket($page));
	$s_alias = ($alias == '') ? $s_page : $alias;
	
	if ($page == '')
	{
		return "<a href=\"$anchor\">$s_alias</a>";
	}
	
	$r_page = rawurlencode($page);
	$r_refer = ($refer == '') ? '' : '&amp;refer='.rawurlencode($refer);
	
	if (!array_key_exists($page,$related) and $page != $vars['page'] and is_page($page))
	{
		$related[$page] = get_filetime($page);
	}
	
	if (is_page($page))
	{
		$passage = get_pg_passage($page,FALSE);
		$title = $link_compact ? '' : " title=\"$s_page$passage\"";
		return "<a href=\"$script?$r_page$anchor\"$title>$s_alias</a>";
	}
	else
	{
		return $link_compact ?
			"$s_alias<a href=\"$script?cmd=edit&amp;page=$r_page$r_refer\">?</a>" :
			"<span class=\"noexists\">$s_alias<a href=\"$script?cmd=edit&amp;page=$r_page$r_refer\">?</a></span>";
	}
}
// 相対参照を展開
function get_fullname($name,$refer)
{
	global $defaultpage;
	
	if ($name == './')
	{
		return $refer;
	}
	
	if (substr($name,0,2) == './')
	{
		$arrn = preg_split('/\//',$name,-1,PREG_SPLIT_NO_EMPTY);
		$arrn[0] = $refer;
		return join('/',$arrn);
	}
	
	if (substr($name,0,3) == '../')
	{
		$arrn = preg_split('/\//',$name,-1,PREG_SPLIT_NO_EMPTY);
		$arrp = preg_split('/\//',$refer,-1,PREG_SPLIT_NO_EMPTY);
		
		while (count($arrn) > 0 and $arrn[0] == '..')
		{
			array_shift($arrn);
			array_pop($arrp);
		}
		$name = count($arrp) ? join('/',array_merge($arrp,$arrn)) :
			(count($arrn) ? "$defaultpage/".join('/',$arrn) : $defaultpage);
	}
	return $name;
}
?>
