<?php
// $Id: spam.php,v 1.4 2006/11/26 14:01:38 henoheno Exp $
// Copyright (C) 2006 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version

// Functions for Concept-work of spam-uri metrics

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
		'([^\s<>"\'\[\]\#]+)?' .			// 6: File and query string
		'(?:\#([a-z0-9._~%!$&\'()*+,;=:@-]*))?' .	// 7: Fragment
		'#i',
		 $string, $array, PREG_SET_ORDER | PREG_OFFSET_CAPTURE
	);
	//var_dump(recursive_map('htmlspecialchars', $array));

	// Shrink $array
	static $parts = array(
		1 => 'scheme', 2 => 'userinfo', 3 => 'host', 4 => 'port',
		5 => 'path', 6 => 'file', 7 => 'fragment'
	);
	$default = array('');
	foreach(array_keys($array) as $uri) {
		array_rename_keys($array[$uri], $parts, TRUE, $default);
		$offset = $array[$uri]['scheme'][1]; // Scheme's offset

		foreach(array_keys($array[$uri]) as $part) {
			// Remove offsets for each part
			$array[$uri][$part] = & $array[$uri][$part][0];
		}

		if ($normalize) {
			$array[$uri]['scheme'] = scheme_normalize($array[$uri]['scheme']);
			if ($array[$uri]['scheme'] === '') {
				unset ($array[$uri]);
				continue;
			}
			$array[$uri]['host'] = strtolower($array[$uri]['host']);
			$array[$uri]['port'] = port_normalize($array[$uri]['port'], $array[$uri]['scheme'], FALSE);
			$array[$uri]['path'] = path_normalize($array[$uri]['path']);
			if ($preserve_rawuri) $array[$uri]['rawuri'] = & $array[$uri][0];
		} else {
			$array[$uri]['uri'] = & $array[$uri][0]; // Raw
		}
		unset($array[$uri][0]); // Matched string itself
		if (! $preserve_chunk) {
			unset(
				$array[$uri]['scheme'],
				$array[$uri]['userinfo'],
				$array[$uri]['host'],
				$array[$uri]['port'],
				$array[$uri]['path'],
				$array[$uri]['file'],
				$array[$uri]['fragment']
			);
		}

		$array[$uri]['area']['offset'] = $offset;
	}

	return $array;
}

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
	$result = 
		$matches[1] . '://' .	// scheme
		$matches[4] .			// nasty.example.com
		'/?refer=' . strtolower($matches[2]) .	// victim.example.org
		' ' . $result;

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
			'()' .	// Preserve?
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
	if (isset($uri['fragment']) && $uri['fragment'] !== '') {
		$tmp[] = '#';
		$tmp[] = & $uri['fragment'];
	}

	return implode('', $tmp);
}

// ---------------------
// Part One : Checker

function generate_glob_regex($string = '', $divider = '/')
{
	static $from = array(
			0 => '*',
			1 => '?',
			2 => '\[',
			3 => '\]',
			4 => '[',
			5 => ']',
		);
	static $mid = array(
			0 => '_AST_',
			1 => '_QUE_',
			2 => '_eRBR_',
			3 => '_eLBR_',
			4 => '_RBR_',
			5 => '_LBR_',
		);
	static $to = array(
			0 => '.*',
			1 => '.',
			2 => '\[',
			3 => '\]',
			4 => '[',
			5 => ']',
		);

	$string = str_replace($from, $mid, $string); // Hide
	$string = preg_quote($string, $divider);
	$string = str_replace($mid, $to, $string);   // Unhide

	return $string;
}

