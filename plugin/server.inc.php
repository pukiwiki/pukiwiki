<?php
// $Id: server.inc.php,v 1.5 2005/01/23 08:15:35 henoheno Exp $
//
// Server information plugin
// by Reimy http://pukiwiki.reimy.com/

function plugin_server_convert()
{
	return '<dl>' . "\n" .
		'<dt>Server Name</dt>'     . '<dd>' . SERVER_NAME . '</dd>' . "\n" .
		'<dt>Server Software</dt>' . '<dd>' . SERVER_SOFTWARE . '</dd>' . "\n" .
		'<dt>Server Admin</dt>'    . '<dd>' .
			'<a href="mailto:' . SERVER_ADMIN . '">' .
			SERVER_ADMIN . '</a></dd>' . "\n" .
		'</dl>' . "\n";
}
?>
