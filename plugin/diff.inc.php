<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: diff.inc.php,v 1.20 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2002      Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Showing colored-diff plugin

function plugin_diff_action()
{
	global $vars;

	$page = isset($vars['page']) ? $vars['page'] : '';
	check_readable($page, true, true);

	$action = isset($vars['action']) ? $vars['action'] : '';
	switch ($action) {
		case 'delete': $retval = plugin_diff_delete($page);	break;
		default:       $retval = plugin_diff_view($page);	break;
	}
	return $retval;
}

function plugin_diff_view($page)
{
	global $script, $hr;
	global $_msg_notfound, $_msg_goto, $_msg_deleted, $_msg_addline, $_msg_delline, $_title_diff;
	global $_title_diff_delete;

	$r_page = rawurlencode($page);
	$s_page = htmlsc($page);

	$menu = array(
		'<li>' . $_msg_addline . '</li>',
		'<li>' . $_msg_delline . '</li>'
	);

	$is_page = is_page($page);
	if ($is_page) {
		$menu[] = ' <li>' . str_replace('$1', '<a href="' . $script . '?' . $r_page . '">' .
			$s_page . '</a>', $_msg_goto) . '</li>';
	} else {
		$menu[] = ' <li>' . str_replace('$1', $s_page, $_msg_deleted) . '</li>';
	}

	$filename = DIFF_DIR . encode($page) . '.txt';
	if (file_exists($filename)) {
		if (! PKWK_READONLY) {
			$menu[] = '<li><a href="' . $script . '?cmd=diff&amp;action=delete&amp;page=' .
				$r_page . '">' . str_replace('$1', $s_page, $_title_diff_delete) . '</a></li>';
		}
		$msg = '<pre>' . diff_style_to_css(htmlsc(join('', file($filename)))) . '</pre>' . "\n";
	} else if ($is_page) {
		$diffdata = trim(htmlsc(join('', get_source($page))));
		$msg = '<pre><span class="diff_added">' . $diffdata . '</span></pre>' . "\n";
	} else {
		return array('msg'=>$_title_diff, 'body'=>$_msg_notfound);
	}

	$menu = join("\n", $menu);
	$body = <<<EOD
<ul>
$menu
</ul>
$hr
EOD;

	return array('msg'=>$_title_diff, 'body'=>$body . $msg);
}

function plugin_diff_delete($page)
{
	global $script, $vars;
	global $_title_diff_delete, $_msg_diff_deleted;
	global $_msg_diff_adminpass, $_btn_delete, $_msg_invalidpass;

	$filename = DIFF_DIR . encode($page) . '.txt';
	$body = '';
	if (! is_pagename($page))     $body = 'Invalid page name';
	if (! file_exists($filename)) $body = make_pagelink($page) . '\'s diff seems not found';
	if ($body) return array('msg'=>$_title_diff_delete, 'body'=>$body);

	if (isset($vars['pass'])) {
		if (pkwk_login($vars['pass'])) {
			unlink($filename);
			return array(
				'msg'  => $_title_diff_delete,
				'body' => str_replace('$1', make_pagelink($page), $_msg_diff_deleted)
			);
		} else {
			$body .= '<p><strong>' . $_msg_invalidpass . '</strong></p>' . "\n";
		}
	}

	$s_page = htmlsc($page);
	$body .= <<<EOD
<p>$_msg_diff_adminpass</p>
<form action="$script" method="post">
 <div>
  <input type="hidden"   name="cmd"    value="diff" />
  <input type="hidden"   name="page"   value="$s_page" />
  <input type="hidden"   name="action" value="delete" />
  <input type="password" name="pass"   size="12" />
  <input type="submit"   name="ok"     value="$_btn_delete" />
 </div>
</form>
EOD;

	return array('msg'=>$_title_diff_delete, 'body'=>$body);
}
?>
