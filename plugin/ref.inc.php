<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: ref.inc.php,v 1.54 2011/02/06 13:50:46 henoheno Exp $
// Copyright (C)
//   2002-2006, 2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Image refernce plugin
// Include an attached image-file as an inline-image

// File icon image
if (! defined('FILE_ICON')) {
	define('FILE_ICON',
	'<img src="' . IMAGE_DIR . 'file.png" width="20" height="20"' .
	' alt="file" style="border-width:0px" />');
}

/////////////////////////////////////////////////
// Default settings

// Horizontal alignment
define('PLUGIN_REF_DEFAULT_ALIGN', 'left'); // 'left', 'center', 'right'

// Text wrapping
define('PLUGIN_REF_WRAP_TABLE', FALSE); // TRUE, FALSE

// NOT RECOMMENDED: getimagesize($uri) for proper width/height
define('PLUGIN_REF_URL_GET_IMAGE_SIZE', FALSE); // FALSE, TRUE

// DANGER, DO NOT USE THIS: Allow direct access to UPLOAD_DIR
define('PLUGIN_REF_DIRECT_ACCESS', FALSE); // FALSE or TRUE
// - This is NOT option for acceralation but old and compatible.
// - Apache: UPLOAD_DIR/.htaccess will prohibit this usage.
// - Browsers: This usage contains any proper mime-type, so
//   some ones will not show proper result. And may cause XSS.

/////////////////////////////////////////////////

// Image suffixes allowed
define('PLUGIN_REF_IMAGE', '/\.(gif|png|jpe?g)$/i');

// Usage (a part of)
define('PLUGIN_REF_USAGE', '([pagename/]attached-file-name[,parameters, ... ][,title])');

function plugin_ref_inline()
{
	// "$aryargs[] = & $body" at plugin.php
	if (func_num_args() == 1) {
		return htmlsc('&ref(): Usage:' . PLUGIN_REF_USAGE . ';');
	}

	$params = plugin_ref_body(func_get_args());
	if (isset($params['_error']) && $params['_error'] != '') {
		return htmlsc('&ref(): ' . $params['_error'] . ';');
	}

	return $params['_body'];
}

function plugin_ref_convert()
{
	if (! func_num_args()) {
		return '<p>' . htmlsc('#ref(): Usage:' . PLUGIN_REF_USAGE) . '</p>' . "\n";
	}

	$params = plugin_ref_body(func_get_args());
	if (isset($params['_error']) && $params['_error'] != '') {
		return '<p>' . htmlsc('#ref(): ' . $params['_error']) . '</p>' . "\n";
	}

	// Wrap with a table
	if ((PLUGIN_REF_WRAP_TABLE && ! $params['nowrap']) || $params['wrap']) {
		// margin:auto
		//	Mozilla 1.x  = x (wrap, and around are ignored)
		//	Opera 6      = o
		//	Netscape 6   = x (wrap, and around are ignored)
		//	IE 6         = x (wrap, and around are ignored)
		// margin:0px
		//	Mozilla 1.x  = x (aligning seems ignored with wrap)
		//	Opera 6      = x (aligning seems ignored with wrap)
		//	Netscape 6   = x (aligning seems ignored with wrap)
		//	IE6          = o
		$margin = ($params['around'] ? '0px' : 'auto');
		$margin_align = ($params['_align'] == 'center') ? '' :
			';margin-' . $params['_align'] . ':0px';
		$params['_body'] = <<<EOD
<table class="style_table" style="margin:$margin$margin_align">
 <tr>
  <td class="style_td">{$params['_body']}</td>
 </tr>
</table>
EOD;
	}

	if ($params['around']) {
		$style = ($params['_align'] == 'right') ? 'float:right' : 'float:left';
	} else {
		$style = 'text-align:' . $params['_align'];
	}
	return '<div class="img_margin" style="' . htmlsc($style) . '">' .
		$params['_body'] . '</div>' . "\n";
}

