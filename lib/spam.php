<?php
// $Id: spam.php,v 1.33 2008/12/28 08:33:05 henoheno Exp $
// Copyright (C) 2006-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Functions for Concept-work of spam-uri metrics
//
// (PHP 4 >= 4.3.0): preg_match_all(PREG_OFFSET_CAPTURE): $method['uri_XXX'] related feature

if (! defined('SPAM_INI_FILE'))   define('SPAM_INI_FILE',   'spam.ini.php');
if (! defined('DOMAIN_INI_FILE')) define('DOMAIN_INI_FILE', 'domain.ini.php');

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
// Utilities

// Very roughly, shrink the lines of var_export()
// NOTE: If the same data exists, it must be corrupted.
function var_export_shrink($expression, $return = FALSE, $ignore_numeric_keys = FALSE)
{
	$result = var_export($expression, TRUE);

	$result = preg_replace(
		// Remove a newline and spaces
		'# => \n *array \(#', ' => array (',
		$result
	);

	if ($ignore_numeric_keys) {
		$result =preg_replace(
			// Remove numeric keys
			'#^( *)[0-9]+ => #m', '$1',
			$result
		);
	}

	if ($return) {
		return $result;
	} else {
		echo   $result;
		return NULL;
	}
}

// Data structure: Create an array they _refer_only_one_ value
function one_value_array($num = 0, $value = NULL)
{
	$num   = max(0, intval($num));
	$array = array();

	for ($i = 0; $i < $num; $i++) {
		$array[] = & $value;
	}

	return $array;
}

// Reverse $string with specified delimiter
function delimiter_reverse($string = 'foo.bar.example.com', $from_delim = '.', $to_delim = NULL)
{
	$to_null = ($to_delim === NULL);

	if (! is_string($from_delim) || (! $to_null && ! is_string($to_delim))) {
		return FALSE;
	}
	if (is_array($string)) {
		// Map, Recurse
		$count = count($string);
		$from  = one_value_array($count, $from_delim);
		if ($to_null) {
			// Note: array_map() vanishes all keys
			return array_map('delimiter_reverse', $string, $from);
		} else {
			$to = one_value_array($count, $to_delim);
			// Note: array_map() vanishes all keys
			return array_map('delimiter_reverse', $string, $from, $to);
		}
	}
	if (! is_string($string)) {
		return FALSE;
	}

	// Returns com.example.bar.foo
	if ($to_null) $to_delim = & $from_delim;
	return implode($to_delim, array_reverse(explode($from_delim, $string)));
}

// ksort() by domain
function ksort_by_domain(& $array)
{
	$sort = array();
	foreach(array_keys($array) as $key) {
		$reversed = delimiter_reverse($key);
		if ($reversed !== FALSE) {
			$sort[$reversed] = $key;
		}
	}
	ksort($sort, SORT_STRING);

	$result = array();
	foreach($sort as $key) {
		$result[$key] = & $array[$key];
	}

	$array = $result;
}

// Roughly strings(1) using PCRE
// This function is useful to:
//   * Reduce the size of data, from removing unprintable binary data
//   * Detect _bare_strings_ from binary data
// References:
//   http://www.freebsd.org/cgi/man.cgi?query=strings (Man-page of GNU strings)
//   http://www.pcre.org/pcre.txt
// Note: mb_ereg_replace() is one of mbstring extension's functions
//   and need to init its encoding.
function strings($binary = '', $min_len = 4, $ignore_space = FALSE, $multibyte = FALSE)
{
	// String only
	$binary = (is_array($binary) || $binary === TRUE) ? '' : strval($binary);

	$regex = $ignore_space ?
		'[^[:graph:] \t\n]+' :		// Remove "\0" etc, and readable spaces
		'[^[:graph:][:space:]]+';	// Preserve readable spaces if possible

	$binary = $multibyte ?
		mb_ereg_replace($regex,           "\n",  $binary) :
		preg_replace('/' . $regex . '/s', "\n",  $binary);

	if ($ignore_space) {
		$binary = preg_replace(
			array(
				'/[ \t]{2,}/',
				'/^[ \t]/m',
				'/[ \t]$/m',
			),
			array(
				' ',
				'',
				''
			),
			 $binary);
	}

	if ($min_len > 1) {
		// The last character seems "\n" or not
		$br = (! empty($binary) && $binary[strlen($binary) - 1] == "\n") ? "\n" : '';

		$min_len = min(1024, intval($min_len));
		$regex = '/^.{' . $min_len . ',}/S';
		$binary = implode("\n", preg_grep($regex, explode("\n", $binary))) . $br;
	}

	return $binary;
}


