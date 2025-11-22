<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// draft.inc.php
// Copyright 2024 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Draft management plugin (cmd=draft)
//
// Version 1.1.0
//
// [Changelog]
// 1.1.0 (2025-11-15): Add access control
//   - Prohibit access in READONLY mode
//   - Require authentication when $edit_auth is enabled
// 1.0.0 (2025-11-15): Initial release
//   - Draft list, delete, and publish functionality

function plugin_draft_action()
{
	global $vars;

	// 読み取り専用モードではアクセス拒否
	if (PKWK_READONLY) {
		die_message('Prohibited by admin (READONLY mode)');
	}

	// 認証が有効な場合、ログインユーザーのみアクセス可能
	global $edit_auth;
	if ($edit_auth) {
		$auth_user = get_auth_user();
		if (!$auth_user) {
			die_message('Authentication required to view draft list');
		}
	}

	// Load draft library
	require_once(LIB_DIR . 'draft.php');

	$action = isset($vars['action']) ? $vars['action'] : 'list';

	switch ($action) {
		case 'delete':
			return plugin_draft_delete();
		case 'publish':
			return plugin_draft_publish();
		case 'force_publish':
			return plugin_draft_force_publish();
		default:
			return plugin_draft_list();
	}
}

/**
 * Show draft list
 */
function plugin_draft_list()
{
	global $script;
	global $_msg_draft_not_found, $_msg_draft_list, $_msg_draft_edit, $_msg_draft_publish, $_msg_draft_delete;
	global $_msg_draft_publish_confirm, $_msg_draft_delete_confirm;

	$drafts = get_draft_list();

	if (empty($drafts)) {
		$body = '<p>' . $_msg_draft_not_found . '</p>';
	} else {
		$body = '<ul>';
		$ticket = get_ticket();
		foreach ($drafts as $page) {
			if (!is_editable($page)) continue;

			$time = get_draft_filetime($page);
			$time_str = format_date($time);
			$page_link = make_pagelink($page);
			$edit_link = $script . '?cmd=edit&amp;page=' . rawurlencode($page) . '&amp;load_draft=true';
			$confirm_publish = htmlsc(json_encode($_msg_draft_publish_confirm));
			$confirm_delete  = htmlsc(json_encode($_msg_draft_delete_confirm));
			
			// Edit form
			$edit_form = '<form action="' . $script . '" method="get" style="display:inline-block; margin:0;">' .
				'<input type="hidden" name="cmd" value="edit" />' .
				'<input type="hidden" name="page" value="' . htmlsc($page) . '" />' .
				'<input type="hidden" name="load_draft" value="true" />' .
				'<input type="submit" value="' . $_msg_draft_edit . '" />' .
				'</form>';

			// Delete form
			$delete_form = '<form action="' . $script . '" method="post" style="display:inline-block; margin:0; margin-left:4px;">' .
				'<input type="hidden" name="cmd" value="draft" />' .
				'<input type="hidden" name="action" value="delete" />' .
				'<input type="hidden" name="page" value="' . htmlsc($page) . '" />' .
				'<input type="hidden" name="ticket" value="' . $ticket . '" />' .
				'<input type="submit" value="' . $_msg_draft_delete . '" onclick="return confirm(' . $confirm_delete . ')" />' .
				'</form>';

			$body .= '<li>';
			$body .= $page_link;
			$body .= ' (' . $time_str . ')';
			$body .= '<div style="margin-top:4px; margin-bottom:8px;">';
			$body .= $edit_form;
			$body .= $delete_form;
			$body .= '</div>';
			$body .= '</li>';
		}
		$body .= '</ul>';
	}

	return array(
		'msg' => $_msg_draft_list,
		'body' => $body
	);
}

/**
 * Delete draft
 */
function plugin_draft_delete()
{
	global $vars, $script;
	global $_msg_draft_deleted, $_msg_draft_delete_error, $_msg_draft_delete, $_msg_draft_list;
	global $_msg_draft_invalid_action;

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !check_ticket()) {
		return array(
			'msg' => 'Error',
			'body' => '<p>' . $_msg_draft_invalid_action . '</p>'
		);
	}

	$page = isset($vars['page']) ? $vars['page'] : '';
	if ($page === '') {
		return array(
			'msg' => 'エラー',
			'body' => '<p>ページが指定されていません。</p>'
		);
	}

	// Check permission
	check_editable($page, true, true);

	if (draft_delete($page)) {
		$body = '<p>' . $_msg_draft_deleted . '</p>';
		$body .= '<p><a href="' . $script . '?cmd=draft">' . $_msg_draft_list . 'に戻る</a></p>';
	} else {
		$body = '<p>' . $_msg_draft_delete_error . '</p>';
	}

	return array(
		'msg' => $_msg_draft_delete,
		'body' => $body
	);
}

/**
 * Publish draft (convert to main page)
 */
