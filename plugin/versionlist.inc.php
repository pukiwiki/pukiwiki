<?php
/*
 * PukiWiki versionlistプラグイン
 *
 * CopyRight 2002 S.YOSHIMURA GPL2
 * http://masui.net/pukiwiki/ yosimura@excellence.ac.jp
 *
 * $Id: versionlist.inc.php,v 1.6 2003/07/03 05:27:46 arino Exp $
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
	$SCRIPT_DIR = array('./','./plugin/');
	/* 探索ディレクトリ設定。本当は、pukiwiki.ini.php かな */

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
			if (!preg_match('/\.php$/i',$file))
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
