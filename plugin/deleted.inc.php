<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: deleted.inc.php,v 1.1 2003/02/18 01:23:46 panda Exp $
//
//削除されたページ(BACKUP_DIRにあって、DATA_DIRにないファイル)の一覧を表示する

function plugin_deleted_init()
{
	if (LANG == 'ja') {
		$messages = array(
			'_deleted_plugin_title' => '削除ページの一覧',
			'_deleted_plugin_title_withfilename' => '削除ページファイルの一覧',
		);
	}
	else {
		$messages = array(
			'_deleted_plugin_title' => 'deleted pages',
			'_deleted_plugin_title_withfilename' => 'deleted pages (with filename)',
		);
	}
	set_plugin_messages($messages);
}

function plugin_deleted_action()
{
	global $get;
	global $_deleted_plugin_title,$_deleted_plugin_title_withfilename;

	$retval = array();

	$retval['msg'] = $_deleted_plugin_title;
	if ($withfilename = array_key_exists('file',$get)) {
		$retval['msg'] = $_deleted_plugin_title_withfilename;
	}
	$backup_pages = get_existpages(BACKUP_DIR,BACKUP_EXT);
	$exist_pages = get_existpages();
	$deleted_pages = array_diff($backup_pages,$exist_pages);
	$retval['body'] = page_list($deleted_pages,'backup',$withfilename);
	
	return $retval;
}
?>