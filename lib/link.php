<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: link.php,v 1.20 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C) 2003-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Backlinks / AutoLinks related functions

// ------------------------------------------------------------
// DATA STRUCTURE of *.ref and *.rel files

// CACHE_DIR/encode('foobar').ref
// ---------------------------------
// Page-name1<tab>0<\n>
// Page-name2<tab>1<\n>
// ...
// Page-nameN<tab>0<\n>
//
//	0 = Added when link(s) to 'foobar' added clearly at this page
//	1 = Added when the sentence 'foobar' found from the page
//	    by AutoLink feature

// CACHE_DIR/encode('foobar').rel
// ---------------------------------
// Page-name1<tab>Page-name2<tab> ... <tab>Page-nameN
//
//	List of page-names linked from 'foobar'

// ------------------------------------------------------------


// データベースから関連ページを得る
function links_get_related_db($page)
{
	$ref_name = CACHE_DIR . encode($page) . '.ref';
	if (! file_exists($ref_name)) return array();

	$times = array();
	foreach (file($ref_name) as $line) {
		list($_page) = explode("\t", rtrim($line));
		$time = get_filetime($_page);	
		if($time != 0) $times[$_page] = $time;
	}
	return $times;
}

//ページの関連を更新する
function links_update($page)
{
	if (PKWK_READONLY) return; // Do nothing

	if (ini_get('safe_mode') == '0') set_time_limit(0);

	$time = is_page($page, TRUE) ? get_filetime($page) : 0;

	$rel_old        = array();
	$rel_file       = CACHE_DIR . encode($page) . '.rel';
	$rel_file_exist = file_exists($rel_file);
	if ($rel_file_exist === TRUE) {
		$lines = file($rel_file);
		unlink($rel_file);
		if (isset($lines[0]))
			$rel_old = explode("\t", rtrim($lines[0]));
	}
	$rel_new  = array(); // 参照先
	$rel_auto = array(); // オートリンクしている参照先
	$links    = links_get_objects($page, TRUE);
	foreach ($links as $_obj) {
		if (! isset($_obj->type) || $_obj->type != 'pagename' ||
		    $_obj->name === $page || $_obj->name == '')
			continue;

		if (is_a($_obj, 'Link_autolink')) { // 行儀が悪い
			$rel_auto[] = $_obj->name;
		} else {
			$rel_new[]  = $_obj->name;
		}
	}
	$rel_new = array_unique($rel_new);
	
	// autolinkしか向いていないページ
	$rel_auto = array_diff(array_unique($rel_auto), $rel_new);

	// 全ての参照先ページ
	$rel_new = array_merge($rel_new, $rel_auto);

	// .rel:$pageが参照しているページの一覧
	if ($time) {
		// ページが存在している
		if (! empty($rel_new)) {
    			$fp = fopen($rel_file, 'w')
    				or die_message('cannot write ' . htmlsc($rel_file));
			fputs($fp, join("\t", $rel_new));
			fclose($fp);
		}
	}

	// .ref:$_pageを参照しているページの一覧
	links_add($page, array_diff($rel_new, $rel_old), $rel_auto);
	links_delete($page, array_diff($rel_old, $rel_new));

	global $WikiName, $autolink, $nowikiname, $search_non_list;

	// $pageが新規作成されたページで、AutoLinkの対象となり得る場合
	if ($time && ! $rel_file_exist && $autolink
		&& (preg_match("/^$WikiName$/", $page) ? $nowikiname : strlen($page) >= $autolink))
	{
		// $pageを参照していそうなページを一斉更新する(おい)
		$search_non_list = 1;
		$pages           = do_search($page, 'AND', TRUE);
		foreach ($pages as $_page) {
			if ($_page !== $page)
				links_update($_page);
		}
	}
	$ref_file = CACHE_DIR . encode($page) . '.ref';

	// $pageが削除されたときに、
	if (! $time && file_exists($ref_file)) {
		foreach (file($ref_file) as $line) {
			list($ref_page, $ref_auto) = explode("\t", rtrim($line));

			// $pageをAutoLinkでしか参照していないページを一斉更新する(おいおい)
			if ($ref_auto)
				links_delete($ref_page, array($page));
		}
	}
}

