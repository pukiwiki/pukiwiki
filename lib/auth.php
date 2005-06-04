<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: auth.php,v 1.15 2005/06/04 00:40:14 henoheno Exp $
// Copyright (C) 2003-2005 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Authentication related functions

define('PKWK_PASSPHRASE_LIMIT_LENGTH', 512);

// Passwd-auth related ----

function pkwk_login($pass = '')
{
	global $adminpass;

	if (! PKWK_READONLY && isset($adminpass) &&
		pkwk_hash_compute($adminpass, $pass) === $adminpass) {
		return TRUE;
	} else {
		sleep(2);       // Blocking brute force attack
		return FALSE;
	}
}

// Compute RFC2307 'userPassword' value, like slappasswd (OpenLDAP)
// $scheme : Specify '{scheme}' or '{scheme}salt'
// $phrase : Pass-phrase
// $prefix : Output with a scheme-prefix or not
// $canonical : Correct or Preserve $scheme prefix
function pkwk_hash_compute($scheme = '{php_md5}', $phrase = '', $prefix = TRUE, $canonical = FALSE)
{
	if (strlen($phrase) > PKWK_PASSPHRASE_LIMIT_LENGTH)
		die('pkwk_hash_compute(): malicious message length');

	// With a {scheme}salt or not
	$matches = array();
	if (preg_match('/^(\{.+\})(.*)$/', $scheme, $matches)) {
		$scheme = $matches[1];
		$salt   = $matches[2];
	} else {
		$scheme  = ''; // Treat as '{CLEARTEXT}';
		$salt    = '';
	}

	// Compute and add a scheme-prefix
	switch (strtolower($scheme)) {
	case '{x-php-crypt}' : /* FALLTHROUGH */
	case '{php_crypt}'   :
		$hash = ($prefix ? ($canonical ? '{x-php-crypt}' : $scheme) : '') .
			($salt != '' ? crypt($phrase, $salt) : crypt($phrase));
		break;
	case '{x-php-md5}'   : /* FALLTHROUGH */
	case '{php_md5}'     :
		$hash = ($prefix ? ($canonical ? '{x-php-md5}' : $scheme) : '') .
			md5($phrase);
		break;
	case '{x-php-sha1}'  : /* FALLTHROUGH */
	case '{php_sha1}'    :
		$hash = ($prefix ? ($canonical ? '{x-php-sha1}' : $scheme) : '') .
			sha1($phrase);
		break;

	case '{crypt}'       : /* FALLTHROUGH */
	case '{ldap_crypt}'  :
		$hash = ($prefix ? ($canonical ? '{CRYPT}' : $scheme) : '') .
			($salt != '' ? crypt($phrase, $salt) : crypt($phrase));
		break;

	case '{md5}'         : /* FALLTHROUGH */
	case '{ldap_md5}'    :
		$hash = ($prefix ? ($canonical ? '{MD5}' : $scheme) : '') .
			base64_encode(hex2bin(md5($phrase)));
		break;
	case '{smd5}'        : /* FALLTHROUGH */
	case '{ldap_smd5}'   :
		// MD5 Key length = 128bits = 16bytes
		$salt = ($salt != '' ? substr(base64_decode($salt), 16) : substr(crypt(''), -8));
		$hash = ($prefix ? ($canonical ? '{SMD5}' : $scheme) : '') .
			base64_encode(hex2bin(md5($phrase . $salt)) . $salt);
		break;

	case '{sha}'         : /* FALLTHROUGH */
	case '{ldap_sha}'    :
		$hash = ($prefix ? ($canonical ? '{SHA}' : $scheme) : '') .
			base64_encode(hex2bin(sha1($phrase)));
		break;
	case '{ssha}'        : /* FALLTHROUGH */
	case '{ldap_ssha}'   :
		// SHA-1 Key length = 160bits = 20bytes
		$salt = ($salt != '' ? substr(base64_decode($salt), 20) : substr(crypt(''), -8));
		$hash = ($prefix ? ($canonical ? '{SSHA}' : $scheme) : '') .
			base64_encode(hex2bin(sha1($phrase . $salt)) . $salt);
		break;

	case '{cleartext}'   : /* FALLTHROUGH */
	case '{clear}'       : /* FALLTHROUGH */
	case ''              :
		$hash = ($prefix ? ($canonical ? '' : $scheme) : '') .
			$phrase; // Keep NO prefix with $canonical
		break;

	default:
		$hash = FALSE; break; // Invalid scheme
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
				htmlspecialchars(strip_bracket($page)), $_title_cannotedit);
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
	global $auth_method_type, $auth_users, $_msg_auth;

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
	if (! isset($_SERVER['PHP_AUTH_USER']) &&
		! isset($_SERVER ['PHP_AUTH_PW']) &&
		isset($_SERVER['HTTP_AUTHORIZATION']) &&
		preg_match('/^Basic (.*)$/', $_SERVER['HTTP_AUTHORIZATION'], $matches))
	{

		// Basic-auth with $_SERVER['HTTP_AUTHORIZATION']
		list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
			explode(':', base64_decode($matches[1]));
	}

	if (PKWK_READONLY ||
		! isset($_SERVER['PHP_AUTH_USER']) ||
		! in_array($_SERVER['PHP_AUTH_USER'], $user_list) ||
		! isset($auth_users[$_SERVER['PHP_AUTH_USER']]) ||
		pkwk_hash_compute($auth_users[$_SERVER['PHP_AUTH_USER']],
			$_SERVER['PHP_AUTH_PW']) !== $auth_users[$_SERVER['PHP_AUTH_USER']])
	{
		// Auth failed
		pkwk_common_headers();
		if ($auth_flag) {
			header('WWW-Authenticate: Basic realm="' . $_msg_auth . '"');
			header('HTTP/1.0 401 Unauthorized');
		}
		if ($exit_flag) {
			$body = $title = str_replace('$1',
				htmlspecialchars(strip_bracket($page)), $title_cannot);
			$page = str_replace('$1', make_search($page), $title_cannot);
			catbody($title, $page, $body);
			exit;
		}
		return FALSE;
	} else {
		return TRUE;
	}
}
?>
