<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: img.inc.php,v 1.9 2004/08/18 14:25:52 henoheno Exp $
//

// 画像をインライン表示
function plugin_img_convert()
{
	static $usage = '#img(): USAGE: (URI-to-image,r|right|l|left[,clear])';

	$args = func_get_args();

	$url = isset($args[0]) ? $args[0] : '';
	if (! is_url($url) || ! preg_match('/\.(jpe?g|gif|png)$/i', $url))
		return $usage;

	$arg = isset($args[1]) ? strtoupper($args[1]) : '';
	if ($arg == 'R' || $arg == 'RIGHT') {
		$align = 'right';
	} else if ($arg == 'L' || $arg == 'LEFT') {
		$align = 'left';
	} else {
		return '<div style="clear:both"></div>'; // Ugly but compatible
	}

	$arg = isset($args[2]) ? strtoupper($args[2]) : '';
	if ($arg == 'C' || $arg == 'CLEAR') {
		$clear = '<div style="clear:both"></div>'; // No word-wrap
	} else {
		$clear = '';
	}

	return <<<EOD

<div style="float:$align;padding:.5em 1.5em .5em 1.5em">
 <img src="$url" alt="" />
</div>$clear
EOD;
}
?>
