<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// edit.inc.php
// Copyright 2001-2022 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Edit plugin (cmd=edit)

// Remove #freeze written by hand
define('PLUGIN_EDIT_FREEZE_REGEX', '/^(?:#freeze(?!\w)\s*)+/im');

function plugin_edit_action()
{
	global $vars, $_title_edit;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');

	// Create initial pages
	plugin_edit_setup_initial_pages();

	$page = isset($vars['page']) ? $vars['page'] : '';
	check_editable($page, true, true);
	check_readable($page, true, true);

	if (isset($vars['preview'])) {
		return plugin_edit_preview($vars['msg']);
	} else if (isset($vars['template'])) {
		return plugin_edit_preview_with_template();
	} else if (isset($vars['write'])) {
		return plugin_edit_write();
	} else if (isset($vars['cancel'])) {
		return plugin_edit_cancel();
	}
	ensure_valid_page_name_length($page);
	$postdata = @join('', get_source($page));
	if ($postdata === '') $postdata = auto_template($page);
	$postdata = remove_author_info($postdata);
	return array('msg'=>$_title_edit, 'body'=>edit_form($page, $postdata));
}

function ensure_valid_page_name_length($page)
{
	if (is_page($page)) {
		// Continue
	} else {
		if (is_pagename_bytes_within_soft_limit($page)) {
			// Continue
		} else {
			if (page_exists_in_history($page)) {
				if (is_pagename_bytes_within_hard_limit($page)) {
					// Continue
				} else {
					die_message('Page name too long (hard limit): ' . htmlsc($page));
					exit;
				}
			} else {
				die_message('Page name too long: ' . htmlsc($page));
				exit;
			}
		}
	}
}

/**
 * Preview with template
 */
function plugin_edit_preview_with_template()
{
	global $vars;
	$msg = '';
	$page = isset($vars['page']) ? $vars['page'] : '';
	// Loading template
	$template_page;
	if (isset($vars['template_page']) && is_page($template_page = $vars['template_page'])) {
		if (is_page_readable($template_page)) {
			$msg = remove_author_info(get_source($vars['template_page'], TRUE, TRUE));
			// Cut fixed anchors
			$msg = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $msg);
		}
	}
	return plugin_edit_preview($msg);
}

/**
 * Preview
 *
 * @param msg preview target
 */
function plugin_edit_preview($msg)
{
	global $vars;
	global $_title_preview, $_msg_preview, $_msg_preview_delete;

	$page = isset($vars['page']) ? $vars['page'] : '';

	$msg = preg_replace(PLUGIN_EDIT_FREEZE_REGEX, '', $msg);
	$postdata = $msg;

	if (isset($vars['add']) && $vars['add']) {
		if (isset($vars['add_top']) && $vars['add_top']) {
			$postdata  = $postdata . "\n\n" . @join('', get_source($page));
		} else {
			$postdata  = @join('', get_source($page)) . "\n\n" . $postdata;
		}
	}

	$body = $_msg_preview . '<br />' . "\n";
	if ($postdata === '')
		$body .= '<strong>' . $_msg_preview_delete . '</strong>';
	$body .= '<br />' . "\n";

	if ($postdata) {
		$postdata = make_str_rules($postdata);
		$postdata = explode("\n", $postdata);
		$postdata = drop_submit(convert_html($postdata));
		$body .= '<div id="preview">' . $postdata . '</div>' . "\n";
	}
	$body .= edit_form($page, $msg, $vars['digest'], FALSE);

	return array('msg'=>$_title_preview, 'body'=>$body);
}

// Inline: Show edit (or unfreeze text) link
function plugin_edit_inline()
{
	static $usage = '&edit(pagename#anchor[[,noicon],nolabel])[{label}];';

	global $vars, $fixed_heading_anchor_edit;

	if (PKWK_READONLY) return ''; // Show nothing 

	// Arguments
	$args = func_get_args();

	// {label}. Strip anchor tags only
	$s_label = strip_htmltag(array_pop($args), FALSE);

	$page = array_shift($args);
	if ($page === NULL) $page = '';
	$_noicon = $_nolabel = FALSE;
	foreach($args as $arg){
		switch(strtolower($arg)){
		case ''       :                   break;
		case 'nolabel': $_nolabel = TRUE; break;
		case 'noicon' : $_noicon  = TRUE; break;
		default       : return $usage;
		}
	}

	// Separate a page-name and a fixed anchor
	list($s_page, $id, $editable) = anchor_explode($page, TRUE);

	// Default: This one
	if ($s_page == '') $s_page = isset($vars['page']) ? $vars['page'] : '';

	// $s_page fixed
	$isfreeze = is_freeze($s_page);
	$ispage   = is_page($s_page);

	// Paragraph edit enabled or not
	$short = htmlsc('Edit');
	if ($fixed_heading_anchor_edit && $editable && $ispage && ! $isfreeze) {
		// Paragraph editing
		$id    = rawurlencode($id);
		$title = htmlsc(sprintf('Edit %s', $page));
		$icon = '<img src="' . IMAGE_DIR . 'paraedit.png' .
			'" width="9" height="9" alt="' .
			$short . '" title="' . $title . '" /> ';
		$class = ' class="anchor_super"';
	} else {
		// Normal editing / unfreeze
		$id    = '';
		if ($isfreeze) {
			$title = 'Unfreeze %s';
			$icon  = 'unfreeze.png';
		} else {
			$title = 'Edit %s';
			$icon  = 'edit.png';
		}
		$title = htmlsc(sprintf($title, $s_page));
		$icon = '<img src="' . IMAGE_DIR . $icon .
			'" width="20" height="20" alt="' .
			$short . '" title="' . $title . '" />';
		$class = '';
	}
	if ($_noicon) $icon = ''; // No more icon
	if ($_nolabel) {
		if (!$_noicon) {
			$s_label = '';     // No label with an icon
		} else {
			$s_label = $short; // Short label without an icon
		}
	} else {
		if ($s_label == '') $s_label = $title; // Rich label with an icon
	}

	// URL
	$script = get_base_uri();
	if ($isfreeze) {
		$url   = $script . '?cmd=unfreeze&amp;page=' . rawurlencode($s_page);
	} else {
		$s_id = ($id == '') ? '' : '&amp;id=' . $id;
		$url  = $script . '?cmd=edit&amp;page=' . rawurlencode($s_page) . $s_id;
	}
	$atag  = '<a' . $class . ' href="' . $url . '" title="' . $title . '">';
	static $atags = '</a>';

	if ($ispage) {
		// Normal edit link
		return $atag . $icon . $s_label . $atags;
	} else {
		// Dangling edit link
		return '<span class="noexists">' . $atag . $icon . $atags .
			$s_label . $atag . '?' . $atags . '</span>';
	}
}

