<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: deleted.inc.php,v 1.5 2004/07/31 03:09:20 henoheno Exp $
//
//削除されたページ(BACKUP_DIRにあって、DATA_DIRにないファイル)の一覧を表示する

function plugin_deleted_action()
{
	global $vars;
	global $_deleted_plugin_title,$_deleted_plugin_title_withfilename;

	$retval = array();

	$retval['msg'] = $_deleted_plugin_title;
	if ($withfilename = array_key_exists('file',$vars))
	{
		$retval['msg'] = $_deleted_plugin_title_withfilename;
	}
	$backup_pages = get_existpages(BACKUP_DIR,BACKUP_EXT);
	$exist_pages = get_existpages();
	$deleted_pages = array_diff($backup_pages,$exist_pages);
	$retval['body'] = page_list($deleted_pages,'backup',$withfilename);

	return $retval;
}
?>
