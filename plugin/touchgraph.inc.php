<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: touchgraph.inc.php,v 1.6 2005/01/03 12:37:45 henoheno Exp $
//
// Output an index for 'TouchGraph WikiBrowser'
// http://www.touchgraph.com/
//
// Usage: (Check also TGWikiBrowser's sample)
//    java -Dfile.encoding=EUC-JP \
//    -cp TGWikiBrowser.jar;BrowserLauncher.jar com.touchgraph.wikibrowser.TGWikiBrowser \
//    http://<pukiwiki site>/index.php?plugin=touchgraph \
//    http://<pukiwiki site>/index.php? FrontPage 2 true
//
// Note: -Dfile.encoding=EUC-JP (or UTF-8) may not work with Windows OS
//   http://www.simeji.com/wiki/pukiwiki.php?Java%A4%CE%CD%AB%DD%B5 (in Japanese)

define('PLUGIN_TOUCHGRAPH_REVERSE', 0);

function plugin_touchgraph_action()
{
	pkwk_headers_sent();
	header('Content-type: text/plain');
	if (PLUGIN_TOUCHGRAPH_REVERSE) {
		plugin_touchgraph_ref(); // reverse
	} else {
		plugin_touchgraph_rel();
	}
	exit;
}

function plugin_touchgraph_rel()
{
	foreach (get_existpages() as $page) {
		$file = CACHE_DIR . encode($page) . '.rel';
		if (file_exists($file)) {
			echo $page;
			echo ' ';
			$data = file($file);
			echo str_replace("\t", ' ', trim($data[0]));
			echo "\n";
		}
	}
}

function plugin_touchgraph_ref()
{
	foreach (get_existpages() as $page) {
		$file = CACHE_DIR . encode($page) . '.ref';
		if (file_exists($file)) {
			echo $page;
			foreach (file($file) as $line) {
				list($name) = explode("\t", $line);
				echo ' ';
				echo $name;
			}
			echo "\n";
		}
	}
}
?>
