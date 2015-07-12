<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: auth.php,v 1.22 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C) 2003-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Authentication related functions

define('PKWK_PASSPHRASE_LIMIT_LENGTH', 512);

/////////////////////////////////////////////////
// Auth type

define('AUTH_TYPE_NONE', 0);
define('AUTH_TYPE_BASIC', 1);
define('AUTH_TYPE_EXTERNAL', 2);

define('AUTH_TYPE_EXTERNAL_REMOTE_USER', 4);
define('AUTH_TYPE_EXTERNAL_X_FORWARDED_USER', 5);


// Passwd-auth related ----

function pkwk_login($pass = '')
{
	global $adminpass;

	if (! PKWK_READONLY && isset($adminpass) &&
		pkwk_hash_compute($pass, $adminpass) === $adminpass) {
		return TRUE;
	} else {
		sleep(2);       // Blocking brute force attack
		return FALSE;
	}
}

// Compute RFC2307 'userPassword' value, like slappasswd (OpenLDAP)
// $phrase : Pass-phrase
// $scheme : Specify '{scheme}' or '{scheme}salt'
// $prefix : Output with a scheme-prefix or not
// $canonical : Correct or Preserve $scheme prefix
function pkwk_hash_compute($phrase = '', $scheme = '{x-php-md5}', $prefix = TRUE, $canonical = FALSE)
{
	if (! is_string($phrase) || ! is_string($scheme)) return FALSE;

	if (strlen($phrase) > PKWK_PASSPHRASE_LIMIT_LENGTH)
		die('pkwk_hash_compute(): malicious message length');

	// With a {scheme}salt or not
	$matches = array();
	if (preg_match('/^(\{.+\})(.*)$/', $scheme, $matches)) {
		$scheme = & $matches[1];
		$salt   = & $matches[2];
	} else if ($scheme != '') {
		$scheme  = ''; // Cleartext
		$salt    = '';
	}

	// Compute and add a scheme-prefix
	switch (strtolower($scheme)) {

	// PHP crypt()
	case '{x-php-crypt}' :
		$hash = ($prefix ? ($canonical ? '{x-php-crypt}' : $scheme) : '') .
			($salt != '' ? crypt($phrase, $salt) : crypt($phrase));
		break;

	// PHP md5()
	case '{x-php-md5}'   :
		$hash = ($prefix ? ($canonical ? '{x-php-md5}' : $scheme) : '') .
			md5($phrase);
		break;

	// PHP sha1()
	case '{x-php-sha1}'  :
		$hash = ($prefix ? ($canonical ? '{x-php-sha1}' : $scheme) : '') .
			sha1($phrase);
		break;

	// LDAP CRYPT
	case '{crypt}'       :
		$hash = ($prefix ? ($canonical ? '{CRYPT}' : $scheme) : '') .
			($salt != '' ? crypt($phrase, $salt) : crypt($phrase));
		break;

	// LDAP MD5
	case '{md5}'         :
		$hash = ($prefix ? ($canonical ? '{MD5}' : $scheme) : '') .
			base64_encode(pkwk_hex2bin(md5($phrase)));
		break;

	// LDAP SMD5
	case '{smd5}'        :
		// MD5 Key length = 128bits = 16bytes
		$salt = ($salt != '' ? substr(base64_decode($salt), 16) : substr(crypt(''), -8));
		$hash = ($prefix ? ($canonical ? '{SMD5}' : $scheme) : '') .
			base64_encode(pkwk_hex2bin(md5($phrase . $salt)) . $salt);
		break;

	// LDAP SHA
	case '{sha}'         :
		$hash = ($prefix ? ($canonical ? '{SHA}' : $scheme) : '') .
			base64_encode(pkwk_hex2bin(sha1($phrase)));
		break;

	// LDAP SSHA
	case '{ssha}'        :
		// SHA-1 Key length = 160bits = 20bytes
		$salt = ($salt != '' ? substr(base64_decode($salt), 20) : substr(crypt(''), -8));
		$hash = ($prefix ? ($canonical ? '{SSHA}' : $scheme) : '') .
			base64_encode(pkwk_hex2bin(sha1($phrase . $salt)) . $salt);
		break;

	// LDAP CLEARTEXT and just cleartext
	case '{cleartext}'   : /* FALLTHROUGH */
	case ''              :
		$hash = ($prefix ? ($canonical ? '{CLEARTEXT}' : $scheme) : '') .
			$phrase;
		break;

	// Invalid scheme
	default:
		$hash = FALSE;
		break;
	}

	return $hash;
}


