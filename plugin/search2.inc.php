<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// search2.inc.php
// Copyright 2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Search2 plugin - Show detail result using JavaScript

define('PLUGIN_SEARCH2_MAX_LENGTH', 80);
define('PLUGIN_SEARCH2_MAX_BASE',   16); // #search(1,2,3,...,15,16)

define('PLUGIN_SEARCH2_RESULT_RECORD_LIMIT', 500);
define('PLUGIN_SEARCH2_RESULT_RECORD_LIMIT_START', 100);
define('PLUGIN_SEARCH2_SEARCH_WAIT_MILLISECONDS', 1000);

// Show a search box on a page
function plugin_search2_convert()
{
	$args = func_get_args();
	return plugin_search_search_form('', '', $args);
}

function plugin_search2_action()
{
	global $vars, $_title_search, $_title_result;

	$action = isset($vars['action']) ? $vars['action'] : '';
	$base = isset($vars['base']) ? $vars['base'] : '';
	$start_s = isset($vars['start']) ? $vars['start'] : '';
	$start_index = pkwk_ctype_digit($start_s) ? intval($start_s) : 0;
	$bases = array();
	if ($base !== '') {
		$bases[] = $base;
	}
	if ($action === '') {
		$q = isset($vars['q']) ? $vars['q'] : '';
		if ($q === '') {
			return array('msg' => $_title_search,
				'body' => plugin_search2_search_form($q, '', $bases));
		} else {
			$msg  = str_replace('$1', htmlsc($q), $_title_result);
			return array('msg' => $msg,
					'body' => plugin_search2_search_form($q, '', $bases));
		}
	} else if ($action === 'query') {
		$text = isset($vars['q']) ? $vars['q'] : '';
		header('Content-Type: application/json; charset=UTF-8');
		plugin_search2_do_search($text, $base, $start_index);
		exit;
	}
}

