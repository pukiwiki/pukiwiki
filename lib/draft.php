<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// draft.php
// Copyright
//   2024 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Draft related functions
//
// Version 1.1.0
//
// [Changelog]
// 1.1.0 (2025-11-15): Add access control support
//   - Used by draft.inc.php with access restrictions
// 1.0.0 (2025-11-15): Initial release
//   - Core draft management functions

/**
 * Check if draft exists for the page
 *
 * @param $page page name
 * @return bool true if draft exists
 */
function has_draft($page)
{
	$file = get_draft_filename($page);
	return file_exists($file);
}

/**
 * Get draft content only (backward compatible wrapper)
 *
 * @param $page page name
 * @param $lock lock
 * @param $join true: return string, false: return array of string
 * @return mixed draft data or FALSE if error occurred
 */
function get_draft($page, $lock = TRUE, $join = FALSE)
{
	$result = get_draft_with_meta($page, $lock, $join);
	if ($result === FALSE) return FALSE;
	return $result['content'];
}

/**
 * Get draft content with metadata
 *
 * @param string $page
 * @param bool $lock
 * @param bool $join true: return string, false: return array of string
 * @return array{content:mixed,meta:array<string,mixed>}|FALSE
 */
function get_draft_with_meta($page, $lock = TRUE, $join = FALSE)
{
	$file = get_draft_filename($page);
	$result = array(
		'content' => $join ? '' : array(),
		'meta'    => array(
			'saved'  => NULL,
			'digest' => NULL,
		),
	);

	if (!file_exists($file)) {
		return $result;
	}

	$fp = @fopen($file, 'r');
	if ($fp === FALSE) return FALSE;

	if ($lock) {
		flock($fp, LOCK_SH);
	}

	$size = filesize($file);
	if ($size === FALSE) {
		$content = FALSE;
	} else if ($size == 0) {
		$content = '';
	} else {
		$content = fread($fp, $size);
	}

	if ($lock) {
		flock($fp, LOCK_UN);
	}
	@fclose($fp);

	if ($content === FALSE) {
		return FALSE;
	}

	// Normalize CRLF
	$content = str_replace("\r", '', $content);
	$lines = explode("\n", $content);

	// Parse metadata lines (top of file)
	while (!empty($lines)) {
		$line = $lines[0];
		if (strpos($line, '#draft_saved:') === 0) {
			$result['meta']['saved'] = substr($line, strlen('#draft_saved:'));
			array_shift($lines);
			continue;
		}
		if (strpos($line, '#draft_digest:') === 0) {
			$result['meta']['digest'] = substr($line, strlen('#draft_digest:'));
			array_shift($lines);
			continue;
		}
		break;
	}

	$body_join = implode("\n", $lines);

	if ($join) {
		$result['content'] = $body_join;
		return $result;
	}

	// Create array with newline suffix (similar to file())
	if ($body_join === '') {
		$result['content'] = array();
	} else {
		$with_newlines = preg_split('/(?<=\n)/', $body_join);
		if ($with_newlines === FALSE) {
			$result['content'] = array();
		} else {
			$result['content'] = $with_newlines;
		}
	}

	return $result;
}

/**
 * Get draft physical file name
 *
 * @param $page page name
 * @return string draft file path
 */
function get_draft_filename($page)
{
	return DRAFT_DIR . encode($page) . '.txt';
}

/**
 * Get draft last-modified filetime
 *
 * @param $page page name
 * @return int filetime or 0 if not exists
 */
function get_draft_filetime($page)
{
	return has_draft($page) ? filemtime(get_draft_filename($page)) - LOCALZONE : 0;
}

/**
 * Save draft data
 *
 * @param $page page name
 * @param $postdata draft data
 * @return bool success
 */
function draft_write($page, $postdata)
{
	if (PKWK_READONLY) return FALSE;

	$page = strip_bracket($page);
	$file = get_draft_filename($page);

	// Add metadata
	$page_digest = md5(join('', get_source($page)));
	$metadata = '#draft_saved:' . get_date_atom(UTIME) . "\n";
	$metadata .= '#draft_digest:' . $page_digest . "\n";
	$content = $metadata . $postdata;

	// Write to file
	$fp = fopen($file, 'w');
	if ($fp === FALSE) return FALSE;

	$success = FALSE;
	if (flock($fp, LOCK_EX)) {
		rewind($fp);
		$written = fputs($fp, $content);
		flock($fp, LOCK_UN);
		
		if ($written !== FALSE && $written >= strlen($content)) {
			$success = TRUE;
		}
	}
	fclose($fp);

	return $success;
}

/**
 * Delete draft data
 *
 * @param $page page name
 * @return bool success
 */
function draft_delete($page)
{
	if (PKWK_READONLY) return FALSE;

	$file = get_draft_filename($page);
	if (file_exists($file)) {
		return unlink($file);
	}
	return TRUE;
}

/**
 * Get list of all draft pages
 *
 * @return array list of page names that have drafts
 */
function get_draft_list()
{
	$pages = array();

	if ($dh = opendir(DRAFT_DIR)) {
		while (($file = readdir($dh)) !== FALSE) {
			if (preg_match('/^(.+)\.txt$/', $file, $matches)) {
				$page = decode($matches[1]);
				$pages[] = $page;
			}
		}
		closedir($dh);
	}

	// Sort by modification time (newest first)
	usort($pages, function($a, $b) {
		return get_draft_filetime($b) - get_draft_filetime($a);
	});

	return $pages;
}
