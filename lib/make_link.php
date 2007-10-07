<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: make_link.php,v 1.37 2007/10/07 13:08:50 henoheno Exp $
// Copyright (C)
//   2003-2007 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions

// Hyperlink decoration
function make_link($string, $page = '')
{
	global $vars;
	static $converter;

	if (! isset($converter)) $converter = new InlineConverter();

	$clone = $converter->get_clone($converter);

	return $clone->convert($string, ($page != '') ? $page : $vars['page']);
}

// Converters of inline element
class InlineConverter
{
	var $converters; // as array()
	var $pattern;
	var $pos;
	var $result;

	function get_clone($obj) {
		static $clone_func;

		if (! isset($clone_func)) {
			if (version_compare(PHP_VERSION, '5.0.0', '<')) {
				$clone_func = create_function('$a', 'return $a;');
			} else {
				$clone_func = create_function('$a', 'return clone $a;');
			}
		}
		return $clone_func($obj);
	}

	function __clone() {
		$converters = array();
		foreach ($this->converters as $key=>$converter) {
			$converters[$key] = $this->get_clone($converter);
		}
		$this->converters = $converters;
	}

	function InlineConverter($converters = NULL, $excludes = NULL)
	{
		if ($converters === NULL) {
			$converters = array(
				'plugin',        // Inline plugins
				'note',          // Footnotes
				'url',           // URLs
				'url_interwiki', // URLs (interwiki definition)
				'mailto',        // mailto: URL schemes
				'interwikiname', // InterWikiNames
				'autoalias',     // AutoAlias
				'autolink',      // AutoLinks
				'bracketname',   // BracketNames
				'wikiname',      // WikiNames
				'autoalias_a',   // AutoAlias(alphabet)
				'autolink_a',    // AutoLinks(alphabet)
			);
		}

		if ($excludes !== NULL)
			$converters = array_diff($converters, $excludes);

		$this->converters = $patterns = array();
		$start = 1;

		foreach ($converters as $name) {
			$classname = 'Link_' . $name;
			$converter = new $classname($start);
			$pattern   = $converter->get_pattern();
			if ($pattern === FALSE) continue;

			$patterns[] = '(' . "\n" . $pattern . "\n" . ')';
			$this->converters[$start] = $converter;
			$start += $converter->get_count();
			++$start;
		}
		$this->pattern = join('|', $patterns);
	}

	function convert($string, $page)
	{
		$this->page   = $page;
		$this->result = array();

		$string = preg_replace_callback('/' . $this->pattern . '/x',
			array(& $this, 'replace'), $string);

		$arr = explode("\x08", make_line_rules(htmlspecialchars($string)));
		$retval = '';
		while (! empty($arr)) {
			$retval .= array_shift($arr) . array_shift($this->result);
		}
		return $retval;
	}

	function replace($arr)
	{
		$obj = $this->get_converter($arr);

		$this->result[] = ($obj !== NULL && $obj->set($arr, $this->page) !== FALSE) ?
			$obj->toString() : make_line_rules(htmlspecialchars($arr[0]));

		return "\x08"; // Add a mark into latest processed part
	}

	function get_objects($string, $page)
	{
		$matches = $arr = array();
		preg_match_all('/' . $this->pattern . '/x', $string, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$obj = $this->get_converter($match);
			if ($obj->set($match, $page) !== FALSE) {
				$arr[] = $this->get_clone($obj);
				if ($obj->body != '')
					$arr = array_merge($arr, $this->get_objects($obj->body, $page));
			}
		}
		return $arr;
	}

	function & get_converter(& $arr)
	{
		foreach (array_keys($this->converters) as $start) {
			if ($arr[$start] == $arr[0])
				return $this->converters[$start];
		}
		return NULL;
	}
}

// Base class of inline elements
class Link
{
	var $start;   // Origin number of parentheses (0 origin)
	var $text;    // Matched string

	var $type;
	var $page;
	var $name;
	var $body;
	var $alias;

	// Constructor
	function Link($start)
	{
		$this->start = $start;
	}

	// Return a regex pattern to match
	function get_pattern() {}

	// Return number of parentheses (except (?:...) )
	function get_count() {}

	// Set pattern that matches
	function set($arr, $page) {}

	function toString() {}

	// Private: Get needed parts from a matched array()
	function splice($arr)
	{
		$count = $this->get_count() + 1;
		$arr   = array_pad(array_splice($arr, $this->start, $count), $count, '');
		$this->text = $arr[0];
		return $arr;
	}

