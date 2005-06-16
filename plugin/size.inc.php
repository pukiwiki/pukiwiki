<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: size.inc.php,v 1.10 2005/06/16 15:04:08 henoheno Exp $
//
// Text-size changing via CSS plugin

define('PLUGIN_SIZE_MAX', 60); // px
define('PLUGIN_SIZE_MIN',  8); // px

// ----
define('PLUGIN_SIZE_USAGE', '&size(px){Text you want to change};');

function plugin_size_inline()
{
	if (func_num_args() != 2) return PLUGIN_SIZE_USAGE;

	list($size, $body) = func_get_args();

	// strip_autolink() is not needed for size plugin
	//$body = strip_htmltag($body);
	
	if ($size == '' || $body == '' || ! preg_match('/^\d+$/', $size))
		return PLUGIN_SIZE_USAGE;

	$size = max(PLUGIN_SIZE_MIN, min(PLUGIN_SIZE_MAX, $size));
	return '<span style="font-size:' . $size .
		'px;display:inline-block;line-height:130%;text-indent:0px">' .
		$body . '</span>';
}
?>
