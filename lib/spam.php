<?php
// $Id: spam.php,v 1.1 2006/11/12 14:13:03 henoheno Exp $
// Copyright (C) 2006 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version

// Functions for Concept-work of spam-uri metrics

// Return an array of URIs in the $string
// [OK] http://nasty.example.org#nasty_string
// [OK] http://nasty.example.org/foo/xxx#nasty_string/bar
// [OK] ftp://dfshodfs:80/dfsdfs
function uri_pickup($string = '', $normalize = TRUE)
{
	// Not available for: user@password, IDN, Fragment(=ignored)
	$array = array();
	preg_match_all(
		// Refer RFC3986
		'#(\b[a-z][a-z0-9.+-]{1,8})://' .	// 1: Scheme
		'(' .
			// 2: Host
			'\[[0-9a-f:.]+\]' . '|' .				// IPv6([colon-hex and dot]): RFC2732
			'(?:[0-9]{1-3}\.){3}[0-9]{1-3}' . '|' .	// IPv4(dot-decimal): 001.22.3.44
			'[^\s<>"\'\[\]:/\#?]+' . 				// FQDN: foo.example.org
		')' .
		'(?::([a-z0-9]{2,}))?' .			// 3: Port
		'((?:/+[^\s<>"\'\[\]/\#]+)*/+)?' .	// 4: Directory path or path-info
		'([^\s<>"\'\[\]\#]+)?' .			// 5: File and query string
											// #: Fragment(ignored)
		'#i',
		 $string, $array, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
	//var_dump(recursive_map('htmlspecialchars', $array));

	// Shrink $array
	$parts = array(1 => 'scheme', 2 => 'host', 3 => 'port',
		4 => 'path', 5 => 'file');
	$default = array('');
	foreach(array_keys($array) as $uri) {
		unset($array[$uri][0]); // Matched string itself
		array_rename_keys($array[$uri], $parts, TRUE, $default);
		$offset = $array[$uri]['scheme'][1]; // Scheme's offset

		// Remove offsets for each part
		if ($normalize) {
			foreach(array_keys($array[$uri]) as $part) {
				$array[$uri][$part] = strtolower($array[$uri][$part][0]);
			}
			$array[$uri]['path'] = path_normalize($array[$uri]['path']);
		} else {
			foreach(array_keys($array[$uri]) as $part) {
				$array[$uri][$part] = & $array[$uri][$part][0];
			}
		}
		$array[$uri]['offset'] = $offset;
		$array[$uri]['area']   = 0;
	}

	return $array;
}

// Preprocess: rawurldecode() and adding space(s) to detect/count some URIs _if possible_
// NOTE: It's maybe danger to var_dump(result). [e.g. 'javascript:']
// [OK] http://victim.example.org/go?http%3A%2F%2Fnasty.example.org
// [OK] http://victim.example.org/http://nasty.example.org
function spam_uri_pickup_preprocess($string = '')
{
	if (is_string($string)) {
		return preg_replace(
			array(
				'#(?:https?|ftp):/#',
				'#\b[a-z][a-z0-9.+-]{1,8}://#i',
				'#[a-z][a-z0-9.+-]{1,8}://#i'
			),
			' $0',
			rawurldecode($string)
			);
	} else {
		return '';
	}
}

// TODO: Area selection (Check BBCode only, check anchor only, check ...)
// Main function of spam-uri pickup
function spam_uri_pickup($string = '')
{
	$string = spam_uri_pickup_preprocess($string);

	$array  = uri_pickup($string);

	// Area elevation for '(especially external)link' intension
	if (! empty($array)) {
		// Anchor tags by preg_match_all()
		// [OK] <a href="http://nasty.example.com">visit http://nasty.example.com/</a>
		// [OK] <a href=\'http://nasty.example.com/\' >discount foobar</a> 
		// [NG] <a href="http://ng.example.com">visit http://ng.example.com _not_ended_
		// [NG] <a href=  >Good site!</a> <a href= "#" >test</a>
		$areas = array();
		preg_match_all('#<a\b[^>]*href[^>]*>.*?</a\b[^>]*(>)#i',
			 $string, $areas, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		//var_dump(recursive_map('htmlspecialchars', $areas));
		foreach(array_keys($areas) as $area) {
			$areas[$area] =  array(
				$areas[$area][0][1], // [0][1] = Area start (<a href>)
				$areas[$area][1][1], // [1][1] = Area end   (</a>)
			);
		}
		area_measure($areas, $array);

		// phpBB's "BBCode" by preg_match_all()
		// [url]http://nasty.example.com/[/url]
		// [link]http://nasty.example.com/[/link]
		// [url=http://nasty.example.com]visit http://nasty.example.com/[/url]
		// [link http://nasty.example.com/]buy something[/link]
		// ?? [url=][/url]
		$areas = array();
		preg_match_all('#\[(url|link)\b[^\]]*\].*?\[/\1\b[^\]]*(\])#i',
			 $string, $areas, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		//var_dump(recursive_map('htmlspecialchars', $areas));
		foreach(array_keys($areas) as $area) {
			$areas[$area] = array(
				$areas[$area][0][1], // [0][1] = Area start ([url])
				$areas[$area][2][1], // [4][1] = Area end   ([/url])
			);
		}
		area_measure($areas, $array);

		// Various Wiki syntax
		// [text_or_uri>text_or_uri]
		// [text_or_uri:text_or_uri]
		// [text_or_uri|text_or_uri]
		// [text_or_uri->text_or_uri]
		// [text_or_uri text_or_uri] // MediaWiki
		// MediaWiki: [http://nasty.example.com/ visit http://nasty.example.com/]

		// Remove 'offset's for area_measure()
		//foreach(array_keys($array) as $key)
		//	unset($array[$key]['offset']);
	}

	return $array;
}

// $array['something'] => $array['wanted']
function array_rename_keys(& $array, $keys = array('from' => 'to'), $force = FALSE, $default = '')
{
	if (! is_array($array) || ! is_array($keys))
		return FALSE;

	// Nondestructive test
	if (! $force)
		foreach(array_keys($keys) as $from)
			if (! isset($array[$from]))
				return FALSE;

	foreach($keys as $from => $to) {
		if ($from === $to) continue;
		if (! $force || isset($array[$from])) {
			$array[$to] = & $array[$from];
			unset($array[$from]);
		} else  {
			$array[$to] = $default;
		}
	}

	return TRUE;
}

// If in doubt, it's a little doubtful
function area_measure($areas, & $array, $belief = -1, $a_key = 'area', $o_key = 'offset')
{
	if (! is_array($areas) || ! is_array($array)) return;

	$areas_keys = array_keys($areas);
	foreach(array_keys($array) as $u_index) {
		$offset = isset($array[$u_index][$o_key]) ?
			intval($array[$u_index][$o_key]) : 0;
		foreach($areas_keys as $a_index) {
			if (isset($array[$u_index][$a_key])) {
				$offset_s = intval($areas[$a_index][0]);
				$offset_e = intval($areas[$a_index][1]);
				// [Area => inside <= Area]
				if ($offset_s < $offset && $offset < $offset_e) {
					$array[$u_index][$a_key] += $belief;
				}
			}
		}
	}
}


// ---------------------
// Part Two

// Path normalization
// example.org => example.org/
// example.org#hoge -> example.org/#hoge
// example.org/path/a/b/./c////./d -> example.org/path/a/b/c/d
// example.org/path/../../a/../back
function path_normalize($path = '', $divider = '/', $addroot = TRUE)
{
	if (! is_string($path) || $path == '') {
		$path = $addroot ? $divider : '';
	} else {
		$path = trim($path);
		$last = ($path[strlen($path) - 1] == $divider) ? $divider : '';
		$array = explode($divider, $path);

		// Remove paddings
		foreach(array_keys($array) as $key) {
			if ($array[$key] == '' || $array[$key] == '.')
				 unset($array[$key]);
		}
		// Back-track
		$tmp = array();
		foreach($array as $value) {
			if ($value == '..') {
				array_pop($tmp);
			} else {
				array_push($tmp, $value);
			}
		}
		$array = & $tmp;

		$path = $addroot ? $divider : '';
		if (! empty($array)) $path .= implode($divider, $array) . $last;
	}

	return $path;
}

// Input: '/a/b'
// Output: array('' => array('a' => array('b' => NULL)))
function array_tree($string, $delimiter = '/', $reverse = FALSE)
{
	// Create a branch
	$tree = NULL;
	$tmps = explode($delimiter, $string);
	if (! $reverse) $tmps = array_reverse($tmps);
	foreach ($tmps as $tmp) {
		$tree = array($tmp => $tree);
	}
	return $tree;
}


// ---------------------
// Part One : Checker

// Simple/fast spam check
function is_uri_spam($target = '')
{
	$is_spam = FALSE;
	$urinum = 0;

	if (is_array($target)) {
		foreach($target as $str) {
			// Recurse
			list($is_spam, $_urinum) = is_uri_spam($str);
			$urinum += $_urinum;
			if ($is_spam) break;
		}
	} else {
		$pickups = spam_uri_pickup($target);
		$urinum += count($pickups);
		if (! empty($pickups)) {
			// Some users want to post some URLs, but ...
			if ($urinum > 8) {
				$is_spam = TRUE;	// Too many!
			} else {
				foreach($pickups as $pickup) {
					if ($pickup['area'] < 0) {
						$is_spam = TRUE;
						break;
					}
				}
			}
		}
	}

	return array($is_spam, $urinum);
}

// ---------------------

// Check User-Agent (not testing yet)
function is_invalid_useragent($ua_name = '' /*, $ua_vars = ''*/ )
{
	return $ua_name === '';
}

// ---------------------

// TODO: Multi-metrics (uri, host, user-agent, ...)
// TODO: Mail to administrator with more measurement data?
// Simple/fast spam filter ($target: 'a string' or an array())
function pkwk_spamfilter($action, $page, $target = array('title' => ''))
{
	$is_spam = FALSE;

	//$is_spam =  is_invalid_useragent('NOTYET');
	if ($is_spam) {
		$action .= ' (Invalid User-Agent)';
	} else {
		list($is_spam) = is_uri_spam($target);
	}

	if ($is_spam) {
		// Mail to administrator(s)
		global $notify, $notify_subject;
		if ($notify) {
			$footer['ACTION'] = $action;
			$footer['PAGE']   = '[blocked] ' . $page;
			$footer['URI']    = get_script_uri() . '?' . rawurlencode($page);
			$footer['USER_AGENT']  = TRUE;
			$footer['REMOTE_ADDR'] = TRUE;
			pkwk_mail_notify($notify_subject,  var_export($target, TRUE), $footer);
			unset($footer);
		}
	}

	if ($is_spam) spam_exit();
}

// ---------------------

// Common bahavior for blocking
// NOTE: Call this function from various blocking feature, to disgueise the reason 'why blocked'
function spam_exit()
{
	die("\n");
}

?>
