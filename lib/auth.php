<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: auth.php,v 1.7 2005/01/31 14:32:38 henoheno Exp $
//
// Basic authentication related functions

// 編集不可能なページを編集しようとしたとき
function check_editable($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	global $script, $_title_cannotedit, $_msg_unfreeze;

	if (edit_auth($page, $auth_flag, $exit_flag) && is_editable($page))
		return TRUE;

	if (! $exit_flag) return FALSE;

	$body = $title = str_replace('$1', htmlspecialchars(strip_bracket($page)), $_title_cannotedit);

	if (is_freeze($page))
		$body .= '(<a href="' . $script . '?cmd=unfreeze&amp;page=' .
			rawurlencode($page) . '">' . $_msg_unfreeze . '</a>)';

	$page = str_replace('$1', make_search($page), $_title_cannotedit);

	catbody($title, $page, $body);
	exit;
}

// 閲覧不可能なページを閲覧しようとしたとき (？)
function check_readable($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	return read_auth($page, $auth_flag, $exit_flag);
}

// 編集認証
function edit_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	global $edit_auth, $edit_auth_pages, $_title_cannotedit;

	return $edit_auth ?  basic_auth($page, $auth_flag, $exit_flag,
		$edit_auth_pages, $_title_cannotedit) : TRUE;
}

// 閲覧認証
function read_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	global $read_auth, $read_auth_pages, $_title_cannotread;

	return $read_auth ?  basic_auth($page, $auth_flag, $exit_flag,
		$read_auth_pages, $_title_cannotread) : TRUE;
}

// Basic authentication
function basic_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot)
{
	global $auth_users, $auth_method_type;
	global $_msg_auth;

	// 認証要否判断対象
	$target_str = '';
	if ($auth_method_type == 'pagename') {
		// ページ名でチェックする
		$target_str = $page;
	} else if ($auth_method_type == 'contents') {
		// ページ内の文字列でチェックする
		$target_str = join('', get_source($page));
	}

	// 合致したパターンで定義されたユーザのリスト
	$user_list = array();
	foreach($auth_pages as $key=>$val)
		if (preg_match($key, $target_str))
			$user_list = array_merge($user_list, explode(',', $val));

	if (empty($user_list)) return TRUE; // No limit

	// PHP_AUTH* 変数が未定義の場合
	$matches = array();
	if (! isset($_SERVER['PHP_AUTH_USER'])
		&& ! isset($_SERVER ['PHP_AUTH_PW'])
		&& isset($_SERVER['HTTP_AUTHORIZATION'])
		&& preg_match('/^Basic (.*)$/', $_SERVER['HTTP_AUTHORIZATION'], $matches))
	{
		// HTTP_AUTHORIZATION 変数を使用した Basic 認証
		list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
			explode(':', base64_decode($matches[1]));
	}

	// ユーザリストに含まれるいずれかのユーザと認証されればOK
	if (PKWK_READONLY
		|| ! isset($_SERVER['PHP_AUTH_USER'])
		|| ! in_array($_SERVER['PHP_AUTH_USER'], $user_list)
		|| ! isset($_SERVER['PHP_AUTH_USER'], $auth_users)
		|| $auth_users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW'])
	{
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
	}
	return TRUE;
}
?>
