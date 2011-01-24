<?php
// $Id: spam.php,v 1.31.2.3 2011/01/24 15:34:39 henoheno Exp $
// Copyright (C) 2006-2009, 2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Functions for Concept-work of spam-uri metrics
//
// (PHP 4 >= 4.3.0): preg_match_all(PREG_OFFSET_CAPTURE): $method['uri_XXX'] related feature


if (! defined('LIB_DIR'))   define('LIB_DIR', './');
require(LIB_DIR . 'spam_pickup.php');
require(LIB_DIR . 'spam_util.php');

if (! defined('SPAM_INI_FILE'))   define('SPAM_INI_FILE',   'spam.ini.php');


// ---------------------
// Regex

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

 	if (mb_strpos($string, '.') === FALSE || is_ip($string)) {
		// "localhost", IPv4, etc
		return generate_glob_regex($string, $divider);
	}

	// FQDN or something
	$part = explode('.', $string, 2);
	if ($part[0] == '') {
		// ".example.org"
		$part[0] = '(?:.*\.)?';
	} else if ($part[0] == '*') {
		// "*.example.org"
		$part[0] = '.*\.';
	} else {
		// example.org, etc
		return generate_glob_regex($string, $divider);
	}

	$part[1] = generate_glob_regex($part[1], $divider);

	return implode('', $part);
}


// ---------------------
// Load

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
		$regex = generate_host_regex($value, '#');
		if (! empty($regex)) {
			$array[$value] = '#^' . $regex . '$#i';
		}
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

	if (! $asap || ! $is_spam) {
	
		// Method pickup
		$_method = array();
		foreach(array(
				'area_anchor',	// There's HTML anchor tag
				'area_bbcode',	// There's 'BBCode' linking tag
			) as $key) {
			if (isset($method[$key])) $_method[$key] = TRUE;
		}

		if ($_method) {
			$_asap   = isset($method['asap']) ? array('asap' => TRUE) : array();
			$_result = area_pickup($target, $_method + $_asap);
			$_asap   = NULL;
		} else {
			$_result = FALSE;
		}

		if ($_result) {
			foreach(array_keys($_method) as $key) {
				if (isset($_result[$key])) {
					$sum[$key] = $_result[$key];
					if (isset($method[$key]) && $sum[$key] > $method[$key]) {
						$is_spam[$key] = TRUE;
					}
				}
			}
		}

		unset($_asap, $_method, $_result);
	}

	// Return if ...
	if ($asap && $is_spam) return $progress;

	// ----------------------------------------
	// URI: Pickup

	$pickups = spam_uri_pickup($target, $method);


	// Return if ...
	if (empty($pickups)) return $progress;

	// Normalize all
	$pickups = uri_pickup_normalize($pickups);

	// ----------------------------------------
	// Pickup some part of URI

	$hosts = array();
	foreach ($pickups as $key => $pickup) {
		$hosts[$key] = & $pickup['host'];
	}

	// ----------------------------------------
	// URI: Bad host <pre-filter> (Separate good/bad hosts from $hosts)

	if ((! $asap || ! $is_spam) && isset($method['badhost'])) {
		$list    = get_blocklist('pre');
		$blocked = blocklist_distiller($hosts, array_keys($list), $asap);
		foreach($list as $key => $type){
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
					$subs[] = $key;			// 'example.com'
				} else {
					$subs[] = $sub . '. ';	// 'A.foo.bar. '
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
			echo htmlsc(var_export($data, TRUE));
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
