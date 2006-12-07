<?php
// $Id: spam.php,v 1.7 2006/12/07 14:32:50 henoheno Exp $
// Copyright (C) 2006 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version

// Functions for Concept-work of spam-uri metrics

if (! defined('SPAM_INI_FILE')) define('SPAM_INI_FILE', 'spam.ini.php');

// ---------------------
// URI pickup

// Return an array of URIs in the $string
// [OK] http://nasty.example.org#nasty_string
// [OK] http://nasty.example.org:80/foo/xxx#nasty_string/bar
// [OK] ftp://nasty.example.org:80/dfsdfs
// [OK] ftp://cnn.example.com&story=breaking_news@10.0.0.1/top_story.htm (from RFC3986)
function uri_pickup($string = '', $normalize = TRUE,
	$preserve_rawuri = FALSE, $preserve_chunk = TRUE)
{
	// Not available for: IDN(ignored)
	$array = array();
	preg_match_all(
		// scheme://userinfo@host:port/path/or/pathinfo/maybefile.and?query=string#fragment
		// Refer RFC3986 (Regex below is not strict)
		'#(\b[a-z][a-z0-9.+-]{1,8})://' .	// 1: Scheme
		'(?:' .
			'([^\s<>"\'\[\]/\#?@]*)' .		// 2: Userinfo (Username)
		'@)?' .
		'(' .
			// 3: Host
			'\[[0-9a-f:.]+\]' . '|' .				// IPv6([colon-hex and dot]): RFC2732
			'(?:[0-9]{1-3}\.){3}[0-9]{1-3}' . '|' .	// IPv4(dot-decimal): 001.22.3.44
			'[^\s<>"\'\[\]:/\#?]+' . 				// FQDN: foo.example.org
		')' .
		'(?::([0-9]*))?' .					// 4: Port
		'((?:/+[^\s<>"\'\[\]/\#]+)*/+)?' .	// 5: Directory path or path-info
		'([^\s<>"\'\[\]\#?]+)?' .			// 6: File?
		'(?:\?([^\s<>"\'\[\]\#]+))?' .		// 7: Query string
		'(?:\#([a-z0-9._~%!$&\'()*+,;=:@-]*))?' .	// 8: Fragment
		'#i',
		 $string, $array, PREG_SET_ORDER | PREG_OFFSET_CAPTURE
	);
	//var_dump(recursive_map('htmlspecialchars', $array));

	// Shrink $array
	static $parts = array(
		1 => 'scheme', 2 => 'userinfo', 3 => 'host', 4 => 'port',
		5 => 'path', 6 => 'file', 7 => 'query', 8 => 'fragment'
	);
	$default = array('');
	foreach(array_keys($array) as $uri) {
		$_uri = & $array[$uri];
		array_rename_keys($_uri, $parts, TRUE, $default);

		$offset = $_uri['scheme'][1]; // Scheme's offset
		foreach(array_keys($_uri) as $part) {
			// Remove offsets for each part
			$_uri[$part] = & $_uri[$part][0];
		}

		if ($normalize) {
			$_uri['scheme'] = scheme_normalize($_uri['scheme']);
			if ($_uri['scheme'] === '') {
				unset ($array[$uri]);
				continue;
			}
			$_uri['host']  = strtolower($_uri['host']);
			$_uri['port']  = port_normalize($_uri['port'], $_uri['scheme'], FALSE);
			$_uri['path']  = path_normalize($_uri['path']);
			if ($preserve_rawuri) $_uri['rawuri'] = & $_uri[0];

			// DEBUG
			//$_uri['uri'] = uri_array_implode($_uri);
		} else {
			$_uri['uri'] = & $_uri[0]; // Raw
		}
		unset($_uri[0]); // Matched string itself
		if (! $preserve_chunk) {
			unset(
				$_uri['scheme'],
				$_uri['userinfo'],
				$_uri['host'],
				$_uri['port'],
				$_uri['path'],
				$_uri['file'],
				$_uri['query'],
				$_uri['fragment']
			);
		}
		
		// Area offset for area_measure()
		$_uri['area']['offset'] = $offset;
	}

	return $array;
}

