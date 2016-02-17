<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// file.php
// Copyright (C)
//   2002-2016 PukiWiki Development Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// File related functions

// RecentChanges
define('PKWK_MAXSHOW_ALLOWANCE', 10);
define('PKWK_MAXSHOW_CACHE', 'recent.dat');

// AutoLink
define('PKWK_AUTOLINK_REGEX_CACHE', 'autolink.dat');

// Get source(wiki text) data of the page
// Returns FALSE if error occurerd
function get_source($page = NULL, $lock = TRUE, $join = FALSE)
{
	//$result = NULL;	// File is not found
	$result = $join ? '' : array();
		// Compat for "implode('', get_source($file))",
		// 	-- this is slower than "get_source($file, TRUE, TRUE)"
		// Compat for foreach(get_source($file) as $line) {} not to warns

	$path = get_filename($page);
	if (file_exists($path)) {

		if ($lock) {
			$fp = @fopen($path, 'r');
			if ($fp === FALSE) return FALSE;
			flock($fp, LOCK_SH);
		}

		if ($join) {
			// Returns a value
			$size = filesize($path);
			if ($size === FALSE) {
				$result = FALSE;
			} else if ($size == 0) {
				$result = '';
			} else {
				$result = fread($fp, $size);
				if ($result !== FALSE) {
					// Removing line-feeds
					$result = str_replace("\r", '', $result);
				}
			}
		} else {
			// Returns an array
			$result = file($path);
			if ($result !== FALSE) {
				// Removing line-feeds
				$result = str_replace("\r", '', $result);
			}
		}

		if ($lock) {
			flock($fp, LOCK_UN);
			@fclose($fp);
		}
	}

	return $result;
}

// Get last-modified filetime of the page
function get_filetime($page)
{
	return is_page($page) ? filemtime(get_filename($page)) - LOCALZONE : 0;
}

// Get physical file name of the page
function get_filename($page)
{
	return DATA_DIR . encode($page) . '.txt';
}

// Put a data(wiki text) into a physical file(diff, backup, text)
function page_write($page, $postdata, $notimestamp = FALSE)
{
	if (PKWK_READONLY) return; // Do nothing

	$postdata = make_str_rules($postdata);
	$text_without_author = remove_author_info($postdata);
	$postdata = add_author_info($text_without_author);
	$is_delete = empty($text_without_author);

	// Do nothing when it has no changes
	$oldpostdata = is_page($page) ? join('', get_source($page)) : '';
	$oldtext_without_author = remove_author_info($oldpostdata);
	if ($text_without_author === $oldtext_without_author) {
		// Do nothing on updating with unchanged content
		return;
	}
	// Create and write diff
	$diffdata    = do_diff($oldpostdata, $postdata);
	file_write(DIFF_DIR, $page, $diffdata);

	// Create backup
	make_backup($page, $is_delete, $postdata); // Is $postdata null?

	// Create wiki text
	file_write(DATA_DIR, $page, $postdata, $notimestamp, $is_delete);

	links_update($page);
}

// Modify original text with user-defined / system-defined rules
function make_str_rules($source)
{
	global $str_rules, $fixed_heading_anchor;

	$lines = explode("\n", $source);
	$count = count($lines);

	$modify    = TRUE;
	$multiline = 0;
	$matches   = array();
	for ($i = 0; $i < $count; $i++) {
		$line = & $lines[$i]; // Modify directly

		// Ignore null string and preformatted texts
		if ($line == '' || $line{0} == ' ' || $line{0} == "\t") continue;

		// Modify this line?
		if ($modify) {
			if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
			    $multiline == 0 &&
			    preg_match('/#[^{]*(\{\{+)\s*$/', $line, $matches)) {
			    	// Multiline convert plugin start
				$modify    = FALSE;
				$multiline = strlen($matches[1]); // Set specific number
			}
		} else {
			if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
			    $multiline != 0 &&
			    preg_match('/^\}{' . $multiline . '}\s*$/', $line)) {
			    	// Multiline convert plugin end
				$modify    = TRUE;
				$multiline = 0;
			}
		}
		if ($modify === FALSE) continue;

		// Replace with $str_rules
		foreach ($str_rules as $pattern => $replacement)
			$line = preg_replace('/' . $pattern . '/', $replacement, $line);
		
		// Adding fixed anchor into headings
		if ($fixed_heading_anchor &&
		    preg_match('/^(\*{1,3}.*?)(?:\[#([A-Za-z][\w-]*)\]\s*)?$/', $line, $matches) &&
		    (! isset($matches[2]) || $matches[2] == '')) {
			// Generate unique id
			$anchor = generate_fixed_heading_anchor_id($matches[1]);
			$line = rtrim($matches[1]) . ' [#' . $anchor . ']';
		}
	}

	// Multiline part has no stopper
	if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
	    $modify === FALSE && $multiline != 0)
		$lines[] = str_repeat('}', $multiline);

	return implode("\n", $lines);
}

