<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: md5.inc.php,v 1.3 2003/12/03 12:29:19 arino Exp $
//
//  MD5パスワードへの変換
function plugin_md5_action()
{
	global $vars;
	
	if (!array_key_exists('md5',$vars))
	{
		return FALSE;
	}
	return array(
		'msg'=>'Make password of MD5',
		'body'=> htmlspecialchars($vars['md5']).' : '.md5($vars['md5'])
	);
}
?>
