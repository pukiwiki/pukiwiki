<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: file.php,v 1.13.2.4 2005/05/29 18:47:05 teanan Exp $
//
// File related functions

// Get source(wiki text) data of the page
function get_source($page = NULL)
{
	// Removing line-feeds: Because file() doesn't remove them.
	return is_page($page) ? str_replace("\r", '', file(get_filename($page))) : array();
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
	global $trackback;
	global $autoalias, $aliaspage;

	if (PKWK_READONLY) return; // Do nothing

	$postdata = make_str_rules($postdata);

	// Create and write diff
	$oldpostdata = is_page($page) ? join('', get_source($page)) : '';
	$diffdata    = do_diff($oldpostdata, $postdata);
	file_write(DIFF_DIR, $page, $diffdata);

	// Create backup
	make_backup($page, $postdata == ''); // Is $postdata null?

	// Create wiki text
	file_write(DATA_DIR, $page, $postdata, $notimestamp);

	if ($trackback) {
		// TrackBack Ping
		$_diff = explode("\n", $diffdata);
		$plus  = join("\n", preg_replace('/^\+/', '', preg_grep('/^\+/', $_diff)));
		$minus = join("\n", preg_replace('/^-/',  '', preg_grep('/^-/',  $_diff)));
		tb_send($page, $plus, $minus);
	}

	links_update($page);

	// for AutoAlias
	if ($autoalias>0 && $page==$aliaspage) {
		// AutoAliasName is updated
		$pages = array_keys(get_autoaliases());
		if(count($pages)>0) {
			autolink_pattern_write(CACHE_DIR . 'autoalias.dat',
				get_autolink_pattern($pages, $autoalias));
		} else {
			@unlink(CACHE_DIR . 'autoalias.dat');
		}
	}
}

// User-defined rules (replace the source)
function make_str_rules($str)
{
	global $str_rules, $fixed_heading_anchor;

	$arr = explode("\n", $str);

	$retvars = $matches = array();
	foreach ($arr as $str) {
		if ($str != '' && $str{0} != ' ' && $str{0} != "\t")
			foreach ($str_rules as $rule => $replace)
				$str = preg_replace('/' . $rule . '/', $replace, $str);
		
		// Adding fixed anchor into headings
		if ($fixed_heading_anchor &&
			preg_match('/^(\*{1,3}(.(?!\[#[A-Za-z][\w-]+\]))+)$/', $str, $matches))
		{
			// Generate ID:
			// A random alphabetic letter + 7 letters of random strings from md()
			$anchor = chr(mt_rand(ord('a'), ord('z'))) .
				substr(md5(uniqid(substr($matches[1], 0, 100), 1)), mt_rand(0, 24), 7);
			$str = rtrim($matches[1]) . ' [#' . $anchor . ']';
		}
		$retvars[] = $str;
	}

	return join("\n", $retvars);
}

// Output to a file
function file_write($dir, $page, $str, $notimestamp = FALSE)
{
	global $update_exec, $_msg_invalidiwn;
	global $notify, $notify_diff_only, $notify_to, $notify_subject, $notify_header;
	global $smtp_server, $smtp_auth;
	global $whatsdeleted, $maxshow_deleted;

	if (PKWK_READONLY) return; // Do nothing

	if (! is_pagename($page))
		die_message(str_replace('$1', htmlspecialchars($page),
		            str_replace('$2', 'WikiName', $_msg_invalidiwn)));

	$page      = strip_bracket($page);
	$timestamp = FALSE;
	$file      = $dir . encode($page) . '.txt';

	if ($dir == DATA_DIR && $str == '' && file_exists($file)) {
		unlink($file);
		add_recent($page, $whatsdeleted, '', $maxshow_deleted); // RecentDeleted
	}

	if ($str != '') {
		$str = preg_replace('/' . "\r" . '/', '', $str);
		$str = rtrim($str) . "\n";

		if ($notimestamp && file_exists($file))
			$timestamp = filemtime($file) - LOCALZONE;

		$fp = fopen($file, 'w') or
			die_message('Cannot write page file or diff file or other ' .
			htmlspecialchars($page) .
			'<br />Maybe permission is not writable or filename is too long');

		set_file_buffer($fp, 0);
		flock($fp, LOCK_EX);
		rewind($fp);
		fputs($fp, $str);
		flock($fp, LOCK_UN);
		fclose($fp);
		if ($timestamp) 
			touch($file, $timestamp + LOCALZONE);
	}

	// Clear is_page() cache
	is_page($page, TRUE);

	if (! $timestamp && $dir == DATA_DIR)
		put_lastmodified();

	// Execute $update_exec here
	if ($update_exec && $dir == DATA_DIR)
		system($update_exec . ' > /dev/null &');

	if ($notify && $dir == DIFF_DIR) {
		if ($notify_diff_only) $str = preg_replace('/^[^-+].*\n/m', '', $str);
		$str .= "\n" .
			str_repeat('-', 30) . "\n" .
			'URI: ' . get_script_uri() . '?' . rawurlencode($page) . "\n" .
			'REMOTE_ADDR: ' . $_SERVER['REMOTE_ADDR'] . "\n";

 		$subject = str_replace('$page', $page, $notify_subject);
		ini_set('SMTP', $smtp_server);
 		mb_language(LANG);

		if ($smtp_auth) pop_before_smtp();
 		mb_send_mail($notify_to, $subject, $str, $notify_header);
	}
}