// Common function
function plugin_ref_body($args)
{
	global $script, $vars;
	global $WikiName, $BracketName;

	$page = isset($vars['page']) ? $vars['page'] : '';

	$params = array(
		// Align
		'left'   => FALSE,
		'center' => FALSE,
		'right'  => FALSE,
		'_align' => PLUGIN_REF_DEFAULT_ALIGN,

		// Wrap with table or not
		'wrap'   => FALSE,
		'nowrap' => FALSE,

		'around' => FALSE, // wrap around
		'noicon' => FALSE, // Suppress showing icon
		'nolink' => FALSE, // Suppress link to image itself
		'noimg'  => FALSE, // Suppress showing image

		'zoom'   => FALSE, // Image size spacified
		'_%'     => 0,     // percentage

		'_size'  => FALSE, // Image size specified
		'_w'     => 0,     // width
		'_h'     => 0,     // height

		'_args'  => array(),
		'_done'  => FALSE,
		'_error' => ''
	);

	// [Page_name/maybe-separated-with/slashes/]AttachedFileName.sfx or URI
	$name    = array_shift($args);
	$is_url  = is_url($name);

	$file    = ''; // Path to the attached file
	$is_file = FALSE;

	if(! $is_url) {
		if (! is_dir(UPLOAD_DIR)) {
			$params['_error'] = 'No UPLOAD_DIR';
			return $params;
		}

		$matches = array();
		if (preg_match('#^(.+)/([^/]+)$#', $name, $matches)) {
			// Page_name/maybe-separated-with/slashes and AttachedFileName.sfx
			if ($matches[1] == '.' || $matches[1] == '..') {
				$matches[1] .= '/'; // Restore relative paths
			}
			$name    = $matches[2]; // AttachedFileName.sfx
			$page    = get_fullname(strip_bracket($matches[1]), $page); // strip is a compat
			$file    = UPLOAD_DIR . encode($page) . '_' . encode($name);
			$is_file = is_file($file);

		} else if (isset($args[0]) && $args[0] != '' && ! isset($params[$args[0]])) {
			// Is the second argument a page-name or a path-name?
			$_page = array_shift($args);

			// Looks like WikiName, or double-bracket-inserted pagename? (compat)
			$is_bracket_bracket = preg_match('/^(' . $WikiName . '|\[\[' . $BracketName . '\]\])$/', $_page);

			$_page   = get_fullname(strip_bracket($_page), $page); // strip is a compat
			$file    = UPLOAD_DIR .  encode($_page) . '_' . encode($name);
			$is_file = is_file($file);

			if (! $is_bracket_bracket || ! $is_file) {
				// Promote new design
				if ($is_file && is_file(UPLOAD_DIR . encode($page) . '_' . encode($name))) {
					// Because of race condition NOW
					$params['_error'] =
						'The same file name "' . $name . '" at both page: "' .
						$page . '" and "' .  $_page .
						'". Try ref(pagename/filename) to specify one of them';
				} else {
					// Because of possibility of race condition, in the future
					$params['_error'] =
						'The style ref(filename,pagename) is ambiguous ' .
						'and become obsolete. ' .
						'Please try ref(pagename/filename)';
				}
				return $params;
			}
			$page = $_page; // Believe it (compat)

		} else {
			// Simple single argument
			$file    = UPLOAD_DIR . encode($page) . '_' . encode($name);
			$is_file = is_file($file);
		}

		if (! $is_file) {
			$params['_error'] = 'File not found: "' .
				$name . '" at page "' . $page . '"';
			return $params;
		}
	}

	// $params
	if (! empty($args)) {
		foreach ($args as $arg) {
			ref_check_arg($arg, $params);
		}
	}
	foreach (array('right', 'left', 'center') as $align) {
		if ($params[$align])  {
			$params['_align'] = $align;
			break;
		}
	}
	$seems_image = (! $params['noimg'] && preg_match(PLUGIN_REF_IMAGE, $name));

	$width = $height = 0;
	$title = $url = $url2 = '';
	$matches = array();

	if ($is_url) {
		$url   = $name;
		$url2  = $name;

		if (PKWK_DISABLE_INLINE_IMAGE_FROM_URI) {
			//$params['_error'] = 'PKWK_DISABLE_INLINE_IMAGE_FROM_URI prohibits this';
			//return $params;
			$s_url = htmlsc($url);
			$params['_body'] = '<a href="' . $s_url . '">' . $s_url . '</a>';
			return $params;
		}

		$title = preg_match('#([^/]+)$#', $url, $matches) ? $matches[1] : $url;

		if (PLUGIN_REF_URL_GET_IMAGE_SIZE && $seems_image && (bool)ini_get('allow_url_fopen')) {
			$size = @getimagesize($name);
			if (is_array($size)) {
				$width  = $size[0];
				$height = $size[1];
			}
		}

	} else {
		$title = $name;

		// Count downloads with attach plugin
		$url  = $script . '?plugin=attach' . '&refer=' . rawurlencode($page) .
			'&openfile=' . rawurlencode($name); // Show its filename at the last
		$url2 = '';

		if ($seems_image) {

			// URI for in-line image output
			$url2 = $url;
			if (PLUGIN_REF_DIRECT_ACCESS) {
				$url = $file; // Try direct-access, if possible
			} else {
				// With ref plugin (faster than attach)
				$url = $script . '?plugin=ref' . '&page=' . rawurlencode($page) .
					'&src=' . rawurlencode($name); // Show its filename at the last
			}

			$size = @getimagesize($file);
			if (is_array($size)) {
				$width  = $size[0];
				$height = $size[1];
			}
		}
	}

	if (! empty($params['_args'])) {
		$_title = array();
		foreach ($params['_args'] as $arg) {
			if (preg_match('/^([0-9]+)x([0-9]+)$/', $arg, $matches)) {
				$params['_size'] = TRUE;
				$params['_w'] = $matches[1];
				$params['_h'] = $matches[2];

			} else if (preg_match('/^([0-9.]+)%$/', $arg, $matches) && $matches[1] > 0) {
				$params['_%'] = $matches[1];

			} else {
				$_title[] = $arg;
			}
		}

		if (! empty($_title)) {
			$title = join(',', $_title);
		}
	}

	$s_url   = htmlsc($url);
	$s_title = htmlsc($title);
	$s_info  = '';
	if ($seems_image) {
		$s_title = make_line_rules($s_title);
		if ($params['_size']) {
			if ($width == 0 && $height == 0) {
				$width  = $params['_w'];
				$height = $params['_h'];
			} else if ($params['zoom']) {
				$_w = $params['_w'] ? $width  / $params['_w'] : 0;
				$_h = $params['_h'] ? $height / $params['_h'] : 0;
				$zoom = max($_w, $_h);
				if ($zoom) {
					$width  = intval($width  / $zoom);
					$height = intval($height / $zoom);
				}
			} else {
				$width  = $params['_w'] ? $params['_w'] : $width;
				$height = $params['_h'] ? $params['_h'] : $height;
			}
		}
		if ($params['_%']) {
			$width  = intval($width  * $params['_%'] / 100);
			$height = intval($height * $params['_%'] / 100);
		}
		if ($width && $height) {
			$s_info = 'width="'  . htmlsc($width) .
			        '" height="' . htmlsc($height) . '" ';
		}
		$body = '<img src="' . $s_url . '" ' .
			'alt="'   . $s_title . '" ' .
			'title="' . $s_title . '" ' .
			$s_info . '/>';
		if (! $params['nolink'] && $url2) {
			$params['_body'] =
				'<a href="' . htmlsc($url2) . '" title="' . $s_title . '">' .
				$body . '</a>';
		} else {
			$params['_body'] = $body;
		}
	} else {
		if (! $is_url) {
			$s_info = htmlsc(get_date('Y/m/d H:i:s', filemtime($file) - LOCALZONE) .
				' ' . sprintf('%01.1f', round(filesize($file) / 1024, 1)) . 'KB');
		}
		$icon = $params['noicon'] ? '' : FILE_ICON;
		$params['_body'] = '<a href="' . $s_url . '" title="' . $s_info . '">' .
			$icon . $s_title . '</a>';
	}

	return $params;
}