// ---------------------
// Utilities: Arrays

// Count leaves (A leaf = value that is not an array, or an empty array)
function array_count_leaves($array = array(), $count_empty = FALSE)
{
	if (! is_array($array) || (empty($array) && $count_empty)) return 1;

	// Recurse
	$count = 0;
	foreach ($array as $part) {
		$count += array_count_leaves($part, $count_empty);
	}
	return $count;
}

// Merge two leaves
// Similar to PHP array_merge_leaves(), except strictly preserving keys as string
function array_merge_leaves($array1, $array2, $sort_keys = TRUE)
{
	// Array(s) only 
	$is_array1 = is_array($array1);
	$is_array2 = is_array($array2);
	if ($is_array1) {
		if ($is_array2) {
			;	// Pass
		} else {
			return $array1;
		}
	} else if ($is_array2) {
		return $array2;
	} else {
		return $array2; // Not array ($array1 is overwritten)
	}

	$keys_all = array_merge(array_keys($array1), array_keys($array2));
	if ($sort_keys) sort($keys_all, SORT_STRING);

	$result = array();
	foreach($keys_all as $key) {
		$isset1 = isset($array1[$key]);
		$isset2 = isset($array2[$key]);
		if ($isset1 && $isset2) {
			// Recurse
			$result[$key] = array_merge_leaves($array1[$key], $array2[$key], $sort_keys);
		} else if ($isset1) {
			$result[$key] = & $array1[$key];
		} else {
			$result[$key] = & $array2[$key];
		}
	}
	return $result;
}

// An array-leaves to a flat array
function array_flat_leaves($array, $unique = TRUE)
{
	if (! is_array($array)) return $array;

	$tmp = array();
	foreach(array_keys($array) as $key) {
		if (is_array($array[$key])) {
			// Recurse
			foreach(array_flat_leaves($array[$key]) as $_value) {
				$tmp[] = $_value;
			}
		} else {
			$tmp[] = & $array[$key];
		}
	}

	return $unique ? array_values(array_unique($tmp)) : $tmp;
}

