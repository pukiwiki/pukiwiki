<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// img.inc.php
// Copyright 2002-2021 PukiWiki Development Team
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
			} else if (preg_match('#^(\d+)x$#', $arg, $m)) {
				$style = 'max-width:' . $m[1] . 'px;height:auto;';
				break;
			} if (preg_match('#^x(\d+)$#', $arg, $m)) {
				$style = 'width:auto;max-height:' . $m[1] . 'px;';
				break;
			} else if (preg_match('#^(\d+)w$#', $arg, $m)) {
				$style = 'max-width:' . $m[1] . 'px;height:auto;';
				break;
			} if (preg_match('#^(\d+)h$#', $arg, $m)) {
				$style = 'width:auto;max-height:' . $m[1] . 'px;';
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

/**
 * @param[in] $args func_get_args() of xxx_inline() and xxx_convert()
 * @return array(url, is_url, file_path, page, style, a_begin, a_end)
 */
function plugin_img_get_props($args)
{
	global $vars;
	$is_file = false;
	$is_url = false;
	$file_path = isset($args[0]) ? $args[0] : '';
	$page = isset($vars['page']) ? $vars['page'] : '';
	if (is_url($file_path)) {
		$url = $file_path;
		$is_url = true;
	} else if (isset($file_path)) {
		// $file_path s not an URL. It should be attached-file path
		$matches = null;
		if (preg_match('#^(.+)/([^/]+)$#', $file_path, $matches)) {
			// (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
			if ($matches[1] == '.' || $matches[1] == '..') {
				$matches[1] .= '/'; // Restore relative paths
			}
			$attach_name = $matches[2];
			$attach_page = get_fullname($matches[1], $page);
		} else {
			// Simple single argument
			$attach_name = $file_path;
			$attach_page = $page;
		}
		$file = UPLOAD_DIR . encode($attach_page) . '_' . encode($attach_name);
		$is_file = is_file($file);
		if ($is_file) {
			$url = get_base_uri() . '?plugin=attach' .
				'&refer=' . rawurlencode($attach_page) .
				'&openfile=' . rawurlencode($attach_name);
			$is_url = true;
		}
	}
	$h_url = htmlsc($url);
	$style = plugin_img_get_style($args);
	$a_begin = '';
	$a_end = '';
	if (plugin_img_get_islink($args)) {
		$a_begin = "<a href=\"$h_url\" class=\"image-link\">";
		$a_end = '</a>';
	}
	return (object)array('url' => $url, 'is_url' => $is_url,
		'file_path' => $file_path, 'is_file' => $is_file,
		'style' => $style,
		'a_begin' => $a_begin, 'a_end' => $a_end,);
}

function plugin_img_inline()
{
	$args = func_get_args();
	$p = plugin_img_get_props($args);
	if (!PLUGIN_IMG_SHOW_IMAGE) {
		if ($p->is_url) {
			$h_url = htmlsc($p->url);
			$title = '&amp;img(): PLUGIN_IMG_SHOW_IMAGE prohibits this';
			return "<a href=\"$h_url\" title=\"$title\">$h_url</a>";
		}
		return '&amp;img(): File not found: ' . htmlsc($p->file_path) . "\n";
	}
	if ($p->is_url) {
		$h_url = htmlsc($p->url);
		$style = $p->style;
		$a_begin = $p->a_begin;
		$a_end = $p->a_end;
		return <<<EOD
$a_begin<img class="plugin-img-inline" src="$h_url" style="$style" alt="" />$a_end
EOD;
	}
	return '&amp;img(): File not found: ' . htmlsc($p->file_path) . "\n";
}

function plugin_img_convert()
{
	$args = func_get_args();
	$p = plugin_img_get_props($args);
	// Check the 2nd argument first, for compatibility
	$arg = isset($args[1]) ? strtoupper($args[1]) : '';
	if ($a->file_path === '' && $arg == 'CLEAR') {
		// Stop word-wrapping only (Ugly but compatible)
		// Short usage: #img(,clear)
		return PLUGIN_IMG_CLEAR;
	}
	if ($arg === '' || $arg === 'L' || $arg === 'LEFT') {
		$align = 'left';
	} else if ($arg === 'R' || $arg === 'RIGHT') {
		$align = 'right';
	}
	$arg2 = isset($args[2]) ? strtoupper($args[2]) : '';
	$clear = ($arg2 === 'C' || $arg2 === 'CLEAR') ? PLUGIN_IMG_CLEAR : '';
	if (!PLUGIN_IMG_SHOW_IMAGE) {
		if ($p->is_url) {
			$h_url = htmlsc($p->url);
			$title = '#img(): PLUGIN_IMG_SHOW_IMAGE prohibits this';
			return "<div><a href=\"$h_url\" title=\"$title\">$h_url</a></div>";
		}
		return '#img(): File not found: ' . htmlsc($p->file_path) . "\n";
	}
	if ($p->is_url) {
		$h_url = htmlsc($p->url);
		$style = $p->style;
		$a_begin = $p->a_begin;
		$a_end = $p->a_end;
		return <<<EOD
<div style="float:$align;padding:.5em 1.5em .5em 1.5em;">
 $a_begin<img class="plugin-img-block" src="$h_url" style="$style" alt="" />$a_end
</div>$clear
EOD;
	}
	return '#img(): File not found: ' . htmlsc($p->file_path) . "\n";
}
