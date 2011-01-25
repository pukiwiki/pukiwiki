<?php
// $Id: spam_util.php,v 1.2 2011/01/25 14:00:15 henoheno Exp $
// Copyright (C) 2006-2009, 2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Functions for Concept-work of spam-uri metrics


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


if (! function_exists('htmlsc')) {
	// Interface with PukiWiki
	//if (! defined('CONTENT_CHARSET')) define('CONTENT_CHARSET', 'ISO-8859-1');

	// Sugar with default settings
	function htmlsc($string = '', $flags = ENT_QUOTES, $charset = CONTENT_CHARSET)
	{
		return htmlspecialchars($string, $flags, $charset);     // htmlsc()
	}
}

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

?>
