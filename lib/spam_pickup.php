<?php
// $Id: spam_pickup.php,v 1.5 2007/10/20 04:44:08 henoheno Exp $
// Copyright (C) 2006-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Functions for Concept-work of spam-uri metrics
//

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
		'#(\b[a-z][a-z0-9.+-]{1,8}):[/\\\]+' .		// 1: Scheme
		'(?:' .
			'([^\s<>"\'\[\]/\#?@]*)' .		// 2: Userinfo (Username)
		'@)?' .
		'(' .
			// 3: Host
			'\[[0-9a-f:.]+\]' . '|' .				// IPv6([colon-hex and dot]): RFC2732
			'(?:[0-9]{1,3}\.){3}[0-9]{1,3}' . '|' .	// IPv4(dot-decimal): 001.22.3.44
			'[a-z0-9_-][a-z0-9_.-]+[a-z0-9_-]' . 	// hostname(FQDN) : foo.example.org
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

// Pickupped URI array => An URI (See uri_pickup())
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

// ---------------------
// URI normalization

// Normalize an array of URI arrays
// NOTE: Give me the uri_pickup() results
function uri_pickup_normalize(& $pickups, $destructive = TRUE)
{
	if (! is_array($pickups)) return $pickups;

	if ($destructive) {
		foreach (array_keys($pickups) as $key) {
			$_key = & $pickups[$key];
			$_key['scheme']   = isset($_key['scheme'])   ? scheme_normalize($_key['scheme']) : '';
			$_key['host']     = isset($_key['host'])     ? host_normalize($_key['host'])     : '';
			$_key['port']     = isset($_key['port'])     ? port_normalize($_key['port'], $_key['scheme'], FALSE) : '';
			$_key['path']     = isset($_key['path'])     ? strtolower(path_normalize($_key['path'])) : '';
			$_key['file']     = isset($_key['file'])     ? file_normalize($_key['file'])   : '';
			$_key['query']    = isset($_key['query'])    ? query_normalize($_key['query']) : '';
			$_key['fragment'] = isset($_key['fragment']) ? strtolower($_key['fragment'])   : '';
		}
	} else {
		foreach (array_keys($pickups) as $key) {
			$_key = & $pickups[$key];
			$_key['scheme']   = isset($_key['scheme'])   ? scheme_normalize($_key['scheme']) : '';
			$_key['host']     = isset($_key['host'])     ? strtolower($_key['host'])         : '';
			$_key['port']     = isset($_key['port'])     ? port_normalize($_key['port'], $_key['scheme'], FALSE) : '';
			$_key['path']     = isset($_key['path'])     ? path_normalize($_key['path']) : '';
		}
	}

	return $pickups;
}

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
	$regex = '#<a\b[^>]*\bhref\b[^>]*>.*?</a\b[^>]*(>)#is';
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
	$regex = '#\[(url|link|img|email)\b[^\]]*\].*?\[/\1\b[^\]]*(\])#is';
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

// Preprocess: Removing/Modifying uninterest part for URI detection
function spam_uri_removing_hocus_pocus($binary = '', $method = array())
{
	$from = $to = array();

 	// Remove sequential spaces and too short lines
	$length = 4 ; // 'http'(1) and '://'(2) and 'fqdn'(1)
	if (is_array($method)) {
		// '<a'(2) or 'href='(5) or '>'(1) or '</a>'(4)
		// '[uri'(4) or ']'(1) or '[/uri]'(6) 
		if (isset($method['area_anchor']) || isset($method['uri_anchor']) ||
		    isset($method['area_bbcode']) || isset($method['uri_bbcode']))
				$length = 1;	// Seems not effective
	}
	$binary = strings($binary, $length, TRUE, FALSE); // Multibyte NOT needed

	// Remove/Replace quoted-spaces within tags
	$from[] = '#(<\w+ [^<>]*?\w ?= ?")([^"<>]*? [^"<>]*)("[^<>]*?>)#ie';
	$to[]   = "'$1' . str_replace(' ' , '%20' , trim('$2')) . '$3'";

	// Remove words (has no '<>[]:') between spaces
	$from[] = '/[ \t][\w.,()\ \t]+[ \t]/';
	$to[]   = ' ';

	return preg_replace($from, $to, $binary);
}

