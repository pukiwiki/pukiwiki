<?php
// $Id: stationary.inc.php,v 1.2 2005/02/08 14:15:14 henoheno Exp $
//
// Stationary plugin
// License: The same as PukiWiki

// Someting define
define('PLUGIN_STATIONARY_MAX', 10);

function plugin_stationary_init()
{
	if (PKWK_SAFE_MODE || PKWK_READONLY) return; // Do nothing

}

function plugin_stationary_convert()
{
	if (PKWK_SAFE_MODE || PKWK_READONLY) return ''; // Show nothing

	$result = '#stationary()';

	return htmlspecialchars($result) . '<br/>';
}

function plugin_stationary_inline()
{
	if (PKWK_SAFE_MODE || PKWK_READONLY) return ''; // Show nothing

	$result = '&stationary(){};';

	return htmlspecialchars($result);
}

function plugin_stationary_action()
{
	if (PKWK_SAFE_MODE || PKWK_READONLY)
		die_message('PKWK_SAFE_MODE or PKWK_READONLY prohibits this');

	$msg  = 'Message';
	$body = 'Message body';

	return array('msg'=>htmlspecialchars($msg), 'body'=>htmlspecialchars($body));
}
?>
