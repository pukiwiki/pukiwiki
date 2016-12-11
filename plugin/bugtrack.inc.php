<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// bugtrack.inc.php
// Copyright
//   2002-2016 PukiWiki Development Team
//   2002 Y.MASUI GPL2  http://masui.net/pukiwiki/ masui@masui.net
//
// BugTrack plugin

// Numbering format
define('PLUGIN_BUGTRACK_NUMBER_FORMAT', '%d'); // Like 'page/1'
//define('PLUGIN_BUGTRACK_NUMBER_FORMAT', '%03d'); // Like 'page/001'

function plugin_bugtrack_init()
{
	global $_plugin_bugtrack;
	static $init;

	if (isset($init)) return; // Already init
	if (isset($_plugin_bugtrack)) die('Global $_plugin_bugtrack had been init. Why?');
	$init = TRUE;

	$_plugin_bugtrack = array(
		'priority_list'  => array('緊急', '重要', '普通', '低'),
		'state_list'     => array('提案', '着手', '完了', '保留', '却下'),
		'state_sort'     => array('着手', '保留', '完了', '提案', '却下'),
		'state_bgcolor'  => array('#ccccff', '#ffcc99', '#ccffcc', '#ffccff', '#cccccc', '#ff3333'),
		'header_bgcolor' => '#ffffcc',
		'base'     => 'ページ',
		'summary'  => 'サマリ',
		'nosummary'=> 'ここにサマリを記入して下さい',
		'priority' => '優先順位',
		'state'    => '状態',
		'name'     => '投稿者',
		'noname'   => '名無しさん',
		'date'     => '投稿日',
		'body'     => 'メッセージ',
		'category' => 'カテゴリー',
		'pagename' => 'ページ名',
		'pagename_comment' => '空欄のままだと自動的にページ名が振られます。',
		'version_comment'  => '空欄でも構いません',
		'version'  => 'バージョン',
		'submit'   => '追加'
		);
}

// #bugtrack: Show bugtrack form
function plugin_bugtrack_convert()
{
	global $vars;

	if (PKWK_READONLY) return ''; // Show nothing

	$base = $vars['page'];
	$category = array();
	if (func_num_args()) {
		$category = func_get_args();
		$_base    = get_fullname(strip_bracket(array_shift($category)), $base);
		if (is_pagename($_base)) $base = $_base;
	}

	return plugin_bugtrack_print_form($base, $category);
}

