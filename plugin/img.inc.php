<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: img.inc.php,v 1.10 2004/08/18 14:40:11 henoheno Exp $
//

// 画像をインライン表示
function plugin_img_convert()
{
	$usage = "#img(): USAGE: (URI-to-image[,right|left[,clear]])<br />\n";
	$clear = '<div style="clear:both"></div>'; // No word-wrap

	$args = func_get_args();

	$url = isset($args[0]) ? $args[0] : '';
	if (! is_url($url) || ! preg_match('/\.(jpe?g|gif|png)$/i', $url))
		return $usage;

	$arg = isset($args[1]) ? strtoupper($args[1]) : '';
	if ($arg == '' || $arg == 'L' || $arg == 'LEFT') {
		$align = 'left';
	} else if ($arg == 'R' || $arg == 'RIGHT') {
		$align = 'right';
	} else {
		// Stopping word-wrap only (Ugly but compatible)
		return $clear;
	}

	// Before output
	$arg = isset($args[2]) ? strtoupper($args[2]) : '';
	if (! $arg == 'C' && $arg != 'CLEAR') $clear = '';

	return <<<EOD

<div style="float:$align;padding:.5em 1.5em .5em 1.5em">
 <img src="$url" alt="" />
</div>$clear
EOD;
}
?>