// Write, add, or insert new comment
function plugin_edit_write()
{
	global $vars;
	global $_title_collided, $_msg_collided_auto, $_msg_collided, $_title_deleted;
	global $notimeupdate, $_msg_invalidpass, $do_update_diff_table;

	$page   = isset($vars['page'])   ? $vars['page']   : '';
	$add    = isset($vars['add'])    ? $vars['add']    : '';
	$digest = isset($vars['digest']) ? $vars['digest'] : '';

	ensure_valid_page_name_length($page);
	$vars['msg'] = preg_replace(PLUGIN_EDIT_FREEZE_REGEX, '', $vars['msg']);
	$msg = & $vars['msg']; // Reference

	$retvars = array();

	// Collision Detection
	$oldpagesrc = join('', get_source($page));
	$oldpagemd5 = md5($oldpagesrc);
	if ($digest !== $oldpagemd5) {
		$vars['digest'] = $oldpagemd5; // Reset

		$original = isset($vars['original']) ? $vars['original'] : '';
		$old_body = remove_author_info($oldpagesrc);
		list($postdata_input, $auto) = do_update_diff($old_body, $msg, $original);

		$retvars['msg' ] = $_title_collided;
		$retvars['body'] = ($auto ? $_msg_collided_auto : $_msg_collided) . "\n";
		$retvars['body'] .= $do_update_diff_table;
		$retvars['body'] .= edit_form($page, $postdata_input, $oldpagemd5, FALSE);
		return $retvars;
	}

	// Action?
	if ($add) {
		// Add
		if (isset($vars['add_top']) && $vars['add_top']) {
			$postdata  = $msg . "\n\n" . @join('', get_source($page));
		} else {
			$postdata  = @join('', get_source($page)) . "\n\n" . $msg;
		}
	} else {
		// Edit or Remove
		$postdata = & $msg; // Reference
	}

	// NULL POSTING, OR removing existing page
	if ($postdata === '') {
		page_write($page, $postdata);
		$retvars['msg' ] = $_title_deleted;
		$retvars['body'] = str_replace('$1', htmlsc($page), $_title_deleted);
		return $retvars;
	}

	// $notimeupdate: Checkbox 'Do not change timestamp'
	$notimestamp = isset($vars['notimestamp']) && $vars['notimestamp'] != '';
	if ($notimeupdate > 1 && $notimestamp && ! pkwk_login($vars['pass'])) {
		// Enable only administrator & password error
		$retvars['body']  = '<p><strong>' . $_msg_invalidpass . '</strong></p>' . "\n";
		$retvars['body'] .= edit_form($page, $msg, $digest, FALSE);
		return $retvars;
	}

	page_write($page, $postdata, $notimeupdate != 0 && $notimestamp);
	pkwk_headers_sent();
	header('Location: ' . get_page_uri($page, PKWK_URI_ROOT));
	exit;
}

// Cancel (Back to the page / Escape edit page)
function plugin_edit_cancel()
{
	global $vars;
	pkwk_headers_sent();
	header('Location: ' . get_page_uri($vars['page'], PKWK_URI_ROOT));
	exit;
}

/**
 * Setup initial pages
 */
function plugin_edit_setup_initial_pages()
{
	global $autoalias, $no_autoticketlinkname;

	// Related: Rename plugin
	if (exist_plugin('rename') && function_exists('plugin_rename_setup_initial_pages')) {
		plugin_rename_setup_initial_pages();
	}
	// AutoTicketLinkName page
	if (! $no_autoticketlinkname) {
		init_autoticketlink_def_page();
	}
	// AutoAliasName page
	if ($autoalias) {
		init_autoalias_def_page();
	}
}
