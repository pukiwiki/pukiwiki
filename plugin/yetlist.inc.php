<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: yetlist.inc.php,v 1.12 2003/03/02 02:41:30 panda Exp $
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
	foreach (get_existpages() as $page) {
		$obj = new InlineConverter(array('page','auto'));
		$source = join("\n",preg_replace('/^(\s|\/\/|#).*$/','',get_source($page)));
		foreach ($obj->get_objects($source,$page) as $_obj) {
			if (($_obj->name != '') and ($_obj->type == 'pagename') and !is_page($_obj->name)) {
				$refer[$_obj->name][] = $page;
			}
		}
	}
	
	if (count($refer) == 0) {
		return $ret;
	}
	
	ksort($refer);
	
	foreach($refer as $page=>$refs) {
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		
		$link_refs = array();
		foreach(array_unique($refs) as $_refer) {
			$r_refer = rawurlencode($_refer);
			$s_refer = htmlspecialchars($_refer);
			
			$link_refs[] = "<a href=\"$script?$r_refer\">$s_refer</a>";
		}
		$link_ref = join(' ',$link_refs);
		// 参照元ページが複数あった場合、referは最後のページを指す(いいのかな)
		$ret['body'] .= "<li><a href=\"$script?cmd=edit&amp;page=$r_page&amp;refer=$r_refer\">$s_page</a> <em>($link_ref)</em></li>\n";
	}
	
	if ($ret['body'] != '') {
		$ret['body'] = "<ul>\n{$ret['body']}</ul>\n";
	}
	
	return $ret;
}
?>