	// Set basic parameters
	function setParam($page, $name, $body, $type = '', $alias = '')
	{
		static $converter = NULL;

		$this->page = $page;
		$this->name = $name;
		$this->body = $body;
		$this->type = $type;
		if (! PKWK_DISABLE_INLINE_IMAGE_FROM_URI &&
			is_url($alias) && preg_match('/\.(gif|png|jpe?g)$/i', $alias)) {
			$alias = '<img src="' . htmlspecialchars($alias) . '" alt="' . $name . '" />';
		} else if ($alias != '') {
			if ($converter === NULL)
				$converter = new InlineConverter(array('plugin'));

			$alias = make_line_rules($converter->convert($alias, $page));

			// BugTrack/669: A hack removing anchor tags added by AutoLink
			$alias = preg_replace('#</?a[^>]*>#i', '', $alias);
		}
		$this->alias = $alias;

		return TRUE;
	}
}

// Inline plugins
class Link_plugin extends Link
{
	var $pattern;
	var $plain,$param;

	function Link_plugin($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		$this->pattern = <<<EOD
&
(      # (1) plain
 (\w+) # (2) plugin name
 (?:
  \(
   ((?:(?!\)[;{]).)*) # (3) parameter
  \)
 )?
)
EOD;
		return <<<EOD
{$this->pattern}
(?:
 \{
  ((?:(?R)|(?!};).)*) # (4) body
 \}
)?
;
EOD;
	}

	function get_count()
	{
		return 4;
	}

	function set($arr, $page)
	{
		list($all, $this->plain, $name, $this->param, $body) = $this->splice($arr);

		// Re-get true plugin name and patameters (for PHP 4.1.2)
		$matches = array();
		if (preg_match('/^' . $this->pattern . '/x', $all, $matches)
			&& $matches[1] != $this->plain) 
			list(, $this->plain, $name, $this->param) = $matches;

		return parent::setParam($page, $name, $body, 'plugin');
	}

	function toString()
	{
		$body = ($this->body == '') ? '' : make_link($this->body);
		$str = FALSE;

		// Try to call the plugin
		if (exist_plugin_inline($this->name))
			$str = do_plugin_inline($this->name, $this->param, $body);

		if ($str !== FALSE) {
			return $str; // Succeed
		} else {
			// No such plugin, or Failed
			$body = (($body == '') ? '' : '{' . $body . '}') . ';';
			return make_line_rules(htmlspecialchars('&' . $this->plain) . $body);
		}
	}
}

// Footnotes
class Link_note extends Link
{
	function Link_note($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		return <<<EOD
\(\(
 ((?:(?R)|(?!\)\)).)*) # (1) note body
\)\)
EOD;
	}

	function get_count()
	{
		return 1;
	}

	function set($arr, $page)
	{
		global $foot_explain, $vars;
		static $note_id = 0;

		list(, $body) = $this->splice($arr);

		if (PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR) {
			$script = '';
		} else {
			$script = get_script_uri() . '?' . rawurlencode($page);
		}

		$id   = ++$note_id;
		$note = make_link($body);
		$page = isset($vars['page']) ? rawurlencode($vars['page']) : '';

		// Footnote
		$foot_explain[$id] = '<a id="notefoot_' . $id . '" href="' .
			$script . '#notetext_' . $id . '" class="note_super">*' .
			$id . '</a>' . "\n" .
			'<span class="small">' . $note . '</span><br />';

		// A hyperlink, content-body to footnote
		if (! is_numeric(PKWK_FOOTNOTE_TITLE_MAX) || PKWK_FOOTNOTE_TITLE_MAX <= 0) {
			$title = '';
		} else {
			$title = strip_tags($note);
			$count = mb_strlen($title, SOURCE_ENCODING);
			$title = mb_substr($title, 0, PKWK_FOOTNOTE_TITLE_MAX, SOURCE_ENCODING);
			$abbr  = (mb_strlen($title) < $count) ? '...' : '';
			$title = ' title="' . $title . $abbr . '"';
		}
		$name = '<a id="notetext_' . $id . '" href="' . $script .
			'#notefoot_' . $id . '" class="note_super"' . $title .
			'>*' . $id . '</a>';

		return parent::setParam($page, $name, $body);
	}

	function toString()
	{
		return $this->name;
	}
}