function add_author_info($wikitext)
{
	global $auth_user, $auth_user_fullname;
	$author = preg_replace('/"/', '', $auth_user);
	$fullname = $auth_user_fullname;
	if (!$fullname && $author) {
		// Fullname is empty, use $author as its fullname
		$fullname = preg_replace('/^[^:]*:/', '', $author);
	}
	$displayname = preg_replace('/"/', '', $fullname);
	$user_prefix = get_auth_user_prefix();
	$author_text = sprintf('#author("%s","%s","%s")',
		get_date_atom(UTIME + LOCALZONE),
		($author ? $user_prefix . $author : ''),
		$displayname) . "\n";
	return $author_text . $wikitext;
}

function remove_author_info($wikitext)
{
	return preg_replace('/^\s*#author\([^\n]*(\n|$)/m', '', $wikitext);
}

function get_date_atom($timestamp)
{
	// Compatible with DATE_ATOM format
	// return date(DATE_ATOM, $timestamp);
	$zmin = abs(LOCALZONE / 60);
	return date('Y-m-d\TH:i:s', $timestamp) . sprintf('%s%02d:%02d',
		(LOCALZONE < 0 ? '-' : '+') , $zmin / 60, $zmin % 60);
}

// Generate ID
function generate_fixed_heading_anchor_id($seed)
{
	// A random alphabetic letter + 7 letters of random strings from md()
	return chr(mt_rand(ord('a'), ord('z'))) .
		substr(md5(uniqid(substr($seed, 0, 100), TRUE)),
		mt_rand(0, 24), 7);
}

// Read top N lines as an array
// (Use PHP file() function if you want to get ALL lines)
function file_head($file, $count = 1, $lock = TRUE, $buffer = 8192)
{
	$array = array();

	$fp = @fopen($file, 'r');
	if ($fp === FALSE) return FALSE;
	set_file_buffer($fp, 0);
	if ($lock) flock($fp, LOCK_SH);
	rewind($fp);
	$index = 0;
	while (! feof($fp)) {
		$line = fgets($fp, $buffer);
		if ($line != FALSE) $array[] = $line;
		if (++$index >= $count) break;
	}
	if ($lock) flock($fp, LOCK_UN);
	if (! fclose($fp)) return FALSE;

	return $array;
}

