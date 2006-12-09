<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: pukiwiki.php,v 1.13 2006/12/09 08:31:57 henoheno Exp $
//
// PukiWiki 1.4.*
//  Copyright (C) 2002-2006 by PukiWiki Developers Team
//  http://pukiwiki.sourceforge.jp/
//
// PukiWiki 1.3.*
//  Copyright (C) 2002-2004 by PukiWiki Developers Team
//  http://pukiwiki.sourceforge.jp/
//
// PukiWiki 1.3 (Base)
//  Copyright (C) 2001-2002 by yu-ji <sng@factage.com>
//  http://factage.com/sng/pukiwiki/
//
// Special thanks
//  YukiWiki by Hiroshi Yuki <hyuki@hyuki.com>
//  http://www.hyuki.com/yukiwiki/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

if (! defined('DATA_HOME')) define('DATA_HOME', '');

/////////////////////////////////////////////////
// Include subroutines

if (! defined('LIB_DIR')) define('LIB_DIR', '');

require(LIB_DIR . 'func.php');
require(LIB_DIR . 'file.php');
require(LIB_DIR . 'plugin.php');
require(LIB_DIR . 'html.php');
require(LIB_DIR . 'backup.php');

require(LIB_DIR . 'convert_html.php');
require(LIB_DIR . 'make_link.php');
require(LIB_DIR . 'diff.php');
require(LIB_DIR . 'config.php');
require(LIB_DIR . 'link.php');
require(LIB_DIR . 'auth.php');
require(LIB_DIR . 'proxy.php');
if (! extension_loaded('mbstring')) {
	require(LIB_DIR . 'mbstring.php');
}

// Defaults
$notify = $trackback = $referer = 0;

// Load *.ini.php files and init PukiWiki
require(LIB_DIR . 'init.php');

// Load optional libraries
if ($notify) {
	require(LIB_DIR . 'mail.php'); // Mail notification
}
if ($trackback || $referer) {
	// Referer functionality uses trackback functions
	// without functional reason now
	require(LIB_DIR . 'trackback.php'); // TrackBack
}

/////////////////////////////////////////////////
// Main

$retvars = array();
$page  = isset($vars['page'])  ? $vars['page']  : '';
$refer = isset($vars['refer']) ? $vars['refer'] : '';

if (isset($vars['cmd'])) {
	$base   = $page;
	$plugin = & $vars['cmd'];
} else if (isset($vars['plugin'])) {
	$base   =  $refer;
	$plugin = & $vars['plugin'];
} else {
	$base   =  $refer;
	$plugin = '';
}


// Spam filtering
if ($spam && $method != 'GET') {
	// Adjustment
	$_spam   = $spam;
	$_plugin = strtolower($plugin);
	switch ($_plugin) {
		//case 'plugin-name':
		//	$_spam = FALSE; // Don't check, or check later
		//	break;
		case 'search':
			$_page = '';
			$_spam = FALSE;
		   break;
		case 'edit':
			$_page = & $page;
			if (isset($vars['add']) && $vars['add']) {
				$_spam   = TRUE;
				$_plugin = 'add';
			} else {
				// TODO: Add some metrics (quantitiy, non_uniq, badhost etc)
				$_spam = FALSE;
			}
			break;
		case 'bugtrack': $_page = & $post['base'];  break;
		case 'tracker':  $_page = & $post['_base']; break;
		//case 'article':  /*FALLTHROUGH*/
		//case 'comment':  /*FALLTHROUGH*/
		//case 'insert':   /*FALLTHROUGH*/
		//case 'lookup':   /*FALLTHROUGH*/
		//case 'pcomment': /*FALLTHROUGH*/
		default: $_page = & $refer; break;
	}

	if ($_spam) {
		require(LIB_DIR . 'spam.php');
		pkwk_spamfilter($method . ' to #' . $_plugin, $_page, $vars);
	}
}

// Plugin execution
if ($plugin != '') {
	if (! exist_plugin_action($plugin)) {
		$msg = 'plugin=' . htmlspecialchars($plugin) . ' is not implemented.';
		$retvars = array('msg'=>$msg,'body'=>$msg);
		$base    = & $defaultpage;
	} else {
		$retvars = do_plugin_action($plugin);
		if ($retvars === FALSE) exit; // Done
	}
}

// Page output
$title = htmlspecialchars(strip_bracket($base));
$page  = make_search($base);
if (isset($retvars['msg']) && $retvars['msg'] != '') {
	$title = str_replace('$1', $title, $retvars['msg']);
	$page  = str_replace('$1', $page,  $retvars['msg']);
}

if (isset($retvars['body']) && $retvars['body'] != '') {
	$body = & $retvars['body'];
} else {
	if ($base == '' || ! is_page($base)) {
		$base  = & $defaultpage;
		$title = htmlspecialchars(strip_bracket($base));
		$page  = make_search($base);
	}

	$vars['cmd']  = 'read';
	$vars['page'] = & $base;

	$body  = convert_html(get_source($base));

	if ($trackback) $body .= tb_get_rdf($base); // Add TrackBack-Ping URI
	if ($referer) ref_save($base);
}

// Output
catbody($title, $page, $body);
exit;
?>