function plugin_bugtrack_print_form($base, $category)
{
	global $_plugin_bugtrack;
	static $id = 0;

	++$id;

	$select_priority = "\n";
	$count = count($_plugin_bugtrack['priority_list']);
	$selected = '';
	for ($i = 0; $i < $count; ++$i) {
		if ($i == ($count - 1)) $selected = ' selected="selected"'; // The last one
		$priority_list = htmlsc($_plugin_bugtrack['priority_list'][$i]);
		$select_priority .= '    <option value="' . $priority_list . '"' .
			$selected . '>' . $priority_list . '</option>' . "\n";
	}

	$select_state = "\n";
	for ($i = 0; $i < count($_plugin_bugtrack['state_list']); ++$i) {
		$state_list = htmlsc($_plugin_bugtrack['state_list'][$i]);
		$select_state .= '    <option value="' . $state_list . '">' .
			$state_list . '</option>' . "\n";
	}

	if (empty($category)) {
		$encoded_category = '<input name="category" id="_p_bugtrack_category_' . $id .
			'" type="text" />';
	} else {
		$encoded_category = '<select name="category" id="_p_bugtrack_category_' . $id . '">';
		foreach ($category as $_category) {
			$s_category = htmlsc($_category);
			$encoded_category .= '<option value="' . $s_category . '">' .
				$s_category . '</option>' . "\n";
		}
		$encoded_category .= '</select>';
	}

	$script     = get_script_uri();
	$s_base     = htmlsc($base);
	$s_name     = htmlsc($_plugin_bugtrack['name']);
	$s_category = htmlsc($_plugin_bugtrack['category']);
	$s_priority = htmlsc($_plugin_bugtrack['priority']);
	$s_state    = htmlsc($_plugin_bugtrack['state']);
	$s_pname    = htmlsc($_plugin_bugtrack['pagename']);
	$s_pnamec   = htmlsc($_plugin_bugtrack['pagename_comment']);
	$s_version  = htmlsc($_plugin_bugtrack['version']);
	$s_versionc = htmlsc($_plugin_bugtrack['version_comment']);
	$s_summary  = htmlsc($_plugin_bugtrack['summary']);
	$s_body     = htmlsc($_plugin_bugtrack['body']);
	$s_submit   = htmlsc($_plugin_bugtrack['submit']);
	$body = <<<EOD
<form action="$script" method="post">
 <table border="0">
  <tr>
   <th><label for="_p_bugtrack_name_$id">$s_name</label></th>
   <td><input  id="_p_bugtrack_name_$id" name="name" size="20" type="text" /></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_category_$id">$s_category</label></th>
   <td>$encoded_category</td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_priority_$id">$s_priority</label></th>
   <td><select id="_p_bugtrack_priority_$id" name="priority">$select_priority   </select></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_state_$id">$s_state</label></th>
   <td><select id="_p_bugtrack_state_$id" name="state">$select_state   </select></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_pagename_$id">$s_pname</label></th>
   <td><input  id="_p_bugtrack_pagename_$id" name="pagename" size="20" type="text" />
    <small>$s_pnamec</small></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_version_$id">$s_version</label></th>
   <td><input  id="_p_bugtrack_version_$id" name="version" size="10" type="text" />
    <small>$s_versionc</small></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_summary_$id">$s_summary</label></th>
   <td><input  id="_p_bugtrack_summary_$id" name="summary" size="60" type="text" /></td>
  </tr>
  <tr>
   <th><label   for="_p_bugtrack_body_$id">$s_body</label></th>
   <td><textarea id="_p_bugtrack_body_$id" name="body" cols="60" rows="6"></textarea></td>
  </tr>
  <tr>
   <td colspan="2" align="center">
    <input type="submit" value="$s_submit" />
    <input type="hidden" name="plugin" value="bugtrack" />
    <input type="hidden" name="mode"   value="submit" />
    <input type="hidden" name="base"   value="$s_base" />
   </td>
  </tr>
 </table>
</form>
EOD;

	return $body;
}

// Add new issue
function plugin_bugtrack_action()
{
	global $post;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if ($post['mode'] != 'submit') return FALSE;

	$page = plugin_bugtrack_write($post['base'], $post['pagename'], $post['summary'],
		$post['name'], $post['priority'], $post['state'], $post['category'],
		$post['version'], $post['body']);

	pkwk_headers_sent();
	header('Location: ' . get_script_uri() . '?' . pagename_urlencode($page));
	exit;
}

function plugin_bugtrack_write($base, $pagename, $summary, $name, $priority, $state, $category, $version, $body)
{
	global $post;

	$base     = strip_bracket($base);
	$pagename = strip_bracket($pagename);

	$postdata = plugin_bugtrack_template($base, $summary, $name, $priority,
		$state, $category, $version, $body);

	$page_list = plugin_bugtrack_get_page_list($base, false);
	usort($page_list, '_plugin_bugtrack_list_paganame_compare');
	if (count($page_list) == 0) {
		$id = 1;
	} else {
		$latest_page = $page_list[count($page_list) - 1]['name'];
		$id = intval(substr($latest_page, strlen($base) + 1)) + 1;
	}
	$page = $base . '/' . sprintf(PLUGIN_BUGTRACK_NUMBER_FORMAT, $id);

	if ($pagename == '') {
		page_write($page, $postdata);
	} else {
		$pagename = get_fullname($pagename, $base);
		if (is_page($pagename) || ! is_pagename($pagename)) {
			$pagename = $page; // Set default
		} else {
			page_write($page, 'move to [[' . $pagename . ']]');
		}
		page_write($pagename, $postdata);
	}

	return $page;
}

// Generate new page contents
function plugin_bugtrack_template($base, $summary, $name, $priority, $state, $category, $version, $body)
{
	global $_plugin_bugtrack, $WikiName;

	if (! preg_match("/^$WikiName$$/",$base)) $base = '[[' . $base . ']]';
	if ($name != '' && ! preg_match("/^$WikiName$$/",$name)) $name = '[[' . $name . ']]';

	if ($name    == '') $name    = $_plugin_bugtrack['noname'];
	if ($summary == '') $summary = $_plugin_bugtrack['nosummary'];

	 return <<<EOD
* $summary

- ${_plugin_bugtrack['base'    ]}: $base
- ${_plugin_bugtrack['name'    ]}: $name
- ${_plugin_bugtrack['priority']}: $priority
- ${_plugin_bugtrack['state'   ]}: $state
- ${_plugin_bugtrack['category']}: $category
- ${_plugin_bugtrack['date'    ]}: &now;
- ${_plugin_bugtrack['version' ]}: $version

** ${_plugin_bugtrack['body']}
$body
--------

#comment
EOD;
}

