<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: yetlist.inc.php,v 1.13 2003/03/10 11:30:25 panda Exp $
//

function plugin_yetlist_init()
{
	if (LANG == 'ja')
	{
		$messages = array(
			'_title_yetlist'    => '未作成のページ一覧'
		);
	}
	else
	{
		$messages = array(
			'_title_yetlist'    => 'List of pages, are not made yet'
		);
	}
	set_plugin_messages($messages);
}
function plugin_yetlist_action()
{
	global $script;
	global $_title_yetlist;
	
	$ret['msg'] = $_title_yetlist;
	$ret['body'] = '';
	
	$refer = array();
	$exists = get_existpages();
	if (defined('LINK_DB'))
	{
		$sql = <<<EOD
SELECT DISTINCT page.name,refpage.name AS refer
 FROM page
  LEFT JOIN link ON page.id = ref_id
   LEFT JOIN page AS refpage ON page_id = refpage.id
    WHERE page.lastmod=0;
EOD;
		$rows = db_query($sql);
		foreach ($rows as $row)
		{
			$refer[$row['name']][] = $row['refer'];
		}
	}
	else
	{
		$pages = array_diff(get_existpages(CACHE_DIR,'.ref'),get_existpages());
		foreach ($pages as $page)
		{
			foreach (file(CACHE_DIR.encode($page).'.ref') as $line)
			{
				list($_page,$lastmod) = explode("\t",$line);
				$refer[$page][] = $_page;
			}
		}
	}
	
	if (count($refer) == 0)
	{
		return $ret;
	}
	
	ksort($refer);
	
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
		$ret['body'] .= "<li><a href=\"$script?cmd=edit&amp;page=$r_page&amp;refer=$r_refer\">$s_page</a> <em>($link_ref)</em></li>\n";
	}
	
	if ($ret['body'] != '')
	{
		$ret['body'] = "<ul>\n{$ret['body']}</ul>\n";
	}
	
	return $ret;
}
?>