// $array['something'] => $array['wanted']
function array_rename_keys(& $array, $keys = array('from' => 'to'), $force = FALSE, $default = '')
{
	if (! is_array($array) || ! is_array($keys)) return FALSE;

	// Nondestructive test
	if (! $force) {
		foreach(array_keys($keys) as $from) {
			if (! isset($array[$from])) {
				return FALSE;
			}
		}
	}

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

// Remove redundant values from array()
function array_unique_recursive($array = array())
{
	if (! is_array($array)) return $array;

	$tmp = array();
	foreach($array as $key => $value){
		if (is_array($value)) {
			$array[$key] = array_unique_recursive($value);
		} else {
			if (isset($tmp[$value])) {
				unset($array[$key]);
			} else {
				$tmp[$value] = TRUE;
			}
		}
	}

	return $array;
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

	if (mb_strpos($string, '.') === FALSE) {
		// localhost
		return generate_glob_regex($string, $divider);
	}

	if (is_ip($string)) {
		// IPv4
		return generate_glob_regex($string, $divider);
	} else {
		// FQDN or something
		$part = explode('.', $string, 2);
		if ($part[0] == '') {
			// .example.org
			$part[0] = '(?:.*\.)?';
		} else if ($part[0] == '*') {
			// *.example.org
			$part[0] = '.*\.';
		} else {
			// example.org, etc
			return generate_glob_regex($string, $divider);
		}
		$part[1] = generate_glob_regex($part[1], $divider);
		return implode('', $part);
	}
}

// Rough hostname checker
// TODO: Strict digit, 0x, CIDR, '999.999.999.999', ':', '::G'
function is_ip($string = '')
{
	if (! is_string($string)) return FALSE;

	if (strpos($string, ':') !== FALSE) {
		return 6;	// Seems IPv6
	}

	if (preg_match('/^' .
		'(?:[0-9]{1,3}\.){3}[0-9]{1,3}' . '|' .
		'(?:[0-9]{1,3}\.){1,3}'         . '$/',
		$string)) {
		return 4;	// Seems IPv4(dot-decimal)
	}

	return FALSE;	// Seems not IP
}

// Load SPAM_INI_FILE and return parsed one
function get_blocklist($list = '')
{
	static $regexes;

	if ($list === NULL) {
		$regexes = NULL;	// Unset
		return array();
	}

	if (! isset($regexes)) {
		$regexes = array();
		if (file_exists(SPAM_INI_FILE)) {
			$blocklist = array();

			include(SPAM_INI_FILE);
			//	$blocklist['list'] = array(
			//  	//'goodhost' => FALSE;
			//  	'badhost' => TRUE;
			// );
			//	$blocklist['badhost'] = array(
			//		'*.blogspot.com',	// Blog services's subdomains (only)
			//		'IANA-examples' => '#^(?:.*\.)?example\.(?:com|net|org)$#',
			//	);

			foreach(array(
					'pre',
					'list',
				) as $special) {

				if (! isset($blocklist[$special])) continue;

				$regexes[$special] = $blocklist[$special];

				foreach(array_keys($blocklist[$special]) as $_list) {
					if (! isset($blocklist[$_list])) continue;

					foreach ($blocklist[$_list] as $key => $value) {
						if (is_array($value)) {
							$regexes[$_list][$key] = array();
							foreach($value as $_key => $_value) {
								get_blocklist_add($regexes[$_list][$key], $_key, $_value);
							}
						} else {
							get_blocklist_add($regexes[$_list], $key, $value);
						}
					}

					unset($blocklist[$_list]);
				}
			}
		}
	}

	if ($list === '') {
		return $regexes;		// ALL of
	} else if (isset($regexes[$list])) {
		return $regexes[$list];	// A part of
	} else {
		return array();			// Found nothing
	}
}

// Subroutine of get_blocklist(): Add new regex to the $array
function get_blocklist_add(& $array, $key = 0, $value = '*.example.org/path/to/file.html')
{
	if (is_string($key)) {
		$array[$key]   = & $value; // Treat $value as a regex for FQDN(host)s
	} else {
		$array[$value] = '#^' . generate_host_regex($value, '#') . '$#i';
	}
}

// Blocklist metrics: Separate $host, to $blocked and not blocked
function blocklist_distiller(& $hosts, $keys = array('goodhost', 'badhost'), $asap = FALSE)
{
	if (! is_array($hosts)) $hosts = array($hosts);
	if (! is_array($keys))  $keys  = array($keys);

	$list = get_blocklist('list');
	$blocked = array();

	foreach($keys as $key){
		foreach (get_blocklist($key) as $label => $regex) {
			if (is_array($regex)) {
				foreach($regex as $_label => $_regex) {
					$group = preg_grep($_regex, $hosts);
					if ($group) {
						$hosts = array_diff($hosts, $group);
						$blocked[$key][$label][$_label] = $group;
						if ($asap && $list[$key]) break;
					}
				}
			} else {
				$group = preg_grep($regex, $hosts);
				if ($group) {
					$hosts = array_diff($hosts, $group);
					$blocked[$key][$label] = $group;
					if ($asap && $list[$key]) break;
				}
			}
		}
	}

	return $blocked;
}


// ---------------------


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
	// Return value
	$progress = array(
		'method'  => array(
			// Theme to do  => Dummy, optional value, or optional array()
			//'quantity'    => 8,
			//'uniqhost'    => TRUE,
			//'non_uniqhost'=> 3,
			//'non_uniquri' => 3,
			//'badhost'     => TRUE,
			//'area_anchor' => 0,
			//'area_bbcode' => 0,
			//'uri_anchor'  => 0,
			//'uri_bbcode'  => 0,
		),
		'sum' => array(
			// Theme        => Volume found (int)
		),
		'is_spam' => array(
			// Flag. If someting defined here,
			// one or more spam will be included
			// in this report
		),
		'blocked' => array(
			// Hosts blocked
			//'category' => array(
			//	'host',
			//)
		),
		'hosts' => array(
			// Hosts not blocked
		),
	);

	// ----------------------------------------
	// Aliases

	$sum     = & $progress['sum'];
	$is_spam = & $progress['is_spam'];
	$progress['method'] = & $method;	// Argument
	$blocked = & $progress['blocked'];
	$hosts   = & $progress['hosts'];
	$asap    = isset($method['asap']);

	// ----------------------------------------
	// Init

	if (! is_array($method) || empty($method)) {
		$method = check_uri_spam_method();
	}
	foreach(array_keys($method) as $key) {
		if (! isset($sum[$key])) $sum[$key] = 0;
	}
	if (! isset($sum['quantity'])) $sum['quantity'] = 0;

	// ----------------------------------------
	// Recurse

	if (is_array($target)) {
		foreach($target as $str) {
			if (! is_string($str)) continue;

			$_progress = check_uri_spam($str, $method);	// Recurse

			// Merge $sum
			$_sum = & $_progress['sum'];
			foreach (array_keys($_sum) as $key) {
				if (! isset($sum[$key])) {
					$sum[$key] = & $_sum[$key];
				} else {
					$sum[$key] += $_sum[$key];
				}
			}

			// Merge $is_spam
			$_is_spam = & $_progress['is_spam'];
			foreach (array_keys($_is_spam) as $key) {
				$is_spam[$key] = TRUE;
				if ($asap) break;
			}
			if ($asap && $is_spam) break;

			// Merge only
			$blocked = array_merge_leaves($blocked, $_progress['blocked'], FALSE);
			$hosts   = array_merge_leaves($hosts,   $_progress['hosts'],   FALSE);
		}

		// Unique values
		$blocked = array_unique_recursive($blocked);
		$hosts   = array_unique_recursive($hosts);

		// Recount $sum['badhost']
		$sum['badhost'] = array_count_leaves($blocked);

		return $progress;
	}

	// ----------------------------------------
	// Area measure

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

	// ----------------------------------------
	// URI: Pickup

	$pickups = uri_pickup_normalize(spam_uri_pickup($target, $method));
	$hosts = array();
	foreach ($pickups as $key => $pickup) {
		$hosts[$key] = & $pickup['host'];
	}

	// Return if ...
	if (empty($pickups)) return $progress;

	// ----------------------------------------
	// URI: Bad host <pre-filter> (Separate good/bad hosts from $hosts)

	if ((! $asap || ! $is_spam) && isset($method['badhost'])) {
		$list    = get_blocklist('pre');
		$blocked = blocklist_distiller($hosts, array_keys($list), $asap);
		foreach($list as $key=>$type){
			if (! $type) unset($blocked[$key]); // Ignore goodhost etc
		}
		unset($list);
		if (! empty($blocked)) $is_spam['badhost'] = TRUE;
	}

	// Return if ...
	if ($asap && $is_spam) return $progress;

	// Remove blocked from $pickups
	foreach(array_keys($pickups) as $key) {
		if (! isset($hosts[$key])) {
			unset($pickups[$key]);
		}
	}

	// ----------------------------------------
	// URI: Check quantity

	$sum['quantity'] += count($pickups);
		// URI quantity
	if ((! $asap || ! $is_spam) && isset($method['quantity']) &&
		$sum['quantity'] > $method['quantity']) {
		$is_spam['quantity'] = TRUE;
	}

	// ----------------------------------------
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

	// ----------------------------------------
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

	// ----------------------------------------
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

	// ----------------------------------------
	// Host: Uniqueness (uniq / non-uniq)

	$hosts = array_unique($hosts);

	if (isset($sum['uniqhost'])) $sum['uniqhost'] += count($hosts);
	if ((! $asap || ! $is_spam) && isset($method['non_uniqhost'])) {
		$sum['non_uniqhost'] = $sum['quantity'] - $sum['uniqhost'];
		if ($sum['non_uniqhost'] > $method['non_uniqhost']) {
			$is_spam['non_uniqhost'] = TRUE;
		}
	}

	// Return if ...
	if ($asap && $is_spam) return $progress;

	// ----------------------------------------
	// URI: Bad host (Separate good/bad hosts from $hosts)

	if ((! $asap || ! $is_spam) && isset($method['badhost'])) {
		$list    = get_blocklist('list');
		$blocked = array_merge_leaves(
			$blocked,
			blocklist_distiller($hosts, array_keys($list), $asap),
			FALSE
		);
		foreach($list as $key=>$type){
			if (! $type) unset($blocked[$key]); // Ignore goodhost etc
		}
		unset($list);
		if (! empty($blocked)) $is_spam['badhost'] = TRUE;
	}

	// Return if ...
	//if ($asap && $is_spam) return $progress;

	// ----------------------------------------
	// End

	return $progress;
}

