<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: include.inc.php,v 1.10 2004/08/06 14:13:02 henoheno Exp $
//

// ページをインクルードする
function plugin_include_convert()
{
	global $script, $vars, $get, $post, $_msg_include_restrict;
	static $included = array();

	// Get an argument
	if (func_num_args() == 0) return;
	list($page) = func_get_args();
	$page = strip_bracket($page);

	// Loop yourself
	$self = isset($vars['page']) ? $vars['page'] : '';
	$included[$self] = TRUE;

	// I'm stuffed
	if (isset($included[$page]) || ! is_page($page)) return '';

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
	$s_page = htmlspecialchars($page);
	$r_page = rawurlencode($page);
	$link = "<a href=\"$script?cmd=edit&page=$r_page\">$s_page</a>";

	if ($page == 'MenuBar') {
		$body = "<span align=\"center\"><h5 class=\"side_label\">$link</h5></span>" .
			"<small>$body</small>";
	} else {
		$body = "<h1>$link</h1>\n" .
			"$body\n";
	}

	return $body;
}
?>
