<?php
// RSS 1.0 plugin - had been merged into rss plugin
// $Id: rss10.inc.php,v 1.16 2004/12/02 11:34:25 henoheno Exp $

function plugin_rss10_action()
{
	pkwk_headers_sent();
	header('Status: 301 Moved Permanently');
	header('Location: ' . $script . '?cmd=rss&ver=1.0'); // HTTP
	exit;
}
?>
