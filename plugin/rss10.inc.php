<?php
// RSS 1.0 plugin - had been merged into rss plugin
// $Id: rss10.inc.php,v 1.14 2004/11/28 04:52:43 henoheno Exp $
function plugin_rss10_action()
{
	header('Status: 301 Moved Permanently');
	header('Location: ' . $script . '?cmd=rss&ver=1.0'); // HTTP
	exit;
}
?>
