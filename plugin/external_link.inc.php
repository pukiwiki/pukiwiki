<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// external_link.inc.php
// Copyright
//   2018 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// PukiWiki External Link Plugin

function plugin_external_link_action()
{
	global $vars, $external_link_cushion, $_external_link_messages;
	$charset = CONTENT_CHARSET;
	header('Content-Type: text/html; charset=' . $charset);
	$valid_url = false;
	if (isset($vars['url'])) {
		$url = $vars['url'];
		if (is_url($url)) {
			$valid_url = true;
		}
	}
	if (!$valid_url) {
		$error_message = <<< EOM
<html>
  <body>
    The URL is invalid.
  </body>
</html>
EOM;
		print($error_message);
		exit;
	}
	$encoded_url = htmlsc($url);
	$refreshwait = $external_link_cushion['wait_seconds'];
	$h_title = htmlsc(str_replace('%s', $url, $_external_link_messages['page_title']));
	$h_desc = htmlsc($_external_link_messages['desc']);
	$h_wait = htmlsc(str_replace('%s', (string)$external_link_cushion['wait_seconds'],
		$_external_link_messages['wait_n_seconds']));
	$body = <<< EOM
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=$charset" />
    <meta http-equiv="Refresh" content="$refreshwait;URL=$encoded_url" />
    <title>$h_title</title>
  </head>
  <body>
		<p>$h_desc</p>
		<p>$h_wait</p>
		<p><a href="$encoded_url">$encoded_url</a></p>
  </body>
</html>
EOM;
	print($body);
	exit;
}
