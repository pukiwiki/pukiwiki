<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: img.inc.php,v 1.17 2007/04/08 13:16:09 henoheno Exp $
// Copyright (C) 2002-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Inline-image plugin (Output inline-image tag from a URI)

define('PLUGIN_IMG_USAGE', '#img(): Usage: (URI-to-image[,right[,clear]])' . '<br />');
define('PLUGIN_IMG_CLEAR', '<div style="clear:both"></div>'); // Stop word-wrapping

function plugin_img_convert()
{
	if (PKWK_DISABLE_INLINE_IMAGE_FROM_URI)
		return '#img(): PKWK_DISABLE_INLINE_IMAGE_FROM_URI prohibits this' .
			'<br />' . "\n";

	$args = func_get_args();

	// Check the second argument first, for compatibility
	$align = isset($args[1]) ? strtolower($args[1]) : '';
	if ($align == '' || $align == 'l' || $align == 'left') {
		$align = 'left';	// Default
	} else if ($align == 'r' || $align == 'right') {
		$align = 'right';
	} else {
		$align = '';
	}

	// Stop word-wrapping only (Ugly but compatible)
	// Usage: #img(,clear)
	if (empty($align)) return PLUGIN_IMG_CLEAR;

	// The first
	$url = isset($args[0]) ? $args[0] : '';
	if (! is_url($url) || ! preg_match('/\.(jpe?g|gif|png)$/i', $url)) {
		return PLUGIN_IMG_USAGE;
	} else {
		$url = htmlspecialchars($url);
	}

	// The third
	$clear = isset($args[2]) ? strtolower($args[2]) : '';
	if ($clear == 'c' || $clear == 'clear') {
		$clear = PLUGIN_IMG_CLEAR;
	} else {
		$clear = '';
	}

	return
		'<div style="float:' . $align . ';padding:.5em 1.5em .5em 1.5em">'. "\n" .
		' <img src="' . $url . '" alt="" />' . "\n" .
		'</div>' . $clear;
}
?>