// Update RecentDeleted
function add_recent($page, $recentpage, $subject = '', $limit = 0)
{
	if (PKWK_READONLY || $limit == 0 || $page == '' || $recentpage == '') return;

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
		htmlspecialchars($subject) . "\n");

	// Get latest $limit reports
	$lines = array_splice($lines, 0, $limit);

	// Update
	$fp = fopen(get_filename($recentpage), 'w') or
		die_message('Cannot write page file ' .
		htmlspecialchars($recentpage) .
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

// Update RecentChanges
function put_lastmodified()
{
	global $maxshow, $whatsnew, $non_list, $autolink;

	if (PKWK_READONLY) return; // Do nothing

	$pages = get_existpages();
	$recent_pages = array();
	$non_list_pattern = '/' . $non_list . '/';
	foreach($pages as $page)
		if ($page != $whatsnew && ! preg_match($non_list_pattern, $page))
			$recent_pages[$page] = get_filetime($page);

	// Sort decending order of last-modification date
	arsort($recent_pages, SORT_NUMERIC);

	// Create recent.dat (for recent.inc.php)
	$fp = fopen(CACHE_DIR . 'recent.dat', 'w') or
		die_message('Cannot write cache file ' .
		CACHE_DIR . 'recent.dat' .
		'<br />Maybe permission is not writable or filename is too long');

	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	foreach ($recent_pages as $page=>$time)
		fputs($fp, $time . "\t" . $page . "\n");
	flock($fp, LOCK_UN);
	fclose($fp);

	// Create RecentChanges
	$fp = fopen(get_filename($whatsnew), 'w') or
		die_message('Cannot write page file ' .
		htmlspecialchars($whatsnew) .
		'<br />Maybe permission is not writable or filename is too long');

	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	foreach (array_splice(array_keys($recent_pages), 0, $maxshow) as $page) {
		$time      = $recent_pages[$page];
		$s_lastmod = htmlspecialchars(format_date($time));
		$s_page    = htmlspecialchars($page);
		fputs($fp, '-' . $s_lastmod . ' - [[' . $s_page . ']]' . "\n");
	}
	fputs($fp, '#norelated' . "\n"); // :)
	flock($fp, LOCK_UN);
	fclose($fp);

	// For AutoLink
	if ($autolink) {
		autolink_pattern_write(CACHE_DIR . 'autolink.dat',
			get_autolink_pattern($pages, $autolink));
	}
}

// update autolink data
function autolink_pattern_write($filename, $autolink_pattern)
{
	list($pattern, $pattern_a, $forceignorelist) = $autolink_pattern;

	$fp = fopen($filename, 'w') or
		die_message("Cannot write autolink file $filename<br />" .
			'Maybe permission is not writable');
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	fputs($fp, $pattern   . "\n");
	fputs($fp, $pattern_a . "\n");
	fputs($fp, join("\t", $forceignorelist) . "\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}

// Get elapsed date of the pate
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

// Get a page list of this wiki
function get_existpages($dir = DATA_DIR, $ext = '.txt')
{
	$aryret = array();

	$pattern = '((?:[0-9A-F]{2})+)';
	if ($ext != '') $ext = preg_quote($ext, '/');
	$pattern = '/^' . $pattern . $ext . '$/';

	$dp = @opendir($dir) or
		die_message($dir . ' is not found or not readable.');
	$matches = array();
	while ($file = readdir($dp))
		if (preg_match($pattern, $file, $matches))
			$aryret[$file] = decode($matches[1]);
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

				$tmpfname = tempnam(CACHE_DIR, 'PageReading');
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

				$tmpfname = tempnam(CACHE_DIR, 'PageReading');
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

			asort($readings); // Sort by pronouncing(alphabetical/reading) order
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

// Get a list of encoded files (must specify a directory and a suffix)
function get_existfiles($dir, $ext)
{
	$pattern = '/^(?:[0-9A-F]{2})+' . preg_quote($ext, '/') . '$/';
	$aryret = array();
	$dp = @opendir($dir) or die_message($dir . ' is not found or not readable.');
	while ($file = readdir($dp))
		if (preg_match($pattern, $file))
			$aryret[] = $dir . $file;
	closedir($dp);
	return $aryret;
}

// Get a list of related pages of the page
function links_get_related($page)
{
	global $vars, $related;
	static $links = array();

	if (isset($links[$page])) return $links[$page];

	// If possible, merge related pages generated by make_link()
	$links[$page] = ($page == $vars['page']) ? $related : array();

	// Get repated pages from DB
	$links[$page] += links_get_related_db($vars['page']);

	return $links[$page];
}
?>
