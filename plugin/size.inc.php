<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: size.inc.php,v 1.7 2005/01/01 10:43:46 henoheno Exp $
//
// Text-size changing via CSS plugin

define('PLUGIN_SIZE_MAX', 36); // px
define('PLUGIN_SIZE_MIN', 8);  // px

// ----
define('PLUGIN_SIZE_USAGE', '&size(px){Text string};');

function plugin_size_inline()
{
	if (func_num_args() != 2) return PLUGIN_SIZE_USAGE;

	list($size, $body) = func_get_args();
	if ($size == '' || $body == '' || ! preg_match('/^\d+$/', $size))
		return PLUGIN_SIZE_USAGE;

	$size = max(PLUGIN_SIZE_MIN, min(PLUGIN_SIZE_MAX, $size));
	return '<span style="font-size:' . $size .
		'px;display:inline-block;line-height:130%;text-indent:0px">' .
		$body . '</span>';
}
?>
