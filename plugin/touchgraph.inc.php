<?php
// PukiWiki - Yet another WikiWikiWeb clone
// touchgraph.inc.php
// Copyright
//   2005-2022 PukiWiki Development Team
//
// Output an index for 'TouchGraph WikiBrowser'
// https://sourceforge.net/projects/touchgraph/
// https://www.touchgraph.com/
//
// Prepare:
//   On Windows OS, change console active codepage by chcp command
//   UTF-8:
//     chcp 65001
//   EUC-JP:
//     chcp 20932
//
// Usage: (Check also TGWikiBrowser's sample)
//    java -Dfile.encoding=UTF-8 -jar TGWikiBrowser.jar \
//    "http://<pukiwiki site>/?plugin=touchgraph" \
//    "http://<pukiwiki site>/?" FrontPage 2 true

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
				if (! is_page($name)) {
					continue;
				}
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
				if (! is_page($name)) {
					continue;
				}
				echo ' ', $name;
			}
			echo "\n";
		}
	}
}
