<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: color.inc.php,v 1.15 2004/11/21 13:37:53 henoheno Exp $
//
// Text color plugin

// Allow CSS instead of <font> tag
// NOTE: <font> tag become invalid from XHTML 1.1
define('PLUGIN_COLOR_ALLOW_CSS', TRUE); // TRUE, FALSE

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

	if (PLUGIN_COLOR_ALLOW_CSS === TRUE && $html_transitional === FALSE) {
		if ($bgcolor != '') $bgcolor = ';background-color:' . $bgcolor;
		return '<span style="color:' . $color . $bgcolor . '">' . $text . '</span>';
	} else {
		if ($bgcolor != '') return '&color(): bgcolor (with CSS) not allowd;';
		return '<font color="' . $color . '">' . $text . '</font>';
	}
}
?>
