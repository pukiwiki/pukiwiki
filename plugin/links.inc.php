<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: links.inc.php,v 1.8 2003/02/23 02:12:39 panda Exp $
//

function plugin_links_action()
{
	global $vars;
	
	set_time_limit(0);
	
	if (!defined('LINK_DB')) {
		if ($vars['page'] != '') {
			links_update_file($vars['page']);
			return;
		}
		return links_init_file();
	}
	else {
		if ($vars['page'] != '') {
			links_update_db($vars['page']);
			return;
		}
		return links_init_db();
	}
}
function links_init_file()
{
	global $get,$adminpass,$whatsnew,$non_list;
	
	if (md5($get['adminpass']) != $adminpass) {
		return array('msg'=>'update database',
			'body'=>'<p>administrator password require.</p>');
	}
	
	// データベースの初期化
	foreach (get_existfiles(CACHE_DIR,'.ref') as $cache) {
		unlink($cache);
	}
	foreach (get_existfiles(CACHE_DIR,'.rel') as $cache) {
		unlink($cache);
	}
	$pages = get_existpages();
	$ref = array(); // 参照元
	$obj = new InlineConverter(array('page','auto')); 
	foreach ($pages as $page) {
		if ($page == $whatsnew) {
			continue;
		}
		$time = get_filetime($page);
		$rel = array(); // 参照先
		$links = $obj->get_objects(join('',get_source($page)),$page);
		foreach ($links as $_obj) {
			if (!isset($_obj->type) or $_obj->type != 'pagename') {
				continue;
			}			
			$_page = $_obj->name;
			if ($_page != $page and !preg_match("/$non_list/",$_page)) {
				$rel[$_page] = 1;
				$ref[$_page][$page] = $time;
			}
		}
		$fp = fopen(CACHE_DIR.encode($page).'.rel','w')
			or die_message('cannot write '.htmlspecialchars(CACHE_DIR.encode($page).'.rel'));
		fputs($fp,join("\t",array_keys($rel)));
		fclose($fp);
	}
	
	foreach ($ref as $page=>$arr) {
		$fp = fopen(CACHE_DIR.encode($page).'.ref','w')
			or die_message('cannot write '.htmlspecialchars(CACHE_DIR.encode($page).'.ref'));
		foreach ($arr as $_page=>$time) {
			fputs($fp,"$_page\t$time\n");
		}
		fclose($fp);
	}
	return array('msg'=>'update database','body'=>'<p>done.</p>');
}
function links_update_file($page)
{
	global $whatsnew,$non_list;
	
	$obj = new InlineConverter();
	$time = is_page($page) ? get_filetime($page) : 0;
	
	$rel_old = array();
	$rel_file = CACHE_DIR.encode($page).'.rel';
	if (file_exists($rel_file)) {
		$lines = file($rel_file);
		if (array_key_exists(0,$lines)) {
			$rel_old = explode("\t",rtrim($lines[0]));
		}
	}
	$rel_new = array(); // 参照先
	$links = $obj->get_objects(join('',get_source($page)),$page);
	foreach ($links as $_obj) {
		if (!isset($_obj->type) or $_obj->type != 'pagename') {
			continue;
		}			
		$_page = $_obj->name;
		if ($_page != $page and !preg_match("/$non_list/",$_page)) {
			$rel_new[$_page] = 1;
		}
	}
	$rel_new = array_keys($rel_new);
	
	$fp = fopen($rel_file,'w')
		or die_message('cannot write '.htmlspecialchars($rel_file));
	fputs($fp,join("\t",$rel_new));
	fclose($fp);
	
	$add = array_diff($rel_new,$rel_old);
	foreach ($add as $_page) {
		$ref_file = CACHE_DIR.encode($_page).'.ref';
		$ref = "$page\t$time\n";
		if (file_exists($ref_file)) {
			foreach (file($ref_file) as $line) {
				list($ref_page,$time) = explode("\t",rtrim($line));
				if ($ref_page != $page) {
					$ref .= $line;
				}
			}
		}
		$fp = fopen($ref_file,'w')
			 or die_message('cannot write '.htmlspecialchars($ref_file));
		fputs($fp,$ref);
		fclose($fp);
	}
	$del = array_diff($rel_old,$rel_new);
	foreach ($del as $_page) {
		$ref_file = CACHE_DIR.encode($_page).'.ref';
		if (file_exists($ref_file)) {
			$ref = '';
			foreach (file($ref_file) as $line) {
				list($ref_page,$time) = explode("\t",rtrim($line));
				if ($ref_page != $page) {
					$ref .= $line;
				}
			}
			$fp = fopen($ref_file,'w')
				or die_message('cannot write '.htmlspecialchars($ref_file));
			fputs($fp,$ref);
			fclose($fp);
		}
	}
}
function links_init_db()
{
	global $get,$adminpass,$whatsnew;
	
	if (md5($get['adminpass']) != $adminpass) {
		return array('msg'=>'update database',
			'body'=>'<p>administrator password require.</p>');
	}
	// データベースの初期化
	$pages = get_existpages();
	db_exec('DELETE FROM page;');
	db_exec('DELETE FROM link;');
	foreach ($pages as $page) {
		if ($page == $whatsnew) {
			continue;
		}
		$time = get_filetime($page);
		$a_page = addslashes($page);
		db_exec("INSERT INTO page (name,lastmod) VALUES ('$a_page',$time);");
	}
	$rows = db_query('SELECT id,name FROM page;');
	$pages = array();
	foreach ($rows as $row) {
		$pages[$row['name']] = $row['id'];
	}
	
	$obj = new InlineConverter(); 
	foreach ($pages as $page=>$id) {
		$links = $obj->get_objects(join('',get_source($page)),$page);
		foreach ($links as $_obj) {
			if ($_obj->type == 'pagename') {
				$_page = $_obj->name;
				if (array_key_exists($_page,$pages)) {
					$ref_id = $pages[$_page];
					if ($ref_id and $ref_id != $id) {
						db_exec("INSERT INTO link (page_id,ref_id) VALUES ($id,$ref_id);");
					}
				}
			}
		}
	}
	return array('msg'=>'update database','body'=>'<p>done.</p>');
}
function links_update_db($page)
{
	global $vars,$whatsnew;

	if ($page == $whatsnew) {
		return;
	}
	
	$is_page = is_page($page);
	$time = ($is_page) ? get_filetime($page) : 0;
	$a_page = addslashes($page);
	
	$rows = db_query("SELECT id FROM page WHERE name='$a_page';");
	
	if (count($rows) == 0) { // not exist
		db_exec("INSERT INTO page (name,lastmod) VALUES ('$a_page',$time);");
	}
	else {
		$id = $rows[0]['id'];
		db_exec("DELETE FROM link WHERE page_id=$id;");
		if ($is_page) {
			db_exec("UPDATE page SET lastmod=$time WHERE id=$id;");
		}
		else {
			db_exec("DELETE FROM page WHERE id=$id;");
			db_exec("DELETE FROM link WHERE ref_id=$id;");
			return;
		}
	}
	
	$rows = db_query("SELECT id,name FROM page;");
	$pages = array();
	foreach ($rows as $row) {
		$pages[$row['name']] = $row['id'];
	}
	
	$id = $pages[$page];
	
	$obj = new InlineConverter(array('page','auto'));
	$links = $obj->get_objects(join('',get_source($page)),$page);
	foreach ($links as $_obj) {
		if (!isset($_obj->type) or $_obj->type != 'pagename') {
			continue;
		}			
		$_page = $_obj->name;
		if (!array_key_exists($_page,$pages)) {
			continue;
		}
		$ref_id = $pages[$_page];
		if ($ref_id and $ref_id != $id) {
			db_exec("INSERT INTO link (page_id,ref_id) VALUES ($id,$ref_id);");
		}
	}
}
?>