function plugin_search2_do_search($query_text, $base, $start_index)
{
	global $whatsnew, $non_list, $search_non_list;
	global $_msg_andresult, $_msg_orresult, $_msg_notfoundresult;
	global $search_auth;

	$result_record_limit = $start_index === 0 ?
		PLUGIN_SEARCH2_RESULT_RECORD_LIMIT_START : PLUGIN_SEARCH2_RESULT_RECORD_LIMIT;
	$retval = array();

	$b_type_and = true; // AND:TRUE OR:FALSE
	$key_candidates = preg_split('/\s+/', $query_text, -1, PREG_SPLIT_NO_EMPTY);
	for ($i = count($key_candidates) - 1; $i >= 0; $i--) {
		if ($key_candidates[$i] === 'OR') {
			$b_type_and = false;
			unset($key_candidates[$i]);
		}
	}
	$key_candidates = array_merge($key_candidates);
	$keys = get_search_words($key_candidates);
	foreach ($keys as $key=>$value)
		$keys[$key] = '/' . $value . '/S';

	$pages = get_existpages();

	// Avoid
	if ($base != '') {
		$pages = preg_grep('/^' . preg_quote($base, '/') . '/S', $pages);
	}
	if (! $search_non_list) {
		$pages = array_diff($pages, preg_grep('/' . $non_list . '/S', $pages));
	}
	natsort($pages);
	$pages = array_flip($pages);
	unset($pages[$whatsnew]);
	$page_names = array_keys($pages);

	$found_pages = array();
	$readable_page_index = -1;
	$scan_page_index = -1;
	$saved_scan_start_index = -1;
	$last_read_page_name = null;
	foreach ($page_names as $page) {
		$b_match = FALSE;
		$pagename_only = false;
		$scan_page_index++;
		if (! is_page_readable($page)) {
			if ($search_auth) {
				// $search_auth - 1: User can know page names that contain search text if the page is readable
				continue;
			}
			// $search_auth - 0: All users can know page names that conntain search text
			$pagename_only = true;
		}
		$readable_page_index++;
		if ($readable_page_index < $start_index) {
			// Skip: It's not time to read
			continue;
		}
		if ($saved_scan_start_index === -1) {
			$saved_scan_start_index = $scan_page_index;
		}
		// Search for page name and contents
		$body = get_source($page, TRUE, TRUE, TRUE);
		$target = $page . "\n" . remove_author_header($body);
		foreach ($keys as $key) {
			$b_match = preg_match($key, $target);
			if ($b_type_and xor $b_match) break; // OR
		}
		if ($b_match) {
			// Found!
			$filemtime = null;
			$author_info = get_author_info($body);
			if ($author_info === false || $pagename_only) {
				$updated_at = get_date_atom(filemtime(get_filename($page)));
			}
			if ($pagename_only) {
				// The user cannot read this page body
				$found_pages[] = array('name' => (string)$page,
					'url' => get_page_uri($page), 'updated_at' => $updated_at,
					'body' => '', 'pagename_only' => 1);
			} else {
				$found_pages[] = array('name' => (string)$page,
					'url' => get_page_uri($page), 'updated_at' => $updated_at,
					'body' => (string)$body);
			}
		}
		$last_read_page_name = $page;
		if ($start_index + $result_record_limit <= $readable_page_index + 1) {
			// Read page limit
			break;
		}
	}
	$message = str_replace('$1', htmlsc($query_text), str_replace('$2', count($found_pages),
		str_replace('$3', count($page_names), $b_type_and ? $_msg_andresult : $_msg_orresult)));
	$search_done = (boolean)($scan_page_index + 1 === count($page_names));
	$result_obj = array(
		'message' => $message,
		'q' => $query_text,
		'start_index' => $start_index,
		'limit' => $result_record_limit,
		'read_page_count' => $readable_page_index - $start_index + 1,
		'scan_page_count' => $scan_page_index - $saved_scan_start_index + 1,
		'page_count' => count($page_names),
		'last_read_page_name' => $last_read_page_name,
		'next_start_index' => $readable_page_index + 1,
		'search_done' => $search_done,
		'results' => $found_pages);
	$obj = $result_obj;
	if (!defined('PKWK_UTF8_ENABLE')) {
		if (SOURCE_ENCODING === 'EUC-JP') {
			mb_convert_variables('UTF-8', 'CP51932', $obj);
		} else {
			mb_convert_variables('UTF-8', SOURCE_ENCODING, $obj);
		}
	}
	print(json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function plugin_search2_search_form($s_word = '', $type = '', $bases = array())
{
	global $_btn_search;
	global $_search_pages, $_search_all;
	global $_msg_andresult, $_msg_orresult, $_msg_notfoundresult;
	global $_search_detail, $_search_searching;
	global $_msg_unsupported_webbrowser, $_msg_use_alternative_link;

	$script = get_base_uri();
	$h_search_text = htmlsc($s_word);

	$base_option = '';
	if (!empty($bases)) {
		$base_msg = '';
		$_num = 0;
		$check = ' checked';
		foreach($bases as $base) {
			++$_num;
			if (PLUGIN_SEARCH2_MAX_BASE < $_num) break;
			$s_base   = htmlsc($base);
			$base_str = '<strong>' . $s_base . '</strong>';
			$base_label = str_replace('$1', $base_str, $_search_pages);
			$base_msg  .=<<<EOD
 <div>
  <label>
   <input type="radio" name="base" value="$s_base" $check> $base_label
  </label>
 </div>
EOD;
			$check = '';
		}
		$base_msg .=<<<EOD
<label><input type="radio" name="base" value=""> $_search_all</label>
EOD;
		$base_option = '<div class="small">' . $base_msg . '</div>';
	}
	$_search2_result_notfound = htmlsc($_msg_notfoundresult);
	$_search2_result_found = htmlsc($_msg_andresult);
	$_search2_search_wait_milliseconds = PLUGIN_SEARCH2_SEARCH_WAIT_MILLISECONDS;
	$result_page_panel =<<<EOD
<input type="checkbox" id="_plugin_search2_detail" checked><label for="_plugin_search2_detail">$_search_detail</label>
<input type="hidden" id="_plugin_search2_msg_searching" value="$_search_searching">
<input type="hidden" id="_plugin_search2_msg_result_notfound" value="$_search2_result_notfound">
<input type="hidden" id="_plugin_search2_msg_result_found" value="$_search2_result_found">
<input type="hidden" id="_search2_search_wait_milliseconds" value="$_search2_search_wait_milliseconds">
EOD;
	if ($h_search_text == '') {
		$result_page_panel = '';
	}

	$plain_search_link = '<a href="' . $script . '?cmd=search' . '">' . htmlsc($_btn_search) . '</a>';
	$alt_msg = str_replace('$1', $plain_search_link, $_msg_use_alternative_link);
	$form =<<<EOD
<form action="$script" method="GET" class="_plugin_search2_form">
 <div>
  <input type="hidden" name="cmd" value="search2">
  <input type="search"  name="q" value="$h_search_text" size="30">
  <input type="submit" value="$_btn_search">
 </div>
$base_option
</form>
EOD;
	$second_form =<<<EOD
<div class="_plugin_search2_second_form" style="display:none;">
<div class="_plugin_search2_search_status"></div>
<div class="_plugin_search2_message"></div>
$form
</div>
EOD;


	return <<<EOD
<noscript>
 <p>$_msg_unsupported_webbrowser $alt_msg</p>
</noscript>
<p class="_plugin_search2_nosupport_message" style="display:none;">
  $_msg_unsupported_webbrowser $alt_msg
</p>
$form
<div class="_plugin_search2_search_status"></div>
<div class="_plugin_search2_message"></div>
$result_page_panel
<ul id="result-list">
</ul>
$second_form
EOD;
}
