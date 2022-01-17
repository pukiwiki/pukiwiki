<?php
// PukiWiki - Yet another WikiWikiWeb clone
// touchgraph.inc.php
// Copyright
//   2005-2022 PukiWiki Development Team
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
	header('Content-Type: text/plain; charset=' . SOURCE_ENCODING);
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
	foreach (get_existpages() as $page) {
		if (check_non_list($page)) continue;

		$file = CACHE_DIR . encode($page) . '.rel';
		if (file_exists($file)) {
			echo $page;
			$data = file($file);
			foreach(explode("\t", trim($data[0])) as $name) {
				if (check_non_list($name)) continue;
				echo ' ', $name;
			}
			echo "\n";
		}
	}
}

// Reverse
function plugin_touchgraph_ref()
{
	foreach (get_existpages() as $page) {
		if (check_non_list($page)) continue;

		$file = CACHE_DIR . encode($page) . '.ref';
		if (file_exists($file)) {
			echo $page;
			foreach (file($file) as $line) {
				list($name) = explode("\t", $line);
				if (check_non_list($name)) continue;
				echo ' ', $name;
			}
			echo "\n";
		}
	}
}
