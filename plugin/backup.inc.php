<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: backup.inc.php,v 1.3 2003/02/03 10:28:51 panda Exp $
//
// バックアップ
function plugin_backup_action()
{
	global $script,$get,$do_backup,$hr;
	global $_msg_backuplist,$_msg_diff,$_msg_nowdiff,$_msg_source,$_msg_backup;
	global $_msg_view,$_msg_goto,$_msg_deleleted,$_msg_addline,$_msg_delline;
	global $_title_backupdiff,$_title_backupnowdiff,$_title_backupsource;
	global $_title_backup,$_title_pagebackuplist,$_title_backuplist;
	
	if (!$do_backup) { return; }

	$name = $s_page = $s_name = $r_page = '';
	if (array_key_exists('page',$get)) {
		$s_page = htmlspecialchars($get['page']);
		$r_page = rawurlencode($get['page']);
//		$name = strip_bracket($get['page']);
//		$s_name = htmlspecialchars($name);
	}
	$s_age = (array_key_exists('age',$get) and is_numeric($get['age'])) ? $get['age'] : 0;
	$s_action = $r_action = '';
	if (array_key_exists('action',$get)) {
		$s_action = htmlspecialchars($get['action']);
		$r_action = rawurlencode($get['action']);
	}
	
	$body = '';
	
	if (!array_key_exists('page',$get) or $get['page'] == '') {
		return array('msg'=>$_title_backuplist,'body'=>get_backup_list_all());
	}
	
	if ($s_age == 0) {
		return array('msg'=>$_title_pagebackuplist,'body'=>get_backup_list($get['page']));
	}
	
	$body  = "<ul>\n";
	$body .= " <li><a href=\"$script?cmd=backup\">$_msg_backuplist</a></li>\n";

	$href = "$script?cmd=backup&amp;page=$r_page&amp;age=$s_age";
	
	if (is_page($get['page'])) {
		if ($s_action != 'diff') {
			$body .= " <li>".str_replace('$1',"<a href=\"$href&amp;action=diff\">$_msg_diff</a>",$_msg_view)."</li>\n";
		}
		if ($s_action != 'nowdiff') {
			$body .= " <li>".str_replace('$1',"<a href=\"$href&amp;action=nowdiff\">$_msg_nowdiff</a>",$_msg_view)."</li>\n";
		}
	}
	
	if ($s_action != 'source') {
		$body .= " <li>".str_replace('$1',"<a href=\"$href&amp;action=source\">$_msg_source</a>",$_msg_view)."</li>\n";
	}
	if ($s_action != '') {
		$body .= " <li>".str_replace('$1',"<a href=\"$href\">$_msg_backup</a>",$_msg_view)."</li>\n";
	}
	
	if (is_page($get['page'])) {
		$body .= " <li>".str_replace('$1',"<a href=\"$script?$r_page\">$s_page</a>",$_msg_goto)."</li>\n";
	}
	else {
		$body .= " <li>".str_replace('$1',$s_page,$_msg_deleleted)."</li>\n";
	}

	$backups = get_backup($get['page']);
	if (count($backups) > 0) {
		$body .= "  <ul>\n";
		foreach($backups as $age => $val) {
			$date = format_date($val['time'],TRUE);
			if ($age == $get['age']) {
				$body .= "   <li><em>$age $date</em></li>\n";
			}
			else {
				$body .= "   <li><a href=\"$script?cmd={$get['cmd']}&amp;action=$r_action&amp;page=$r_page&amp;age=$age\">$age $date</a></li>\n";
			}
		}
		$body .= "  </ul>\n";
	}
	$body .= " </li>\n";
	$body .= "</ul>\n";
	
	if ($s_action == 'diff') {
		$old = ($get['age'] > 1) ? join('',$backups[$get['age']-1]['data']) : '';
		$cur = join('',$backups[$get['age']]['data']);
		$body .= backup_diff(do_diff($old,$cur));
		
		return array('msg'=>str_replace('$2',$s_age,$_title_backupdiff),'body'=>$body);
	}
	else if ($s_action == 'nowdiff') {
		$old = join('',$backups[$get['age']]['data']);
		$cur = join('',get_source($get['page']));
		$body .= backup_diff(do_diff($old,$cur));
		
		return array('msg'=>str_replace('$2',$s_age,$_title_backupnowdiff),'body'=>$body);
	}
	else if ($s_action == 'source') {
		$body .= "<pre>".htmlspecialchars(join('',$backups[$get['age']]['data']))."</pre>\n";
		
		return array('msg'=>str_replace('$2',$s_age,$_title_backupsource),'body'=>$body);
	}
	// else
	$body .= "$hr\n".
		drop_submit(convert_html($backups[$get['age']]['data']));
	
	return array('msg'=>str_replace('$2',$s_age,$_title_backup),'body'=>$body);
}
function backup_diff($str) 
{
	global $_msg_addline,$_msg_delline,$hr;
	
	$str = htmlspecialchars($str);
	$str = preg_replace('/^(\-)(.*)$/m','<span class="diff_removed"> $2</span>',$str);
	$str = preg_replace('/^(\+)(.*)$/m','<span class="diff_added"> $2</span>',$str);
	$str = trim($str);
	$str = <<<EOD
$hr
<ul>
 <li>$_msg_addline</li>
 <li>$_msg_delline</li>
</ul>
<pre>$str</pre>
EOD;
	
	return $str;
}

// バックアップ一覧を取得
function get_backup_list($page)
{
	global $script;
	global $_msg_backuplist,$_msg_diff,$_msg_nowdiff,$_msg_source,$_msg_nobackup;
	
	$r_page = rawurlencode($page);
	$s_page = htmlspecialchars($page);
	$retval  = "<ul>\n";
	$retval .= " <li><a href=\"$script?cmd=backup\">$_msg_backuplist</a>\n";
	
	$backups = get_backup($page);
	$retval .= "  <ul>\n";
	if (count($backups) > 0) {
		foreach ($backups as $age=>$data) {
			$date = format_date($data['time'],TRUE);
			$href = "$script?cmd=backup&amp;page=$r_page&amp;age=$age";
			$retval .= <<<EOD
   <li><a href="$href">$age $date</a>
     [ <a href="$href&amp;action=diff">$_msg_diff</a>
     | <a href="$href&amp;action=nowdiff">$_msg_nowdiff</a>
     | <a href="$href&amp;action=source">$_msg_source</a>
     ]
   </li>
EOD;
		}
	}
	else {
		$title = $s_page . get_pg_passage($page,FALSE);
		$link = "<a href=\"$script?cmd=read&amp;page=$r_page\" title=\"$title\">$s_page</a>";
		$msg = str_replace('$1',$link,$_msg_nobackup);
		$retval .= "   <li>$msg</li>\n";
	}
	$retval .= "  </ul>\n </li>\n</ul>\n";
	
	return $retval;
}
// 全ページのバックアップ一覧を取得
function get_backup_list_all($withfilename = FALSE)
{
	global $cantedit;
	
	$_pages = get_existpages(BACKUP_DIR);
	if (count($_pages) == 0) {
		return '';
	}
	
	$pages = array();
	foreach($_pages as $page) {
		if (!in_array($page,$cantedit)) {
			$pages[] = $page;
		}
	}
	
	return page_list($pages,'backup',$withfilename);
}
?>