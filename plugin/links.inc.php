<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: links.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//
function plugin_links_action()
{
	global $vars,$whatsnew;
	
	set_time_limit(0);
	
	if ($vars['page'] != '' and $vars['page'] != $whatsnew) {
		$page = $vars['page'];
		$is_page = is_page($page);
		$time = ($is_page) ? get_filetime($page) : 0;
		$a_page = addslashes($page);
		
		$rows = db_query("SELECT id FROM page WHERE name='$a_page';");
		
		if (count($rows) == 0) // not exist
			db_exec("INSERT INTO page (name,lastmod) VALUES ('$a_page',$time);");
		else {
			$id = $rows[0]['id'];
			db_exec("DELETE FROM link WHERE page_id=$id;");
			if ($is_page)
				db_exec("UPDATE page SET lastmod=$time WHERE id=$id;");
			else {
				db_exec("DELETE FROM page WHERE id=$id;");
				db_exec("DELETE FROM link WHERE ref_id=$id;");
				return;
			}
		}
		
		$rows = db_query("SELECT id,name FROM page;");
		$pages = array();
		foreach ($rows as $row)
			$pages[$row['name']] = $row['id'];
		
		$id = $pages[$page];
		
		$obj = new link_wrapper($page);
		$links = $obj->get_link(join('',get_source($page)));
		foreach ($links as $_obj) {
			if ($_obj->type == 'WikiName') {
//				$name = strip_bracket($_obj->name);
				$_page = $_obj->name;
				if (array_key_exists($_page,$pages)) {
					$ref_id = $pages[$_page];
					if ($ref_id and $ref_id != $id)
						db_exec("INSERT INTO link (page_id,ref_id) VALUES ($id,$ref_id);");
				}
			}
		}
		return;
	}
	
	// データベースの初期化
	$pages = get_existpages();
	db_exec('DELETE FROM page;');
	db_exec('DELETE FROM link;');
	foreach ($pages as $page) {
		if ($page == $whatsnew)
			continue;
		$time = get_filetime($page);
		$a_page = addslashes($page);
		db_exec("INSERT INTO page (name,lastmod) VALUES ('$a_page',$time);");
	}
	$rows = db_query('SELECT id,name FROM page;');
	$pages = array();
	foreach ($rows as $row)
		$pages[$row['name']] = $row['id'];
	
	$obj = new link_wrapper();
	foreach ($pages as $page=>$id) {
		$data = get_source($page);
		$obj->page = $page;
		$links = $obj->get_link(join('',$data));
		foreach ($links as $_obj) {
			if ($_obj->type == 'WikiName') {
//				$name = strip_bracket($_obj->name);
				$_page = $_obj->name;
				if (array_key_exists($_page,$pages)) {
					$ref_id = $pages[$_page];
					if ($ref_id and $ref_id != $id)
						db_exec("INSERT INTO link (page_id,ref_id) VALUES ($id,$ref_id);");
				}
			}
		}
	}
	return array('msg'=>'Result','body'=>'<p>done.</p>');
}


?>