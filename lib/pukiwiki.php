<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: pukiwiki.php,v 1.21 2007/08/26 15:17:28 henoheno Exp $
//
// PukiWiki 1.4.*
//  Copyright (C) 2002-2007 by PukiWiki Developers Team
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
$notify = 0;

// Load *.ini.php files and init PukiWiki
require(LIB_DIR . 'init.php');

// Load optional libraries
if ($notify) {
	require(LIB_DIR . 'mail.php'); // Mail notification
}

/////////////////////////////////////////////////
// Main

$retvars = array();
$page  = isset($vars['page'])  ? $vars['page']  : '';
$refer = isset($vars['refer']) ? $vars['refer'] : '';

if (isset($vars['cmd'])) {
	$plugin = & $vars['cmd'];
} else if (isset($vars['plugin'])) {
	$plugin = & $vars['plugin'];
} else {
	$plugin = '';
}

// Spam filtering
if ($spam && $method != 'GET') {
	// Adjustment
	$_spam   = ! empty($spam);
	$_plugin = strtolower($plugin);
	$_ignore = array();

	switch ($_plugin) {
		case 'search': $_spam = FALSE; break;
		case 'edit':
			$_page = & $page;
			if (isset($vars['add']) && $vars['add']) {
				$_plugin = 'add';
			} else {
				$_ignore[] = 'original';
			}
			break;
		case 'bugtrack': $_page = & $vars['base'];  break;
		case 'tracker':  $_page = & $vars['_base']; break;
		case 'read':     $_page = & $page;  break;
		default: $_page = & $refer; break;
	}

	if ($_spam) {
		require(LIB_DIR . 'spam.php');
		require(LIB_DIR . 'spam_pickup.php');

		if (isset($spam['method'][$_plugin])) {
			$_method = & $spam['method'][$_plugin];
		} else if (isset($spam['method']['_default'])) {
			$_method = & $spam['method']['_default'];
		} else {
			$_method = array();
		}
		$exitmode = isset($spam['exitmode']) ? $spam['exitmode'] : '';

		// Hack: ignorance several keys
		if ($_ignore) {
			$_vars = array();
			foreach($vars as $key => $value) {
				$_vars[$key] = & $vars[$key];
			}
			foreach($_ignore as $key) {
				unset($_vars[$key]);
			}
		} else {
			$_vars = & $vars;
		}

		pkwk_spamfilter($method . ' to #' . $_plugin, $_page, $_vars, $_method, $exitmode);
	}
}

// Plugin execution
if ($plugin != '') {
	if (exist_plugin_action($plugin)) {
		$retvars = do_plugin_action($plugin);
		if ($retvars === FALSE) exit; // Done

		// Rescan $vars (Some plugins rewrite it)
		if (isset($vars['cmd'])) {
			$base = isset($vars['page'])  ? $vars['page']  : '';
		} else {
			$base = isset($vars['refer']) ? $vars['refer'] : '';
		}
	} else {
		$msg = 'plugin=' . htmlspecialchars($plugin) . ' is not implemented.';
		$retvars = array('msg'=>$msg,'body'=>$msg);
		$base    = & $defaultpage;
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
}

// Output
catbody($title, $page, $body);
exit;
?>
