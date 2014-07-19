<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: random.inc.php,v 1.9 2011/01/25 15:01:01 henoheno Exp $
//

/*
 *プラグイン random
  配下のページをランダムに表示する

 *Usage
  #random(メッセージ)

 *パラメータ
 -メッセージ~
 リンクに表示する文字列

 */

function plugin_random_convert()
{
	global $script, $vars;

	$title = '[Random Link]'; // default
	if (func_num_args()) {
		$args  = func_get_args();
		$title = $args[0];
	}

	return "<p><a href=\"$script?plugin=random&amp;refer=" .
		rawurlencode($vars['page']) . '">' .
		htmlsc($title) . '</a></p>';
}

function plugin_random_action()
{
	global $vars;

	$pattern = strip_bracket($vars['refer']) . '/';
	$pages = array();
	foreach (get_existpages() as $_page) {
		if (strpos($_page, $pattern) === 0)
			$pages[$_page] = strip_bracket($_page);
	}

	srand((double)microtime() * 1000000);
	$page = array_rand($pages);

	if ($page != '') $vars['refer'] = $page;

	return array('body'=>'','msg'=>'');
}
?>
