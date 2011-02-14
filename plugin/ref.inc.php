<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: ref.inc.php,v 1.56 2011/02/14 15:45:07 henoheno Exp $
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
	// NOTE: Already "$aryargs[] = & $body" at plugin.php
	if (func_num_args() == 1) {
		return htmlsc('&ref(): Usage:' . PLUGIN_REF_USAGE . ';');
	}

	$params = plugin_ref_body(func_get_args());
	if (isset($params['_error'])) {
		return htmlsc('&ref(): ERROR: ' . $params['_error'] . ';');
	}
	if (! isset($params['_body'])) {
		return htmlsc('&ref(): ERROR: No _body;');
	}

	return $params['_body'];
}

function plugin_ref_convert()
{
	if (! func_num_args()) {
		return '<p>' . htmlsc('#ref(): Usage:' . PLUGIN_REF_USAGE) . '</p>' . "\n";
	}

	$params = plugin_ref_body(func_get_args());
	if (isset($params['_error'])) {
		return '<p>' . htmlsc('#ref(): ERROR: ' . $params['_error']) . '</p>' . "\n";
	}
	if (! isset($params['_body'])) {
		return '<p>' . htmlsc('#ref(): ERROR: No _body') . '</p>' . "\n";
	}

	// Wrap with a table
	if ((PLUGIN_REF_WRAP_TABLE && ! isset($params['nowrap'])) || isset($params['wrap'])) {
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
		$s_margin = isset($params['around']) ? '0px' : 'auto';
		if (! isset($params['_align']) || $params['_align'] == 'center') {
			$s_margin_align = '';
		} else {
			$s_margin_align = ';margin-' . htmlsc($params['_align']) . ':0px';
		}
		$params['_body'] = <<<EOD
<table class="style_table" style="margin:$s_margin$s_margin_align">
 <tr>
  <td class="style_td">{$params['_body']}</td>
 </tr>
</table>
EOD;
	}

	if (isset($params['around'])) {
		$style = ($params['_align'] == 'right') ? 'float:right' : 'float:left';
	} else {
		$style = 'text-align:' . $params['_align'];
	}
	return '<div class="img_margin" style="' . htmlsc($style) . '">' . "\n" .
		$params['_body'] . "\n" .
		'</div>' . "\n";
}

