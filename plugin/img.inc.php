<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// img.inc.php
// Copyright 2002-2018 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Inline-image plugin (Output inline-image tag from a URI)

define('PLUGIN_IMG_USAGE', '#img(): Usage: (URI-to-image[,right[,clear]])<br />' . "\n");
define('PLUGIN_IMG_CLEAR', '<div style="clear:both"></div>' . "\n"); // Stop word-wrapping
if (defined('PKWK_DISABLE_INLINE_IMAGE_FROM_URI') && PKWK_DISABLE_INLINE_IMAGE_FROM_URI) {
	define('PLUGIN_IMG_SHOW_IMAGE', 0); // 1: Show image, 0: Don't show image
} else {
	define('PLUGIN_IMG_SHOW_IMAGE', 1); // 1: Show image, 0: Don't show image
}

function plugin_img_get_style($args)
{
	$style = '';
	for ($i = 1; $i <= 3; $i++) {
		if (isset($args[$i])) {
			$arg = $args[$i];
			$m = null;
			if (preg_match('#^(\d+)x(\d+)$#', $arg, $m)) {
				$style = 'max-width:' . $m[1] . 'px;max-height:' . $m[2] . 'px;';
				break;
			} else if (preg_match('#^(\d+)px$#', $arg, $m)) {
				$style = 'max-width:' . $m[1] . 'px;max-height:' . $m[1] . 'px;';
				break;
			} else if (preg_match('#^(\d+)%$#', $arg, $m)) {
				// Note: zoom is not standard. Recommend using MAXpx or WIDTHxHEIGHT
				$style = 'zoom:' . $m[1] . '%;';
				break;
			}
		}
	}
	return $style;
}

/**
 * Determine link or not.
 */
function plugin_img_get_islink($args)
{
	for ($i = 1; $i <= 4; $i++) {
		if (isset($args[$i])) {
			if ($args[$i] === 'nolink') {
				return false;
			}
		}
	}
	return true;
}

function plugin_img_inline()
{
	$args = func_get_args();
	$url = isset($args[0]) ? $args[0] : '';
	if (!PLUGIN_IMG_SHOW_IMAGE) {
		if (is_url($url)) {
			$h_url = htmlsc($url);
			$title = '#img(): PLUGIN_IMG_SHOW_IMAGE prohibits this';
			return "<a href=\"$h_url\" title=\"$title\">$h_url</a>";
		}
		return '&amp;img(): PLUGIN_IMG_SHOW_IMAGE prohibits this' . "\n";
	}
	$size = isset($args[2]) ? strtolower($args[2]) : '';
	if (is_url($url)) {
		$h_url = htmlsc($url);
		$style = plugin_img_get_style($args);
		$a_begin = '';
		$a_end = '';
		if (plugin_img_get_islink($args)) {
			$a_begin = "<a href=\"$h_url\" class=\"image-link\">";
			$a_end = '</a>';
		}
		return <<<EOD
$a_begin<img class="plugin-img-inline" src="$h_url" style="$style" alt="" />$a_end
EOD;
	}
}

function plugin_img_convert()
{
	$args = func_get_args();
	$url = isset($args[0]) ? $args[0] : '';
	$h_url = htmlsc($url);
	if (!PLUGIN_IMG_SHOW_IMAGE) {
		if (is_url($url)) {
			$title = '#img(): PLUGIN_IMG_SHOW_IMAGE prohibits this';
			return "<div><a href=\"$h_url\" title=\"$title\">$h_url</a></div>";
		}
		return '#img(): PLUGIN_IMG_SHOW_IMAGE prohibits this' .
			'<br />' . "\n";
	}
	// Check the 2nd argument first, for compatibility
	$arg = isset($args[1]) ? strtoupper($args[1]) : '';
	if ($arg == '' || $arg == 'L' || $arg == 'LEFT') {
		$align = 'left';
	} else if ($arg == 'R' || $arg == 'RIGHT') {
		$align = 'right';
	} else if ($url === '' && $arg == 'CLEAR') {
		// Stop word-wrapping only (Ugly but compatible)
		// Short usage: #img(,clear)
		return PLUGIN_IMG_CLEAR;
	}
	$url = isset($args[0]) ? $args[0] : '';
	if (! is_url($url)) {
		return PLUGIN_IMG_USAGE;
	}
	$h_url = htmlsc($url);
	$arg = isset($args[2]) ? strtoupper($args[2]) : '';
	$clear = ($arg == 'C' || $arg == 'CLEAR') ? PLUGIN_IMG_CLEAR : '';
	$style = plugin_img_get_style($args);
	$a_begin = '';
	$a_end = '';
	if (plugin_img_get_islink($args)) {
		$a_begin = "<a href=\"$h_url\" class=\"image-link\">";
		$a_end = '</a>';
	}
	return <<<EOD
<div style="float:$align;padding:.5em 1.5em .5em 1.5em;">
 $a_begin<img class="plugin-img-block" src="$h_url" style="$style" alt="" />$a_end
</div>$clear
EOD;
}
