<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: color.inc.php,v 1.11 2004/11/21 10:09:39 henoheno Exp $
//
// Text color plugin

define('PLUGIN_COLOR_ALLOW_CSS', FALSE); // TRUE, FALSE

// ----
define('PLUGIN_COLOR_USAGE', '&color(foreground[,background]){text};');
define('PLUGIN_COLOR_REGEX', '/^(#[0-9a-f]{6}|[a-z-]+)$/i');
function plugin_color_inline()
{
	global $html_transitional;

	$args = func_get_args();
	$text = array_pop($args); // htmlspecialchars(text)

	list($color, $bgcolor) = array_pad($args, 2, '');
	if ($text == '' && $bgcolor != '') {
		// Maybe the old style: '&color(foreground,text);'
		$text    = htmlspecialchars($bgcolor);
		$bgcolor = '';
	}
	if ($color == '' || $text == '' || func_num_args() > 3)
		return PLUGIN_COLOR_USAGE;

	// Invalid color
	foreach(array($color, $bgcolor) as $col){
		if ($col != '' && ! preg_match(PLUGIN_COLOR_REGEX, $col))
			return '&color():Invalid color: ' . htmlspecialchars($col) . ';';
	}

	if ($html_transitional === TRUE && PLUGIN_COLOR_ALLOW_CSS === TRUE) {
		if ($bgcolor != '') $bgcolor = ';background-color:' . $bgcolor;
		return '<span style="color:' . $color . $bgcolor . '">' . $text . '</span>';
	} else {
		// Using <font> tag with:
		//   NG: XHTML 1.1
		//   OK: XHTML 1.0 Transitional
		if ($bgcolor != '') return '&color(): bgcolor (with CSS) not allowd;';
		return '<font color="' . $color . '">' . $text . '</font>';
	}
}
?>
