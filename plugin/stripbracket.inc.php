<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: stripbracket.inc.php,v 1.2 2003/01/31 01:49:35 panda Exp $
//

/*
 stripbracket プラグイン
 データファイルの'[[ ]]'を取り除く
 ファイルのオーナーをPHPの実行者(apache,www-dataなど)にする
 (ファイルをコピーしているだけ :) )

*/

function plugin_stripbracket_action() {
	$result = array();
	
	$dirs = array('attach','backup','counter','diff','wiki');
	
	umask(0133);
	
	foreach ($dirs as $dir) {
		if (!$dp = @opendir($dir)) {
			continue;
		}
		while ($file = readdir($dp)) {
			if (preg_match('/^5B5B([^_]+)5D5D(.+)$/',$file,$matches)) {
				$newfile = $matches[1].$matches[2];
				$page = decode($matches[1]);
				if (file_exists("$dir/$newfile")) {
					$result[] = "-$page file $dir/$newfile already exists.";
					continue;
				}
			}
			else {
				$newfile = $file;
			}
			// get owner
			copy("$dir/$file","$dir/__TEMP__");
			touch("$dir/__TEMP__",filemtime("$dir/$file"));
			unlink("$dir/$file");
			rename("$dir/__TEMP__","$dir/$newfile");
		}
		closedir($dir);
	}
	return array('msg'=>'stripbracket result','body'=>convert_html($result));
}
?>