// Destructive normalize of URI array
// NOTE: Give me the uri_pickup() result with chunks
function uri_array_normalize(& $pickups, $preserve = TRUE)
{
	if (! is_array($pickups)) return $pickups;

	foreach (array_keys($pickups) as $key) {
		$_key = & $pickups[$key];
		$_key['path']     = isset($_key['path']) ? strtolower($_key['path']) : '';
		$_key['file']     = isset($_key['file']) ? file_normalize($_key['file']) : '';
		$_key['query']    = isset($_key['query']) ? query_normalize(strtolower($_key['query']), TRUE) : '';
		$_key['fragment'] = (isset($_key['fragment']) && $preserve) ?
			strtolower($_key['fragment']) : ''; // Just ignore
	}

	return $pickups;
}

// An URI array => An URI (See uri_pickup())
function uri_array_implode($uri = array())
{
	if (empty($uri) || ! is_array($uri)) return NULL;

	$tmp = array();
	if (isset($uri['scheme']) && $uri['scheme'] !== '') {
		$tmp[] = & $uri['scheme'];
		$tmp[] = '://';
	}
	if (isset($uri['userinfo']) && $uri['userinfo'] !== '') {
		$tmp[] = & $uri['userinfo'];
		$tmp[] = '@';
	}
	if (isset($uri['host']) && $uri['host'] !== '') {
		$tmp[] = & $uri['host'];
	}
	if (isset($uri['port']) && $uri['port'] !== '') {
		$tmp[] = ':';
		$tmp[] = & $uri['port'];
	}
	if (isset($uri['path']) && $uri['path'] !== '') {
		$tmp[] = & $uri['path'];
	}
	if (isset($uri['file']) && $uri['file'] !== '') {
		$tmp[] = & $uri['file'];
	}
	if (isset($uri['query']) && $uri['query'] !== '') {
		$tmp[] = '?';
		$tmp[] = & $uri['query'];
	}
	if (isset($uri['fragment']) && $uri['fragment'] !== '') {
		$tmp[] = '#';
		$tmp[] = & $uri['fragment'];
	}

	return implode('', $tmp);
}

