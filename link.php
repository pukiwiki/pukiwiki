<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: link.php,v 1.1 2003/03/13 14:08:17 panda Exp $
//

// データベースから関連ページを得る
function links_get_related_db($page)
{
	$links = array();
	$ref_name = CACHE_DIR.encode($page).'.ref';
	if (file_exists($ref_name))
	{
		foreach (file($ref_name) as $line)
		{
			list($_page,$time) = explode("\t",rtrim($line));
			$links[$_page] = $time;
		}
	}
	return $links;
}
//ページの関連を更新する
function links_update($page)
{
	global $whatsnew;
	
	set_time_limit(0);
	
	$time = is_page($page) ? get_filetime($page) : 0;
	
	$rel_old = array();
	$rel_file = CACHE_DIR.encode($page).'.rel';
	if ($rel_file_exist = file_exists($rel_file))
	{
		$lines = file($rel_file);
		if (array_key_exists(0,$lines))
		{
			$rel_old = explode("\t",rtrim($lines[0]));
		}
		unlink($rel_file);
	}
	$rel_new = array(); // 参照先
	$rel_auto = array(); // オートリンクしている参照先
	$links = links_get_objects($page,TRUE);
	foreach ($links as $_obj)
	{
		if (!isset($_obj->type) or $_obj->type != 'pagename' or $_obj->name == $page)
		{
			continue;
		}
		if (is_a($_obj,'Link_autolink')) // 行儀が悪い
		{
			$rel_auto[] = $_obj->name;
		}
		else
		{
			$rel_new[] = $_obj->name;
		}
	}
	$rel_new = array_unique($rel_new);
	// autolinkしか向いていないページ
	$rel_auto = array_diff(array_unique($rel_auto),$rel_new);
	// 全ての参照先ページ
	$rel_new = array_merge($rel_new,$rel_auto);
	
	// .rel:$pageが参照しているページの一覧
	if ($time) // ページが存在しているときは空でも作る
	{
		$fp = fopen($rel_file,'w')
			or die_message('cannot write '.htmlspecialchars($rel_file));
		if (count($rel_new))
		{
			fputs($fp,join("\t",$rel_new));
		}
		fclose($fp);
	}
	// .ref:$_pageを参照しているページの一覧
	links_add($page,array_diff($rel_new,$rel_old),$rel_auto);
	links_delete($page,array_diff($rel_old,$rel_new));
	
	global $WikiName,$autolink,$nowikiname,$search_non_list;
	// $pageが新規作成されたページで、AutoLinkの対象となり得る場合
	if ($time and !$rel_file_exist and $autolink
		and (preg_match("/^$WikiName$/",$page) ? $nowikiname : strlen($page) >= $autolink))
	{
		// $pageを参照していそうなページを一斉更新する(おい)
		$search_non_list = 1;
		$pages = do_search($page,'AND',TRUE);
		foreach ($pages as $_page)
		{
			if ($_page != $page)
			{
				links_update($_page);
			}
		}
	}
	$ref_file = CACHE_DIR.encode($page).'.ref';
	//$pageが削除されたときに、
	if (!$time and file_exists($ref_file))
	{
		foreach (file($ref_file) as $line)
		{
			list($ref_page,$time,$auto) = explode("\t",rtrim($line));
			//$pageをAutoLinkでしか参照していないページを一斉更新する(おいおい)
			if ($auto)
			{
				links_delete($ref_page,array($page));
			}
		}
	}
}
//ページの関連を初期化する
function links_init()
{
	global $whatsnew;
	
	set_time_limit(0);
	
	// データベースの初期化
	foreach (get_existfiles(CACHE_DIR,'.ref') as $cache)
	{
		unlink($cache);
	}
	foreach (get_existfiles(CACHE_DIR,'.rel') as $cache)
	{
		unlink($cache);
	}
	$pages = get_existpages();
	$ref = array(); // 参照元
	$ref_notauto = array();
	foreach ($pages as $page)
	{
		if ($page == $whatsnew)
		{
			continue;
		}
		$time = get_filetime($page);
		$rel = array(); // 参照先
		$links = links_get_objects($page);
		foreach ($links as $_obj)
		{
			if (!isset($_obj->type) or $_obj->type != 'pagename' or $_obj->name == $page)
			{
				continue;
			}
			$rel[] = $_obj->name;
			if (!is_a($_obj,'Link_autolink'))
			{
				$ref_notauto[$_obj->name][$page] = TRUE;
			}
			$ref[$_obj->name][$page] = $time;
		}
		$rel = array_unique($rel);
		if (count($rel))
		{
			$fp = fopen(CACHE_DIR.encode($page).'.rel','w')
				or die_message('cannot write '.htmlspecialchars(CACHE_DIR.encode($page).'.rel'));
			fputs($fp,join("\t",$rel));
			fclose($fp);
		}
	}
	
	foreach ($ref as $page=>$arr)
	{
		if (count($arr) == 0)
		{
			continue;
		}
		$fp = fopen(CACHE_DIR.encode($page).'.ref','w')
			or die_message('cannot write '.htmlspecialchars(CACHE_DIR.encode($page).'.ref'));
		foreach ($arr as $_page=>$time)
		{
			$auto = (array_key_exists($page,$ref_notauto)
				and array_key_exists($_page,$ref_notauto[$page])) ? 0 : 1;
			fputs($fp,"$_page\t$time\t$auto\n");
		}
		fclose($fp);
	}
}
function links_add($page,$add,$rel_auto)
{
	$rel_auto = array_flip($rel_auto);
	foreach ($add as $_page)
	{
		$all_auto = array_key_exists($_page,$rel_auto);
		$is_page = is_page($_page);
		$ref = "$page\t$time\t".($all_auto ? 1 : 0)."\n";
		
		$ref_file = CACHE_DIR.encode($_page).'.ref';
		if (file_exists($ref_file))
		{
			foreach (file($ref_file) as $line)
			{
				list($ref_page,$time,$auto) = explode("\t",rtrim($line));
				if (!$auto)
				{
					$all_auto = FALSE;
				}
				if ($ref_page != $page)
				{
					$ref .= $line;
				}
			}
			unlink($ref_file);
		}
		if ($is_page or !$all_auto)
		{
			$fp = fopen($ref_file,'w')
				 or die_message('cannot write '.htmlspecialchars($ref_file));
			fputs($fp,$ref);
			fclose($fp);
		}
	}
}
function links_delete($page,$del)
{
	foreach ($del as $_page)
	{
		$all_auto = TRUE;
		$is_page = is_page($_page);
		
		$ref_file = CACHE_DIR.encode($_page).'.ref';
		if (!file_exists($ref_file))
		{
			continue;
		}
		$ref = '';
		foreach (file($ref_file) as $line)
		{
			list($ref_page,$time,$auto) = explode("\t",rtrim($line));
			if ($ref_page != $page)
			{
				if (!$auto)
				{
					$all_auto = FALSE;
				}
				$ref .= $line;
			}
		}
		unlink($ref_file);
		if ($is_page and !$all_auto and $ref != '')
		{
			$fp = fopen($ref_file,'w')
				or die_message('cannot write '.htmlspecialchars($ref_file));
			fputs($fp,$ref);
			fclose($fp);
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
