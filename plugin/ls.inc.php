<?php
/*
 * PukiWiki lsプラグイン
 *
 * CopyRight 2002 Y.MASUI GPL2
 * http://masui.net/pukiwiki/ masui@masui.net
 *
 * $Id: ls.inc.php,v 1.5 2003/01/27 05:38:46 panda Exp $
 */

function plugin_ls_convert()
{
	global $script,$vars;
	
	$aryargs = func_num_args() ? func_get_args() : array();
	
	$with_title = (array_search('title',$aryargs)!==FALSE);
	
	$ls = '';
	
	$prefix = strip_bracket($vars['page']).'/';
	
	$pages = array();
	foreach (get_existpages() as $_page)
		if (strpos($_page,$prefix) === 0)
			$pages[] = $_page;
	natcasesort($pages);
	
	$comment = '';
	foreach ($pages as $page) {
		if ($with_title) {
			list($comment) = get_source($page);
			$comment = ereg_replace("^[-*]+",'',$comment);
			if ($comment != '' and substr($comment,0,1) != '#')
				$comment = ' - ' . convert_html($comment);
			else {
				$comment = '';
			}
		}
		$r_page = rawurlencode($page);
		$s_page = strip_bracket($page);
		$passage = get_pg_passage($page,FALSE);
		$ls .= " <li><a href=\"$script?cmd=read&amp;page=$r_page\" title=\"$s_page $passage\">$s_page</a>$comment</li>\n";
	}
	
	if ($ls == '')
	  return '';
	
	return "<ul>\n$ls</ul>\n";
}
?>
