<?php
/*
 * PukiWiki versionlistプラグイン
 *
 * CopyRight 2002 S.YOSHIMURA GPL2
 * http://masui.net/pukiwiki/ yosimura@excellence.ac.jp
 *
 * $Id: versionlist.inc.php,v 1.11 2004/07/24 09:45:38 henoheno Exp $
 */

function plugin_versionlist_action()
{
	global $_title_versionlist;
	
	return array(
		'msg' => $_title_versionlist,
		'body' => plugin_versionlist_convert()
	);
}

function plugin_versionlist_convert()
{
	/* 探索ディレクトリ設定 */
	$SCRIPT_DIR = array('./');
	if (SUB_DIR   != './') array_push($SCRIPT_DIR, SUB_DIR);
	if (DATA_HOME != './' && DATA_HOME != SUB_DIR) array_push($SCRIPT_DIR, DATA_HOME);
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
			$comment = array('file'=>htmlspecialchars($sdir.$file),'rev'=>'','date'=>'');
			if (preg_match('/\$'.'Id: (.+),v (\d+\.\d+) (\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2})/',$data,$matches))
			{
//				$comment['file'] = htmlspecialchars($sdir.$matches[1]);
				$comment['rev'] = htmlspecialchars($matches[2]);
				$comment['date'] = htmlspecialchars($matches[3]);
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
