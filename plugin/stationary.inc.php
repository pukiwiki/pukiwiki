<?php
// $Id: stationary.inc.php,v 1.1 2005/02/08 14:04:05 henoheno Exp $
//
// Stationary plugin
// License: The same as PukiWiki

// Someting define
define('PLUGIN_STATIONARY_MAX', 10);

function plugin_stationary_init()
{

}

function plugin_stationary_convert()
{
	return '#stationary()' . '</br>' ."\n";
}

function plugin_stationary_inline()
{
	return '&stationary();';
}

function plugin_stationary_action()
{
	die_message('stationary');
}

?>
