<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: new.inc.php,v 1.2 2003/04/30 08:17:15 arino Exp $
//

// 全体の表示フォーマット
define('NEW_MESSAGE','<span class="comment_date">%s</span>');

function plugin_new_init()
{
	global $_plugin_new_elapses;
	
	// 経過秒数 => 新着表示タグ
	$messages = array(
		'_plugin_new_elapses' => array(
			1*60*60*24 => ' <span class="new1">New!</span>',
			5*60*60*24 => ' <span class="new5">New</span>',
		),
	);
	set_plugin_messages($messages);
}
function plugin_new_inline()
{
	global $_plugin_new_elapses;
	
	if (func_num_args() < 1)
	{
		return FALSE;
	}
	
	$args = func_get_args();
	
	$date = array_pop($args);
	if (($timestamp = strtotime($date)) === -1)
	{
		return FALSE;
	}
	$retval = htmlspecialchars($date);

	$erapse = UTIME - $timestamp + LOCALZONE;
	foreach ($_plugin_new_elapses as $limit=>$tag)
	{
		if ($erapse <= $limit)
		{
			$retval .= $tag;
			break;
		}
	}
	return sprintf(NEW_MESSAGE,$retval);
}
?>