// URLs
class Link_url extends Link
{
	function Link_url($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		$s1 = $this->start + 1;
		return <<<EOD
(\[\[             # (1) open bracket
 ((?:(?!\]\]).)+) # (2) alias
 (?:>|:)
)?
(                 # (3) url
 (?:(?:https?|ftp|news):\/\/|mailto:)[\w\/\@\$()!?&%#:;.,~'=*+-]+
)
(?($s1)\]\])      # close bracket
EOD;
	}

	function get_count()
	{
		return 3;
	}

	function set($arr, $page)
	{
		list(, , $alias, $name) = $this->splice($arr);
		return parent::setParam($page, htmlspecialchars($name),
			'', 'url', $alias == '' ? $name : $alias);
	}

	function toString()
	{
		if (FALSE) {
			$rel = '';
		} else {
			$rel = ' rel="nofollow"';
		}
		return '<a href="' . $this->name . '"' . $rel . '>' . $this->alias . '</a>';
	}
}

// URLs (InterWiki definition on "InterWikiName")
class Link_url_interwiki extends Link
{
	function Link_url_interwiki($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		return <<<EOD
\[       # open bracket
(        # (1) url
 (?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*'();\/?:\@&=+\$,%#\w.-]*
)
\s
([^\]]+) # (2) alias
\]       # close bracket
EOD;
	}

	function get_count()
	{
		return 2;
	}

	function set($arr, $page)
	{
		list(, $name, $alias) = $this->splice($arr);
		return parent::setParam($page, htmlspecialchars($name), '', 'url', $alias);
	}

	function toString()
	{
		return '<a href="' . $this->name . '" rel="nofollow">' . $this->alias . '</a>';
	}
}

// mailto: URL schemes
class Link_mailto extends Link
{
	var $is_image, $image;

	function Link_mailto($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		$s1 = $this->start + 1;
		return <<<EOD
(?:
 \[\[
 ((?:(?!\]\]).)+)(?:>|:)  # (1) alias
)?
([\w.-]+@[\w-]+\.[\w.-]+) # (2) mailto
(?($s1)\]\])              # close bracket if (1)
EOD;
	}

	function get_count()
	{
		return 2;
	}

	function set($arr, $page)
	{
		list(, $alias, $name) = $this->splice($arr);
		return parent::setParam($page, $name, '', 'mailto', $alias == '' ? $name : $alias);
	}
	
	function toString()
	{
		return '<a href="mailto:' . $this->name . '" rel="nofollow">' . $this->alias . '</a>';
	}
}

// InterWikiName-rendered URLs
class Link_interwikiname extends Link
{
	var $url    = '';
	var $param  = '';
	var $anchor = '';

	function Link_interwikiname($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		$s2 = $this->start + 2;
		$s5 = $this->start + 5;
		return <<<EOD
\[\[                  # open bracket
(?:
 ((?:(?!\]\]).)+)>    # (1) alias
)?
(\[\[)?               # (2) open bracket
((?:(?!\s|:|\]\]).)+) # (3) InterWiki
(?<! > | >\[\[ )      # not '>' or '>[['
:                     # separator
(                     # (4) param
 (\[\[)?              # (5) open bracket
 (?:(?!>|\]\]).)+
 (?($s5)\]\])         # close bracket if (5)
)
(?($s2)\]\])          # close bracket if (2)
\]\]                  # close bracket
EOD;
	}

	function get_count()
	{
		return 5;
	}

	function set($arr, $page)
	{
		global $script;

		list(, $alias, , $name, $this->param) = $this->splice($arr);

		$matches = array();
		if (preg_match('/^([^#]+)(#[A-Za-z][\w-]*)$/', $this->param, $matches))
			list(, $this->param, $this->anchor) = $matches;

		$url = get_interwiki_url($name, $this->param);
		$this->url = ($url === FALSE) ?
			$script . '?' . rawurlencode('[[' . $name . ':' . $this->param . ']]') :
			htmlspecialchars($url);

		return parent::setParam(
			$page,
			htmlspecialchars($name . ':' . $this->param),
			'',
			'InterWikiName',
			$alias == '' ? $name . ':' . $this->param : $alias
		);
	}

	function toString()
	{
		return '<a href="' . $this->url . $this->anchor . '" title="' .
			$this->name . '" rel="nofollow">' . $this->alias . '</a>';
	}
}

// BracketNames
class Link_bracketname extends Link
{
	var $anchor, $refer;

