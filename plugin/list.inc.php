<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: list.inc.php,v 1.2 2003/04/01 08:05:26 panda Exp $
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
	
	$pages = array_diff(get_existpages(),array($whatsnew));
	if (!$withfilename)
	{
		$pages = array_diff($pages,preg_grep("/$non_list/",$pages));
	}
	if (count($pages) == 0)
	{
	        return '';
	}
	
	return page_list($pages,'read',$withfilename);
}
?>
