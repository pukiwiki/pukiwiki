<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: versionlist.inc.php,v 1.21 2007/06/30 04:19:25 henoheno Exp $
// Copyright (C)
//	 2002-2007 PukiWiki Developers Team
//	 2002      S.YOSHIMURA GPL2 yosimura@excellence.ac.jp
// License: GPL v2
//
// Listing files, with CVS/RCS/SVN revisions and commit-dates from '$Id' keywords


function plugin_versionlist_action()
{
	global $_title_versionlist;

	if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibits this');

	return array(
		'msg'  => $_title_versionlist,
		'body' => plugin_versionlist_convert());
}

function plugin_versionlist_convert()
{
	if (PKWK_SAFE_MODE) return ''; // Show nothing

	// Directories to scan
	$scan['.'       ] = NULL;
	$scan[LIB_DIR   ] = NULL;
	$scan[DATA_HOME ] = NULL;
	$scan[PLUGIN_DIR] = NULL;
	$scan[SKIN_DIR  ] = NULL;

	$row = $matches = array();
	foreach (array_keys($scan) as $sdir) {
		if (! $dir = @dir($sdir)) continue;
		if ($sdir == '.') $sdir = '';
		while(FALSE !== ($file = $dir->read())) {
			if (! preg_match('/\.(?:php|css|js)$/i', $file)) continue;
			$path       = $sdir . $file;
			$row[$path] = array();
			$data       = join('', file($path));
			if (preg_match('#\$' . 'Id: .+ (\d+(?:\.\d+)*) (\d{4}[/-]\d{2}[/-]\d{2} \d{2}:\d{2}:\d{2}[^ ]*) .+ \$#', $data, $matches)) {
				$row[$path]['rev']  = $matches[1];	// "1", "1.23" or "1.23.45.6"
				$row[$path]['date'] = $matches[2];
			}
		}
		$dir->close();
	}
	if (empty($row)) return '';
	unset($data);

	ksort($row, SORT_STRING);

	$retval = array();
	$retval[] = <<<EOD
<table border="1">
 <thead>
  <tr>
   <th>File</th>
   <th>Revision</th>
   <th>Date</th>
  </tr>
 </thead>
 <tbody>
EOD;

	foreach (array_keys($row) as $path) {
		$file = htmlspecialchars($path);
		$rev  = isset($row[$path]['rev'])  ? htmlspecialchars($row[$path]['rev'])  : '';
		$date = isset($row[$path]['date']) ? htmlspecialchars($row[$path]['date']) : '';
		$retval[] = <<<EOD
  <tr>
   <td>$file</td>
   <td align="right">$rev</td>
   <td>$date</td>
  </tr>
EOD;
		unset($row[$path]);
	}

	$retval[] = <<<EOD
 </tbody>
</table>
EOD;

	return implode("\n", $retval);
}
?>
