<?php
/*
 * PukiWiki BugTrackプラグイン
 *
 * CopyRight 2002 Y.MASUI GPL2
 * http://masui.net/pukiwiki/ masui@masui.net
 *
 * 変更履歴:
 *  2002.06.17: 作り始め
 *
 * $Id: bugtrack.inc.php,v 1.18 2004/12/02 11:34:25 henoheno Exp $
 */

function plugin_bugtrack_init()
{
	$messages = array(
		'_bugtrack_plugin_priority_list' => array('緊急','重要','普通','低'),
		'_bugtrack_plugin_state_list' => array('提案','着手','CVS待ち','完了','保留','却下'),
		'_bugtrack_plugin_state_sort' => array('着手','CVS待ち','保留','完了','提案','却下'),
		'_bugtrack_plugin_state_bgcolor' => array('#ccccff','#ffcc99','#ccddcc','#ccffcc','#ffccff','#cccccc','#ff3333'),

		'_bugtrack_plugin_title' => '$1 Bugtrack Plugin',
		'_bugtrack_plugin_base' => 'ページ',
		'_bugtrack_plugin_summary' => 'サマリ',
		'_bugtrack_plugin_priority' => '優先順位',
		'_bugtrack_plugin_state' => '状態',
		'_bugtrack_plugin_name' => '投稿者',
		'_bugtrack_plugin_date' => '投稿日',
		'_bugtrack_plugin_body' => 'メッセージ',
		'_bugtrack_plugin_category' => 'カテゴリー',
		'_bugtrack_plugin_pagename' => 'ページ名',
		'_bugtrack_plugin_pagename_comment' => '<small>空欄のままだと自動的にページ名が振られます。</small>',
		'_bugtrack_plugin_version_comment' => '<small>空欄でも構いません</small>',
		'_bugtrack_plugin_version' => 'バージョン',
		'_bugtrack_plugin_submit' => '追加'
		);
	set_plugin_messages($messages);
}

function plugin_bugtrack_action()
{
	global $post, $vars, $_bugtrack_plugin_title;

	if ($post['mode'] == 'submit') {
		$page = plugin_bugtrack_write($post['base'], $post['pagename'], $post['summary'], $post['name'], $post['priority'], $post['state'], $post['category'], $post['version'], $post['body']);
		pkwk_headers_sent();
		header('Location: ' . get_script_uri() . '?' . rawurlencode($page));
		die;
	}
	return FALSE;
/*
	else {
		$ret['msg'] = $_bugtrack_plugin_title;
		$ret["body"] = plugin_bugtrack_print_form($vars['category']);
	}

	return $ret;
*/
}