// Preprocess: Domain exposure callback (See spam_uri_pickup_preprocess())
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

// Preprocess: minor-rawurldecode() and adding space(s) and something
// to detect/count some URIs _if possible_
// NOTE: It's maybe danger to var_dump(result). [e.g. 'javascript:']
// [OK] http://victim.example.org/?site:nasty.example.org
// [OK] http://victim.example.org/nasty.example.org
// [OK] http://victim.example.org/go?http%3A%2F%2Fnasty.example.org
// [OK] http://victim.example.org/http://nasty.example.org
function spam_uri_pickup_preprocess($string = '', $method = array())
{
	if (! is_string($string)) return '';

	// rawurldecode(), just to catch encoded 'http://path/to/file', not to change '%20' to ' '
	$string = strtr(
		$string,
		array(
			'%3A' => ':',
			'%3a' => ':',
			'%2F' => '/',
			'%2f' => '/',
			'%5C' => '\\',
			'%5c' => '\\',
		)
	);

	$string = spam_uri_removing_hocus_pocus($string, $method);

	// Domain exposure (simple)
	// http://victim.example.org/nasty.example.org/path#frag
	// => http://nasty.example.org/?refer=victim.example.org and original
	$string = preg_replace(
		'#h?ttp://' .
		'(' .
			'a9\.com/' . '|' .
			'aboutus\.org/' . '|' .
			'alexa\.com/data/details\?url='  . '|' .
			'ime\.(?:nu|st)/' . '|' .	// 2ch.net
			'link\.toolbot\.com/' . '|' .
			'urlx\.org/' . '|' .
			'big5.51job.com/gate/big5/'	 . '|' .
			'big5.china.com/gate/big5/'	 . '|' .
			'big5.shippingchina.com:8080/' . '|' .
			'big5.xinhuanet.com/gate/big5/' . '|' .
			'bhomiyo.com/en.xliterate/' . '|' .
			'google.com/translate_c\?u=(?:http://)?' . '|' .
			'web.archive.org/web/2[^/]*/(?:http://)?' . '|' .
			'technorati.com/blogs/' .
		')' .
		'([a-z0-9.%_-]+\.[a-z0-9.%_-]+)' .	// nasty.example.org
		'#i',
		'http://$2/?refer=$1 $0',			// Preserve $0 or remove?
		$string
	);

	// Domain exposure (site:) See _preg_replace_callback_domain_exposure()
	$string = preg_replace_callback(
		array(
			'#(h?ttp)://' .	// 1:Scheme
			// 2:Host
			'(' .
				'(?:[a-z0-9_.-]+\.)?[a-z0-9_-]+\.[a-z0-9_-]+' .
				// Something Google: http://www.google.com/supported_domains
				// AltaVista: http://es.altavista.com/web/results?q=site%3Anasty.example.org+foobar
				// Live Search: search.live.com
				// MySpace: http://sads.myspace.com/Modules/Search/Pages/Search.aspx?_snip_&searchString=site:nasty.example.org
				// (also searchresults.myspace.com)
				// alltheweb.com
				// search.bbc.co.uk
				// search.orange.co.uk
				// ...
			')' .
			'/' .
			//TODO: Specify URL-enable characters
			'([a-z0-9?=&.,%_/\'\\\+-]+)' .				// 3:path/?query=foo+bar+
			'(?:\b|%20)site:([a-z0-9.%_-]+\.[a-z0-9.%_-]+)' .	// 4:site:nasty.example.com
			'()' .										// 5:Preserve or remove?
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

	$string = spam_uri_pickup_preprocess($string, $method);

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

?>
