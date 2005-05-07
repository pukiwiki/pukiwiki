<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: ruby.inc.php,v 1.6 2005/05/07 07:41:31 henoheno Exp $
//
// Ruby annotation plugin: Add a pronounciation into kanji-word or acronym(s)
// See also about ruby: http://www.w3.org/TR/ruby/
//
// NOTE:
//  Ruby tag works with MSIE only now,
//  but readable for other browsers like: 'words(pronunciation)'

define('PLUGIN_RUBY_USAGE', '&ruby(pronunciation){words};');

function plugin_ruby_inline()
{
	if (func_num_args() != 2) return PLUGIN_RUBY_USAGE;

	list($ruby, $body) = func_get_args();

	// strip_htmltag() is just for avoiding AutoLink insertion
	$body = strip_htmltag($body);

	if ($ruby == '' || $body == '') return PLUGIN_RUBY_USAGE;

	return '<ruby><rb>' . $body . '</rb>' . '<rp>(</rp>' .
		'<rt>' .  htmlspecialchars($ruby) . '</rt>' . '<rp>)</rp>' .
		'</ruby>';
}
?>
