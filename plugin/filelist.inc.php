<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: filelist.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//
// ファイル名一覧の表示
// cmd=filelist
function plugin_filelist_action()
{
	return do_plugin_action('list');
}
?>