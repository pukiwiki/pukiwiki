<?php
/*
 * PukiWiki lsプラグイン
 *
 * CopyRight 2002 Y.MASUI GPL2
 * http://masui.net/pukiwiki/ masui@masui.net
 *
 * $Id: ls.inc.php,v 1.6 2003/01/31 01:49:35 panda Exp $
 */

function plugin_ls_convert()
{
	global $script,$vars;
	
	$aryargs = func_num_args() ? func_get_args() : array();
	
	$with_title = (array_search('title',$aryargs)!==FALSE);
	
	$ls = array();
	
	$prefix = $vars['page'].'/';
	
	$pages = array();
	foreach (get_existpages() as $page) {
		if (strpos($page,$prefix) === 0) {
			$pages[] = $page;
		}
	}
	natcasesort($pages);
	
	foreach ($pages as $page) {
		$comment = '';
		if ($with_title) {
			list($comment) = get_source($page);
			$comment = '- ' . ereg_replace('^[-*]+','',$comment);
		}
		$ls[] = "-[[$page]] $comment";
	}
	
	return convert_html($ls);
}
?>
