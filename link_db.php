<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: link_db.php,v 1.1 2003/03/13 14:08:28 panda Exp $
//

// データベースから関連ページを得る
function links_get_related_db($page)
{
	$links = array();
	
	$a_page = addslashes($page);
	
	// $pageが参照しているページ
	$sql = <<<EOD
SELECT refpage.name,refpage.lastmod
 FROM page
  LEFT JOIN link ON page.id = page_id
   LEFT JOIN page AS refpage ON ref_id = refpage.id
    WHERE page.name = '$a_page' and refpage.lastmod > 0;
EOD;
	$rows = db_query($sql);
	// $pageを参照しているページ
	$sql = <<<EOD
SELECT DISTINCT refpage.name,refpage.lastmod
 FROM page
  LEFT JOIN link ON page.id = ref_id
   LEFT JOIN page AS refpage ON page_id = refpage.id
    WHERE page.name = '$a_page';
EOD;
	$rows += db_query($sql);
	
	foreach ($rows as $row)
	{
		if (empty($row['name']) or substr($row['name'],0,1) == ':')
		{
			continue;
		}
		$links[$row['name']] = $row['lastmod'];
	}
	return $links;
}
//ページの関連を更新する
function links_update($page)
{
	global $whatsnew;
	
	$is_page = is_page($page);
	$time = ($is_page) ? get_filetime($page) : 0;
	$a_page = addslashes($page);
	
	$rows = db_query("SELECT id FROM page WHERE name='$a_page';");
	if (count($rows) == 0)
	{
		if (!$is_page)
		{
			return;
		}
		db_exec("INSERT INTO page (name,lastmod) VALUES ('$a_page',$time);");
		$rows = db_query("SELECT id FROM page WHERE name='$a_page';");
		$id = $rows[0]['id'];
	}
	else // if (count($rows) > 0)
	{
		$id = $rows[0]['id'];
		// $pageが参照しているページを初期化
		db_exec("DELETE FROM link WHERE page_id=$id;");
		if (!$is_page)
		{
			$_rows = db_query("SELECT * FROM link WHERE ref_id=$id;");
			if (count($_rows) == 0)
			{
				// $pageを参照しているページがないので、このレコードを削除
				db_exec("DELETE FROM page WHERE id=$id;");
				return;
			}
		}
		// ページの更新時刻をセット
		db_exec("UPDATE page SET lastmod=$time WHERE id=$id;");
	}
	
	// cache
	$pages = array();
	
	$links = links_get_objects($page);
	foreach ($links as $_obj)
	{
		if (!isset($_obj->type) or $_obj->type != 'pagename' or $_obj->name == $page)
		{
			continue;
		}			
		$_page = $_obj->name;
		if (!array_key_exists($_page,$pages))
		{
			$a_page = addslashes($_page);
			$rows = db_query("SELECT id,name FROM page WHERE name='$a_page';");
			if (count($rows) == 0)
			{
				db_exec("INSERT INTO page (name,lastmod) VALUES ('$a_page',0);");
				$rows = db_query("SELECT id,name FROM page WHERE name='$a_page';");
			}
			$pages[$rows[0]['name']] = TRUE;
			$ref_id =$rows[0]['id'];
			db_exec("INSERT INTO link (page_id,ref_id) VALUES ($id,$ref_id);");
		}
	}
	// 誰からも参照されなくなった「存在しないページ」を消去
	// MySQL3は「副問い合わせ」に未対応…?
//		db_exec("DELETE FROM page WHERE id in (SELECT id FROM page LEFT JOIN link ON id=ref_id WHERE lastmod=0 AND page_id IS NULL);");
	$rows = db_query("SELECT id FROM page LEFT JOIN link ON id=ref_id WHERE lastmod=0 AND page_id IS NULL;");
	$_arr = array();
	foreach ($rows as $row)
	{
		$_arr[] = $row['id'];
	}
	if (count($_arr))
	{
		db_exec("DELETE FROM page WHERE id in (".join(',',$_arr).");");
	}
}
//ページの関連を初期化する
function links_init()
{
	global $whatsnew;
	
	set_time_limit(0);
	
	// データベースの初期化
	db_exec('DELETE FROM page;');
	db_exec('DELETE FROM link;');
	$pages = get_existpages();
	foreach ($pages as $page)
	{
		if ($page == $whatsnew)
		{
			continue;
		}
		$time = get_filetime($page);
		$a_page = addslashes($page);
		db_exec("INSERT INTO page (name,lastmod) VALUES ('$a_page',$time);");
	}
	$rows = db_query('SELECT id,name FROM page;');
	$pages = array();
	foreach ($rows as $row)
	{
		$pages[$row['name']] = $row['id'];
	}
	
	foreach ($pages as $page=>$id)
	{
		$links = links_get_objects($page);
		foreach ($links as $_obj)
		{
			if ($_obj->type != 'pagename')
			{
				continue;
			}
			$_page = $_obj->name;
			if (!array_key_exists($_page,$pages))
			{
				$a_page = addslashes($_page);
				db_exec("INSERT INTO page (name) VALUES ('$a_page');");
				$rows = db_query("SELECT id,name FROM page WHERE name='$a_page';");
				$row = $rows[0];
				$pages[$row['name']] = $row['id'];
			}	
				
			$ref_id = $pages[$_page];
			if ($ref_id and $ref_id != $id)
			{
				db_exec("INSERT INTO link (page_id,ref_id) VALUES ($id,$ref_id);");
			}
		}
	}
}
function &links_get_objects($page,$refresh=FALSE)
{
	static $obj;
	
	if (!isset($obj) or $refresh)
	{
		$obj = &new InlineConverter(NULL,array('note'));
	}
	
	return $obj->get_objects(join('',preg_grep('/^(?!\/\/|\s)./',get_source($page))),$page);
}
?>