	function Link_bracketname($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		global $WikiName, $BracketName;

		$s2 = $this->start + 2;
		return <<<EOD
\[\[                     # Open bracket
(?:((?:(?!\]\]).)+)>)?   # (1) Alias
(\[\[)?                  # (2) Open bracket
(                        # (3) PageName
 (?:$WikiName)
 |
 (?:$BracketName)
)?
(\#(?:[a-zA-Z][\w-]*)?)? # (4) Anchor
(?($s2)\]\])             # Close bracket if (2)
\]\]                     # Close bracket
EOD;
	}

	function get_count()
	{
		return 4;
	}

	function set($arr, $page)
	{
		global $WikiName;

		list(, $alias, , $name, $this->anchor) = $this->splice($arr);
		if ($name == '' && $this->anchor == '') return FALSE;

		if ($name == '' || ! preg_match('/^' . $WikiName . '$/', $name)) {
			if ($alias == '') $alias = $name . $this->anchor;
			if ($name != '') {
				$name = get_fullname($name, $page);
				if (! is_pagename($name)) return FALSE;
			}
		}

		return parent::setParam($page, $name, '', 'pagename', $alias);
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

// WikiNames
class Link_wikiname extends Link
{
	function Link_wikiname($start)
	{
		parent::Link($start);
	}

	function get_pattern()
	{
		global $WikiName, $nowikiname;

		return $nowikiname ? FALSE : '(' . $WikiName . ')';
	}

	function get_count()
	{
		return 1;
	}

	function set($arr, $page)
	{
		list($name) = $this->splice($arr);
		return parent::setParam($page, $name, '', 'pagename', $name);
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

// AutoLinks
class Link_autolink extends Link
{
	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only

	function Link_autolink($start)
	{
		global $autolink;

		parent::Link($start);

		if (! $autolink || ! file_exists(CACHE_DIR . PKWK_AUTOLINK_REGEX_CACHE))
			return;

		@list($auto, $auto_a, $forceignorepages) = file(CACHE_DIR . PKWK_AUTOLINK_REGEX_CACHE);
		$this->auto   = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = explode("\t", trim($forceignorepages));
	}

	function get_pattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}

	function get_count()
	{
		return 1;
	}

	function set($arr, $page)
	{
		global $WikiName;

		list($name) = $this->splice($arr);

		// Ignore pages listed, or Expire ones not found
		if (in_array($name, $this->forceignorepages) || ! is_page($name))
			return FALSE;

		return parent::setParam($page, $name, '', 'pagename', $name);
	}

	function toString()
	{
		return make_pagelink($this->name, $this->alias, '', $this->page, TRUE);
	}
}

class Link_autolink_a extends Link_autolink
{
	function Link_autolink_a($start)
	{
		parent::Link_autolink($start);
	}

	function get_pattern()
	{
		return isset($this->auto_a) ? '(' . $this->auto_a . ')' : FALSE;
	}
}

// AutoAlias
class Link_autoalias extends Link
{
	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only
	var $alias;

	function Link_autoalias($start)
	{
		global $autoalias, $aliaspage;

		parent::Link($start);

		if (! $autoalias || ! file_exists(CACHE_DIR . PKWK_AUTOALIAS_REGEX_CACHE) || $this->page == $aliaspage)
		{
			return;
		}

		@list($auto, $auto_a, $forceignorepages) = file(CACHE_DIR . PKWK_AUTOALIAS_REGEX_CACHE);
		$this->auto = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = explode("\t", trim($forceignorepages));
		$this->alias = '';
	}
	function get_pattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		list($name) = $this->splice($arr);
		// Ignore pages listed
		if (in_array($name, $this->forceignorepages)) {
			return FALSE;
		}
		return parent::setParam($page,$name,'','pagename',$name);
	}

	function toString()
	{
		$this->alias = get_autoaliases($this->name);
		if ($this->alias != '') {
			$link = '[[' . $this->name . '>' . $this->alias . ']]';
			return make_link($link);
		}
		return '';
	}
}

class Link_autoalias_a extends Link_autoalias
{
	function Link_autoalias_a($start)
	{
		parent::Link_autoalias($start);
	}
	function get_pattern()
	{
		return isset($this->auto_a) ? '(' . $this->auto_a . ')' : FALSE;
	}
}

// Make hyperlink for the page
function make_pagelink($page, $alias = '', $anchor = '', $refer = '', $isautolink = FALSE)
{
	global $script, $vars, $link_compact, $related, $_symbol_noexists;

	$s_page = htmlspecialchars(strip_bracket($page));
	$s_alias = ($alias == '') ? $s_page : $alias;

	if ($page == '') return '<a href="' . $anchor . '">' . $s_alias . '</a>';

	$r_page  = rawurlencode($page);
	$r_refer = ($refer == '') ? '' : '&amp;refer=' . rawurlencode($refer);

	if (! isset($related[$page]) && $page !== $vars['page'] && is_page($page))
		$related[$page] = get_filetime($page);

	if ($isautolink || is_page($page)) {
		// Hyperlink to the page
		if ($link_compact) {
			$title   = '';
		} else {
			$title   = ' title="' . $s_page . get_pg_passage($page, FALSE) . '"';
		}

		// AutoLink marker
		if ($isautolink) {
			$al_left  = '<!--autolink-->';
			$al_right = '<!--/autolink-->';
		} else {
			$al_left = $al_right = '';
		}

		return $al_left . '<a ' . 'href="' . $script . '?' . $r_page . $anchor .
			'"' . $title . '>' . $s_alias . '</a>' . $al_right;
	} else {
		// Dangling link
		if (PKWK_READONLY) return $s_alias; // No dacorations

		$retval = $s_alias . '<a href="' .
			$script . '?cmd=edit&amp;page=' . $r_page . $r_refer . '">' .
			$_symbol_noexists . '</a>';

		if ($link_compact) {
			return $retval;
		} else {
			return '<span class="noexists">' . $retval . '</span>';
		}
	}
}

// Resolve relative / (Unix-like)absolute path of the page
function get_fullname($name, $refer)
{
	global $defaultpage;

	// 'Here'
	if ($name == '' || $name == './') return $refer;

	// Absolute path
	if ($name{0} == '/') {
		$name = substr($name, 1);
		return ($name == '') ? $defaultpage : $name;
	}

	// Relative path from 'Here'
	if (substr($name, 0, 2) == './') {
		$arrn    = preg_split('#/#', $name, -1, PREG_SPLIT_NO_EMPTY);
		$arrn[0] = $refer;
		return join('/', $arrn);
	}

	// Relative path from dirname()
	if (substr($name, 0, 3) == '../') {
		$arrn = preg_split('#/#', $name,  -1, PREG_SPLIT_NO_EMPTY);
		$arrp = preg_split('#/#', $refer, -1, PREG_SPLIT_NO_EMPTY);

		while (! empty($arrn) && $arrn[0] == '..') {
			array_shift($arrn);
			array_pop($arrp);
		}
		$name = ! empty($arrp) ? join('/', array_merge($arrp, $arrn)) :
			(! empty($arrn) ? $defaultpage . '/' . join('/', $arrn) : $defaultpage);
	}

	return $name;
}

// Render an InterWiki into a URL
function get_interwiki_url($name, $param)
{
	global $WikiName, $interwiki;
	static $interwikinames;
	static $encode_aliases = array('sjis'=>'SJIS', 'euc'=>'EUC-JP', 'utf8'=>'UTF-8');

	if (! isset($interwikinames)) {
		$interwikinames = $matches = array();
		foreach (get_source($interwiki) as $line)
			if (preg_match('/\[(' . '(?:(?:https?|ftp|news):\/\/|\.\.?\/)' .
			    '[!~*\'();\/?:\@&=+\$,%#\w.-]*)\s([^\]]+)\]\s?([^\s]*)/',
			    $line, $matches))
				$interwikinames[$matches[2]] = array($matches[1], $matches[3]);
	}

	if (! isset($interwikinames[$name])) return FALSE;

	list($url, $opt) = $interwikinames[$name];

	// Encoding
	switch ($opt) {

	case '':    /* FALLTHROUGH */
	case 'std': // Simply URL-encode the string, whose base encoding is the internal-encoding
		$param = rawurlencode($param);
		break;

	case 'asis': /* FALLTHROUGH */
	case 'raw' : // Truly as-is
		break;

	case 'yw': // YukiWiki
		if (! preg_match('/' . $WikiName . '/', $param))
			$param = '[[' . mb_convert_encoding($param, 'SJIS', SOURCE_ENCODING) . ']]';
		break;

	case 'moin': // MoinMoin
		$param = str_replace('%', '_', rawurlencode($param));
		break;

	default:
		// Alias conversion of $opt
		if (isset($encode_aliases[$opt])) $opt = & $encode_aliases[$opt];

		// Encoding conversion into specified encode, and URLencode
		$param = rawurlencode(mb_convert_encoding($param, $opt, SOURCE_ENCODING));
	}

	// Replace or Add the parameter
	if (strpos($url, '$1') !== FALSE) {
		$url = str_replace('$1', $param, $url);
	} else {
		$url .= $param;
	}

	$len = strlen($url);
	if ($len > 512) die_message('InterWiki URL too long: ' . $len . ' characters');

	return $url;
}
?>
