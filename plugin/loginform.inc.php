<?php

// PukiWiki - Yet another WikiWikiWeb clone
// Copyright (C) 2015 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// "Login form" plugin

function plugin_loginform_inline()
{
	$logout_param = '?plugin=basicauthlogout';
	return '<a href="' . htmlsc(get_script_uri() . $logout_param) . '">Log out</a>';
}

function plugin_loginform_convert()
{
	return '<div>' . plugin_basicauthlogout_inline() . '</div>';
}

function plugin_loginform_action()
{
	global $auth_user, $auth_type;
	$page_r = $_GET['page'];
	$page = rawurldecode($page_r);
	$pcmd = $_GET['pcmd'];
	$url_after_login_r = $_GET['url_after_login'];
	$url_after_login = rawurldecode($url_after_login_r);
	$page_after_login_r = '';
	if (!$url_after_login_r) $page_after_login_r = $page_r;
	$action_url = get_script_uri() . '?plugin=loginform'
		. '&page=' . $page_r
		. ($url_after_login_r ? '&url_after_login=' . $url_after_login_r : '')
		. ($page_after_login_r ? '&page_after_login=' . $page_after_login_r : '');
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($username && $password && form_auth($username, $password)) {
		// Sign in successfully completed
		form_auth_redirect($url_after_login, $page_after_login_r);
		return;
	}
	if ($pcmd === 'logout') {
		// logout
		switch ($auth_type) {
			case AUTH_TYPE_BASIC:
				header('WWW-Authenticate: Basic realm="Please cancel to log out"');
				header('HTTP/1.0 401 Unauthorized');
				break;
			case AUTH_TYPE_FORM:
			case AUTH_TYPE_EXTERNAL:
			default:
				session_destroy();
				break;
		}
		$auth_user = '';
		return array(
			'msg' => 'Log out',
			'body' => 'Logged out completely<br>'
				. '<a href="'. get_script_uri() . '?' . $page_r . '">'
				. $page . '</a>'
		);
	} else {
		// login
		return array(
			'msg' => 'Login',
			'body' => 'Please input username and password:'
			. '<form action="' . htmlsc($action_url) . '" method="post">'
			. 'Username: <input type="text" name="username"><br>'
			. 'Password: <input type="password" name="password"><br>'
			. '<input type="submit" value="Login">'
			. '</form>'
			. "<br>\n"
			);
	}
}
