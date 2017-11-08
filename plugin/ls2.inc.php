<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// ls2.inc.php
// Copyright
//   2002-2017  PukiWiki Development Team
//   2002       panda  http://home.arino.jp/?ls2.inc.php 
//   2002       Y.MASUI GPL2 http://masui.net/pukiwiki/ masui@masui.net (ls.inc.php)
// License: GPL version 2
//
// List plugin 2

/*
 * 配下のページや、その見出し(*,**,***)の一覧を表示する
 * Usage
 *  #ls2(pattern[,title|include|link|reverse|compact, ...],heading title)
 *
 * pattern  : 省略するときもカンマが必要
 * 'title'  : 見出しの一覧を表示する
 * 'include': インクルードしているページの見出しを再帰的に列挙する
 * 'link   ': actionプラグインを呼び出すリンクを表示
 * 'reverse': ページの並び順を反転し、降順にする
 * 'compact': 見出しレベルを調整する
 *     PLUGIN_LS2_LIST_COMPACTがTRUEの時は無効(変化しない)
 * heading title: 見出しのタイトルを指定する (linkを指定した時のみ)
 */

// 見出しアンカーの書式
define('PLUGIN_LS2_ANCHOR_PREFIX', '#content_1_');

// 見出しアンカーの開始番号
define('PLUGIN_LS2_ANCHOR_ORIGIN', 0);

// 見出しレベルを調整する(デフォルト値)
define('PLUGIN_LS2_LIST_COMPACT', FALSE);

function plugin_ls2_action()
{
	global $vars, $_ls2_msg_title;

	$params = array();
	$keys   = array('title', 'include', 'reverse');
	foreach ($keys as $key)
		$params[$key] = isset($vars[$key]);

	$prefix = isset($vars['prefix']) ? $vars['prefix'] : '';
	$body = plugin_ls2_show_lists($prefix, $params);

	return array('body'=>$body,
		'msg'=>str_replace('$1', htmlsc($prefix), $_ls2_msg_title));
}

function plugin_ls2_convert()
{
	global $vars, $_ls2_msg_title;

	$params = array(
		'link'    => FALSE,
		'title'   => FALSE,
		'include' => FALSE,
		'reverse' => FALSE,
		'compact' => PLUGIN_LS2_LIST_COMPACT,
		'_args'   => array(),
		'_done'   => FALSE
	);

	$args = array();
	$prefix = '';
	if (func_num_args()) {
		$args   = func_get_args();
		$prefix = array_shift($args);
	}
	if ($prefix == '') $prefix = strip_bracket($vars['page']) . '/';

	foreach ($args as $arg)
		plugin_ls2_check_arg($arg, $params);

	$title = (! empty($params['_args'])) ? join(',', $params['_args']) :   // Manual
		str_replace('$1', htmlsc($prefix), $_ls2_msg_title); // Auto

	if (! $params['link'])
		return plugin_ls2_show_lists($prefix, $params);

	$tmp = array();
	$tmp[] = 'plugin=ls2&amp;prefix=' . rawurlencode($prefix);
	if (isset($params['title']))   $tmp[] = 'title=1';
	if (isset($params['include'])) $tmp[] = 'include=1';

	return '<p><a href="' . get_base_uri() . '?' . join('&amp;', $tmp) . '">' .
		$title . '</a></p>' . "\n";
}

function plugin_ls2_show_lists($prefix, & $params)
{
	global $_ls2_err_nopages;

	$pages = array();
	if ($prefix != '') {
		foreach (get_existpages() as $_page)
			if (strpos($_page, $prefix) === 0)
				$pages[] = $_page;
	} else {
		$pages = get_existpages();
	}

	natcasesort($pages);
	if ($params['reverse']) $pages = array_reverse($pages);

	foreach ($pages as $page) $params['page_ ' . $page] = 0;

	if (empty($pages)) {
		return str_replace('$1', htmlsc($prefix), $_ls2_err_nopages);
	} else {
		$params['result'] = $params['saved'] = array();
		foreach ($pages as $page) {
			$read_pages = array(); // read pages per page
			plugin_ls2_get_headings($page, $params, 1, false, $read_pages);
		}
		return join("\n", $params['result']) . join("\n", $params['saved']);
	}
}

