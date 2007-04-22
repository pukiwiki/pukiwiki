<?php
// $Id: spam.php,v 1.24 2007/04/22 14:02:46 henoheno Exp $
// Copyright (C) 2006-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Functions for Concept-work of spam-uri metrics
//
// (PHP 4 >= 4.3.0): preg_match_all(PREG_OFFSET_CAPTURE): $method['uri_XXX'] related feature

if (! defined('SPAM_INI_FILE')) define('SPAM_INI_FILE', 'spam.ini.php');

// ---------------------
// Compat etc

// (PHP 4 >= 4.2.0): var_export(): mail-reporting and dump related
if (! function_exists('var_export')) {
	function var_export() {
		return 'var_export() is not found on this server' . "\n";
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
function uri_pickup($string = '')
{
	if (! is_string($string)) return array();

	// Not available for: IDN(ignored)
	$array = array();
	preg_match_all(
		// scheme://userinfo@host:port/path/or/pathinfo/maybefile.and?query=string#fragment
		// Refer RFC3986 (Regex below is not strict)
		'#(\b[a-z][a-z0-9.+-]{1,8}):/+' .	// 1: Scheme
		'(?:' .
			'([^\s<>"\'\[\]/\#?@]*)' .		// 2: Userinfo (Username)
		'@)?' .
		'(' .
			// 3: Host
			'\[[0-9a-f:.]+\]' . '|' .				// IPv6([colon-hex and dot]): RFC2732
			'(?:[0-9]{1,3}\.){3}[0-9]{1,3}' . '|' .	// IPv4(dot-decimal): 001.22.3.44
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

	// Format the $array
	static $parts = array(
		1 => 'scheme', 2 => 'userinfo', 3 => 'host', 4 => 'port',
		5 => 'path', 6 => 'file', 7 => 'query', 8 => 'fragment'
	);
	$default = array('');
	foreach(array_keys($array) as $uri) {
		$_uri = & $array[$uri];
		array_rename_keys($_uri, $parts, TRUE, $default);
		$offset = $_uri['scheme'][1]; // Scheme's offset = URI's offset
		foreach(array_keys($_uri) as $part) {
			$_uri[$part] = & $_uri[$part][0];	// Remove offsets
		}
	}

	foreach(array_keys($array) as $uri) {
		$_uri = & $array[$uri];
		if ($_uri['scheme'] === '') {
			unset($array[$uri]);	// Considererd harmless
			continue;
		}
		unset($_uri[0]); // Matched string itself
		$_uri['area']['offset'] = $offset;	// Area offset for area_measure()
	}

	return $array;
}

// Normalize an array of URI arrays
// NOTE: Give me the uri_pickup() results
function uri_pickup_normalize(& $pickups, $destructive = TRUE)
{
	if (! is_array($pickups)) return $pickups;

	if ($destructive) {
		foreach (array_keys($pickups) as $key) {
			$_key = & $pickups[$key];
			$_key['scheme']   = isset($_key['scheme']) ? scheme_normalize($_key['scheme']) : '';
			$_key['host']     = isset($_key['host'])     ? host_normalize($_key['host']) : '';
			$_key['port']     = isset($_key['port'])       ? port_normalize($_key['port'], $_key['scheme'], FALSE) : '';
			$_key['path']     = isset($_key['path'])     ? strtolower(path_normalize($_key['path'])) : '';
			$_key['file']     = isset($_key['file'])     ? file_normalize($_key['file']) : '';
			$_key['query']    = isset($_key['query'])    ? query_normalize($_key['query']) : '';
			$_key['fragment'] = isset($_key['fragment']) ? strtolower($_key['fragment']) : '';
		}
	} else {
		foreach (array_keys($pickups) as $key) {
			$_key = & $pickups[$key];
			$_key['scheme']   = isset($_key['scheme']) ? scheme_normalize($_key['scheme']) : '';
			$_key['host']     = isset($_key['host'])   ? strtolower($_key['host']) : '';
			$_key['port']     = isset($_key['port'])   ? port_normalize($_key['port'], $_key['scheme'], FALSE) : '';
			$_key['path']     = isset($_key['path'])   ? path_normalize($_key['path']) : '';
		}
	}

	return $pickups;
}

// An URI array => An URI (See uri_pickup())
// USAGE:
//	$pickups = uri_pickup('a string include some URIs');
//	$uris = array();
//	foreach (array_keys($pickups) as $key) {
//		$uris[$key] = uri_pickup_implode($pickups[$key]);
//	}
function uri_pickup_implode($uri = array())
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
// TODO: link.toolbot.com, urlx.org
function spam_uri_pickup_preprocess($string = '')
{
	if (! is_string($string)) return '';

	$string = rawurldecode($string);

	// Domain exposure (See _preg_replace_callback_domain_exposure())
	$string = preg_replace_callback(
		array(
			'#(http)://' .
			'(' .
				// Something Google: http://www.google.com/supported_domains
				'(?:[a-z0-9.]+\.)?google\.[a-z]{2,3}(?:\.[a-z]{2})?' .
				'|' .
				// AltaVista
				'(?:[a-z0-9.]+\.)?altavista.com' .
				
			')' .
			'/' .
			'([a-z0-9?=&.%_/\'\\\+-]+)' .				// path/?query=foo+bar+
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

// Main function of spam-uri pickup,
// A wrapper function of uri_pickup()
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
function scheme_normalize($scheme = '', $abbrevs_harmfull = TRUE)
{
	// Abbreviations they have no intention of link
	static $abbrevs = array(
		'ttp'	=> 'http',
		'ttps'	=> 'https',
	);

	// Aliases => normalized ones
	static $aliases = array(
		'pop'	=> 'pop3',
		'news'	=> 'nntp',
		'imap4'	=> 'imap',
		'snntp'	=> 'nntps',
		'snews'	=> 'nntps',
		'spop3'	=> 'pop3s',
		'pops'	=> 'pop3s',
	);

	if (! is_string($scheme)) return '';

	$scheme = strtolower($scheme);
	if (isset($abbrevs[$scheme])) {
		$scheme = $abbrevs_harmfull ? $abbrevs[$scheme] : '';
	}
	if (isset($aliases[$scheme])) {
		$scheme = $aliases[$scheme];
	}

	return $scheme;
}

// Hostname normlization (Destructive)
// www.foo     => www.foo   ('foo' seems TLD)
// www.foo.bar => foo.bar
// www.10.20   => www.10.20 (Invalid hostname)
// NOTE:
//   'www' is  mostly used as traditional hostname of WWW server.
//   'www.foo.bar' may be identical with 'foo.bar'.
function host_normalize($host = '')
{
	if (! is_string($host)) return '';

	$host = strtolower($host);
	$matches = array();
	if (preg_match('/^www\.(.+\.[a-z]+)$/', $host, $matches)) {
		return $matches[1];
	} else {
		return $host;
	}
}

// Port normalization: Suppress the (redundant) default port
// HTTP://example.org:80/ => http://example.org/
// HTTP://example.org:8080/ => http://example.org:8080/
// HTTPS://example.org:443/ => https://example.org/
function port_normalize($port, $scheme, $scheme_normalize = FALSE)
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

	// intval() converts '0-1' to '0', so preg_match() rejects these invalid ones
	if (! is_numeric($port) || $port < 0 || preg_match('/[^0-9]/i', $port))
		return '';

	$port = intval($port);
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
function path_normalize($path = '', $divider = '/', $add_root = TRUE)
{
	if (! is_string($divider)) return is_string($path) ? $path : '';

	if ($add_root) {
		$first_div = & $divider;
	} else {
		$first_div = '';
	}
	if (! is_string($path) || $path == '') return $first_div;

	if (strpos($path, $divider, strlen($path) - strlen($divider)) === FALSE) {
		$last_div = '';
	} else {
		$last_div = & $divider;
	}

	$array = explode($divider, $path);

	// Remove paddings ('//' and '/./')
	foreach(array_keys($array) as $key) {
		if ($array[$key] == '' || $array[$key] == '.') {
			 unset($array[$key]);
		}
	}

	// Remove back-tracks ('/../')
	$tmp = array();
	foreach($array as $value) {
		if ($value == '..') {
			array_pop($tmp);
		} else {
			array_push($tmp, $value);
		}
	}
	$array = & $tmp;

	if (empty($array)) {
		return $first_div;
	} else {
		return $first_div . implode($divider, $array) . $last_div;
	}
}

// DirectoryIndex normalize (Destructive and rough)
// TODO: sample.en.ja.html.gz => sample.html
function file_normalize($file = 'index.html.en')
{
	static $simple_defaults = array(
		'default.htm'	=> TRUE,
		'default.html'	=> TRUE,
		'default.asp'	=> TRUE,
		'default.aspx'	=> TRUE,
		'index'			=> TRUE,	// Some system can omit the suffix
	);

	static $content_suffix = array(
		// index.xxx, sample.xxx
		'htm'	=> TRUE,
		'html'	=> TRUE,
		'shtml'	=> TRUE,
		'jsp'	=> TRUE,
		'php'	=> TRUE,
		'php3'	=> TRUE,
		'php4'	=> TRUE,
		'pl'	=> TRUE,
		'py'	=> TRUE,
		'rb'	=> TRUE,
		'cgi'	=> TRUE,
		'xml'	=> TRUE,
	);

	static $language_suffix = array(
		// Reference: Apache 2.0.59 'AddLanguage' default
		'ca'	=> TRUE,
		'cs'	=> TRUE,	// cs
		'cz'	=> TRUE,	// cs
		'de'	=> TRUE,
		'dk'	=> TRUE,	// da
		'el'	=> TRUE,
		'en'	=> TRUE,
		'eo'	=> TRUE,
		'es'	=> TRUE,
		'et'	=> TRUE,
		'fr'	=> TRUE,
		'he'	=> TRUE,
		'hr'	=> TRUE,
		'it'	=> TRUE,
		'ja'	=> TRUE,
		'ko'	=> TRUE,
		'ltz'	=> TRUE,
		'nl'	=> TRUE,
		'nn'	=> TRUE,
		'no'	=> TRUE,
		'po'	=> TRUE,
		'pt'	=> TRUE,
		'pt-br'	=> TRUE,
		'ru'	=> TRUE,
		'sv'	=> TRUE,
		'zh-cn'	=> TRUE,
		'zh-tw'	=> TRUE,

		// Reference: Apache 2.0.59 default 'index.html' variants
		'ee'	=> TRUE,
		'lb'	=> TRUE,
		'var'	=> TRUE,
	);

	static $charset_suffix = array(
		// Reference: Apache 2.0.59 'AddCharset' default
		'iso8859-1'	=> TRUE, // ISO-8859-1
		'latin1'	=> TRUE, // ISO-8859-1
		'iso8859-2'	=> TRUE, // ISO-8859-2
		'latin2'	=> TRUE, // ISO-8859-2
		'cen'		=> TRUE, // ISO-8859-2
		'iso8859-3'	=> TRUE, // ISO-8859-3
		'latin3'	=> TRUE, // ISO-8859-3
		'iso8859-4'	=> TRUE, // ISO-8859-4
		'latin4'	=> TRUE, // ISO-8859-4
		'iso8859-5'	=> TRUE, // ISO-8859-5
		'latin5'	=> TRUE, // ISO-8859-5
		'cyr'		=> TRUE, // ISO-8859-5
		'iso-ru'	=> TRUE, // ISO-8859-5
		'iso8859-6'	=> TRUE, // ISO-8859-6
		'latin6'	=> TRUE, // ISO-8859-6
		'arb'		=> TRUE, // ISO-8859-6
		'iso8859-7'	=> TRUE, // ISO-8859-7
		'latin7'	=> TRUE, // ISO-8859-7
		'grk'		=> TRUE, // ISO-8859-7
		'iso8859-8'	=> TRUE, // ISO-8859-8
		'latin8'	=> TRUE, // ISO-8859-8
		'heb'		=> TRUE, // ISO-8859-8
		'iso8859-9'	=> TRUE, // ISO-8859-9
		'latin9'	=> TRUE, // ISO-8859-9
		'trk'		=> TRUE, // ISO-8859-9
		'iso2022-jp'=> TRUE, // ISO-2022-JP
		'jis'		=> TRUE, // ISO-2022-JP
		'iso2022-kr'=> TRUE, // ISO-2022-KR
		'kis'		=> TRUE, // ISO-2022-KR
		'iso2022-cn'=> TRUE, // ISO-2022-CN
		'cis'		=> TRUE, // ISO-2022-CN
		'big5'		=> TRUE,
		'cp-1251'	=> TRUE, // ru, WINDOWS-1251
		'win-1251'	=> TRUE, // ru, WINDOWS-1251
		'cp866'		=> TRUE, // ru
		'koi8-r'	=> TRUE, // ru, KOI8-r
		'koi8-ru'	=> TRUE, // ru, KOI8-r
		'koi8-uk'	=> TRUE, // ru, KOI8-ru
		'ua'		=> TRUE, // ru, KOI8-ru
		'ucs2'		=> TRUE, // ru, ISO-10646-UCS-2
		'ucs4'		=> TRUE, // ru, ISO-10646-UCS-4
		'utf8'		=> TRUE,

		// Reference: Apache 2.0.59 default 'index.html' variants
		'euc-kr'	=> TRUE,
		'gb2312'	=> TRUE,
	);

	// May uncompress by web browsers on the fly
	// Must be at the last of the filename
	// Reference: Apache 2.0.59 'AddEncoding'
	static $encoding_suffix = array(
		'z'		=> TRUE,
		'gz'	=> TRUE,
	);

	if (! is_string($file)) return '';
	$_file = strtolower($file);
	if (isset($simple_defaults[$_file])) return '';


	// Roughly removing language/character-set/encoding suffixes
	// References:
	//  * Apache 2 document about 'Content-negotiaton', 'mod_mime' and 'mod_negotiation'
	//    http://httpd.apache.org/docs/2.0/content-negotiation.html
	//    http://httpd.apache.org/docs/2.0/mod/mod_mime.html
	//    http://httpd.apache.org/docs/2.0/mod/mod_negotiation.html
	//  * http://www.iana.org/assignments/character-sets
	//  * RFC3066: Tags for the Identification of Languages
	//    http://www.ietf.org/rfc/rfc3066.txt
	//  * ISO 639: codes of 'language names'
	$suffixes = explode('.', $_file);
	$body = array_shift($suffixes);
	if ($suffixes) {
		// Remove the last .gz/.z
		$last_key = end(array_keys($suffixes));
		if (isset($encoding_suffix[$suffixes[$last_key]])) {
			unset($suffixes[$last_key]);
		}
	}
	// Cut language and charset suffixes
	foreach($suffixes as $key => $value){
		if (isset($language_suffix[$value]) || isset($charset_suffix[$value])) {
			unset($suffixes[$key]);
		}
	}
	if (empty($suffixes)) return $body;

	// Index.xxx
	$count = count($suffixes);
	reset($suffixes);
	$current = current($suffixes);
	if ($body == 'index' && $count == 1 && isset($content_suffix[$current])) return '';

	return $file;
}

// Sort query-strings if possible (Destructive and rough)
// [OK] &&&&f=d&b&d&c&a=0dd  =>  a=0dd&b&c&d&f=d
// [OK] nothing==&eg=dummy&eg=padding&eg=foobar  =>  eg=foobar
function query_normalize($string = '', $equal = TRUE, $equal_cutempty = TRUE, $stortolower = TRUE)
{
	if (! is_string($string)) return '';
	if ($stortolower) $string = strtolower($string);

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

// Rough implementation of globbing
//
// USAGE: $regex = '/^' . generate_glob_regex('*.txt', '/') . '$/i';
//
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

	if (! is_string($string)) return '';

	$string = str_replace($from, $mid, $string); // Hide
	$string = preg_quote($string, $divider);
	$string = str_replace($mid, $to, $string);   // Unhide

	return $string;
}

// Rough hostname checker
// [OK] 192.168.
// TODO: Strict digit, 0x, CIDR, IPv6
function is_ip($string = '')
{
	if (preg_match('/^' .
		'(?:[0-9]{1,3}\.){3}[0-9]{1,3}' . '|' .
		'(?:[0-9]{1,3}\.){1,3}' . '$/',
		$string)) {
		return 4;	// Seems IPv4(dot-decimal)
	} else {
		return 0;	// Seems not IP
	}
}

// Generate host (FQDN, IPv4, ...) regex
// 'localhost'     : Matches with 'localhost' only
// 'example.org'   : Matches with 'example.org' only (See host_normalize() about 'www')
// '.example.org'  : Matches with ALL FQDN ended with '.example.org'
// '*.example.org' : Almost the same of '.example.org' except 'www.example.org'
// '10.20.30.40'   : Matches with IPv4 address '10.20.30.40' only
// [TODO] '192.'   : Matches with all IPv4 hosts started with '192.'
// TODO: IPv4, CIDR?, IPv6
function generate_host_regex($string = '', $divider = '/')
{
	if (! is_string($string)) return '';

	if (mb_strpos($string, '.') === FALSE)
		return generate_glob_regex($string, $divider);

	$result = '';
	if (is_ip($string)) {
		// IPv4
		return generate_glob_regex($string, $divider);
	} else {
		// FQDN or something
		$part = explode('.', $string, 2);
		if ($part[0] == '') {
			$part[0] = '(?:.*\.)?';	// And all related FQDN
		} else if ($part[0] == '*') {
			$part[0] = '.*\.';	// All subdomains/hosts only
		} else {
			return generate_glob_regex($string, $divider);
		}
		$part[1] = generate_glob_regex($part[1], $divider);
		return implode('', $part);
	}
}

function get_blocklist($list = '')
{
	static $regexs;

	if (! isset($regexs)) {
		$regexs = array();
		if (file_exists(SPAM_INI_FILE)) {
			$blocklist = array();
			include(SPAM_INI_FILE);
			//	$blocklist['badhost'] = array(
			//		'*.blogspot.com',	// Blog services's subdomains (only)
			//		'IANA-examples' => '#^(?:.*\.)?example\.(?:com|net|org)$#',
			//	);
			if (isset($blocklist['list'])) {
				$regexs['list'] = & $blocklist['list'];
			} else {
				// Default
				$blocklist['list'] = array(
					'goodhost' => FALSE,
					'badhost'  => TRUE,
				);
			}
			foreach(array_keys($blocklist['list']) as $_list) {
				if (! isset($blocklist[$_list])) continue;
				foreach ($blocklist[$_list] as $key => $value) {
					if (is_array($value)) {
						$regexs[$_list][$key] = array();
						foreach($value as $_key => $_value) {
							get_blocklist_add($regexs[$_list][$key], $_key, $_value);
						}
					} else {
						get_blocklist_add($regexs[$_list], $key, $value);
					}
				}
				unset($blocklist[$_list]);
			}
		}
	}

	if ($list == '') {
		return $regexs;	// ALL
	} else if (isset($regexs[$list])) {
		return $regexs[$list];
	} else {	
		return array();
	}
}

// Subroutine of get_blocklist()
function get_blocklist_add(& $array, $key = 0, $value = '*.example.org')
{
	if (is_string($key)) {
		$array[$key] = & $value; // Treat $value as a regex
	} else {
		$array[$value] = '/^' . generate_host_regex($value, '/') . '$/i';
	}
}

function is_badhost($hosts = array(), $asap = TRUE, & $remains)
{
	$result = array();
	if (! is_array($hosts)) $hosts = array($hosts);
	foreach(array_keys($hosts) as $key) {
		if (! is_string($hosts[$key])) {
			unset($hosts[$key]);
		}
	}
	if (empty($hosts)) return $result;

	foreach(get_blocklist('list') as $key=>$value){
		if ($value) {
			foreach (get_blocklist($key) as $label => $regex) {
				if (is_array($regex)) {
					$result[$label] = array();
					foreach($regex as $_label => $_regex) {
						if (is_badhost_avail($_label, $_regex, $hosts, $result[$label]) && $asap) {
							break;
						}
					}
					if (empty($result[$label])) unset($result[$label]);
				} else {
					if (is_badhost_avail($label, $regex, $hosts, $result) && $asap) {
						break;
					}
				}
			}
		} else {
			foreach (get_blocklist($key) as $regex) {
				$hosts = preg_grep_invert($regex, $hosts);
			}
			if (empty($hosts)) return $result;
		}
	}

	$remains = $hosts;
	return $result;
}

// Subroutine for is_badhost()
function is_badhost_avail($label = '*.example.org', $regex = '/^.*\.example\.org$/', & $hosts, & $result)
{
	$group = preg_grep($regex, $hosts);
	if ($group) {

		// DEBUG var_dump($group); // badhost detail

		$result[$label] = & $group;
		$hosts = array_diff($hosts, $result[$label]);
		return TRUE;
	} else {
		return FALSE;
	}
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
		'error'   => array(),
	);
	$sum     = & $progress['sum'];
	$is_spam = & $progress['is_spam'];
	$remains = & $progress['remains'];
	$error   = & $progress['error'];
	$asap    = isset($method['asap']);

	// Recurse
	if (is_array($target)) {
		foreach($target as $str) {
			// Recurse
			$_progress = check_uri_spam($str, $method);
			$_sum      = & $_progress['sum'];
			$_is_spam  = & $_progress['is_spam'];
			$_remains  = & $_progress['remains'];
			$_error    = & $_progress['error'];
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
					if (is_int($_key)) {
						$remains[$key][]      = $_value;
					} else {
						$remains[$key][$_key] = $_value;
					}
				}
			}
			if (! empty($_error)) $error += $_error;
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
	if ($asap && $is_spam) return $progress;

	// URI: Pickup
	$pickups = uri_pickup_normalize(spam_uri_pickup($target, $method));
	//$remains['uri_pickup'] = & $pickups;

	// Return if ...
	if (empty($pickups)) return $progress;

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

		$uris = array();
		foreach (array_keys($pickups) as $key) {
			$uris[$key] = uri_pickup_implode($pickups[$key]);
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
	if ($asap && $is_spam) return $progress;

	// Host: Uniqueness (uniq / non-uniq)
	$hosts = array();
	foreach ($pickups as $pickup) $hosts[] = & $pickup['host'];
	$hosts = array_unique($hosts);
	//$remains['uniqhost'] = & $hosts;
	$sum['uniqhost'] += count($hosts);
	if ((! $asap || ! $is_spam) && isset($method['non_uniqhost'])) {
		$sum['non_uniqhost'] = $sum['quantity'] - $sum['uniqhost'];
		if ($sum['non_uniqhost'] > $method['non_uniqhost']) {
			$is_spam['non_uniqhost'] = TRUE;
		}
	}

	// Return if ...
	if ($asap && $is_spam) return $progress;

	// URI: Bad host
	if ((! $asap || ! $is_spam) && isset($method['badhost'])) {
		$__remains = array();
		$badhost = is_badhost($hosts, $asap, $__remains);
		if (! $asap) {
			if ($__remains) {
				$remains['badhost'] = array();
				foreach ($__remains as $value) {
					$remains['badhost'][$value] = TRUE;
				}
			}
		}
		unset($__remains);
		if (! empty($badhost)) {
			//var_dump($badhost);	// BADHOST detail
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
	pkwk_mail_notify($notify_subject,  var_export($target, TRUE), $summary, TRUE);
}

?>
