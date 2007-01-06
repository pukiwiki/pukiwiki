<?php
// $Id: spam.php,v 1.13 2007/01/06 02:06:29 henoheno Exp $
// Copyright (C) 2006-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
// Functions for Concept-work of spam-uri metrics
// (PHP 4 >= 4.3.0): preg_match_all(PREG_OFFSET_CAPTURE): $method['uri_XXX'] related feature

if (! defined('SPAM_INI_FILE')) define('SPAM_INI_FILE', 'spam.ini.php');

// ---------------------
// Compat etc

// (PHP 4 >= 4.2.0): var_export(): mail-reporting and dump related
if (! function_exists('var_export')) {
	function var_export() {
		return 'var_export() is not found' . "\n";
	}
}

// (PHP 4 >= 4.2.0): preg_grep() enables invert option
function preg_grep_invert($pattern = '//', $input = array())
{
	static $invert;
	if (! isset($invert)) $invert = defined('PREG_GREP_INVERT');

	if ($invert) {
		return preg_grep($pattern, $input, PREG_GREP_INVERT);
	} else {
		$result = preg_grep($pattern, $input);
		if ($result) {
			return array_diff($input, preg_grep($pattern, $input));
		} else {
			return $input;
		}
	}
}

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
			'[a-z0-9.-]+' . 						// hostname(FQDN) : foo.example.org
		')' .
		'(?::([0-9]*))?' .					// 4: Port
		'((?:/+[^\s<>"\'\[\]/\#]+)*/+)?' .	// 5: Directory path or path-info
		'([^\s<>"\'\[\]\#?]+)?' .			// 6: File?
		'(?:\?([^\s<>"\'\[\]\#]+))?' .		// 7: Query string
		'(?:\#([a-z0-9._~%!$&\'()*+,;=:@-]*))?' .	// 8: Fragment
		'#i',
		 $string, $array, PREG_SET_ORDER | PREG_OFFSET_CAPTURE
	);

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
				unset($array[$uri]);
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
// Area pickup

