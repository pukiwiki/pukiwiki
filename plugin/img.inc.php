<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: img.inc.php,v 1.12 2004/08/23 11:41:57 henoheno Exp $
//

// Stop word-wrapping
define('PLUGIN_IMG_CLEAR', "<div style=\"clear:both\"></div>\n");

// 画像をインライン表示
function plugin_img_convert()
{
	$usage = "#img(): Usage: (URI-to-image[,right[,clear]])<br />\n";
	$args = func_get_args();

	// Check the 2nd argument first, for compatibility
	$arg = isset($args[1]) ? strtoupper($args[1]) : '';
	if ($arg == '' || $arg == 'L' || $arg == 'LEFT') {
		$align = 'left';
	} else if ($arg == 'R' || $arg == 'RIGHT') {
		$align = 'right';
	} else {
		// Stop word-wrapping only (Ugly but compatible)
		// Short usage: #img(,clear)
		return PLUGIN_IMG_CLEAR;
	}

	$url = isset($args[0]) ? $args[0] : '';
	if (! is_url($url) || ! preg_match('/\.(jpe?g|gif|png)$/i', $url))
		return $usage;

	$arg = isset($args[2]) ? strtoupper($args[2]) : '';
	$clear = ($arg == 'C' || $arg == 'CLEAR') ? PLUGIN_IMG_CLEAR : '';

	return <<<EOD
<div style="float:$align;padding:.5em 1.5em .5em 1.5em">
 <img src="$url" alt="" />
</div>$clear
EOD;
}
?>
