<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: include.inc.php,v 1.15 2004/08/10 11:38:21 henoheno Exp $
//

define('INCLUDE_MAX', 4); // 一度にインクルードできるページの最大数

// ページを(可能ならば再帰的に)インクルードする
function plugin_include_convert()
{
	global $script, $vars, $get, $post, $menubar, $_msg_include_restrict;
	static $included = array();
	static $count = 1;

	if (func_num_args() == 0) return '#include(): No argument<br />';

	// Get an argument
	list($page) = func_get_args();
	$page = strip_bracket($page);
	$s_page = htmlspecialchars($page);
	$r_page = rawurlencode($page);
	$link = "<a href=\"$script?$r_page\">$s_page</a>"; // Read link

	// Loop yourself
	$root = isset($vars['page']) ? $vars['page'] : '';
	$included[$root] = TRUE;

	// I'm stuffed
	if (isset($included[$page])) {
		return "#include(): Included already: $link<br />";
	} if (! is_page($page)) {
		return "#include(): No such page: $s_page<br />";
	} if ($count > INCLUDE_MAX) {
		return "#include(): Limit exceeded: $link<br />";
	} else {
		++$count;
	}

	// One page, only one time, at a time
	$included[$page] = TRUE;

	// Include A page, that probably includes another pages
	$get['page'] = $post['page'] = $vars['page'] = $page;
	if (check_readable($page, false, false)) {
		$body = convert_html(get_source($page));
	} else {
		$body = str_replace('$1', $page, $_msg_include_restrict);
	}
	$get['page'] = $post['page'] = $vars['page'] = $root;

	// Add a title with edit link, before included document
	$link = "<a href=\"$script?cmd=edit&amp;page=$r_page\">$s_page</a>";

	if ($page == $menubar) {
		$body = "<span align=\"center\"><h5 class=\"side_label\">$link</h5></span>" .
			"<small>$body</small>";
	} else {
		$body = "<h1>$link</h1>\n" .
			"$body\n";
	}

	return $body;
}
?>
