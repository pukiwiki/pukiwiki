<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: ruby.inc.php,v 1.7 2007/11/11 11:02:40 henoheno Exp $
//
// Ruby annotation plugin: Attach a pronounciation into kanji-word(s) or acronym(s)
// See also about ruby: http://www.w3.org/TR/ruby/
//
// NOTE:
//  Ruby tag works with MSIE only now,
//  but readable for other browsers like: 'words(pronunciation)'

define('PLUGIN_RUBY_USAGE', '&amp;ruby(pronunciation){words};');

function plugin_ruby_inline()
{
	if (func_num_args() != 2) return PLUGIN_RUBY_USAGE;

	$args = func_get_args();
	$body = trim(strip_autolink(array_pop($args))); // htmlspecialchars() already
	$ruby = isset($args[0]) ? trim($args[0]) : '';

	if ($ruby == '' || $body == '') return PLUGIN_RUBY_USAGE;

	return 
		'<ruby>' .
			'<rb>' . $body . '</rb>' .
			'<rp>(</rp>' .
				'<rt>' .  htmlspecialchars($ruby) . '</rt>' .
			'<rp>)</rp>' .
		'</ruby>';
}
?>
