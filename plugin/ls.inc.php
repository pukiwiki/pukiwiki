<?php
/*
 * PukiWiki lsプラグイン
 *
 * CopyRight 2002 Y.MASUI GPL2
 * http://masui.net/pukiwiki/ masui@masui.net
 *
 * $Id: ls.inc.php,v 1.7 2003/04/29 00:39:53 arino Exp $
 */

function plugin_ls_convert()
{
	global $vars;
	
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
			// 見出しの固有ID部を削除
			$comment = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/','$1$2',$comment);
			
			$comment = '- ' . ereg_replace('^[-*]+','',$comment);
		}
		$ls[] = "-[[$page]] $comment";
	}
	
	return convert_html($ls);
}
?>
