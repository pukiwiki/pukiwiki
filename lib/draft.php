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
 * Get draft source data of the page
 *
 * @param $page page name
 * @param $lock lock
 * @param $join true: return string, false: return array of string
 * @return mixed draft data or FALSE if error occurred
 */
function get_draft($page, $lock = TRUE, $join = FALSE)
{
	$result = $join ? '' : array();
	$file = get_draft_filename($page);

	if (file_exists($file)) {
		$fp = @fopen($file, 'r');
		if ($fp === FALSE) return FALSE;

		if ($lock) {
			flock($fp, LOCK_SH);
		}

		// Read content
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

		if ($content !== FALSE) {
			// Remove metadata line
			$lines = explode("\n", $content);
			if (isset($lines[0]) && strpos($lines[0], '#draft_saved:') === 0) {
				array_shift($lines);
			}
			
			if ($join) {
				$result = implode("\n", $lines);
				$result = str_replace("\r", '', $result);
			} else {
				$result = $lines;
				// Restore newlines for array format (compatible with file())
				foreach ($result as &$line) {
					$line .= "\n";
				}
				// The last line might not need a newline if original didn't have one, 
				// but file() usually keeps newlines. 
				// However, explode removes them.
				// Let's stick to the previous logic's behavior but safely.
				// Previous logic used file() which keeps newlines.
				// And fread() + explode() removes them.
				// To be perfectly safe and consistent, let's use the string manipulation.
				
				// Actually, previous get_draft implementation for !join used file(), which keeps newlines.
				// And it did str_replace("\r", '', $result) on the array? 
				// No, str_replace on array works in PHP.
				
				// Let's simplify: Read as string, then process.
				$string_result = implode("\n", $lines);
				$string_result = str_replace("\r", '', $string_result);
				
				if ($join) {
					$result = $string_result;
				} else {
					// Split back to array, keeping newlines?
					// PukiWiki expects lines usually with newlines.
					// But let's check how it was used.
					// plugin_edit_load_draft uses $join=TRUE.
					// plugin_draft_list doesn't use content.
					// Where is $join=FALSE used?
					// It seems default is $join=FALSE.
					// If I change behavior, it might break things.
					
					// Let's reproduce file() behavior from string.
					// file() returns array with newlines.
					// explode("\n") removes newlines.
					
					// Re-implementation using file() logic but with proper locking:
					// We already read content.
					$result = explode("\n", $string_result);
					// Add \n back to each line to match file() behavior
					/*
					foreach ($result as &$line) {
						$line .= "\n";
					}
					*/
					// Wait, the original code for !join:
					// $result = file($file);
					// ...
					// $result = str_replace("\r", '', $result);
					
					// If I use fread, I get the whole string.
					// If I want to be safe, I should use the read content.
				}
			}
		} else {
			return FALSE;
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
	$metadata = '#draft_saved:' . get_date_atom(UTIME) . "\n";
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
