<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: include.inc.php,v 1.13 2004/08/09 14:47:55 henoheno Exp $
//

define('INCLUDE_MAX', 4); // 一度に(連鎖的に)インクルードできる最大数

// ページを(再帰)インクルードする
function plugin_include_convert()
{
	global $script, $vars, $get, $post, $menubar, $_msg_include_restrict;
	static $included = array();
	static $count = 0;

	if (func_num_args() == 0) return '#include(): No argument<br />';

	// Get an argument
	list($page) = func_get_args();
	$page = strip_bracket($page);
	$s_page = htmlspecialchars($page);

	// Loop yourself
	$self = isset($vars['page']) ? $vars['page'] : '';
	$included[$self] = TRUE;

	// I'm stuffed
	if (isset($included[$page])) return "#include(): Already included: $s_page<br />";
	if (! is_page($page))        return "#include(): No such page: $s_page<br />";
	if(++$count > INCLUDE_MAX)   return '#include(): Include-max reached(' . INCLUDE_MAX . ")<br />";

	// One page, only one time, at a time
	$included[$page] = TRUE;

	// Include a $page, that probably includes more pages
	$get['page'] = $post['page'] = $vars['page'] = $page;
	if (check_readable($page, false, false)) {
		$body = convert_html(get_source($page));
	} else {
		$body = str_replace('$1', $page, $_msg_include_restrict);
	}
	$get['page'] = $post['page'] = $vars['page'] = $self;

	// Add a title with edit link, before included document
	$r_page = rawurlencode($page);
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
