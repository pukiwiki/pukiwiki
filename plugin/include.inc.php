<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: include.inc.php,v 1.8 2003/07/03 06:12:17 arino Exp $
//

/*
 include.inc.php
 ページをインクルードする
*/

function plugin_include_convert()
{
	global $script,$vars,$get,$post,$hr,$WikiName,$BracketName;
	global $_msg_include_restrict;
	static $include_list = array(); //処理済ページ名の配列
	
	if (func_num_args() == 0)
	{
		return;
	}
	
	$include_list[$vars['page']] = TRUE;
	
	list($page) = func_get_args();
	$page = strip_bracket($page);
	
	if (!is_page($page) or isset($include_list[$page]))
	{
		return '';
	}
	$include_list[$page] = TRUE;
	
	$_page = $vars['page'];
	$get['page'] = $post['page'] = $vars['page'] = $page;
	
	// includeのときは、認証画面をいちいち出さず、後始末もこちらでつける
	if (check_readable($page, false, false)) {
		$body = convert_html(get_source($page));
	} else {
		$body = str_replace('$1',$page,$_msg_include_restrict);
	}
	
	$get['page'] = $post['page'] = $vars['page'] = $_page;
	
	$s_page = htmlspecialchars($page);
	$r_page = rawurlencode($page);
	$link = "<a href=\"$script?cmd=edit&amp;page=$r_page\">$s_page</a>";
	if ($page == 'MenuBar')
	{
		$body = <<<EOD
<span align="center"><h5 class="side_label">$link</h5></span>
<small>$body</small>
EOD;
	}
	else
	{
		$body = "<h1>$link</h1>\n$body\n";
	}
	
	return $body;
}
?>