// $array['something'] => $array['wanted']
function array_rename_keys(& $array, $keys = array('from' => 'to'), $force = FALSE, $default = '')
{
	if (! is_array($array) || ! is_array($keys)) return FALSE;

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

// ---------------------
// Spam-uri pickup

// Domain exposure callback (See spam_uri_pickup_preprocess())
// http://victim.example.org/?foo+site:nasty.example.com+bar
// => http://nasty.example.com/?refer=victim.example.org
// NOTE: 'refer=' is not so good for (at this time).
// Consider about using IP address of the victim, try to avoid that.
function _preg_replace_callback_domain_exposure($matches = array())
{
	$result = '';

	// Preserve the victim URI as a complicity or ...
	if (isset($matches[5])) {
		$result =
			$matches[1] . '://' .	// scheme
			$matches[2] . '/' .		// victim.example.org
			$matches[3];			// The rest of all (before victim)
	}

	// Flipped URI
	if (isset($matches[4])) {
		$result = 
			$matches[1] . '://' .	// scheme
			$matches[4] .			// nasty.example.com
			'/?refer=' . strtolower($matches[2]) .	// victim.example.org
			' ' . $result;
	}

	return $result;
}

// Preprocess: rawurldecode() and adding space(s) and something
// to detect/count some URIs _if possible_
// NOTE: It's maybe danger to var_dump(result). [e.g. 'javascript:']
// [OK] http://victim.example.org/go?http%3A%2F%2Fnasty.example.org
// [OK] http://victim.example.org/http://nasty.example.org
function spam_uri_pickup_preprocess($string = '')
{
	if (! is_string($string)) return '';

	$string = rawurldecode($string);

	// Domain exposure (See _preg_replace_callback_domain_exposure())
	$string = preg_replace_callback(
		array(
			// Something Google: http://www.google.com/supported_domains
			'#(http)://([a-z0-9.]+\.google\.[a-z]{2,3}(?:\.[a-z]{2})?)/' .
			'([a-z0-9?=&.%_+-]+)' .		// ?query=foo+
			'\bsite:([a-z0-9.%_-]+)' .	// site:nasty.example.com
			//'()' .	// Preserve or remove?
			'#i',
		),
		'_preg_replace_callback_domain_exposure',
		$string
	);

	// URI exposure (uriuri => uri uri)
	$string = preg_replace(
		array(
			'#(?<! )(?:https?|ftp):/#i',
		//	'#[a-z][a-z0-9.+-]{1,8}://#i',
		//	'#[a-z][a-z0-9.+-]{1,8}://#i'
		),
		' $0',
		$string
	);

	return $string;
}

// Main function of spam-uri pickup
function spam_uri_pickup($string = '', $area = array())
{
	if (! is_array($area) || empty($area)) {
		$area = array(
				'anchor' => TRUE,
				'bbcode' => TRUE,
			);
	}

	$string = spam_uri_pickup_preprocess($string);

	$array  = uri_pickup($string);

	// Area elevation for '(especially external)link' intension
	if (! empty($array)) {

		$area_shadow = array();
		foreach(array_keys($array) as $key){
			$area_shadow[$key] = & $array[$key]['area'];
			$area_shadow[$key]['anchor'] = 0;
			$area_shadow[$key]['bbcode'] = 0;
		}

		// Anchor tags by preg_match_all()
		// [OK] <a href="http://nasty.example.com">visit http://nasty.example.com/</a>
		// [OK] <a href=\'http://nasty.example.com/\' >discount foobar</a> 
		// [NG] <a href="http://ng.example.com">visit http://ng.example.com _not_ended_
		// [NG] <a href=  >Good site!</a> <a href= "#" >test</a>
		if (isset($area['anchor'])) {
			$areas = array();
			preg_match_all('#<a\b[^>]*href[^>]*>.*?</a\b[^>]*(>)#i',
				 $string, $areas, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
			//var_dump(recursive_map('htmlspecialchars', $areas));
			foreach(array_keys($areas) as $_area) {
				$areas[$_area] =  array(
					$areas[$_area][0][1], // Area start (<a href>)
					$areas[$_area][1][1], // Area end   (</a>)
				);
			}
			area_measure($areas, $area_shadow, 1, 'anchor');
		}

		// phpBB's "BBCode" by preg_match_all()
		// [url]http://nasty.example.com/[/url]
		// [link]http://nasty.example.com/[/link]
		// [url=http://nasty.example.com]visit http://nasty.example.com/[/url]
		// [link http://nasty.example.com/]buy something[/link]
		// ?? [url=][/url]
		if (isset($area['bbcode'])) {
			$areas = array();
			preg_match_all('#\[(url|link)\b[^\]]*\].*?\[/\1\b[^\]]*(\])#i',
				 $string, $areas, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
			//var_dump(recursive_map('htmlspecialchars', $areas));
			foreach(array_keys($areas) as $_area) {
				$areas[$_area] = array(
					$areas[$_area][0][1], // Area start ([url])
					$areas[$_area][2][1], // Area end   ([/url])
				);
			}
			area_measure($areas, $area_shadow, 1, 'bbcode');
		}

		// Various Wiki syntax
		// [text_or_uri>text_or_uri]
		// [text_or_uri:text_or_uri]
		// [text_or_uri|text_or_uri]
		// [text_or_uri->text_or_uri]
		// [text_or_uri text_or_uri] // MediaWiki
		// MediaWiki: [http://nasty.example.com/ visit http://nasty.example.com/]

		// Remove 'offset's for area_measure()
		foreach(array_keys($array) as $key)
			unset($array[$key]['area']['offset']);
	}

	return $array;
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
// Normalization

// Scheme normalization: Renaming the schemes
// snntp://example.org =>  nntps://example.org
// NOTE: Keep the static lists simple. See also port_normalize().
function scheme_normalize($scheme = '', $considerd_harmfull = TRUE)
{
	// Abbreviations considerable they don't have link intension
	static $abbrevs = array(
		'ttp'	=> 'http',
		'ttps'	=> 'https',
	);

	// Alias => normalized
	static $aliases = array(
		'pop'	=> 'pop3',
		'news'	=> 'nntp',
		'imap4'	=> 'imap',
		'snntp'	=> 'nntps',
		'snews'	=> 'nntps',
		'spop3'	=> 'pop3s',
		'pops'	=> 'pop3s',
	);

	$scheme = strtolower(trim($scheme));
	if (isset($abbrevs[$scheme])) {
		if ($considerd_harmfull) {
			$scheme = $abbrevs[$scheme];
		} else {
			$scheme = '';
		}
	}
	if (isset($aliases[$scheme])) $scheme = $aliases[$scheme];

	return $scheme;
}

// Port normalization: Suppress the (redundant) default port
// HTTP://example.org:80/ => http://example.org/
// HTTP://example.org:8080/ => http://example.org:8080/
// HTTPS://example.org:443/ => https://example.org/
function port_normalize($port, $scheme, $scheme_normalize = TRUE)
{
	// Schemes that users _maybe_ want to add protocol-handlers
	// to their web browsers. (and attackers _maybe_ want to use ...)
	// Reference: http://www.iana.org/assignments/port-numbers
	static $array = array(
		// scheme => default port
		'ftp'     =>    21,
		'ssh'     =>    22,
		'telnet'  =>    23,
		'smtp'    =>    25,
		'tftp'    =>    69,
		'gopher'  =>    70,
		'finger'  =>    79,
		'http'    =>    80,
		'pop3'    =>   110,
		'sftp'    =>   115,
		'nntp'    =>   119,
		'imap'    =>   143,
		'irc'     =>   194,
		'wais'    =>   210,
		'https'   =>   443,
		'nntps'   =>   563,
		'rsync'   =>   873,
		'ftps'    =>   990,
		'telnets' =>   992,
		'imaps'   =>   993,
		'ircs'    =>   994,
		'pop3s'   =>   995,
		'mysql'   =>  3306,
	);

	$port = trim($port);
	if ($port === '') return $port;

	if ($scheme_normalize) $scheme = scheme_normalize($scheme);
	if (isset($array[$scheme]) && $port == $array[$scheme])
		$port = ''; // Ignore the defaults

	return $port;
}

// Path normalization
// http://example.org => http://example.org/
// http://example.org#hoge => http://example.org/#hoge
// http://example.org/path/a/b/./c////./d => http://example.org/path/a/b/c/d
// http://example.org/path/../../a/../back => http://example.org/back
function path_normalize($path = '', $divider = '/', $addroot = TRUE)
{
	if (! is_string($path) || $path == '')
		return $addroot ? $divider : '';

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

	return $path;
}

// DirectoryIndex normalize (Destructive and rough)
function file_normalize($string = 'index.html.en')
{
	static $array = array(
		'index'			=> TRUE,	// Some system can omit the suffix
		'index.htm'		=> TRUE,
		'index.html'	=> TRUE,
		'index.shtml'	=> TRUE,
		'index.jsp'		=> TRUE,
		'index.php'		=> TRUE,
		'index.php3'	=> TRUE,
		'index.php4'	=> TRUE,
		//'index.pl'	=> TRUE,
		//'index.py'	=> TRUE,
		//'index.rb'	=> TRUE,
		'index.cgi'		=> TRUE,
		'default.htm'	=> TRUE,
		'default.html'	=> TRUE,
		'default.asp'	=> TRUE,
		'default.aspx'	=> TRUE,
	);

	// Content-negothiation filter:
	// Roughly removing ISO 639 -like
	// 2-letter suffixes (See RFC3066)
	$matches = array();
	if (preg_match('/(.*)\.[a-z][a-z](?:-[a-z][a-z])?$/i', $string, $matches)) {
		$_string = $matches[1];
	} else {
		$_string = & $string;
	}

	if (isset($array[strtolower($_string)])) {
		return '';
	} else {
		return $string;
	}
}

// Sort query-strings if possible (Destructive and rough)
// [OK] &&&&f=d&b&d&c&a=0dd  =>  a=0dd&b&c&d&f=d
// [OK] nothing==&eg=dummy&eg=padding&eg=foobar  =>  eg=foobar
function query_normalize($string = '', $equal = FALSE, $equal_cutempty = TRUE)
{
	$array = explode('&', $string);

	// Remove '&' paddings
	foreach(array_keys($array) as $key) {
		if ($array[$key] == '') {
			 unset($array[$key]);
		}
	}

	// Consider '='-sepalated input and paddings
	if ($equal) {
		$equals = $not_equals = array();
		foreach ($array as $part) {
			if (strpos($part, '=') === FALSE) {
				 $not_equals[] = $part;
			} else {
				list($key, $value) = explode('=', $part, 2);
				$value = ltrim($value, '=');
				if (! $equal_cutempty || $value != '') {
					$equals[$key] = $value;
				}
			}
		}

		$array = & $not_equals;
		foreach ($equals as $key => $value) {
			$array[] = $key . '=' . $value;
		}
		unset($equals);
	}

	natsort($array);
	return implode('&', $array);
}

// ---------------------
// Part One : Checker

function generate_glob_regex($string = '', $divider = '/')
{
	static $from = array(
			 1 => '*',
			11 => '?',
	//		22 => '[',	// Maybe cause regex compilation error (e.g. '[]')
	//		23 => ']',	//
		);
	static $mid = array(
			 1 => '_AST_',
			11 => '_QUE_',
	//		22 => '_RBR_',
	//		23 => '_LBR_',
		);
	static $to = array(
			 1 => '.*',
			11 => '.',
	//		22 => '[',
	//		23 => ']',
		);

	if (is_array($string)) {
		// Recurse
		return '(?:' .
			implode('|',	// OR
				array_map('generate_glob_regex',
					$string,
					array_pad(array(), count($string), $divider)
				)
			) .
		')';
	} else {
		$string = str_replace($from, $mid, $string); // Hide
		$string = preg_quote($string, $divider);
		$string = str_replace($mid, $to, $string);   // Unhide
		return $string;
	}
}

// TODO: Ignore list
// TODO: preg_grep() ?
// TODO: Multi list
function is_badhost($hosts = '', $asap = TRUE)
{
	static $regex;

	if (! isset($regex)) {
		$regex = array();
		$regex['badhost'] = array();

		// Sample
		if (TRUE) {
			$blocklist['badhost'] = array(
				//'*',			// Deny all uri
				//'10.20.*.*',	// 10.20.example.com also matches
				//'*.blogspot.com',	// Blog services subdomains
				//array('blogspot.com', '*.blogspot.com')
			);
			foreach ($blocklist['badhost'] as $part) {
				$regex['badhost'][] = '/^' . generate_glob_regex($part) . '$/i';
			}
		}

		// Load
		if (file_exists(SPAM_INI_FILE)) {
			$blocklist = array();
			require(SPAM_INI_FILE);
			foreach ($blocklist['badhost'] as $part) {
				$regex['badhost'][] = '/^' . generate_glob_regex($part) . '$/i';
			}
		}
	}
	//var_dump($regex);

	$result = 0;
	if (! is_array($hosts)) $hosts = array($hosts);

	foreach($hosts as $host) {
		if (! is_string($host)) $host = '';

		// badhost
		foreach ($regex['badhost'] as $_regex) {
			if (preg_match($_regex, $host)) {
				++$result;
				if ($asap) {
					return $result;
				} else {
					break;
				}
			}
		}
	}

	return $result;
}

// Default (enabled) methods and thresholds
function check_uri_spam_method($times = 1, $t_area = 0, $rule = TRUE)
{
	$times  = intval($times);
	$t_area = intval($t_area);

	// Thresholds
	$method = array(
		'quantity' => 8 * $times,	// Allow N URIs
		'non_uniq' => 3 * $times,	// Allow N duped (and normalized) URIs
	);

	// Areas
	$area = array(
		//'total' => $t_area,	// Allow N areas total, enabled below
		'anchor'  => $t_area,	// Inside <a href> HTML tag
		'bbcode'  => $t_area,	// Inside [url] or [link] BBCode
	);

	// Rules
	$rules = array(
		'uniqhost' => TRUE,	// Show uniq host
		'badhost'  => TRUE,	// Check badhost
	);

	// Remove unused
	foreach (array_keys($method) as $key) {
		if ($method[$key] < 0) unset($method[$key]);
	}
	foreach (array_keys($area) as $key) {
		if ($area[$key] < 0) unset($area[$key]);
	}
	$area  = empty($area) ? array() : array('area' => $area);
	$rules = $rule ? $rules : array();

	return $method + $area + $rules;
}

// TODO: Simplify $progress data structure
// TODO: Simplify. !empty(['is_spam']) just means $is_spam
// Simple/fast spam check
function check_uri_spam($target = '', $method = array(), $asap = TRUE)
{
	$is_spam  = FALSE;
	if (! is_array($method) || empty($method)) {
		$method = check_uri_spam_method();
	}

	$progress = array(
		'sum' =>  array(
			'quantity'    => 0,
			'uniqhost'    => 0,
			'non_uniq'    => 0,
			'badhost'     => 0,
			'area_total'  => 0,
			'area_anchor' => 0,
			'area_bbcode' => 0,
			),
		'is_spam' => array(),
		'method' => & $method,
	);


	if (is_array($target)) {
		// Recurse
		foreach($target as $str) {
			list($_is_spam, $_progress) = check_uri_spam($str, $method, $asap);
			$is_spam = $is_spam || $_is_spam;
			foreach (array_keys($_progress['sum']) as $key) {
				$progress['sum'][$key] += $_progress['sum'][$key];
			}
			foreach(array_keys($_progress['is_spam']) as $key) {
				$progress['is_spam'][$key] = TRUE;
			}
			if ($is_spam && $asap) break;
		}
	} else {
		$pickups = spam_uri_pickup($target);
		if (! empty($pickups)) {
			$progress['sum']['quantity'] += count($pickups);

			// URI quantity
			if ((! $is_spam || ! $asap) && isset($method['quantity']) &&
				$progress['sum']['quantity'] > $method['quantity']) {
				$is_spam = TRUE;
				$progress['is_spam']['quantity'] = TRUE;
			}
			//var_dump($method['quantity'], $is_spam);

			// Using invalid area
			if ((! $is_spam || ! $asap) && isset($method['area'])) {
				foreach($pickups as $pickup) {
					foreach ($pickup['area'] as $key => $value) {
						if ($key == 'offset') continue;
						$p_key = 'area_' . $key;
						$progress['sum']['area_total'] += $value;
						$progress['sum'][$p_key]       += $value;
						if (isset($method['area']['total']) &&
								$progress['sum']['area_total'] > $method['area']['total']) {
							$is_spam = TRUE;
							$progress['is_spam']['area_total'] = TRUE;
							if ($is_spam && $asap) break;
						}
						if(isset($method['area'][$key]) &&
								$progress['sum'][$p_key] > $method['area'][$key]) {
							$is_spam = TRUE;
							$progress['is_spam'][$p_key] = TRUE;
							if ($is_spam && $asap) break;
						}
					}
					if ($is_spam && $asap) break;
				}
			}
			//var_dump($method['area'], $is_spam);


			// URI uniqueness (and removing non-uniques)
			if ((! $is_spam || ! $asap) && isset($method['non_uniq'])) {

				// Destructive normalize of URIs
				uri_array_normalize($pickups);

				$uris = array();
				foreach (array_keys($pickups) as $key) {
					$uris[$key] = uri_array_implode($pickups[$key]);
 				}
				$count = count($uris);
				$uris  = array_unique($uris);
				$progress['sum']['non_uniq'] += $count - count($uris);
				if ($progress['sum']['non_uniq'] > $method['non_uniq']) {
					$is_spam = TRUE;
					$progress['is_spam']['non_uniq'] = TRUE;
				}
				if (! $asap || ! $is_spam) {
					foreach (array_diff(array_keys($pickups),
						array_keys($uris)) as $remove) {
						unset($pickups[$remove]);
					}
				}
				unset($uris);
			}
			//var_dump($method['non_uniq'], $is_spam);

			// Unique host
			$hosts = array();
			foreach ($pickups as $pickup) {
				$hosts[] = & $pickup['host'];
			}
			$hosts = array_unique($hosts);
			$progress['sum']['uniqhost'] += count($hosts);
			//var_dump($method['uniqhost'], $is_spam);

			// Bad host
			if ((! $is_spam || ! $asap) && isset($method['badhost'])) {
				$count = is_badhost($hosts, $asap);
				$progress['sum']['badhost'] += $count;
				if ($count !== 0) {
					$progress['is_spam']['badhost'] = TRUE;
					$is_spam = TRUE;
				}
			}
			//var_dump($method['badhost'], $is_spam);
		}
	}

	return array($is_spam, $progress);
}

// ---------------------
// Reporting

// TODO: Don't show unused $method!
// Summarize $progress (blocked only)
function summarize_spam_progress($progress = array(), $blockedonly = FALSE)
{
	$method = $progress['method'];
	if (isset($method['area'])) {
		foreach(array_keys($method['area']) as $key) {
			$method['area_' . $key] = TRUE;
		}
	}

	if ($blockedonly) {
		$tmp = array_keys($progress['is_spam']);
	} else {
		$tmp = array();
		foreach ($progress['sum'] as $key => $value) {
			if (isset($method[$key])) {
				$tmp[] = $key . '(' . $value . ')';
			}
		}
	}

	return implode(', ', $tmp);
}

// ---------------------
// Exit

// Common bahavior for blocking
// NOTE: Call this function from various blocking feature, to disgueise the reason 'why blocked'
function spam_exit()
{
	die("\n");
}


// ---------------------
// Simple filtering

// TODO: Record them
// Simple/fast spam filter ($target: 'a string' or an array())
function pkwk_spamfilter($action, $page, $target = array('title' => ''), $method = array(), $asap = FALSE)
{
	global $notify;

	list($is_spam, $progress) = check_uri_spam($target, $method, $asap);

	if ($is_spam) {
		// Mail to administrator(s)
		if ($notify) pkwk_spamnotify($action, $page, $target, $progress, $asap);
		// End
		spam_exit();
	}
}

// ---------------------
// PukiWiki original

// Mail to administrator(s)
function pkwk_spamnotify($action, $page, $target = array('title' => ''), $progress = array(), $asap = FALSE)
{
	global $notify_subject;

	$footer['ACTION']  = 'Blocked by: ' . summarize_spam_progress($progress, TRUE);
	if (! $asap) {
		$footer['METRICS'] = summarize_spam_progress($progress);
	}
	$footer['COMMENT'] = $action;
	$footer['PAGE']    = '[blocked] ' . $page;
	$footer['URI']     = get_script_uri() . '?' . rawurlencode($page);
	$footer['USER_AGENT']  = TRUE;
	$footer['REMOTE_ADDR'] = TRUE;
	pkwk_mail_notify($notify_subject,  var_export($target, TRUE), $footer);
}

?>
