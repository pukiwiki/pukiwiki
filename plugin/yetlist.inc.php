<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: yetlist.inc.php,v 1.17 2003/06/05 00:32:14 arino Exp $
//

function plugin_yetlist_init()
{
	if (LANG == 'ja')
	{
		$messages = array(
			'_title_yetlist' => '未作成のページ一覧',
			'_err_notexist'  => '未作成のページはありません。' 
		);
	}
	else
	{
		$messages = array(
			'_title_yetlist' => 'List of pages, are not made yet',
			'_err_notexist'  => 'All pages are made.'
		);
	}
	set_plugin_messages($messages);
}
function plugin_yetlist_action()
{
	global $script;
	global $_title_yetlist,$_err_notexist;
	
	$retval = array(
		'msg' => $_title_yetlist,
		'body' => ''
	);
	
	$refer = array();
	$exists = get_existpages();
	$pages = array_diff(get_existpages(CACHE_DIR,'.ref'),get_existpages());
	foreach ($pages as $page)
	{
		foreach (file(CACHE_DIR.encode($page).'.ref') as $line)
		{
			list($_page) = explode("\t",$line);
			$refer[$page][] = $_page;
		}
	}
	
	if (count($refer) == 0)
	{
		$retval['body'] = $_err_notexist;
		return $retval;
	}
	
	ksort($refer,SORT_STRING);
	
	foreach($refer as $page=>$refs)
	{
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		
		$link_refs = array();
		foreach(array_unique($refs) as $_refer)
		{
			$r_refer = rawurlencode($_refer);
			$s_refer = htmlspecialchars($_refer);
			
			$link_refs[] = "<a href=\"$script?$r_refer\">$s_refer</a>";
		}
		$link_ref = join(' ',$link_refs);
		// 参照元ページが複数あった場合、referは最後のページを指す(いいのかな)
		$retval['body'] .= "<li><a href=\"$script?cmd=edit&amp;page=$r_page&amp;refer=$r_refer\">$s_page</a> <em>($link_ref)</em></li>\n";
	}
	
	if ($retval['body'] != '')
	{
		$retval['body'] = "<ul>\n".$retval['body']."</ul>\n";
	}
	
	return $retval;
}
?>