// Pickup all of markup areas
function area_pickup($string = '', $method = array())
{
	$area = array();
	if (empty($method)) return $area;

	// Anchor tag pair by preg_match and preg_match_all()
	// [OK] <a href></a>
	// [OK] <a href=  >Good site!</a>
	// [OK] <a href= "#" >test</a>
	// [OK] <a href="http://nasty.example.com">visit http://nasty.example.com/</a>
	// [OK] <a href=\'http://nasty.example.com/\' >discount foobar</a> 
	// [NG] <a href="http://ng.example.com">visit http://ng.example.com _not_ended_
	$regex = '#<a\b[^>]*\bhref\b[^>]*>.*?</a\b[^>]*(>)#i';
	if (isset($method['area_anchor'])) {
		$areas = array();
		$count = isset($method['asap']) ?
			preg_match($regex, $string) :
			preg_match_all($regex, $string, $areas);
		if (! empty($count)) $area['area_anchor'] = $count;
	}
	if (isset($method['uri_anchor'])) {
		$areas = array();
		preg_match_all($regex, $string, $areas, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach(array_keys($areas) as $_area) {
			$areas[$_area] =  array(
				$areas[$_area][0][1], // Area start (<a href>)
				$areas[$_area][1][1], // Area end   (</a>)
			);
		}
		if (! empty($areas)) $area['uri_anchor'] = $areas;
	}

	// phpBB's "BBCode" pair by preg_match and preg_match_all()
	// [OK] [url][/url]
	// [OK] [url]http://nasty.example.com/[/url]
	// [OK] [link]http://nasty.example.com/[/link]
	// [OK] [url=http://nasty.example.com]visit http://nasty.example.com/[/url]
	// [OK] [link http://nasty.example.com/]buy something[/link]
	$regex = '#\[(url|link)\b[^\]]*\].*?\[/\1\b[^\]]*(\])#i';
	if (isset($method['area_bbcode'])) {
		$areas = array();
		$count = isset($method['asap']) ?
			preg_match($regex, $string) :
			preg_match_all($regex, $string, $areas, PREG_SET_ORDER);
		if (! empty($count)) $area['area_bbcode'] = $count;
	}
	if (isset($method['uri_bbcode'])) {
		$areas = array();
		preg_match_all($regex, $string, $areas, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach(array_keys($areas) as $_area) {
			$areas[$_area] = array(
				$areas[$_area][0][1], // Area start ([url])
				$areas[$_area][2][1], // Area end   ([/url])
			);
		}
		if (! empty($areas)) $area['uri_bbcode'] = $areas;
	}

	// Various Wiki syntax
	// [text_or_uri>text_or_uri]
	// [text_or_uri:text_or_uri]
	// [text_or_uri|text_or_uri]
	// [text_or_uri->text_or_uri]
	// [text_or_uri text_or_uri] // MediaWiki
	// MediaWiki: [http://nasty.example.com/ visit http://nasty.example.com/]

	return $area;
}

// If in doubt, it's a little doubtful
// if (Area => inside <= Area) $brief += -1
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
			'\bsite:([a-z0-9.%_-]+\.[a-z0-9.%_-]+)' .	// site:nasty.example.com
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
function spam_uri_pickup($string = '', $method = array())
{
	if (! is_array($method) || empty($method)) {
		$method = check_uri_spam_method();
	}

	$string = spam_uri_pickup_preprocess($string);

	$array  = uri_pickup($string);

	// Area elevation of URIs, for '(especially external)link' intension
	if (! empty($array)) {
		$_method = array();
		if (isset($method['uri_anchor'])) $_method['uri_anchor'] = & $method['uri_anchor'];
		if (isset($method['uri_bbcode'])) $_method['uri_bbcode'] = & $method['uri_bbcode'];
		$areas = area_pickup($string, $_method, TRUE);
		if (! empty($areas)) {
			$area_shadow = array();
			foreach (array_keys($array) as $key) {
				$area_shadow[$key] = & $array[$key]['area'];
				foreach (array_keys($_method) as $_key) {
					$area_shadow[$key][$_key] = 0;
				}
			}
			foreach (array_keys($_method) as $_key) {
				if (isset($areas[$_key])) {
					area_measure($areas[$_key], $area_shadow, 1, $_key);
				}
			}
		}
	}

	// Remove 'offset's for area_measure()
	foreach(array_keys($array) as $key)
		unset($array[$key]['area']['offset']);

	return $array;
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

function get_blocklist($list = '')
{
	static $regex;

	if (! isset($regex)) {
		$regex = array();

		// Sample
		if (FALSE) {
			$blocklist['badhost'] = array(
				//'*',			// Deny all uri
				//'10.20.*.*',	// 10.20.example.com also matches
				//'*.blogspot.com',	// Blog services subdomains
				//array('blogspot.com', '*.blogspot.com')
			);
			foreach ($blocklist['badhost'] as $part) {
				$_part = is_array($part) ? implode('/', $part) : $part;
				$regex['badhost'][$_part] = '/^' . generate_glob_regex($part) . '$/i';
			}
		}

		// Load
		if (file_exists(SPAM_INI_FILE)) {
			$blocklist = array();
			require(SPAM_INI_FILE);
			foreach(array('goodhost', 'badhost') as $key) {
				if (! isset($blocklist[$key])) continue;
				foreach ($blocklist[$key] as $part) {
					$_part = is_array($part) ? implode('/', $part) : $part;
					$regex[$key][$_part] = '/^' . generate_glob_regex($part) . '$/i';
				}
			}
		}
	}

	if ($list == '') {
		return $regex;
	} else if (isset($regex[$list])) {
		return $regex[$list];
	} else {	
		return array();
	}
}

function is_badhost($hosts = array(), $asap = TRUE, & $remains = array())
{
	$result = array();
	if (! is_array($hosts)) $hosts = array($hosts);
	foreach(array_keys($hosts) as $key) {
		if (! is_string($hosts[$key])) unset($hosts[$key]);
	}
	if (empty($hosts)) return $result;

	foreach (get_blocklist('goodhost') as $_regex) {
		$hosts = preg_grep_invert($_regex, $hosts);
	}
	if (empty($hosts)) return $result;

	$tmp = array();
	foreach (get_blocklist('badhost') as $part => $_regex) {
		$result[$part] = preg_grep($_regex, $hosts);
		if (empty($result[$part])) {
			unset($result[$part]);
		} else {
			$hosts = array_diff($hosts, $result[$part]);
			if ($asap) break;
		}
	}

	$remains = $hosts;

	return $result;
}

// Default (enabled) methods and thresholds (for content insertion)
function check_uri_spam_method($times = 1, $t_area = 0, $rule = TRUE)
{
	$times  = intval($times);
	$t_area = intval($t_area);

	$positive = array(
		// Thresholds
		'quantity'     =>  8 * $times,	// Allow N URIs
		'non_uniqhost' =>  3 * $times,	// Allow N duped (and normalized) Hosts
		//'non_uniquri'=>  3 * $times,	// Allow N duped (and normalized) URIs

		// Areas
		'area_anchor'  => $t_area,	// Using <a href> HTML tag
		'area_bbcode'  => $t_area,	// Using [url] or [link] BBCode
		//'uri_anchor' => $t_area,	// URI inside <a href> HTML tag
		//'uri_bbcode' => $t_area,	// URI inside [url] or [link] BBCode
	);
	if ($rule) {
		$bool = array(
			// Rules
			//'asap'   => TRUE,	// Quit or return As Soon As Possible
			'uniqhost' => TRUE,	// Show uniq host (at block notification mail)
			'badhost'  => TRUE,	// Check badhost
		);
	} else {
		$bool = array();
	}

	// Remove non-$positive values
	foreach (array_keys($positive) as $key) {
		if ($positive[$key] < 0) unset($positive[$key]);
	}

	return $positive + $bool;
}

// Simple/fast spam check
function check_uri_spam($target = '', $method = array())
{
	if (! is_array($method) || empty($method)) {
		$method = check_uri_spam_method();
	}
	$progress = array(
		'sum' => array(
			'quantity'    => 0,
			'uniqhost'    => 0,
			'non_uniqhost'=> 0,
			'non_uniquri' => 0,
			'badhost'     => 0,
			'area_anchor' => 0,
			'area_bbcode' => 0,
			'uri_anchor'  => 0,
			'uri_bbcode'  => 0,
		),
		'is_spam' => array(),
		'method'  => & $method,
		'remains' => array(),
	);
	$sum     = & $progress['sum'];
	$is_spam = & $progress['is_spam'];
	$remains = & $progress['remains'];
	$asap    = isset($method['asap']);

	// Return if ...
	if (is_array($target)) {
		foreach($target as $str) {
			// Recurse
			$_progress = check_uri_spam($str, $method);
			$_sum      = & $_progress['sum'];
			$_is_spam  = & $_progress['is_spam'];
			$_remains  = & $_progress['remains'];
			foreach (array_keys($_sum) as $key) {
				$sum[$key] += $_sum[$key];
			}
			foreach (array_keys($_is_spam) as $key) {
				if (is_array($_is_spam[$key])) {
					// Marge keys (badhost)
					foreach(array_keys($_is_spam[$key]) as $_key) {
						if (! isset($is_spam[$key][$_key])) {
							$is_spam[$key][$_key] =  $_is_spam[$key][$_key];
						} else {
							$is_spam[$key][$_key] += $_is_spam[$key][$_key];
						}
					}
				} else {
					$is_spam[$key] = TRUE;
				}
			}
			foreach ($_remains as $key=>$value) {
				foreach ($value as $_key=>$_value) {
					$remains[$key][$_key] = $_value;
				}
			}
			if ($asap && $is_spam) break;
		}
		return $progress;
	}

	// Area: There's HTML anchor tag
	if ((! $asap || ! $is_spam) && isset($method['area_anchor'])) {
		$key = 'area_anchor';
		$_asap = isset($method['asap']) ? array('asap' => TRUE) : array();
		$result = area_pickup($target, array($key => TRUE) + $_asap);
		if ($result) {
			$sum[$key] = $result[$key];
			if (isset($method[$key]) && $sum[$key] > $method[$key]) {
				$is_spam[$key] = TRUE;
			}
		}
	}

	// Area: There's 'BBCode' linking tag
	if ((! $asap || ! $is_spam) && isset($method['area_bbcode'])) {
		$key = 'area_bbcode';
		$_asap = isset($method['asap']) ? array('asap' => TRUE) : array();
		$result = area_pickup($target, array($key => TRUE) + $_asap);
		if ($result) {
			$sum[$key] = $result[$key];
			if (isset($method[$key]) && $sum[$key] > $method[$key]) {
				$is_spam[$key] = TRUE;
			}
		}
	}

	// Return if ...
	if ($asap && $is_spam) {
		return $progress;
	}
	// URI Init
	$pickups = spam_uri_pickup($target, $method);
	if (empty($pickups)) {
		return $progress;
	}

	// URI: Check quantity
	$sum['quantity'] += count($pickups);
		// URI quantity
	if ((! $asap || ! $is_spam) && isset($method['quantity']) &&
		$sum['quantity'] > $method['quantity']) {
		$is_spam['quantity'] = TRUE;
	}

	// URI: used inside HTML anchor tag pair
	if ((! $asap || ! $is_spam) && isset($method['uri_anchor'])) {
		$key = 'uri_anchor';
		foreach($pickups as $pickup) {
			if (isset($pickup['area'][$key])) {
				$sum[$key] += $pickup['area'][$key];
				if(isset($method[$key]) &&
					$sum[$key] > $method[$key]) {
					$is_spam[$key] = TRUE;
					if ($asap && $is_spam) break;
				}
				if ($asap && $is_spam) break;
			}
		}
	}

	// URI: used inside 'BBCode' pair
	if ((! $asap || ! $is_spam) && isset($method['uri_bbcode'])) {
		$key = 'uri_bbcode';
		foreach($pickups as $pickup) {
			if (isset($pickup['area'][$key])) {
				$sum[$key] += $pickup['area'][$key];
				if(isset($method[$key]) &&
					$sum[$key] > $method[$key]) {
					$is_spam[$key] = TRUE;
					if ($asap && $is_spam) break;
				}
				if ($asap && $is_spam) break;
			}
		}
	}

	// URI: Uniqueness (and removing non-uniques)
	if ((! $asap || ! $is_spam) && isset($method['non_uniquri'])) {

		// Destructive normalize of URIs
		uri_array_normalize($pickups);

		$uris = array();
		foreach (array_keys($pickups) as $key) {
			$uris[$key] = uri_array_implode($pickups[$key]);
		}
		$count = count($uris);
		$uris  = array_unique($uris);
		$sum['non_uniquri'] += $count - count($uris);
		if ($sum['non_uniquri'] > $method['non_uniquri']) {
			$is_spam['non_uniquri'] = TRUE;
		}
		if (! $asap || ! $is_spam) {
			foreach (array_diff(array_keys($pickups),
				array_keys($uris)) as $remove) {
				unset($pickups[$remove]);
			}
		}
		unset($uris);
	}

	// Return if ...
	if ($asap && $is_spam) {
		return $progress;
	}

	// Host: Uniqueness (uniq / non-uniq)
	$hosts = array();
	foreach ($pickups as $pickup) $hosts[] = & $pickup['host'];
	$hosts = array_unique($hosts);
	$sum['uniqhost'] += count($hosts);
	if ((! $asap || ! $is_spam) && isset($method['non_uniqhost'])) {
		$sum['non_uniqhost'] = $sum['quantity'] - $sum['uniqhost'];
		if ($sum['non_uniqhost'] > $method['non_uniqhost']) {
			$is_spam['non_uniqhost'] = TRUE;
		}
	}

	// Return if ...
	if ($asap && $is_spam) {
		return $progress;
	}

	// URI: Bad host
	if ((! $asap || ! $is_spam) && isset($method['badhost'])) {
		if ($asap) {
			$badhost = is_badhost($hosts, $asap);
		} else {
			$__remains = array();
			$badhost = is_badhost($hosts, $asap, $__remains);
			if ($__remains) {
				$progress['remains']['badhost'] = array();
				foreach ($__remains as $value) {
					$progress['remains']['badhost'][$value] = TRUE;
				}
				unset($__remains);
			}
		}
		if (! empty($badhost)) {
			$sum['badhost'] += array_count_leaves($badhost);
			foreach(array_keys($badhost) as $keys) {
				$is_spam['badhost'][$keys] =
					array_count_leaves($badhost[$keys]);
			}
			unset($badhost);
		}
	}

	return $progress;
}

// Count leaves
function array_count_leaves($array = array(), $count_empty_array = FALSE)
{
	if (! is_array($array) || (empty($array) && $count_empty_array))
		return 1;

	// Recurse
	$result = 0;
	foreach ($array as $part) {
		$result += array_count_leaves($part, $count_empty_array);
	}
	return $result;
}

// ---------------------
// Reporting

// TODO: Don't show unused $method!
// Summarize $progress (blocked only)
function summarize_spam_progress($progress = array(), $blockedonly = FALSE)
{
	if ($blockedonly) {
		$tmp = array_keys($progress['is_spam']);
	} else {
		$tmp = array();
		$method = & $progress['method'];
		if (isset($progress['sum'])) {
			foreach ($progress['sum'] as $key => $value) {
				if (isset($method[$key])) {
					$tmp[] = $key . '(' . $value . ')';
				}
			}
		}
	}

	return implode(', ', $tmp);
}

// ---------------------
// Exit

// Common bahavior for blocking
// NOTE: Call this function from various blocking feature, to disgueise the reason 'why blocked'
function spam_exit($mode = '', $data = array())
{
	switch ($mode) {
		case '':	echo("\n");	break;
		case 'dump':
			echo('<pre>' . "\n");
			echo htmlspecialchars(var_export($data, TRUE));
			echo('</pre>' . "\n");
			break;
	};

	// Force exit
	exit;
}


// ---------------------
// Simple filtering

// TODO: Record them
// Simple/fast spam filter ($target: 'a string' or an array())
function pkwk_spamfilter($action, $page, $target = array('title' => ''), $method = array(), $exitmode = '')
{
	$progress = check_uri_spam($target, $method);

	if (! empty($progress['is_spam'])) {
		// Mail to administrator(s)
		pkwk_spamnotify($action, $page, $target, $progress, $method);

		// Exit
		spam_exit($exitmode, $progress);
	}
}

// ---------------------
// PukiWiki original

// Mail to administrator(s)
function pkwk_spamnotify($action, $page, $target = array('title' => ''), $progress = array(), $method = array())
{
	global $notify, $notify_subject;

	if (! $notify) return;

	$asap = isset($method['asap']);

	$summary['ACTION']  = 'Blocked by: ' . summarize_spam_progress($progress, TRUE);
	if (! $asap) {
		$summary['METRICS'] = summarize_spam_progress($progress);
	}
	if (isset($progress['is_spam']['badhost'])) {
		$badhost = array();
		foreach($progress['is_spam']['badhost'] as $glob=>$number) {
			$badhost[] = $glob . '(' . $number . ')';
		}
		$summary['DETAIL_BADHOST'] = implode(', ', $badhost);
	}
	if (! $asap && $progress['remains']['badhost']) {
		$count = count($progress['remains']['badhost']);
		$summary['DETAIL_NEUTRAL_HOST'] = $count .
			' (' .
				preg_replace(
					'/[^, a-z0-9.-]/i', '',
					implode(', ', array_keys($progress['remains']['badhost']))
				) .
			')';
	}
	$summary['COMMENT'] = $action;
	$summary['PAGE']    = '[blocked] ' . (is_pagename($page) ? $page : '');
	$summary['URI']     = get_script_uri() . '?' . rawurlencode($page);
	$summary['USER_AGENT']  = TRUE;
	$summary['REMOTE_ADDR'] = TRUE;
	pkwk_mail_notify($notify_subject,  var_export($target, TRUE), $summary);
}

?>
