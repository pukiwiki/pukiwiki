<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: diff.inc.php,v 1.1 2003/01/27 05:38:44 panda Exp $
//
//ページの差分を表示する
function plugin_diff_action()
{
	global $script,$get,$hr;
	global $_msg_notfound,$_msg_goto,$_msg_addline,$_msg_delline,$_title_diff;
	
	$r_page = rawurlencode($get['page']);
	$s_page = htmlspecialchars($get['page']);
	$s_name = strip_bracket($s_page);
	
	$msg = $_title_diff;
	$body = '';
	
	if (is_page($get['page'])) {
		$link = str_replace('$1',"<a href=\"$script?$r_page\">$s_name</a>",$_msg_goto);
		$body = <<<EOD
<ul>
 <li>$_msg_addline</li>
 <li>$_msg_delline</li>
 <li>$link</li>
</ul>
$hr
EOD;
	}
	
	if (file_exists(DIFF_DIR.encode($get['page']).'.txt')) {
		$diffdata = htmlspecialchars(join('',file(DIFF_DIR.encode($get['page']).'.txt')));
		$diffdata = preg_replace('/^(\-)(.*)$/m','<span class="diff_removed"> $2</span>',$diffdata);
		$diffdata = preg_replace('/^(\+)(.*)$/m','<span class="diff_added"> $2</span>',$diffdata);
		$diffdata = trim($diffdata);
		$body .= "<pre>$diffdata</pre>\n";
	}
	else if (is_page($get['page'])) {
		$diffdata = trim(htmlspecialchars(join('',get_source($get['page']))));
		$body .= "<pre><span class=\"diff_added\">$diffdata</span></pre>\n";
	}
	else {
		$title = $s_name;
		$body = $_msg_notfound;
	}
	
	return array('msg'=>$msg,'body'=>$body);
}
?>
