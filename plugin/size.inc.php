<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: size.inc.php,v 1.11 2007/11/11 12:09:57 henoheno Exp $
//
// Text-size changing via CSS plugin
//
// See Also:
// CCS 2.1 Specification: 15.7 Font size: the 'font-size' property
// http://www.w3.org/TR/CSS21/fonts.html#propdef-font-size

define('PLUGIN_SIZE_PIXEL_DEFAULT', 12);

define('PLUGIN_SIZE_PIXEL_MAX',     60);
define('PLUGIN_SIZE_PIXEL_MIN',      8);

// NOTE: PIXEL_DEFAULT and PIXEL_MAX limits these
// (Consider &size(500%){&size(500%){TEXT};};)
define('PLUGIN_SIZE_PERCENT_MAX',  500);
define('PLUGIN_SIZE_PERCENT_MIN',   60);

define('PLUGIN_SIZE_REGEX', '/^(?:([0-9]+(?:px)?)|([0-9]+)%)$/i');

// ----
define('PLUGIN_SIZE_USAGE', '&amp;size(pixel or percentage){Text you want to change};');

function plugin_size_inline()
{
	if (func_num_args() != 2) return PLUGIN_SIZE_USAGE;

	$args = func_get_args();
	$body = trim(array_pop($args)); // htmlspecialchars() already
	$size = isset($args[0]) ? trim($args[0]) : '';

	// strip_autolink() seems not needed for size plugin
	//$body = strip_autolink($body);
	
	if ($size == '' || $body == '') {
		return PLUGIN_SIZE_USAGE;
	}

	$matches = array();
	if (preg_match(PLUGIN_SIZE_REGEX, $size, $matches)) {
		if (isset($matches[2])) {
			$percent = max(PLUGIN_SIZE_PERCENT_MIN, min(PLUGIN_SIZE_PERCENT_MAX, $matches[2]));
			$size = PLUGIN_SIZE_PIXEL_DEFAULT * $percent / 100;
 		} else {
 			$size = $matches[1];
 		}
		$size = max(PLUGIN_SIZE_PIXEL_MIN, min(PLUGIN_SIZE_PIXEL_MAX, $size)) . 'px';
	} else {
		return PLUGIN_SIZE_USAGE;
	}

	return '<span style="font-size:' . $size . ';' .
		'display:inline-block;line-height:130%;text-indent:0px">' .
			$body .
		'</span>';
}
?>