// ---------------------
// Reporting

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
				if (isset($method[$key]) && $value) {
					$tmp[] = $key . '(' . $value . ')';
				}
			}
		}
	}

	return implode(', ', $tmp);
}

function summarize_detail_badhost($progress = array())
{
	if (! isset($progress['blocked']) || empty($progress['blocked'])) return '';

	// Flat per group
	$blocked = array();
	foreach($progress['blocked'] as $list => $lvalue) {
		foreach($lvalue as $group => $gvalue) {
			$flat = implode(', ', array_flat_leaves($gvalue));
			if ($flat === $group) {
				$blocked[$list][]       = $flat;
			} else {
				$blocked[$list][$group] = $flat;
			}
		}
	}

	// Shrink per list
	// From: 'A-1' => array('ie.to')
	// To:   'A-1' => 'ie.to'
	foreach($blocked as $list => $lvalue) {
		if (is_array($lvalue) &&
		   count($lvalue) == 1 &&
		   is_numeric(key($lvalue))) {
		    $blocked[$list] = current($lvalue);
		}
	}

	return var_export_shrink($blocked, TRUE, TRUE);
}

function summarize_detail_newtral($progress = array())
{
	if (! isset($progress['hosts'])    ||
	    ! is_array($progress['hosts']) ||
	    empty($progress['hosts'])) return '';

	// Generate a responsible $trie
	$trie = array();
	foreach($progress['hosts'] as $value) {
		// 'A.foo.bar.example.com'
		$resp = whois_responsibility($value);	// 'example.com'
		if (empty($resp)) {
			// One or more test, or do nothing here
			$resp = strval($value);
			$rest = '';
		} else {
			$rest = rtrim(substr($value, 0, - strlen($resp)), '.');	// 'A.foo.bar'
		}
		$trie = array_merge_leaves($trie, array($resp => array($rest => NULL)), FALSE);
	}

	// Format: var_export_shrink() -like output
	$result = array();
	ksort_by_domain($trie);
	foreach(array_keys($trie) as $key) {
		ksort_by_domain($trie[$key]);
		if (count($trie[$key]) == 1 && key($trie[$key]) == '') {
			// Just one 'responsibility.example.com'
			$result[] = '  \'' . $key . '\',';
		} else {
			// One subdomain-or-host, or several ones
			$subs = array();
			foreach(array_keys($trie[$key]) as $sub) {
				if ($sub == '') {
					$subs[] = $key;
				} else {
					$subs[] = $sub . '.' . $key;
				}
			}
			$result[] = '  \'' . $key . '\' => \'' . implode(', ', $subs) . '\',';
		}
		unset($trie[$key]);
	}
	return
		'array (' . "\n" .
			implode("\n", $result) . "\n" .
		')';
}


