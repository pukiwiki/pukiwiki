<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: diff.inc.php,v 1.4 2004/04/03 15:27:07 arino Exp $
//
//ページの差分を表示する
function plugin_diff_action()
{
	global $vars;
	
	check_readable($vars['page'],true,true);
	
	$action = array_key_exists('action',$vars) ? $vars['action'] : '';
	
	switch ($vars['action']) {
	case 'delete':
		$retval = plugin_diff_delete($vars['page']);
		break;
	default:
		$retval = plugin_diff_view($vars['page']);
		break;			
	}
	return $retval;
}
// 差分を表示
function plugin_diff_view($page)
{
	global $script,$hr;
	global $_msg_notfound,$_msg_goto,$_msg_deleted,$_msg_addline,$_msg_delline,$_title_diff;
	global $_title_diff_delete;
	
	$r_page = rawurlencode($page);
	$s_page = htmlspecialchars($page);
	
	$menu = array(
		"<li>$_msg_addline</li>",
		"<li>$_msg_delline</li>"
	);

	if (is_page($page)) {
		$menu[] = " <li>".str_replace('$1',"<a href=\"$script?$r_page\">$s_page</a>",$_msg_goto)."</li>";
	} else {
		$menu[] = " <li>".str_replace('$1',$s_page,$_msg_deleted)."</li>";
	}

	$delete_msg = '';
	$filename = DIFF_DIR.encode($page).'.txt';
	if (file_exists($filename)) {
		$diffdata = htmlspecialchars(join('',file($filename)));
		$diffdata = preg_replace('/^(\-)(.*)$/m','<span class="diff_removed"> $2</span>',$diffdata);
		$diffdata = preg_replace('/^(\+)(.*)$/m','<span class="diff_added"> $2</span>',$diffdata);
		$menu[] = "<li><a href=\"$script?cmd=diff&amp;action=delete&amp;page=$r_page\">" .
			str_replace('$1',$s_page,$_title_diff_delete) . '</a></li>';
		$msg = "<pre>$diffdata</pre>\n";
	}
	else if (is_page($page)) {
		$diffdata = trim(htmlspecialchars(join('',get_source($page))));
		$msg = "<pre><span class=\"diff_added\">$diffdata</span></pre>\n";
	}
	else {
		return array('msg'=>$_title_diff, 'body'=>$_msg_notfound);
	}

	$menu = join("\n",$menu);
	$body = <<<EOD
<ul>
$menu
</ul>
$hr
EOD;

	return array('msg'=>$_title_diff,'body'=>$body.$msg);
}
// バックアップを削除
function plugin_diff_delete($page)
{
	error_reporting(E_ALL);

	global $script,$post,$adminpass;
	global $_title_diff_delete,$_msg_diff_deleted,$_msg_diff_delete;
	global $_msg_diff_adminpass,$_btn_delete,$_msg_invalidpass;
	
	if (!is_pagename($page)) { return; }
	$filename = DIFF_DIR.encode($page).'.txt'; 
	if (!file_exists($filename)) { return; }

	$s_page = htmlspecialchars($page);
	$pass = array_key_exists('pass',$post) ? $post['pass'] : NULL;
	
	if (md5($pass) == $adminpass) {
		unlink($filename);
		return array(
			'msg'  => $_title_diff_delete,
			'body' => str_replace('$1',make_pagelink($page),$_msg_diff_deleted)
		);
	}
	$body = ($pass === NULL) ? '' : "<p><strong>$_msg_invalidpass</strong></p>\n";

	$msg_delete = str_replace('$1',make_pagelink($page),$_msg_diff_delete);
	$body .= <<<EOD
<p>$_msg_diff_adminpass</p>
<form action="$script" method="post">
 <div>
  <input type="hidden" name="cmd" value="diff" />
  <input type="hidden" name="page" value="$s_page" />
  <input type="hidden" name="action" value="delete" />
  <input type="password" name="pass" size="12" />
  <input type="submit" name="ok" value="$_btn_delete" />
 </div>
</form>
EOD;
	return	array('msg'=>$_title_diff_delete,'body'=>$body);
}	
?>
