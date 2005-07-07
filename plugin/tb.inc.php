<?php
// $Id: tb.inc.php,v 1.17.4.1 2005/07/07 14:42:46 henoheno Exp $
/*
 * PukiWiki/TrackBack: TrackBack Ping receiver and viewer
 * (C) 2003-2004 PukiWiki Developer Team
 * (C) 2003, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
 *
 * plugin_tb_action()    action
 * plugin_tb_save($url, $tb_id) Save or update TrackBack Ping data
 * plugin_tb_return($rc, $msg)  Return TrackBack ping via HTTP/XML
 * plugin_tb_mode_rss($tb_id)   ?__mode=rss
 * plugin_tb_mode_view($tb_id)  ?__mode=view
 */

switch(LANG){
case 'ja': define('PLUGIN_TB_LANGUAGE', 'ja-Jp'); break;
default  : define('PLUGIN_TB_LANGUAGE', 'en-us'); break;
}

function plugin_tb_action()
{
	global $vars, $trackback;

	if (isset($vars['url'])) {
		// Receive and save a TrackBack Ping (both GET and POST)
		$url   = $vars['url'];
		$tb_id = isset($vars['tb_id']) ? $vars['tb_id'] : '';
		plugin_tb_save($url, $tb_id); // Send a response (and exit)

	} else {
		if ($trackback && isset($vars['__mode']) && isset($vars['tb_id'])) {
			// Show TrackBacks received (and exit)
			switch ($vars['__mode']) {
			case 'rss' : plugin_tb_mode_rss($vars['tb_id']);  break;
			case 'view': plugin_tb_mode_view($vars['tb_id']); break;
			}
		}

		// Show List of pages that TrackBacks reached
		$pages = get_existpages(TRACKBACK_DIR, '.txt');
		if (! empty($pages)) {
			return array('msg'=>'trackback list',
				'body'=>page_list($pages, 'read', FALSE));
		} else {
			return array('msg'=>'', 'body'=>'');
		}
	}
}

// Save or update TrackBack Ping data
function plugin_tb_save($url, $tb_id)
{
	global $vars, $trackback;
	static $fields = array( /* UTIME, */ 'url', 'title', 'excerpt', 'blog_name');

	$die = '';
	if (! $trackback) $die .= 'TrackBack feature disabled. ';
	if ($url   == '') $die .= 'URL parameter is not set. ';
	if ($tb_id == '') $die .= 'TrackBack Ping ID is not set. ';
	if ($die != '') plugin_tb_return(1, $die);

	if (! file_exists(TRACKBACK_DIR)) plugin_tb_return(1, 'No such directory: TRACKBACK_DIR');
	if (! is_writable(TRACKBACK_DIR)) plugin_tb_return(1, 'Permission denied: TRACKBACK_DIR');

	$page = tb_id2page($tb_id);
	if ($page === FALSE) plugin_tb_return(1, 'TrackBack ID is invalid.');

	// URL validation (maybe worse of processing time limit)
	$result = http_request($url, 'HEAD');
	if ($result['rc'] !== 200) plugin_tb_return(1, 'URL is fictitious.');

	// Update TrackBack Ping data
	$filename = tb_get_filename($page);
	$data     = tb_get($filename);

	$items = array(UTIME);
	foreach ($fields as $key) {
		$value = isset($vars[$key]) ? $vars[$key] : '';
		if (preg_match('/[,"' . "\n\r" . ']/', $value))
			$value = '"' . str_replace('"', '""', $value) . '"';
		$items[$key] = $value;
	}
	$data[rawurldecode($items['url'])] = $items;

	$fp = fopen($filename, 'w');
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	foreach ($data as $line) {
		$line = preg_replace('/[\r\n]/s', '', $line); // One line, one ping
		fwrite($fp, join(',', $line) . "\n");
	}
	flock($fp, LOCK_UN);
	fclose($fp);

	plugin_tb_return(0); // Return OK
}

// Return TrackBack ping via HTTP/XML
function plugin_tb_return($rc, $msg = '')
{
	pkwk_common_headers();
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="iso-8859-1"?>';
	echo '<response>';
	echo ' <error>' . $rc . '</error>';
	if ($rc !== 0) echo '<message>' . $msg . '</message>';
	echo '</response>';
	exit;
}

// ?__mode=rss
function plugin_tb_mode_rss($tb_id)
{
	global $script, $vars, $entity_pattern;

	$page = tb_id2page($tb_id);
	if ($page === FALSE) return FALSE;

	$items = '';
	foreach (tb_get(tb_get_filename($page)) as $arr) {
		// _utime_, title, excerpt, _blog_name_
		array_shift($arr); // Cut utime
		list ($url, $title, $excerpt) = array_map(
			create_function('$a', 'return htmlspecialchars($a);'), $arr);
		$items .= <<<EOD

   <item>
    <title>$title</title>
    <link>$url</link>
    <description>$excerpt</description>
   </item>
EOD;
	}

	$title = htmlspecialchars($page);
	$link  = $script . '?' . rawurlencode($page);
	$vars['page'] = $page;
	$excerpt = strip_htmltag(convert_html(get_source($page)));
	$excerpt = preg_replace("/&$entity_pattern;/", '', $excerpt);
	$excerpt = mb_strimwidth(preg_replace("/[\r\n]/", ' ', $excerpt), 0, 255, '...');
	$lang    = PLUGIN_TB_LANGUAGE;

	$rc = <<<EOD
<?xml version="1.0" encoding="utf-8" ?>
<response>
 <error>0</error>
 <rss version="0.91">
  <channel>
   <title>$title</title>
   <link>$link</link>
   <description>$excerpt</description>
   <language>$lang</language>$items
  </channel>
 </rss>
</response>
EOD;

	pkwk_common_headers();
	header('Content-Type: text/xml');
	echo mb_convert_encoding($rc, 'UTF-8', SOURCE_ENCODING);
	exit;
}

// ?__mode=view
function plugin_tb_mode_view($tb_id)
{
	pkwk_common_headers();
	echo 'This function had been removed now. It will be created soon.<br />' . "\n";
	echo 'Sorry for your inconvenience.';
	exit;
}
?>