// Output to a file
function file_write($dir, $page, $str, $notimestamp = FALSE, $is_delete = FALSE)
{
	global $_msg_invalidiwn, $notify, $notify_diff_only, $notify_subject;
	global $whatsdeleted, $maxshow_deleted;

	if (PKWK_READONLY) return; // Do nothing
	if ($dir != DATA_DIR && $dir != DIFF_DIR) die('file_write(): Invalid directory');

	$page = strip_bracket($page);
	$file = $dir . encode($page) . '.txt';
	$file_exists = file_exists($file);

	// ----
	// Delete?

	if ($dir == DATA_DIR && $is_delete) {
		// Page deletion
		if (! $file_exists) return; // Ignore null posting for DATA_DIR

		// Update RecentDeleted (Add the $page)
		add_recent($page, $whatsdeleted, '', $maxshow_deleted);

		// Remove the page
		unlink($file);

		// Update RecentDeleted, and remove the page from RecentChanges
		lastmodified_add($whatsdeleted, $page);

		// Clear is_page() cache
		is_page($page, TRUE);

		return;

	} else if ($dir == DIFF_DIR && $str === " \n") {
		return; // Ignore null posting for DIFF_DIR
	}

	// ----
	// File replacement (Edit)

	if (! is_pagename($page))
		die_message(str_replace('$1', htmlsc($page),
		            str_replace('$2', 'WikiName', $_msg_invalidiwn)));

	$str = rtrim(preg_replace('/' . "\r" . '/', '', $str)) . "\n";
	$timestamp = ($file_exists && $notimestamp) ? filemtime($file) : FALSE;

	$fp = fopen($file, 'a') or die('fopen() failed: ' .
		htmlsc(basename($dir) . '/' . encode($page) . '.txt') .	
		'<br />' . "\n" .
		'Maybe permission is not writable or filename is too long');
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	ftruncate($fp, 0);
	rewind($fp);
	fputs($fp, $str);
	flock($fp, LOCK_UN);
	fclose($fp);

	if ($timestamp) pkwk_touch_file($file, $timestamp);

	// Optional actions
	if ($dir == DATA_DIR) {
		// Update RecentChanges (Add or renew the $page)
		if ($timestamp === FALSE) lastmodified_add($page);

		// Command execution per update
		if (defined('PKWK_UPDATE_EXEC') && PKWK_UPDATE_EXEC)
			system(PKWK_UPDATE_EXEC . ' > /dev/null &');

	} else if ($dir == DIFF_DIR && $notify) {
		if ($notify_diff_only) $str = preg_replace('/^[^-+].*\n/m', '', $str);
		$footer['ACTION'] = 'Page update';
		$footer['PAGE']   = & $page;
		$footer['URI']    = get_script_uri() . '?' . pagename_urlencode($page);
		$footer['USER_AGENT']  = TRUE;
		$footer['REMOTE_ADDR'] = TRUE;
		pkwk_mail_notify($notify_subject, $str, $footer) or
			die('pkwk_mail_notify(): Failed');
	}

	is_page($page, TRUE); // Clear is_page() cache
}

// Update RecentDeleted
function add_recent($page, $recentpage, $subject = '', $limit = 0)
{
	if (PKWK_READONLY || $limit == 0 || $page == '' || $recentpage == '' ||
	    check_non_list($page)) return;

	// Load
	$lines = $matches = array();
	foreach (get_source($recentpage) as $line)
		if (preg_match('/^-(.+) - (\[\[.+\]\])$/', $line, $matches))
			$lines[$matches[2]] = $line;

	$_page = '[[' . $page . ']]';

	// Remove a report about the same page
	if (isset($lines[$_page])) unset($lines[$_page]);

	// Add
	array_unshift($lines, '-' . format_date(UTIME) . ' - ' . $_page .
		htmlsc($subject) . "\n");

	// Get latest $limit reports
	$lines = array_splice($lines, 0, $limit);

	// Update
	$fp = fopen(get_filename($recentpage), 'w') or
		die_message('Cannot write page file ' .
		htmlsc($recentpage) .
		'<br />Maybe permission is not writable or filename is too long');
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	fputs($fp, '#freeze'    . "\n");
	fputs($fp, '#norelated' . "\n"); // :)
	fputs($fp, join('', $lines));
	flock($fp, LOCK_UN);
	fclose($fp);
}

