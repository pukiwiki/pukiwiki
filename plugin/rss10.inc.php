<?php
// RSS 1.0 plugin - had been merged into rss plugin
// $Id: rss10.inc.php,v 1.15 2004/11/28 12:50:13 henoheno Exp $

function plugin_rss10_action()
{
	header('Status: 301 Moved Permanently');
	header('Location: ' . $script . '?cmd=rss&ver=1.0'); // HTTP
	exit;
}
?>