// ----------------------------------------
// BugTrack-List plugin

function _plugin_bugtrack_list_paganame_compare($a, $b)
{
	return strnatcmp($a['name'], $b['name']);
}


/**
 * Get page list for "$page/"
 */
function plugin_bugtrack_get_page_list($page, $needs_filetime) {
	$page_list = array();
	$pattern = $page . '/';
	$pattern_len = strlen($pattern);
	foreach (get_existpages() as $p) {
		if (strncmp($p, $pattern, $pattern_len) === 0 && pkwk_ctype_digit(substr($p, $pattern_len))) {
			if ($needs_filetime) {
				$page_list[] = array('name'=>$p,'filetime'=>get_filetime($p));
			} else {
				$page_list[] = array('name'=>$p);
			}
		}
	}
	return $page_list;
}

/**
 * #bugtrack_list plugin itself
 *
 * Cache specification
 * * Enable only for PHP5.4+ (Because of JSON_UNESCAPE_UNICODE)
 * * Use cached values for articles that have the unchaged filemtime
 * * Invalidate all cache data everyday
 */
function plugin_bugtrack_list_convert()
{
	global $script, $vars, $_plugin_bugtrack;
	$cache_format_version = 1;
	$cache_expire_time = 60 * 60 * 24;
	$cache_refresh_time_prev;
	static $cache_enabled;
	if (!isset($cache_enabled)) {
		$cache_enabled = defined('JSON_UNESCAPED_UNICODE'); // PHP 5.4+
	}
	$page = $vars['page'];
	if (func_num_args()) {
		list($_page) = func_get_args();
		$_page = get_fullname(strip_bracket($_page), $page);
		if (is_pagename($_page)) $page = $_page;
	}
	$data = array();
	$page_list = plugin_bugtrack_get_page_list($page, true);
	usort($page_list, '_plugin_bugtrack_list_paganame_compare');
	$count_list = count($_plugin_bugtrack['state_list']);
	$data_map = array();
	if ($cache_enabled) {
		// Cache management
		$data_updated = true;
		$cache_filepath = CACHE_DIR . encode($page) . '.bugtrack';
		$json_cached = pkwk_file_get_contents($cache_filepath);
		if ($json_cached) {
			$wrapdata = json_decode($json_cached);
			if (is_object($wrapdata)) {
				if (isset($wrapdata->version, $wrapdata->pages, $wrapdata->refreshed_at)) {
					$cache_time_prev = $wrapdata->refreshed_at;
				  if ($cache_format_version == $wrapdata->version
						&& time() < $cache_time_prev + $cache_expire_time) {
						$data = $wrapdata->pages;
						$cache_refresh_time_prev = $cache_time_prev;
					}
				}
			}
			if (is_array($data) && !empty($data)) {
				$all_ok = true;
				foreach ($page_list as $i=>$page_info) {
					list($page_name, $no, $summary, $name, $priority, $state, $category, $filetime) = $data[$i];
					if ($filetime !== $page_info['filetime'] || $page_name !== $page_info['name']) {
						$all_ok = false;
						break;
					}
				}
				if (!$all_ok) {
					// Clear cache
					foreach ($data as $d) {
						$page_name = $d[0];
						$data_map[$page_name] = $d;
					}
					$data = array();
				}
			}
		}
	}

	if (empty($data) || !empty($data_map)) {
		// No cache
		foreach ($page_list as $page_info) {
			$page_name = $page_info['name'];
			$filled = false;
			if (isset($data_map[$page_name])) {
				$d = $data_map[$page_name];
				list($page_name, $no, $summary, $name, $priority, $state, $category, $filetime) = $d;
				if ($filetime == $page_info['filetime']) {
					$data[] = $d;
					$filled = true;
				}
			}
			if (!$filled) {
				$data[] = plugin_bugtrack_list_pageinfo($page_info['name'], null, true, $page_info['filetime']);
			}
		}
		foreach ($data as $i=>$line) {
			list($page_name, $no, $summary, $name, $priority, $state, $category, $filetime, $state_no_cached, $html_cached) = $line;
			if (isset($state_no_cached) && isset($html_cached)) {
				continue;
			}
			foreach (array('summary', 'name', 'priority', 'state', 'category') as $item)
				$$item = htmlsc($$item);
			$page_link = make_pagelink($page_name);

			$state_no = array_search($state, $_plugin_bugtrack['state_sort']);
			if ($state_no === NULL || $state_no === FALSE) $state_no = $count_list;
			$bgcolor = htmlsc($_plugin_bugtrack['state_bgcolor'][$state_no]);

			$row = <<<EOD
 <tr>
  <td style="background-color:$bgcolor">$page_link</td>
  <td style="background-color:$bgcolor">$state</td>
  <td style="background-color:$bgcolor">$priority</td>
  <td style="background-color:$bgcolor">$category</td>
  <td style="background-color:$bgcolor">$name</td>
  <td style="background-color:$bgcolor">$summary</td>
 </tr>
EOD;
			$rec = &$data[$i];
			$rec[] = $state_no; // color number
			$rec[] = $row; // HTML content
		}
		if ($cache_enabled) {
			// Save cache
			if (isset($cache_refresh_time_prev)) {
				$refreshed_at = $cache_refresh_time_prev;
			} else {
				$refreshed_at = time();
			}
			$json = array('refreshed_at'=>$refreshed_at, 'pages'=>$data, 'version'=>$cache_format_version);
			$cache_body = json_encode($json, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
			file_put_contents($cache_filepath, $cache_body, LOCK_EX);
		}
	}
	$table = array();
	for ($i = 0; $i <= $count_list + 1; ++$i) $table[$i] = array();
	foreach ($data as $line) {
		list($page_name, $no, $summary, $name, $priority, $state, $category, $filetime, $state_no, $html) = $line;
		$table[$state_no][$no] = $html;
	}
	$table_html = ' <tr>' . "\n";
	$bgcolor = htmlsc($_plugin_bugtrack['header_bgcolor']);
	foreach (array('pagename', 'state', 'priority', 'category', 'name', 'summary') as $item)
		$table_html .= '  <th style="background-color:' . $bgcolor . '">' .
			htmlsc($_plugin_bugtrack[$item]) . '</th>' . "\n";
	$table_html .= ' </tr>' . "\n";
	for ($i = 0; $i <= $count_list; ++$i) {
		ksort($table[$i], SORT_NUMERIC);
		$table_html .= join("\n", $table[$i]);
	}
	return '<table border="1" width="100%">' . "\n" .
		$table_html . "\n" .
		'</table>';
}

// Get one set of data from a page (or a page moved to $page)
function plugin_bugtrack_list_pageinfo($page, $no = NULL, $recurse = TRUE, $filetime = NULL)
{
	global $WikiName, $InterWikiName, $BracketName, $_plugin_bugtrack;

	if ($no === NULL)
		$no = preg_match('/\/([0-9]+)$/', $page, $matches) ? $matches[1] : 0;

	$source = get_source($page);

	// Check 'moved' page _just once_
	$regex  = "/move\s*to\s*($WikiName|$InterWikiName|\[\[$BracketName\]\])/";
	$match  = array();
	if ($recurse && preg_match($regex, $source[0], $match))
		return plugin_bugtrack_list_pageinfo(strip_bracket($match[1]), $no, FALSE, $filetime);

	$body = join("\n", $source);
	foreach(array('summary', 'name', 'priority', 'state', 'category') as $item) {
		$regex = '/-\s*' . preg_quote($_plugin_bugtrack[$item], '/') . '\s*:(.*)/';
		if (preg_match($regex, $body, $matches)) {
			if ($item == 'name') {
				$$item = strip_bracket(trim($matches[1]));
			} else {
				$$item = trim($matches[1]);
			}
		} else {
				$$item = ''; // Data not found
		}
	}

	if (preg_match("/\*([^\n]*)/", $body, $matches)) {
		$summary = $matches[1];
		make_heading($summary);
	}

	return array($page, $no, $summary, $name, $priority, $state, $category, $filetime);
}
