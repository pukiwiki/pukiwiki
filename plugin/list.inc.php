<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: list.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//
// 一覧の表示
function plugin_list_action()
{
	global $vars,$_title_list,$_title_filelist,$whatsnew;
	
	header_lastmod($whatsnew);
	
	$filelist = (array_key_exists('cmd',$vars) and $vars['cmd']=='filelist'); //姑息だ…
	
	return array(
		'msg'=>$filelist ? $_title_filelist : $_title_list,
		'body'=>get_list($filelist)
	);
}

// 一覧の取得
function get_list($withfilename)
{
	global $non_list,$whatsnew;
	
	$_pages = get_existpages();
	if (count($_pages) == 0)
		return '';
	
	$pages = array();
	foreach($_pages as $page) {
		if ($page == $whatsnew or
			(!$withfilename and preg_match("/$non_list/",$page)))
			continue;
		$pages[] = $page;
	}
	
	return page_list($pages,'read',$withfilename);
}
?>