// Common function
function plugin_ref_body($args)
{
	global $script, $vars;
	global $WikiName, $BracketName;

	$page = isset($vars['page']) ? $vars['page'] : '';

	$params = array(
		// Options
		'left'   => FALSE, // Align
		'center' => FALSE, //      Align
		'right'  => FALSE, //           Align
		'wrap'   => FALSE, // Wrap the output with table ...
		'nowrap' => FALSE, //   or not
		'around' => FALSE, // Text wrap around or not
		'noicon' => FALSE, // Suppress showing icon
		'noimg'  => FALSE, // Suppress showing image
		'nolink' => FALSE, // Suppress link to image itself
		'zoom'   => FALSE, // Lock image width/height ratio as the original said

		// Flags and values
		'_align' => PLUGIN_REF_DEFAULT_ALIGN,
		'_size'  => FALSE, // Image size specified
		'_w'     => 0,     // Width
		'_h'     => 0,     // Height
		'_%'     => 0,     // Percentage
		//'_title' => '',  // Reserved
		//'_body   => '',  // Reserved
		//'_error' => ''   // Reserved
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
			// Is the second argument a page-name or a path-name? (compat)
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

			$page = $_page; // Suppose it

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

	ref_check_args($args, $params);

	$seems_image = (! isset($params['noimg']) && preg_match(PLUGIN_REF_IMAGE, $name));

	$width = $height = 0;
	$url   = $url2   = '';
	if ($is_url) {
		$url  = $name;
		$url2 = $name;
		if (PKWK_DISABLE_INLINE_IMAGE_FROM_URI) {
			//$params['_error'] = 'PKWK_DISABLE_INLINE_IMAGE_FROM_URI prohibits this';
			//return $params;
			$s_url = htmlsc($url);
			$params['_body'] = '<a href="' . $s_url . '">' . $s_url . '</a>';
			return $params;
		}
		$matches = array();
		$params['_title'] = preg_match('#([^/]+)$#', $url, $matches) ? $matches[1] : $url;

		if ($seems_image && PLUGIN_REF_URL_GET_IMAGE_SIZE && (bool)ini_get('allow_url_fopen')) {
			$size = @getimagesize($name);
			if (is_array($size)) {
				$width  = $size[0];
				$height = $size[1];
			}
		}
	} else {
		// Count downloads with attach plugin
		$url  = $script . '?plugin=attach' . '&refer=' . rawurlencode($page) .
			'&openfile=' . rawurlencode($name); // Show its filename at the last
		$url2 = '';
		$params['_title'] = $name;

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

	$s_url   = htmlsc($url);
	$s_title = isset($params['_title']) ? htmlsc($params['_title']) : '';
	$s_info  = '';
	if ($seems_image) {
		$s_title = make_line_rules($s_title);
		if (ref_check_size($width, $height, $params) &&
		    isset($params['_w']) && isset($params['_h'])) {
			$s_info = 'width="'  . htmlsc($params['_w']) .
			        '" height="' . htmlsc($params['_h']) . '" ';
		}
		$body = '<img src="' . $s_url   . '" ' .
			'alt="'      . $s_title . '" ' .
			'title="'    . $s_title . '" ' .
			$s_info . '/>';
		if (! isset($params['nolink']) && $url2) {
			$params['_body'] =
				'<a href="' . htmlsc($url2) . '" title="' . $s_title . '">' . "\n" .
				$body . "\n" . '</a>';
		} else {
			$params['_body'] = $body;
		}
	} else {
		if (! $is_url && $is_file) {
			$s_info = htmlsc(get_date('Y/m/d H:i:s', filemtime($file) - LOCALZONE) .
				' ' . sprintf('%01.1f', round(filesize($file) / 1024, 1)) . 'KB');
		}
		$icon = isset($params['noicon']) ? '' : FILE_ICON;
		$params['_body'] = '<a href="' . $s_url . '" title="' . $s_info . '">' .
			$icon . $s_title . '</a>';
	}

	return $params;
}

function ref_check_args($args, & $params)
{
	if (! is_array($args) || ! is_array($params)) return;

	$_args   = array();
	$_title  = array();
	$matches = array();

	foreach ($args as $arg) {
		$hit = FALSE;
		if (! empty($arg) && ! preg_match('/^_/', $arg)) {
			$larg = strtolower($arg);
			foreach (array_keys($params) as $key) {
				if (strpos($key, $larg) === 0) {
					$hit          = TRUE;
					$params[$key] = TRUE;
					break;
				}
			}
		}
		if (! $hit) $_args[] = $arg;
	}

	foreach ($_args as $arg) {
		if (preg_match('/^([0-9]+)x([0-9]+)$/', $arg, $matches)) {
			$params['_size'] = TRUE;
			$params['_w']    = intval($matches[1]);
			$params['_h']    = intval($matches[2]);
		} else if (preg_match('/^([0-9.]+)%$/', $arg, $matches) && $matches[1] > 0) {
			$params['_%']    = intval($matches[1]);
		} else {
			$_title[] = $arg;
		}
	}
	unset($_args);
	$params['_title'] = join(',', $_title);
	unset($_title);
	foreach(array_keys($params) as $key) {
		if (! preg_match('/^_/', $key) && empty($params[$key])) {
			unset($params[$key]);
		}
	}

	foreach (array('right', 'left', 'center') as $align) {
		if (isset($params[$align])) {
			$params['_align'] = $align;
			unset($params[$align]);
			break;
		}
	}
}

function ref_check_size($width = 0, $height = 0, & $params)
{
	if (! is_array($params)) return FALSE;

	$width   = intval($width);
	$height  = intval($height);
	$_width  = isset($params['_w']) ? intval($params['_w']) : 0;
	$_height = isset($params['_h']) ? intval($params['_h']) : 0;

	if (isset($params['_size'])) {
		if ($width == 0 && $height == 0) {
			$width  = $_width;
			$height = $_height;
		} else if (isset($params['zoom'])) {
			$_w = $_width  ? $width  / $_width  : 0;
			$_h = $_height ? $height / $_height : 0;
			$zoom = max($_w, $_h);
			if ($zoom) {
				$width  = $width  / $zoom;
				$height = $height / $zoom;
			}
		} else {
			$width  = $_width  ? $_width  : $width;
			$height = $_height ? $_height : $height;
		}
	}

	if (isset($params['_%'])) {
		$width  = $width  * $params['_%'] / 100;
		$height = $height * $params['_%'] / 100;
	}

	$params['_w'] = intval($width);
	$params['_h'] = intval($height);

	return ($params['_w'] && $params['_h']);
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