// Init link cache (Called from link plugin)
function links_init()
{
	global $whatsnew;

	if (PKWK_READONLY) return; // Do nothing

	if (ini_get('safe_mode') == '0') set_time_limit(0);

	// Init database
	foreach (get_existfiles(CACHE_DIR, '.ref') as $cache)
		unlink($cache);
	foreach (get_existfiles(CACHE_DIR, '.rel') as $cache)
		unlink($cache);

	$ref   = array(); // 参照元
	foreach (get_existpages() as $page) {
		if ($page == $whatsnew) continue;

		$rel   = array(); // 参照先
		$links = links_get_objects($page);
		foreach ($links as $_obj) {
			if (! isset($_obj->type) || $_obj->type != 'pagename' ||
			    $_obj->name == $page || $_obj->name == '')
				continue;

			$rel[] = $_obj->name;
			if (! isset($ref[$_obj->name][$page]))
				$ref[$_obj->name][$page] = 1;
			if (! is_a($_obj, 'Link_autolink'))
				$ref[$_obj->name][$page] = 0;
		}
		$rel = array_unique($rel);
		if (! empty($rel)) {
			$fp = fopen(CACHE_DIR . encode($page) . '.rel', 'w')
				or die_message('cannot write ' . htmlsc(CACHE_DIR . encode($page) . '.rel'));
			fputs($fp, join("\t", $rel));
			fclose($fp);
		}
	}

	foreach ($ref as $page=>$arr) {
		$fp  = fopen(CACHE_DIR . encode($page) . '.ref', 'w')
			or die_message('cannot write ' . htmlsc(CACHE_DIR . encode($page) . '.ref'));
		foreach ($arr as $ref_page=>$ref_auto)
			fputs($fp, $ref_page . "\t" . $ref_auto . "\n");
		fclose($fp);
	}
}

function links_add($page, $add, $rel_auto)
{
	if (PKWK_READONLY) return; // Do nothing

	$rel_auto = array_flip($rel_auto);
	
	foreach ($add as $_page) {
		$all_auto = isset($rel_auto[$_page]);
		$is_page  = is_page($_page);
		$ref      = $page . "\t" . ($all_auto ? 1 : 0) . "\n";

		$ref_file = CACHE_DIR . encode($_page) . '.ref';
		if (file_exists($ref_file)) {
			foreach (file($ref_file) as $line) {
				list($ref_page, $ref_auto) = explode("\t", rtrim($line));
				if (! $ref_auto) $all_auto = FALSE;
				if ($ref_page !== $page) $ref .= $line;
			}
			unlink($ref_file);
		}
		if ($is_page || ! $all_auto) {
			$fp = fopen($ref_file, 'w')
				 or die_message('cannot write ' . htmlsc($ref_file));
			fputs($fp, $ref);
			fclose($fp);
		}
	}
}

function links_delete($page, $del)
{
	if (PKWK_READONLY) return; // Do nothing

	foreach ($del as $_page) {
		$ref_file = CACHE_DIR . encode($_page) . '.ref';
		if (! file_exists($ref_file)) continue;

		$all_auto = TRUE;
		$is_page = is_page($_page);

		$ref = '';
		foreach (file($ref_file) as $line) {
			list($ref_page, $ref_auto) = explode("\t", rtrim($line));
			if ($ref_page !== $page) {
				if (! $ref_auto) $all_auto = FALSE;
				$ref .= $line;
			}
		}
		unlink($ref_file);
		if (($is_page || ! $all_auto) && $ref != '') {
			$fp = fopen($ref_file, 'w')
				or die_message('cannot write ' . htmlsc($ref_file));
			fputs($fp, $ref);
			fclose($fp);
		}
	}
}

function & links_get_objects($page, $refresh = FALSE)
{
	static $obj;

	if (! isset($obj) || $refresh)
		$obj = new InlineConverter(NULL, array('note'));

	$result = $obj->get_objects(join('', preg_grep('/^(?!\/\/|\s)./', get_source($page))), $page);
	return $result;
}

