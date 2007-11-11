<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: color.inc.php,v 1.24 2007/11/11 10:43:39 henoheno Exp $
//
// Text color plugin
//
// See Also:
// CCS 2.1 Specification: 4.3.6 Colors
// http://www.w3.org/TR/CSS21/syndata.html#value-def-color

// Allow CSS instead of <font> tag
// NOTE: <font> tag become invalid from XHTML 1.1
define('PLUGIN_COLOR_ALLOW_CSS', 1);

// ----
define('PLUGIN_COLOR_USAGE', '&amp;color(foreground[,background]){text};');
define('PLUGIN_COLOR_REGEX', '/^(?:#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z-]+)$/i');

function plugin_color_inline()
{
	global $pkwk_dtd;

	$args = func_get_args();
	$text    = strip_autolink(array_pop($args)); // htmlspecialchars(text) already
	$color   = isset($args[0]) ? trim($args[0]) : '';
	$bgcolor = isset($args[1]) ? trim($args[1]) : '';

	if (($color == '' && $bgcolor == '') || func_num_args() > 3) {
		return PLUGIN_COLOR_USAGE;
	}
	if ($text == '' ) {
		if ($color != '' && $bgcolor != '') {
			// The old style like: '&color(foreground,text);'
			$text    = htmlspecialchars($bgcolor);
			$bgcolor = '';
		} else {
			return PLUGIN_COLOR_USAGE;
		}
	}
	foreach(array($color, $bgcolor) as $_color){
		if ($_color != '' && ! preg_match(PLUGIN_COLOR_REGEX, $_color)) {
			return '&amp;color():Invalid color: ' . htmlspecialchars($_color) . ';';
		}
	}

	if (PLUGIN_COLOR_ALLOW_CSS || ! isset($pkwk_dtd) || $pkwk_dtd == PKWK_DTD_XHTML_1_1) {
		if ($color   != '') $color   = 'color:'            . $color;
		if ($bgcolor != '') $bgcolor = 'background-color:' . $bgcolor;
		$delimiter = ($color != '' && $bgcolor != '') ? ';' : '';
		return '<span style="' . $color . $delimiter . $bgcolor . '">' .
			$text . '</span>';
	} else {
		if ($bgcolor != '') {
			return '&amp;color(): bgcolor not allowed;';
		}
		return '<font color="' . $color . '">' . $text . '</font>';
	}
}
?>
