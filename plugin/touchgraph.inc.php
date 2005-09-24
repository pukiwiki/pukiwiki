<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: touchgraph.inc.php,v 1.9 2005/09/24 01:30:08 henoheno Exp $
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


function plugin_touchgraph_action()
{
	global $vars;

	pkwk_headers_sent();
	header('Content-type: text/plain');
	if (isset($vars['reverse'])) {
		plugin_touchgraph_ref();
	} else {
		plugin_touchgraph_rel();
	}
	exit;
}

// Normal
function plugin_touchgraph_rel()
{
	global $non_list;

 	$non_list_pattern = '/' . $non_list . '/';
	foreach (get_existpages() as $page) {
		if (preg_match($non_list_pattern, $page)) continue;

		$file = CACHE_DIR . encode($page) . '.rel';
		if (file_exists($file)) {
			echo $page;
			$data = file($file);
			foreach(explode("\t", trim($data[0])) as $name) {
				if (preg_match($non_list_pattern, $name)) continue;
				echo ' ', $name;
			}
			echo "\n";
		}
	}
}

// Reverse
function plugin_touchgraph_ref()
{
	global $non_list;
 
 	$non_list_pattern = '/' . $non_list . '/';
	foreach (get_existpages() as $page) {
		if (preg_match($non_list_pattern, $page)) continue;

		$file = CACHE_DIR . encode($page) . '.ref';
		if (file_exists($file)) {
			echo $page;
			foreach (file($file) as $line) {
				list($name) = explode("\t", $line);
				if (preg_match($non_list_pattern, $name)) continue;
				echo ' ', $name;
			}
			echo "\n";
		}
	}
}
?>
