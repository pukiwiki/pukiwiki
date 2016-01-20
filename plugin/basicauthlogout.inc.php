<?php

// PukiWiki - Yet another WikiWikiWeb clone
// Copyright (C) 2016 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// "Basic auth logout" plugin

function plugin_basicauthlogout_inline()
{
	$logout_param = '?plugin=basicauthlogout';
	return '<a href="' . htmlsc(get_script_uri() . $logout_param) . '">Log out</a>';
}

function plugin_basicauthlogout_convert()
{
	return '<div>' . plugin_basicauthlogout_inline() . '</div>';
}

function plugin_basicauthlogout_action()
{
	global $auth_flag, $_msg_auth;
	pkwk_common_headers();
	if (isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="Please cancel to log out"');
		header('HTTP/1.0 401 Unauthorized');
	}
	return array(
		'msg' => 'Log out',
		'body' => 'Logged out completely');
}
