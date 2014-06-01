<?php
// $Id: versionlist.inc.php,v 1.15 2005/01/29 02:12:52 henoheno Exp $
/*
 * PukiWiki versionlist plugin
 *
 * CopyRight 2002 S.YOSHIMURA GPL2
 * http://masui.net/pukiwiki/ yosimura@excellence.ac.jp
 */

function plugin_versionlist_action()
{
	global $_title_versionlist;

	if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibits this');

	return array(
		'msg' => $_title_versionlist,
		'body' => plugin_versionlist_convert());
}

function plugin_versionlist_convert()
{
	if (PKWK_SAFE_MODE) return ''; // Show nothing
	
	/* 探索ディレクトリ設定 */
	$SCRIPT_DIR = array('./');
	if (LIB_DIR   != './') array_push($SCRIPT_DIR, LIB_DIR);
	if (DATA_HOME != './' && DATA_HOME != LIB_DIR) array_push($SCRIPT_DIR, DATA_HOME);
	array_push($SCRIPT_DIR, PLUGIN_DIR, SKIN_DIR);

	$comments = array();

	foreach ($SCRIPT_DIR as $sdir)
	{
		if (!$dir = @dir($sdir))
		{
			// die_message('directory '.$sdir.' is not found or not readable.');
			continue;
		}
		while($file = $dir->read())
		{
			if (!preg_match("/\.(php|lng|css|js)$/i",$file))
			{
				continue;
			}
			$data = join('',file($sdir.$file));
			$comment = array('file'=>htmlsc($sdir.$file),'rev'=>'','date'=>'');
			if (preg_match('/\$'.'Id: (.+),v (\d+\.\d+) (\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2})/',$data,$matches))
			{
//				$comment['file'] = htmlsc($sdir.$matches[1]);
				$comment['rev'] = htmlsc($matches[2]);
				$comment['date'] = htmlsc($matches[3]);
			}
			$comments[$sdir.$file] = $comment;
		}
		$dir->close();
	}
	if (count($comments) == 0)
	{
		return '';
	}
	ksort($comments);
	$retval = '';
	foreach ($comments as $comment)
	{
		$retval .= <<<EOD

  <tr>
   <td>{$comment['file']}</td>
   <td align="right">{$comment['rev']}</td>
   <td>{$comment['date']}</td>
  </tr>
EOD;
	}
	$retval = <<<EOD
<table border="1">
 <thead>
  <tr>
   <th>filename</th>
   <th>revision</th>
   <th>date</th>
  </tr>
 </thead>
 <tbody>
$retval
 </tbody>
</table>
EOD;
	return $retval;
}
?>