function ref_check_arg($val, & $params)
{
	if (preg_match('/^_/', $val)) {
		$params['_args'][] = $val;	
		return;
	}
	if ($val == '') {
		$params['_done'] = TRUE;
		return;
	}

	if (! $params['_done']) {
		$lval = strtolower($val);
		foreach (array_keys($params) as $key) {
			if (strpos($key, $lval) === 0) {
				$params[$key] = TRUE;
				return;
			}
		}
		$params['_done'] = TRUE;
	}

	$params['_args'][] = $val;
}

// Output an image (fast, non-logging <==> attach plugin)
function plugin_ref_action()
{
	global $vars;

	$usage = 'Usage: plugin=ref&amp;page=page_name&amp;src=attached_image_name';

	if (! isset($vars['page']) || ! isset($vars['src']))
		return array('msg' => 'Invalid argument', 'body' => $usage);

	$page     = $vars['page'];
	$filename = $vars['src'] ;

	$ref = UPLOAD_DIR . encode($page) . '_' . encode(preg_replace('#^.*/#', '', $filename));
	if(! file_exists($ref))
		return array('msg' => 'Attach file not found', 'body' => $usage);

	$got = @getimagesize($ref);
	if (! isset($got[2])) $got[2] = FALSE;
	switch ($got[2]) {
	case 1: $type = 'image/gif' ; break;
	case 2: $type = 'image/jpeg'; break;
	case 3: $type = 'image/png' ; break;
	case 4: $type = 'application/x-shockwave-flash'; break;
	default:
		return array('msg' => 'Seems not an image', 'body' => $usage);
	}

	// Care for Japanese-character-included file name
	if (LANG == 'ja') {
		switch(UA_NAME . '/' . UA_PROFILE){
		case 'Opera/default':
			// Care for using _auto-encode-detecting_ function
			$filename = mb_convert_encoding($filename, 'UTF-8', 'auto');
			break;
		case 'MSIE/default':
			$filename = mb_convert_encoding($filename, 'SJIS', 'auto');
			break;
		}
	}

	// Output
	$size = filesize($ref);
	pkwk_common_headers();
	header('Content-Disposition: inline; filename="' . htmlsc($filename) . '"');
	header('Content-Length: ' . htmlsc($size));
	header('Content-Type: '   . htmlsc($type));
	@readfile($ref);
	exit;
}
?>