function plugin_draft_publish()
{
	global $vars, $script;
	global $_msg_draft_published, $_msg_draft_publish_error, $_msg_draft_publish, $_msg_draft_list;
	global $_msg_draft_not_found, $_msg_draft_invalid_action, $_msg_draft_conflict_title, $_msg_draft_conflict_body, $_msg_draft_conflict_diff;
	global $_msg_draft_edit, $_btn_draft_force_publish, $_msg_draft_force_publish_confirm;

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !check_ticket()) {
		return array(
			'msg' => 'Error',
			'body' => '<p>' . $_msg_draft_invalid_action . '</p>'
		);
	}

	$page = isset($vars['page']) ? $vars['page'] : '';
	if ($page === '') {
		return array(
			'msg' => 'エラー',
			'body' => '<p>ページが指定されていません。</p>'
		);
	}

	// Check permission
	check_editable($page, true, true);

	// Get draft content
	$draft = get_draft_with_meta($page, TRUE, TRUE);
	if ($draft === FALSE || $draft['content'] === '') {
		return array(
			'msg' => 'エラー',
			'body' => '<p>' . $_msg_draft_not_found . '</p>'
		);
	}

	// Conflict detection (digest優先、無ければtimestamp)
	$current_source = get_source($page, TRUE, TRUE);
	if ($current_source === FALSE) $current_source = '';
	$current_digest = md5($current_source);

	$draft_digest = $draft['meta']['digest'];
	$draft_saved_ts = NULL;
	if (!empty($draft['meta']['saved'])) {
		$draft_saved_ts = strtotime($draft['meta']['saved']);
	}

	$conflict = FALSE;
	if ($draft_digest !== NULL && $draft_digest !== '') {
		if ($draft_digest !== $current_digest) {
			$conflict = TRUE;
		}
	} else if ($draft_saved_ts !== NULL && $draft_saved_ts !== FALSE) {
		$page_ts = get_filetime($page) + LOCALZONE;
		if ($page_ts > $draft_saved_ts) {
			$conflict = TRUE;
		}
	}

	if ($conflict) {
		require_once(LIB_DIR . 'diff.php');
		$diff_raw = do_diff($current_source, $draft['content']);
		$diff_html = diff_style_to_css(htmlsc($diff_raw));
		$ticket = get_ticket();
		$edit_link = $script . '?cmd=edit&amp;page=' . rawurlencode($page) . '&amp;load_draft=true';
		$body  = '<p>' . $_msg_draft_conflict_body . '</p>';
		$body .= '<p><a href="' . $edit_link . '">' . $_msg_draft_edit . '</a></p>';
		$body .= '<h3>' . $_msg_draft_conflict_diff . '</h3>';
		$body .= '<pre class="diff">' . $diff_html . '</pre>';
		$body .= '<form action="' . $script . '" method="post" style="margin-top:10px;">' .
			'<input type="hidden" name="cmd" value="draft" />' .
			'<input type="hidden" name="action" value="force_publish" />' .
			'<input type="hidden" name="page" value="' . htmlsc($page) . '" />' .
			'<input type="hidden" name="ticket" value="' . $ticket . '" />' .
			'<input type="submit" value="' . $_btn_draft_force_publish . '" onclick="return confirm(\'' . $_msg_draft_force_publish_confirm . '\')" />' .
			'</form>';
		return array(
			'msg' => $_msg_draft_conflict_title,
			'body' => $body
		);
	}

	// Write to main page
	page_write($page, $draft['content']);

	// Delete draft
	draft_delete($page);

	$page_link = make_pagelink($page);
	$body = '<p>' . $_msg_draft_published . ': ' . $page_link . '</p>';
	$body .= '<p><a href="' . $script . '?cmd=draft">' . $_msg_draft_list . 'に戻る</a></p>';

	return array(
		'msg' => $_msg_draft_publish,
		'body' => $body
	);
}

/**
 * Force publish draft (skip conflict check)
 */
function plugin_draft_force_publish()
{
	global $vars, $script;
	global $_msg_draft_published, $_msg_draft_publish_error, $_msg_draft_publish, $_msg_draft_list;
	global $_msg_draft_not_found, $_msg_draft_invalid_action;

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !check_ticket()) {
		return array(
			'msg' => 'Error',
			'body' => '<p>' . $_msg_draft_invalid_action . '</p>'
		);
	}

	$page = isset($vars['page']) ? $vars['page'] : '';
	if ($page === '') {
		return array(
			'msg' => 'エラー',
			'body' => '<p>ページが指定されていません。</p>'
		);
	}

	// Check permission
	check_editable($page, true, true);

	// Get draft content
	$draft = get_draft_with_meta($page, TRUE, TRUE);
	if ($draft === FALSE || $draft['content'] === '') {
		return array(
			'msg' => 'エラー',
			'body' => '<p>' . $_msg_draft_not_found . '</p>'
		);
	}

	// Write to main page
	page_write($page, $draft['content']);

	// Delete draft
	draft_delete($page);

	$page_link = make_pagelink($page);
	$body = '<p>' . $_msg_draft_published . ': ' . $page_link . '</p>';
	$body .= '<p><a href="' . $script . '?cmd=draft">' . $_msg_draft_list . 'に戻る</a></p>';

	return array(
		'msg' => $_msg_draft_publish,
		'body' => $body
	);
}