function plugin_bugtrack_print_form($base,$category)
{
	global $_bugtrack_plugin_priority_list,$_bugtrack_plugin_state_list;
	global $_bugtrack_plugin_priority, $_bugtrack_plugin_state, $_bugtrack_plugin_name;
	global $_bugtrack_plugin_date, $_bugtrack_plugin_category, $_bugtrack_plugin_body;
	global $_bugtrack_plugin_summary, $_bugtrack_plugin_submit, $_bugtrack_plugin_version;
	global $_bugtrack_plugin_pagename, $_bugtrack_plugin_pagename_comment;
	global $_bugtrack_plugin_version_comment;
	global $script;

	$select_priority = '';
	for ($i = 0; $i < count($_bugtrack_plugin_priority_list); ++$i) {
		if ($i < count($_bugtrack_plugin_priority_list) - 1) {
			$selected = '';
		}
		else {
			$selected = ' selected="selected"';
		}
		$select_priority .= "<option value=\"{$_bugtrack_plugin_priority_list[$i]}\"$selected>{$_bugtrack_plugin_priority_list[$i]}</option>\n";
	}

	$select_state = '';
	for ($i = 0; $i < count($_bugtrack_plugin_state_list); ++$i) {
		$select_state .= "<option value=\"{$_bugtrack_plugin_state_list[$i]}\">{$_bugtrack_plugin_state_list[$i]}</option>\n";
	}

	if (count($category) == 0) {
		$encoded_category = '<input name="category" type="text" />';
	}
	else {
		$encoded_category = '<select name="category">';
		foreach ($category as $_category) {
			$s_category = htmlspecialchars($_category);
			$encoded_category .= "<option value=\"$s_category\">$s_category</option>\n";
		}
		$encoded_category .= '</select>';
	}

	$s_base = htmlspecialchars($base);

	$body = <<<EOD
<form action="$script" method="post">
 <table border="0">
  <tr>
   <th>$_bugtrack_plugin_name</th>
   <td><input name="name" size="20" type="text" /></td>
  </tr>
  <tr>
   <th>$_bugtrack_plugin_category</th>
   <td>$encoded_category</td>
  </tr>
  <tr>
   <th>$_bugtrack_plugin_priority</th>
   <td><select name="priority">$select_priority</select></td>
  </tr>
  <tr>
   <th>$_bugtrack_plugin_state</th>
   <td><select name="state">$select_state</select></td>
  </tr>
  <tr>
   <th>$_bugtrack_plugin_pagename</th>
   <td><input name="pagename" size="20" type="text" />$_bugtrack_plugin_pagename_comment</td>
  </tr>
  <tr>
   <th>$_bugtrack_plugin_version</th>
   <td><input name="version" size="10" type="text" />$_bugtrack_plugin_version_comment</td>
  </tr>
  <tr>
   <th>$_bugtrack_plugin_summary</th>
   <td><input name="summary" size="60" type="text" /></td>
  </tr>
  <tr>
   <th>$_bugtrack_plugin_body</th>
   <td><textarea name="body" cols="60" rows="6"></textarea></td>
  </tr>
  <tr>
   <td colspan="2" align="center">
    <input type="submit" value="$_bugtrack_plugin_submit" />
    <input type="hidden" name="plugin" value="bugtrack" />
    <input type="hidden" name="mode" value="submit" />
    <input type="hidden" name="base" value="$s_base" />
   </td>
  </tr>
 </table>
</form>
EOD;

	return $body;
}

function plugin_bugtrack_template($base, $summary, $name, $priority, $state, $category, $version, $body)
{
	global $_bugtrack_plugin_priority, $_bugtrack_plugin_state, $_bugtrack_plugin_name;
	global $_bugtrack_plugin_date, $_bugtrack_plugin_category, $_bugtrack_plugin_base;
	global $_bugtrack_plugin_body, $_bugtrack_plugin_version;
	global $script, $WikiName;

	if (!preg_match("/^$WikiName$$/",$name)) {
		$name = "[[$name]]";
	}

	if (!preg_match("/^$WikiName$$/",$base)) {
		$base = "[[$base]]";
	}
	 return <<<EOD
*$summary

-$_bugtrack_plugin_base: $base
-$_bugtrack_plugin_name: $name
-$_bugtrack_plugin_priority: $priority
-$_bugtrack_plugin_state: $state
-$_bugtrack_plugin_category: $category
-$_bugtrack_plugin_date: now?
-$_bugtrack_plugin_version: $version

**$_bugtrack_plugin_body
$body
----

#comment
EOD;
}

function plugin_bugtrack_write($base, $pagename, $summary, $name, $priority, $state, $category, $version, $body)
{
	global $post;

	$base = strip_bracket($base);
	$pagename = strip_bracket($pagename);

	$postdata = plugin_bugtrack_template($base, $summary, $name, $priority, $state, $category, $version, $body);

	$i = 0;
	do {
		$i++;
		$page = "$base/$i";
	} while (is_page($page));

	if ($pagename == '') {
		page_write($page,$postdata);
	}
	else {
		$pagename = get_fullname($pagename,$base);
		// すでにページが存在するか、無効なページ名が指定された
		if (is_page($pagename) or !is_pagename($pagename)) {
			// ページ名をデフォルトに戻す
			$pagename = $page;
		}
		else {
			page_write($page,"move to [[$pagename]]");
		}
		page_write($pagename,$postdata);
	}

	return $page;
}

