<?php
/*
 include.inc.php
 ページをインクルードする
*/

function plugin_include_convert()
{
	global $script,$vars,$get,$post,$hr,$WikiName,$BracketName;
	global $include_list; //処理済ページ名の配列
	
	if (!isset($include_list)) { $include_list = array($vars['page']=>TRUE); }
	
	if (func_num_args() == 0) { return; }
	
	list($page) = func_get_args();
	
	if (!preg_match("/^($WikiName|\[\[$BracketName\]\])$/",$page))
		$page = "[[$page]]";

	if (!is_page($page) or isset($include_list[$page])) { return ''; }
	$include_list[$page] = TRUE;
	
	$tmppage = $vars['page'];
	$get['page'] = $post['page'] = $vars['page'] = $page;
	
	$body = convert_html(join('',get_source($page)));
	
	$get['page'] = $post['page'] = $vars['page'] = $tmppage;

	$link = "<a href=\"$script?cmd=edit&page=".rawurlencode($page)."\">".strip_bracket($page)."</a>";
	if ($page == 'MenuBar') {
		$body = "<span align=\"center\"><h5 class=\"side_label\">$link</h5></span>\n<small>$body</small>\n";
	}
	else {
		$body = "<h1>$link</h1>\n$body\n";
	}
	
	return $body;
}
?>