// TODO: Ignore list
// TODO: require_or_include_once(another file) for Admin
function is_badhost($hosts = '', $asap = TRUE)
{
	static $blocklist_regex;

	if (! isset($blocklist_regex)) {
		$blocklist_regex = array();
		$blocklist = array(
			// Deny all uri
			//'*',

			// IP address or ...
			//'10.20.*.*',	// 10.20.example.com also matches
			//'\[1\]',
			
			// Too much malicious sub-domains
			//'*.blogspot.com',

			// 2006-11 dev
			'wwwtahoo.com',

			// 2006-11 dev
			'*.infogami.com',

			// 2006/11/19 17:50 dev
			//'*.google0site.org',
			//'*.bigpricesearch.org',
			//'*.osfind.org',
			//'*.bablomira.biz',
		);
		foreach ($blocklist as $part) {
			$blocklist_regex[] = '#^' . generate_glob_regex($part, '#') . '$#i';
		}
	}

	$result = 0;
	if (! is_array($hosts)) $hosts = array($hosts);
	foreach($hosts as $host) {
		if (! is_string($host)) $host = '';
		foreach ($blocklist_regex as $regex) {
			if (preg_match($regex, $host)) {
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
function check_uri_spam_method()
{
	return array(
		'quantity'   => 8,	// Allow N URIs
		'area' => array(
		//	'total'  => 0,	// Allow N areas total, enabled below
			'anchor' => 0,	// Inside <a href> HTML tag
			'bbcode' => 0,	// Inside [url] or [link] BBCode
			),
		'non_uniq'   => 3,		// Allow N duped (and normalized) URIs
		'badhost'    => TRUE,	// Check badhost
		);
}

// TODO return TRUE or FALSE!
// Simple/fast spam check
function check_uri_spam($target = '', $method = array(), $asap = TRUE)
{
	$is_spam  = FALSE;
	$progress = array(
		'quantity'   => 0,
		'area' => array(
			'total'  => 0,
			'anchor' => 0,
			'bbcode' => 0,
			),
		'non_uniq'   => 0,
		'uniqhost'   => 0,
		'badhost'    => 0,
		'_action'    => array(),
		);

	if (! is_array($method) || empty($method)) {
		$method = check_uri_spam_method();
	}

	if (is_array($target)) {
		// Recurse
		foreach($target as $str) {
			list($is_spam, $_progress) = check_uri_spam($str, $method, $asap);
			$progress['quantity']       += $_progress['quantity'];
			$progress['area']['total']  += $_progress['area']['total'];
			$progress['area']['anchor'] += $_progress['area']['anchor'];
			$progress['area']['bbcode'] += $_progress['area']['bbcode'];
			$progress['non_uniq']       += $_progress['non_uniq'];
			$progress['uniqhost']       += $_progress['uniqhost'];
			$progress['badhost']        += $_progress['badhost'];
			foreach($_progress['_action'] as $key => $value) {
				if (isset($progress['_action'][$key])) {
					$progress['_action'][$key] += $value;
				} else {
					$progress['_action'][$key] =  $value;
				}
			}
			if ($is_spam && $asap) break;
		}
	} else {
		$pickups = spam_uri_pickup($target);
		if (! empty($pickups)) {
			$progress['quantity'] += count($pickups);

			// URI quantity
			if ((! $is_spam || ! $asap) && isset($method['quantity']) &&
				$progress['quantity'] > $method['quantity']) {
				$is_spam = TRUE;
				$progress['_action']['quantity'] = TRUE;
			}
			//var_dump($method['quantity'], $is_spam);

			// Using invalid area
			if ((! $is_spam || ! $asap) && isset($method['area'])) {
				foreach($pickups as $pickup) {
					foreach ($pickup['area'] as $key => $value) {
						if ($key == 'offset') continue;
						$progress['area']['total'] += $value;
						$progress['area'][$key]    += $value;
						if (isset($method['area']['total']) &&
								$progress['area']['total'] > $method['area']['total']) {
							$is_spam = TRUE;
							$progress['_action']['area']['total'] = TRUE;
							if ($is_spam && $asap) break;
						}
						if(isset($method['area'][$key]) &&
								$progress['area'][$key] > $method['area'][$key]) {
							$is_spam = TRUE;
							$progress['_action']['area'][$key] = TRUE;
							if ($is_spam && $asap) break;
						}
					}
					if ($is_spam && $asap) break;
				}
			}
			//var_dump($method['area'], $is_spam);

			// URI uniqueness (and removing non-uniques)
			if ((! $is_spam || ! $asap) && isset($method['non_uniq'])) {
				$uris = array();
				foreach ($pickups as $key => $pickup) {
					$uris[$key] = uri_array_implode($pickup);
				}
				$count = count($uris);
				$uris = array_unique($uris);
				$progress['non_uniq'] += $count - count($uris);
				if ($progress['non_uniq'] > $method['non_uniq']) {
					$is_spam = TRUE;
					$progress['_action']['non_uniq'] = TRUE;
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
			$progress['uniqhost'] += count($hosts);
			//var_dump($method['uniqhost'], $is_spam);

			// Bad host
			if ((! $is_spam || ! $asap) && isset($method['badhost'])) {
				$count = is_badhost($hosts, $asap);
				$progress['badhost'] += $count;
				if ($count !== 0) {
					$progress['_action']['badhost'] = TRUE;
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

// TODO: Show all
// Summarize $progress (blocked only)
function summarize_check_uri_spam_progress($progress = array(), $shownum = TRUE)
{
	//$list = check_uri_spam_method();

	$tmp = array();
	foreach (array_keys($progress['_action']) as $_action) {
		if (is_array($progress['_action'][$_action])) {
			foreach (array_keys($progress['_action'][$_action]) as $_area) {
				$tmp[] = $_action . '=>' . $_area .
					($shownum ? '(' . $progress[$_action][$_area] . ')' : '');
			}
		} else {
			$tmp[] = $_action .
					($shownum ? '(' . $progress[$_action] . ')' : '');
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
// 

// Simple/fast spam filter ($target: 'a string' or an array())
function pkwk_spamfilter($action, $page, $target = array('title' => ''), $method = array(), $asap = FALSE)
{
	global $notify;

	list($is_spam, $progress) = check_uri_spam($target, $method, $asap);

	// Mail to administrator(s)
	if ($is_spam) {
		if ($notify) {
			pkwk_spamnotify($action, $page, $target, $progress);
		}
		spam_exit();
	}
}

// ---------------------
// PukiWiki original

// Mail to administrator(s)
function pkwk_spamnotify($action, $page, $target = array('title' => ''), $progress = array())
{
	global $notify_subject;

	$footer['BLOCKED'] = 'Blocked by: ' .
		summarize_check_uri_spam_progress($progress);
	$footer['ACTION'] = 'Blocked: ' . $action;
	$footer['PAGE']   = '[blocked] ' . $page;
	$footer['URI']    = get_script_uri() . '?' . rawurlencode($page);
	$footer['USER_AGENT']  = TRUE;
	$footer['REMOTE_ADDR'] = TRUE;
	pkwk_mail_notify($notify_subject,  var_export($target, TRUE), $footer);
}

?>