function plugin_bugtrack_convert()
{
	global $vars;

	$base = $vars['page'];
	$category = array();
	if (func_num_args() > 0) {
		$args = func_get_args();
		$category = $args;
		$_base = strip_bracket(array_shift($category));
		$_base = get_fullname($_base,$base);
		if (is_pagename($_base))
		{
			$base = $_base;
		}
	}

	return plugin_bugtrack_print_form($base,$category);
}


function plugin_bugtrack_pageinfo($page,$no = NULL)
{
	global $WikiName, $InterWikiName, $BracketName;

	if ($no === NULL) {
		$no = preg_match('/\/([0-9]+)$/',$page,$matches) ? $matches[1] : 0;
	}

	$source = get_source($page);
	if (preg_match("/move\s*to\s*($WikiName|$InterWikiName|\[\[$BracketName\]\])/",$source[0],$match)) {
		return plugin_bugtrack_pageinfo(strip_bracket($match[1]),$no);
	}

	$body = join("\n",$source);
	$summary = $name = $priority = $state = $category = 'test';
	$itemlist = array();
	foreach(array('summary','name','priority','state','category') as $item) {
		$itemname = '_bugtrack_plugin_'.$item;
		global $$itemname;
		$itemname = $$itemname;
		if (preg_match("/-\s*$itemname\s*:\s*(.*)\s*/",$body,$matches)) {
			if ($item == 'name') {
				$$item = htmlspecialchars(strip_bracket($matches[1]));
			}
			else {
				$$item = htmlspecialchars($matches[1]);
			}
		}
	}

	if (preg_match("/\*([^\n]+)/",$body,$matches)) {
		$summary = $matches[1];
		make_heading($summary);
	}

	return array($page, $no, $summary, $name, $priority, $state, $category);
}

function plugin_bugtrack_list_convert()
{
	global $script,$vars;
	global $_bugtrack_plugin_priority, $_bugtrack_plugin_state, $_bugtrack_plugin_name;
	global $_bugtrack_plugin_date, $_bugtrack_plugin_category, $_bugtrack_plugin_summary;
	global $_bugtrack_plugin_state_sort,$_bugtrack_plugin_state_list,$_bugtrack_plugin_state_bgcolor;

	$page = $vars['page'];
	if (func_num_args()) {
		list($_page) = func_get_args();
		$_page = get_fullname(strip_bracket($_page),$page);
		if (is_pagename($_page))
		{
			$page = $_page;
		}
	}

	$data = array();
	$pattern = "$page/";
	$pattern_len = strlen($pattern);
	foreach (get_existpages() as $page) {
		if (strpos($page,$pattern) === 0 and is_numeric(substr($page,$pattern_len))) {
			$line = plugin_bugtrack_pageinfo($page);
			array_push($data,$line);
		}
	}

	$table = array();
	for ($i = 0; $i <= count($_bugtrack_plugin_state_list) + 1; ++$i) {
		$table[$i] = array();
	}

	foreach ($data as $line) {
		list($page, $no, $summary, $name, $priority, $state, $category) = $line;
		$page_link = make_pagelink($page);
		$state_no = array_search($state,$_bugtrack_plugin_state_sort);
		if ($state_no === NULL or $state_no === FALSE) {
			$state_no = count($_bugtrack_plugin_state_list);
		}

		$bgcolor = $_bugtrack_plugin_state_bgcolor[$state_no];
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
		$table[$state_no][$no] = $row;
	}
	$table_html = <<<EOD
 <tr>
  <th>&nbsp;</th>
  <th>$_bugtrack_plugin_state</th>
  <th>$_bugtrack_plugin_priority</th>
  <th>$_bugtrack_plugin_category</th>
  <th>$_bugtrack_plugin_name</th>
  <th>$_bugtrack_plugin_summary</th>
 </tr>
EOD;
	for ($i = 0; $i <= count($_bugtrack_plugin_state_list); ++$i) {
		ksort($table[$i],SORT_NUMERIC);
		$table_html .= join("\n",$table[$i]);
	}

	return "<table border=\"1\">\n$table_html</table>";
}
?>
