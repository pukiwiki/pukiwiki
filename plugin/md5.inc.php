<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: md5.inc.php,v 1.2 2003/03/02 04:18:31 panda Exp $
//
//  MD5パスワードへの変換
function plugin_md5_action()
{
	global $vars;
	
	return array(
		'msg'=>'Make password of MD5',
		'body'=> htmlspecialchars($vars['md5']).' : '.md5($vars['md5'])
	);
}
?>