function plugin_ls2_get_headings($page, & $params, $level, $include = FALSE,
	&$read_pages)
{
	static $_ls2_anchor = 0;

	if (is_null($read_pages)) {
		$read_pages = array();
	}
	// ページが未表示のとき
	$is_done = isset($read_pages[$page]);
	if (! $is_done) $params["page_$page"] = ++$_ls2_anchor;

	$s_page = htmlsc($page);
	$attrs = get_page_link_a_attrs($page);
	$href   = get_page_uri($page);

	plugin_ls2_list_push($params, $level);
	$ret = $include ? '<li>include ' : '<li>';

	if ($is_done) {
		$ret .= '<a href="' . $href . '" class="' .
			$attrs['class'] . '" data-mtime="' . $attrs['data_mtime'] .
			'">' . $s_page . '</a> ';
		$ret .= '<a href="#list_' . $params["page_$page"] . '"><sup>&uarr;</sup></a>';
		array_push($params['result'], $ret);
		return;
	}

	$ret .= '<a id="list_' . $params["page_$page"] . '" href="' . $href .
		'" class="' . $attrs['class'] . '" data-mtime="' .
		$attrs['data_mtime'] . '">' . $s_page . '</a>';
	array_push($params['result'], $ret);

	$anchor = PLUGIN_LS2_ANCHOR_ORIGIN;
	$matches = array();
	$is_title = $params['title'];
	$is_include = $params['include'];
	if (!$is_title && !$is_include) {
		return;
	}
	foreach (get_source($page) as $line) {
		if ($is_title && preg_match('/^(\*{1,3})/', $line, $matches)) {
			$id    = make_heading($line);
			$heading_level = strlen($matches[1]);
			$id    = PLUGIN_LS2_ANCHOR_PREFIX . $anchor++;
			plugin_ls2_list_push($params, $level + $heading_level);
			array_push($params['result'],
				'<li><a href="' . $href . $id . '">' . $line . '</a>');
		} else if ($is_include &&
			preg_match('/^#include\((.+)\)/', $line, $matches)) {
			$include_args = $matches[1];
			$page2 = $include_args;
			$m2 = null;
			if (preg_match('#^(("([^"]+)")|([^",]+))#', $include_args, $m2)) {
				if ($m2[3]) {
					$page2 = $m2[3];
				} else if ($m2[4]) {
					$page2 = $m2[4];
				}
			}
			$sub_page = get_fullname($page2, $page);
			if (is_page($sub_page)) {
				$read_pages[$page] = 1;
				if (!isset($read_pages[$sub_page])) {
					plugin_ls2_get_headings($sub_page, $params,
						$level + 1, TRUE, $read_pages);
				}
			}
		}
	}
}

//リスト構造を構築する
function plugin_ls2_list_push(& $params, $level)
{
	$result = & $params['result'];
	$saved  = & $params['saved'];
	$cont   = TRUE;
	$open   = '<ul%s>';
	$close  = '</li></ul>';

	while (count($saved) > $level || (! empty($saved) && $saved[0] != $close))
		array_push($result, array_shift($saved));

	$margin = $level - count($saved);

	// count($saved)を増やす
	while (count($saved) < ($level - 1)) array_unshift($saved, '');

	if (count($saved) < $level) {
		$cont = FALSE;
		array_unshift($saved, $close);

		$left = 0;
		if ($params['compact']) {
			$left = 1;   // マージンを固定
			$level -= ($margin - 1); // レベルを修正
		} else {
			$left = $margin;
		}
		$str = sprintf(pkwk_list_attrs_template(), $level, $left);
		array_push($result, sprintf($open, $str));
	}

	if ($cont) array_push($result, '</li>');
}

// オプションを解析する
function plugin_ls2_check_arg($value, & $params)
{
	if ($value == '') {
		$params['_done'] = TRUE;
		return;
	}

	if (! $params['_done']) {
		foreach (array_keys($params) as $param) {
			if (strtolower($value)  == $param &&
			    preg_match('/^[a-z]/', $param)) {
				$params[$param] = TRUE;
				return;
			}
		}
		$params['_done'] = TRUE;
	}

	$params['_args'][] = htmlsc($value); // Link title
}
