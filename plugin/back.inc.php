<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// back.inc.php
// Copyright
//   2003-2018 PukiWiki Development Team
//   2002      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
//
// back plugin

// Allow specifying back link by page name and anchor, or
// by relative or site-abusolute path
define('PLUGIN_BACK_ALLOW_PAGELINK', PKWK_SAFE_MODE); // FALSE(Compat), TRUE

// Allow JavaScript (Compat)
define('PLUGIN_BACK_ALLOW_JAVASCRIPT', TRUE); // TRUE(Compat), FALSE

// ----
define('PLUGIN_BACK_USAGE', '#back([text],[center|left|right][,0(no hr)[,Page-or-URI-to-back]])');
function plugin_back_convert()
{
	global $_msg_back_word, $script;

	if (func_num_args() > 4) return PLUGIN_BACK_USAGE;
	list($word, $align, $hr, $href) = array_pad(func_get_args(), 4, '');

	$word = trim($word);
	$word = ($word == '') ? $_msg_back_word : htmlsc($word);

	$align = strtolower(trim($align));
	switch($align){
	case ''      : $align = 'center';
	               /*FALLTHROUGH*/
	case 'center': /*FALLTHROUGH*/
	case 'left'  : /*FALLTHROUGH*/
	case 'right' : break;
	default      : return PLUGIN_BACK_USAGE;
	}

	$hr = (trim($hr) != '0') ? '<hr class="full_hr" />' . "\n" : '';

	$link = TRUE;
	$href = trim($href);
	if ($href != '') {
		if (PLUGIN_BACK_ALLOW_PAGELINK) {
			if (is_url($href)) {
				$href = htmlsc($href);
			} else {
				$refer = isset($vars['page']) ? $vars['page'] : '';
				$array = anchor_explode($href);
				$page = get_fullname($array[0], $refer);
				if (! is_pagename($page)) {
					return PLUGIN_BACK_USAGE;
				}
				$anchor = ($array[1] != '') ? '#' . rawurlencode($array[1]) : '';
				$href = get_page_uri($page) .  $anchor;
				$link = is_page($page);
			}
		} else {
			if (is_url($href)) {
				$href = htmlsc($href);
			} else {
				return PLUGIN_BACK_USAGE . ': Set a page name or an URI';
			}
		}
	} else {
		if (! PLUGIN_BACK_ALLOW_JAVASCRIPT)
			return PLUGIN_BACK_USAGE . ': Set a page name or an URI';
		$href  = 'javascript:history.go(-1)';
	}

	if($link){
		// Normal link
		return $hr . '<div style="text-align:' . $align . '">' .
			'[ <a href="' . $href . '">' . $word . '</a> ]</div>' . "\n";
	} else {
		// Dangling link
		return $hr . '<div style="text-align:' . $align . '">' .
			'[ <span class="noexists">' . $word . '<a href="' . $href .
			'">?</a></span> ]</div>' . "\n";
	}
}
