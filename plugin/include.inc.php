<?php
// $Id: include.inc.php,v 1.2.2.1 2003/02/14 08:17:22 panda Exp $
function plugin_include_convert()
{
	global $script,$get,$post,$vars,$WikiName,$BracketName,$hr;
	static $include_list; //処理済ページ名の配列
	
	if (!isset($include_list))
		$include_list = array($vars['page']=>TRUE);
	
	if(func_num_args() == 0)
		return;
	
	list($page) = func_get_args();
	
	if (!preg_match("/^($WikiName|$BracketName)$/",$page))
		$page = "[[$page]]";
	
	if (!is_page($page))
		return '';
	
	if (isset($include_list[$page]))
		return '';
	
	$include_list[$page] = TRUE;
	
	$tmppage = $vars['page'];
	
	$get['page'] = $post['page'] = $vars['page'] = $page;

	$body = @join('',@file(get_filename(encode($page))));
	$body = convert_html($body);

	// $link = "<a href=\"$script?".rawurlencode($page)."\">".strip_bracket($page)."</a>";
	$link = "<a href=\"$script?cmd=edit&amp;page=".rawurlencode($page)."\">".strip_bracket($page)."</a>";
	if($page == 'MenuBar'){
		$head = "<span align=\"center\"><h5 class=\"side_label\">$link</h5></span>";
		$body = "$head\n<small>$body</small>\n";
	} else {
		$head = "<h1>$link</h1>\n";
		$body = "$head\n$body\n";
	}

	$get['page'] = $post['page'] = $vars['page'] = $tmppage;
	
	return $body;
}
?>