// Update PKWK_MAXSHOW_CACHE itself (Add or renew about the $page) (Light)
// Use without $autolink
function lastmodified_add($update = '', $remove = '')
{
	global $maxshow, $whatsnew, $autolink;

	// AutoLink implimentation needs everything, for now
	if ($autolink) {
		put_lastmodified(); // Try to (re)create ALL
		return;
	}

	if (($update == '' || check_non_list($update)) && $remove == '')
		return; // No need

	$file = CACHE_DIR . PKWK_MAXSHOW_CACHE;
	if (! file_exists($file)) {
		put_lastmodified(); // Try to (re)create ALL
		return;
	}

	// Open
	pkwk_touch_file($file);
	$fp = fopen($file, 'r+') or
		die_message('Cannot open ' . 'CACHE_DIR/' . PKWK_MAXSHOW_CACHE);
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);

	// Read (keep the order of the lines)
	$recent_pages = $matches = array();
	foreach(file_head($file, $maxshow + PKWK_MAXSHOW_ALLOWANCE, FALSE) as $line)
		if (preg_match('/^([0-9]+)\t(.+)/', $line, $matches))
			$recent_pages[$matches[2]] = $matches[1];

	// Remove if it exists inside
	if (isset($recent_pages[$update])) unset($recent_pages[$update]);
	if (isset($recent_pages[$remove])) unset($recent_pages[$remove]);

	// Add to the top: like array_unshift()
	if ($update != '')
		$recent_pages = array($update => get_filetime($update)) + $recent_pages;

	// Check
	$abort = count($recent_pages) < $maxshow;

	if (! $abort) {
		// Write
		ftruncate($fp, 0);
		rewind($fp);
		foreach ($recent_pages as $_page=>$time)
			fputs($fp, $time . "\t" . $_page . "\n");
	}

	flock($fp, LOCK_UN);
	fclose($fp);

	if ($abort) {
		put_lastmodified(); // Try to (re)create ALL
		return;
	}



	// ----
	// Update the page 'RecentChanges'

	$recent_pages = array_splice($recent_pages, 0, $maxshow);
	$file = get_filename($whatsnew);

	// Open
	pkwk_touch_file($file);
	$fp = fopen($file, 'r+') or
		die_message('Cannot open ' . htmlsc($whatsnew));
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);

	// Recreate
	ftruncate($fp, 0);
	rewind($fp);
	foreach ($recent_pages as $_page=>$time)
		fputs($fp, '-' . htmlsc(format_date($time)) .
			' - ' . '[[' . htmlsc($_page) . ']]' . "\n");
	fputs($fp, '#norelated' . "\n"); // :)

	flock($fp, LOCK_UN);
	fclose($fp);
}