// Check responsibility-root of the FQDN
// 'foo.bar.example.com'        => 'example.com'        (.com        has the last whois for it)
// 'foo.bar.example.au'         => 'example.au'         (.au         has the last whois for it)
// 'foo.bar.example.edu.au'     => 'example.edu.au'     (.edu.au     has the last whois for it)
// 'foo.bar.example.act.edu.au' => 'example.act.edu.au' (.act.edu.au has the last whois for it)
function whois_responsibility($fqdn = 'foo.bar.example.com', $parent = FALSE, $implicit = TRUE)
{
	static $domain;

	if ($fqdn === NULL) {
		$domain = NULL;	// Unset
		return '';
	}
	if (! is_string($fqdn)) return '';

	if (is_ip($fqdn)) return $fqdn;

 	if (! isset($domain)) {
		$domain = array();
 		if (file_exists(DOMAIN_INI_FILE)) {
			include(DOMAIN_INI_FILE);	// Set
		}
	}

	$result  = array();
	$dcursor = & $domain;
	$array   = array_reverse(explode('.', $fqdn));
	$i = 0;
	while(TRUE) {
		if (! isset($array[$i])) break;
		$acursor = $array[$i];
		if (is_array($dcursor) && isset($dcursor[$acursor])) {
			$result[] = & $array[$i];
			$dcursor  = & $dcursor[$acursor];
		} else {
			if (! $parent && isset($acursor)) {
				$result[] = & $array[$i];	// Whois servers must know this subdomain
			}
			break;
		}
		++$i;
	}

	// Implicit responsibility: Top-Level-Domains must not be yours
	// 'bar.foo.something' => 'foo.something'
	if ($implicit && count($result) == 1 && count($array) > 1) {
		$result[] = & $array[1];
	}

	return $result ? implode('.', array_reverse($result)) : '';
}


