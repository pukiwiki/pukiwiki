<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: filelist.inc.php,v 1.2 2004/07/31 03:09:20 henoheno Exp $
//
// ファイル名一覧の表示
// cmd=filelist
function plugin_filelist_action()
{
	return do_plugin_action('list');
}
?>