// Basic-auth related ----

// Check edit-permission
function check_editable($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	global $script, $_title_cannotedit, $_msg_unfreeze;

	if (edit_auth($page, $auth_flag, $exit_flag) && is_editable($page)) {
		// Editable
		return TRUE;
	} else {
		// Not editable
		if ($exit_flag === FALSE) {
			return FALSE; // Without exit
		} else {
			// With exit
			$body = $title = str_replace('$1',
				htmlsc(strip_bracket($page)), $_title_cannotedit);
			if (is_freeze($page))
				$body .= '(<a href="' . $script . '?cmd=unfreeze&amp;page=' .
					rawurlencode($page) . '">' . $_msg_unfreeze . '</a>)';
			$page = str_replace('$1', make_search($page), $_title_cannotedit);
			catbody($title, $page, $body);
			exit;
		}
	}
}

// Check read-permission
function check_readable($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	return read_auth($page, $auth_flag, $exit_flag);
}

function edit_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	global $edit_auth, $edit_auth_pages, $_title_cannotedit;
	return $edit_auth ?  basic_auth($page, $auth_flag, $exit_flag,
		$edit_auth_pages, $_title_cannotedit) : TRUE;
}

function read_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	global $read_auth, $read_auth_pages, $_title_cannotread;
	return $read_auth ?  basic_auth($page, $auth_flag, $exit_flag,
		$read_auth_pages, $_title_cannotread) : TRUE;
}

// Basic authentication
function basic_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot)
{
	global $auth_method_type, $auth_users, $_msg_auth, $auth_user;

	// Checked by:
	$target_str = '';
	if ($auth_method_type == 'pagename') {
		$target_str = $page; // Page name
	} else if ($auth_method_type == 'contents') {
		$target_str = join('', get_source($page)); // Its contents
	}

	$user_list = array();
	foreach($auth_pages as $key=>$val)
		if (preg_match($key, $target_str))
			$user_list = array_merge($user_list, explode(',', $val));

	if (empty($user_list)) return TRUE; // No limit

	$matches = array();
	if (PKWK_READONLY ||
		! $auth_user ||
		! in_array($auth_user, $user_list))
	{
		// Auth failed
		pkwk_common_headers();
		if ($auth_flag && $auth_user === '') {
			header('WWW-Authenticate: Basic realm="' . $_msg_auth . '"');
			header('HTTP/1.0 401 Unauthorized');
		}
		if ($exit_flag) {
			$body = $title = str_replace('$1',
				htmlsc(strip_bracket($page)), $title_cannot);
			$page = str_replace('$1', make_search($page), $title_cannot);
			catbody($title, $page, $body);
			exit;
		}
		return FALSE;
	} else {
		return TRUE;
	}
}

/**
 * Send 401 if client send a invalid credentials
 *
 * @return true if valid, false if invalid credentials
 */
function ensure_valid_auth_user()
{
	global $auth_type, $auth_users, $_msg_auth, $auth_pass, $auth_user;
	switch ($auth_type) {
		case AUTH_TYPE_BASIC:
		{
			if (isset($_SERVER['PHP_AUTH_USER'])) {
				$user = $_SERVER['PHP_AUTH_USER'];
				if (in_array($user, array_keys($auth_users))) {
					if (pkwk_hash_compute(
						$_SERVER['PHP_AUTH_PW'],
						$auth_users[$user]) === $auth_users[$user]) {
						$auth_user = $user;
						return true;
					}
				}
				header('WWW-Authenticate: Basic realm="' . $_msg_auth . '"');
				header('HTTP/1.0 401 Unauthorized');
			}
			$auth_user = '';
			return true; // no auth input
		}
		case AUTH_TYPE_EXTERNAL_REMOTE_USER:
			$auth_user = $_SERVER['REMOTE_USER'];
		case AUTH_TYPE_EXTERNAL_X_FORWARDED_USER:
			$auth_user =  $_SERVER['HTTP_X_FORWARDED_USER'];
		default: // AUTH_TYPE_NONE
			$auth_user = '';
	}
	return true; // is not basic auth
}

/**
 * Get authenticated user name.
 *
 * @global type $auth_user
 * @return type
 */
function get_auth_user()
{
	global $auth_user;
	return $auth_user;
}