// ---------------------
// Exit

// Freeing memories
function spam_dispose()
{
	get_blocklist(NULL);
	whois_responsibility(NULL);
}

// Common bahavior for blocking
// NOTE: Call this function from various blocking feature, to disgueise the reason 'why blocked'
function spam_exit($mode = '', $data = array())
{
	$exit = TRUE;

	switch ($mode) {
		case '':
			echo("\n");
			break;
		case 'dump':
			echo('<pre>' . "\n");
			echo htmlspecialchars(var_export($data, TRUE));
			echo('</pre>' . "\n");
			break;
	};

	if ($exit) exit;	// Force exit
}


// ---------------------
// Simple filtering

// TODO: Record them
// Simple/fast spam filter ($target: 'a string' or an array())
function pkwk_spamfilter($action, $page, $target = array('title' => ''), $method = array(), $exitmode = '')
{
	$progress = check_uri_spam($target, $method);

	if (empty($progress['is_spam'])) {
		spam_dispose();
	} else {

// TODO: detect encoding from $target for mbstring functions
//		$tmp = array();
//		foreach(array_keys($target) as $key) {
//			$tmp[strings($key, 0, FALSE, TRUE)] = strings($target[$key], 0, FALSE, TRUE);	// Removing "\0" etc
//		}
//		$target = & $tmp;

		pkwk_spamnotify($action, $page, $target, $progress, $method);
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

	$tmp = summarize_detail_badhost($progress);
	if ($tmp != '') $summary['DETAIL_BADHOST'] = $tmp;

	$tmp = summarize_detail_newtral($progress);
	if (! $asap && $tmp != '') $summary['DETAIL_NEUTRAL_HOST'] = $tmp;

	$summary['COMMENT'] = $action;
	$summary['PAGE']    = '[blocked] ' . (is_pagename($page) ? $page : '');
	$summary['URI']     = get_script_uri() . '?' . rawurlencode($page);
	$summary['USER_AGENT']  = TRUE;
	$summary['REMOTE_ADDR'] = TRUE;
	pkwk_mail_notify($notify_subject,  var_export($target, TRUE), $summary, TRUE);
}

?>