// Re-create PKWK_MAXSHOW_CACHE (Heavy)
function put_lastmodified()
{
	global $maxshow, $whatsnew, $autolink;

	if (PKWK_READONLY) return; // Do nothing

	// Get WHOLE page list
	$pages = get_existpages();

	// Check ALL filetime
	$recent_pages = array();
	foreach($pages as $page)
		if ($page !== $whatsnew && ! check_non_list($page))
			$recent_pages[$page] = get_filetime($page);

	// Sort decending order of last-modification date
	arsort($recent_pages, SORT_NUMERIC);

	// Cut unused lines
	// BugTrack2/179: array_splice() will break integer keys in hashtable
	$count   = $maxshow + PKWK_MAXSHOW_ALLOWANCE;
	$_recent = array();
	foreach($recent_pages as $key=>$value) {
		unset($recent_pages[$key]);
		$_recent[$key] = $value;
		if (--$count < 1) break;
	}
	$recent_pages = & $_recent;

	// Re-create PKWK_MAXSHOW_CACHE
	$file = CACHE_DIR . PKWK_MAXSHOW_CACHE;
	pkwk_touch_file($file);
	$fp = fopen($file, 'r+') or
		die_message('Cannot open' . 'CACHE_DIR/' . PKWK_MAXSHOW_CACHE);
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	ftruncate($fp, 0);
	rewind($fp);
	foreach ($recent_pages as $page=>$time)
		fputs($fp, $time . "\t" . $page . "\n");
	flock($fp, LOCK_UN);
	fclose($fp);

	// Create RecentChanges
	$file = get_filename($whatsnew);
	pkwk_touch_file($file);
	$fp = fopen($file, 'r+') or
		die_message('Cannot open ' . htmlsc($whatsnew));
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	ftruncate($fp, 0);
	rewind($fp);
	foreach (array_keys($recent_pages) as $page) {
		$time      = $recent_pages[$page];
		$s_lastmod = htmlsc(format_date($time));
		$s_page    = htmlsc($page);
		fputs($fp, '-' . $s_lastmod . ' - [[' . $s_page . ']]' . "\n");
	}
	fputs($fp, '#norelated' . "\n"); // :)
	flock($fp, LOCK_UN);
	fclose($fp);

	// For AutoLink
	if ($autolink) {
		list($pattern, $pattern_a, $forceignorelist) =
			get_autolink_pattern($pages);

		$file = CACHE_DIR . PKWK_AUTOLINK_REGEX_CACHE;
		pkwk_touch_file($file);
		$fp = fopen($file, 'r+') or
			die_message('Cannot open ' . 'CACHE_DIR/' . PKWK_AUTOLINK_REGEX_CACHE);
		set_file_buffer($fp, 0);
		flock($fp, LOCK_EX);
		ftruncate($fp, 0);
		rewind($fp);
		fputs($fp, $pattern   . "\n");
		fputs($fp, $pattern_a . "\n");
		fputs($fp, join("\t", $forceignorelist) . "\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}

// Get elapsed date of the page
function get_pg_passage($page, $sw = TRUE)
{
	global $show_passage;
	if (! $show_passage) return '';

	$time = get_filetime($page);
	$pg_passage = ($time != 0) ? get_passage($time) : '';

	return $sw ? '<small>' . $pg_passage . '</small>' : ' ' . $pg_passage;
}

// Last-Modified header
function header_lastmod($page = NULL)
{
	global $lastmod;

	if ($lastmod && is_page($page)) {
		pkwk_headers_sent();
		header('Last-Modified: ' .
			date('D, d M Y H:i:s', get_filetime($page)) . ' GMT');
	}
}

// Get a list of encoded files (must specify a directory and a suffix)
function get_existfiles($dir = DATA_DIR, $ext = '.txt')
{
	$aryret = array();
	$pattern = '/^(?:[0-9A-F]{2})+' . preg_quote($ext, '/') . '$/';

	$dp = @opendir($dir) or die_message($dir . ' is not found or not readable.');
	while (($file = readdir($dp)) !== FALSE) {
		if (preg_match($pattern, $file)) {
			$aryret[] = $dir . $file;
		}
	}
	closedir($dp);

	return $aryret;
}

// Get a page list of this wiki
function get_existpages($dir = DATA_DIR, $ext = '.txt')
{
	$aryret = array();
	$pattern = '/^((?:[0-9A-F]{2})+)' . preg_quote($ext, '/') . '$/';

	$dp = @opendir($dir) or die_message($dir . ' is not found or not readable.');
	$matches = array();
	while (($file = readdir($dp)) !== FALSE) {
		if (preg_match($pattern, $file, $matches)) {
			$aryret[$file] = decode($matches[1]);
		}
	}
	closedir($dp);

	return $aryret;
}

// Get PageReading(pronounce-annotated) data in an array()
function get_readings()
{
	global $pagereading_enable, $pagereading_kanji2kana_converter;
	global $pagereading_kanji2kana_encoding, $pagereading_chasen_path;
	global $pagereading_kakasi_path, $pagereading_config_page;
	global $pagereading_config_dict;

	$pages = get_existpages();

	$readings = array();
	foreach ($pages as $page) 
		$readings[$page] = '';

	$deletedPage = FALSE;
	$matches = array();
	foreach (get_source($pagereading_config_page) as $line) {
		$line = chop($line);
		if(preg_match('/^-\[\[([^]]+)\]\]\s+(.+)$/', $line, $matches)) {
			if(isset($readings[$matches[1]])) {
				// This page is not clear how to be pronounced
				$readings[$matches[1]] = $matches[2];
			} else {
				// This page seems deleted
				$deletedPage = TRUE;
			}
		}
	}

	// If enabled ChaSen/KAKASI execution
	if($pagereading_enable) {

		// Check there's non-clear-pronouncing page
		$unknownPage = FALSE;
		foreach ($readings as $page => $reading) {
			if($reading == '') {
				$unknownPage = TRUE;
				break;
			}
		}

		// Execute ChaSen/KAKASI, and get annotation
		if($unknownPage) {
			switch(strtolower($pagereading_kanji2kana_converter)) {
			case 'chasen':
				if(! file_exists($pagereading_chasen_path))
					die_message('ChaSen not found: ' . $pagereading_chasen_path);

				$tmpfname = tempnam(realpath(CACHE_DIR), 'PageReading');
				$fp = fopen($tmpfname, 'w') or
					die_message('Cannot write temporary file "' . $tmpfname . '".' . "\n");
				foreach ($readings as $page => $reading) {
					if($reading != '') continue;
					fputs($fp, mb_convert_encoding($page . "\n",
						$pagereading_kanji2kana_encoding, SOURCE_ENCODING));
				}
				fclose($fp);

				$chasen = "$pagereading_chasen_path -F %y $tmpfname";
				$fp     = popen($chasen, 'r');
				if($fp === FALSE) {
					unlink($tmpfname);
					die_message('ChaSen execution failed: ' . $chasen);
				}
				foreach ($readings as $page => $reading) {
					if($reading != '') continue;

					$line = fgets($fp);
					$line = mb_convert_encoding($line, SOURCE_ENCODING,
						$pagereading_kanji2kana_encoding);
					$line = chop($line);
					$readings[$page] = $line;
				}
				pclose($fp);

				unlink($tmpfname) or
					die_message('Temporary file can not be removed: ' . $tmpfname);
				break;

			case 'kakasi':	/*FALLTHROUGH*/
			case 'kakashi':
				if(! file_exists($pagereading_kakasi_path))
					die_message('KAKASI not found: ' . $pagereading_kakasi_path);

				$tmpfname = tempnam(realpath(CACHE_DIR), 'PageReading');
				$fp       = fopen($tmpfname, 'w') or
					die_message('Cannot write temporary file "' . $tmpfname . '".' . "\n");
				foreach ($readings as $page => $reading) {
					if($reading != '') continue;
					fputs($fp, mb_convert_encoding($page . "\n",
						$pagereading_kanji2kana_encoding, SOURCE_ENCODING));
				}
				fclose($fp);

				$kakasi = "$pagereading_kakasi_path -kK -HK -JK < $tmpfname";
				$fp     = popen($kakasi, 'r');
				if($fp === FALSE) {
					unlink($tmpfname);
					die_message('KAKASI execution failed: ' . $kakasi);
				}

				foreach ($readings as $page => $reading) {
					if($reading != '') continue;

					$line = fgets($fp);
					$line = mb_convert_encoding($line, SOURCE_ENCODING,
						$pagereading_kanji2kana_encoding);
					$line = chop($line);
					$readings[$page] = $line;
				}
				pclose($fp);

				unlink($tmpfname) or
					die_message('Temporary file can not be removed: ' . $tmpfname);
				break;

			case 'none':
				$patterns = $replacements = $matches = array();
				foreach (get_source($pagereading_config_dict) as $line) {
					$line = chop($line);
					if(preg_match('|^ /([^/]+)/,\s*(.+)$|', $line, $matches)) {
						$patterns[]     = $matches[1];
						$replacements[] = $matches[2];
					}
				}
				foreach ($readings as $page => $reading) {
					if($reading != '') continue;

					$readings[$page] = $page;
					foreach ($patterns as $no => $pattern)
						$readings[$page] = mb_convert_kana(mb_ereg_replace($pattern,
							$replacements[$no], $readings[$page]), 'aKCV');
				}
				break;

			default:
				die_message('Unknown kanji-kana converter: ' . $pagereading_kanji2kana_converter . '.');
				break;
			}
		}

		if($unknownPage || $deletedPage) {

			asort($readings, SORT_STRING); // Sort by pronouncing(alphabetical/reading) order
			$body = '';
			foreach ($readings as $page => $reading)
				$body .= '-[[' . $page . ']] ' . $reading . "\n";

			page_write($pagereading_config_page, $body);
		}
	}

	// Pages that are not prounouncing-clear, return pagenames of themselves
	foreach ($pages as $page) {
		if($readings[$page] == '')
			$readings[$page] = $page;
	}

	return $readings;
}

// Get a list of related pages of the page
function links_get_related($page)
{
	global $vars, $related;
	static $links = array();

	if (isset($links[$page])) return $links[$page];

	// If possible, merge related pages generated by make_link()
	$links[$page] = ($page === $vars['page']) ? $related : array();

	// Get repated pages from DB
	$links[$page] += links_get_related_db($vars['page']);

	return $links[$page];
}

// _If needed_, re-create the file to change/correct ownership into PHP's
// NOTE: Not works for Windows
function pkwk_chown($filename, $preserve_time = TRUE)
{
	static $php_uid; // PHP's UID

	if (! isset($php_uid)) {
		if (extension_loaded('posix')) {
			$php_uid = posix_getuid(); // Unix
		} else {
			$php_uid = 0; // Windows
		}
	}

	// Lock for pkwk_chown()
	$lockfile = CACHE_DIR . 'pkwk_chown.lock';
	$flock = fopen($lockfile, 'a') or
		die('pkwk_chown(): fopen() failed for: CACHEDIR/' .
			basename(htmlsc($lockfile)));
	flock($flock, LOCK_EX) or die('pkwk_chown(): flock() failed for lock');

	// Check owner
	$stat = stat($filename) or
		die('pkwk_chown(): stat() failed for: '  . basename(htmlsc($filename)));
	if ($stat[4] === $php_uid) {
		// NOTE: Windows always here
		$result = TRUE; // Seems the same UID. Nothing to do
	} else {
		$tmp = $filename . '.' . getmypid() . '.tmp';

		// Lock source $filename to avoid file corruption
		// NOTE: Not 'r+'. Don't check write permission here
		$ffile = fopen($filename, 'r') or
			die('pkwk_chown(): fopen() failed for: ' .
				basename(htmlsc($filename)));

		// Try to chown by re-creating files
		// NOTE:
		//   * touch() before copy() is for 'rw-r--r--' instead of 'rwxr-xr-x' (with umask 022).
		//   * (PHP 4 < PHP 4.2.0) touch() with the third argument is not implemented and retuns NULL and Warn.
		//   * @unlink() before rename() is for Windows but here's for Unix only
		flock($ffile, LOCK_EX) or die('pkwk_chown(): flock() failed');
		$result = touch($tmp) && copy($filename, $tmp) &&
			($preserve_time ? (touch($tmp, $stat[9], $stat[8]) || touch($tmp, $stat[9])) : TRUE) &&
			rename($tmp, $filename);
		flock($ffile, LOCK_UN) or die('pkwk_chown(): flock() failed');

		fclose($ffile) or die('pkwk_chown(): fclose() failed');

		if ($result === FALSE) @unlink($tmp);
	}

	// Unlock for pkwk_chown()
	flock($flock, LOCK_UN) or die('pkwk_chown(): flock() failed for lock');
	fclose($flock) or die('pkwk_chown(): fclose() failed for lock');

	return $result;
}

// touch() with trying pkwk_chown()
function pkwk_touch_file($filename, $time = FALSE, $atime = FALSE)
{
	// Is the owner incorrected and unable to correct?
	if (! file_exists($filename) || pkwk_chown($filename)) {
		if ($time === FALSE) {
			$result = touch($filename);
		} else if ($atime === FALSE) {
			$result = touch($filename, $time);
		} else {
			$result = touch($filename, $time, $atime);
		}
		return $result;
	} else {
		die('pkwk_touch_file(): Invalid UID and (not writable for the directory or not a flie): ' .
			htmlsc(basename($filename)));
	}
}
