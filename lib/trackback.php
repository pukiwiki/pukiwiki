<?php
// $Id: trackback.php,v 1.6 2004/12/18 06:01:04 henoheno Exp $
/*
 * PukiWiki/TrackBack
 * (C) 2003-2004 PukiWiki Developer Team
 * (C) 2003, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
 *
 * http://localhost/pukiwiki/pukiwiki.php?FrontPage と明確に指定しないと
 * TrackBack ID の取得はできない
 *
 * tb_get_id($page)        Get TrackBack Ping ID from page name
 * tb_id2page($tb_id)      Get page name from TrackBack Ping ID
 * tb_get_filename($page)  Get file name of TrackBack Ping data
 * tb_count($page)         Count the number of TrackBack Pings included for the page
 *                         // pukiwiki.skin.LANG.php
 * tb_send($page, $data)   Send TrackBack Ping(s) automatically // file.php
 * tb_delete($page)        Remove TrackBack Ping data // edit.inc.php
 * tb_get($file, $key = 1) TrackBack Ping データ入力
 * tb_get_rdf($page)       文章中に埋め込むためのrdfをデータを生成 // pukiwiki.php
 * tb_get_url($url)        文書をGETし、埋め込まれたTrackBack Ping URLを取得
 * class TrackBack_XML     XMLからTrackBack Ping IDを取得するクラス
 * == Referer 対応分 ==
 * ref_save($page)         Save or update referer data // pukiwiki.php
 */

define('PLUGIN_TRACKBACK_VERSION', 'PukiWiki/TrackBack 0.2');

// Get TrackBack Ping ID from page name
function tb_get_id($page)
{
	return md5($page);
}

// Get page name from TrackBack Ping ID
function tb_id2page($tb_id)
{
	static $pages, $cache = array();

	if (isset($cache[$tb_id])) return $cache[$tb_id];

	if (! isset($pages)) $pages = get_existpages();

	foreach ($pages as $page) {
		$_tb_id = tb_get_id($page);
		$cache[$_tb_id] = $page;
		unset($pages[$page]);
		if ($tb_id == $_tb_id) return $cache[$tb_id];
	}

	$cache[$tb_id] = FALSE;

	return $cache[$tb_id]; // Not found
}

// Get file name of TrackBack Ping data
function tb_get_filename($page, $ext = '.txt')
{
	return TRACKBACK_DIR . encode($page) . $ext;
}

// Count the number of TrackBack Pings included for the page
function tb_count($page, $ext = '.txt')
{
	$filename = tb_get_filename($page, $ext);
	return file_exists($filename) ? count(file($filename)) : 0;
}

// Send TrackBack Ping(s) automatically
// $plus  = Newly added lines may include URLs
// $minus = Removed lines may include URLs
function tb_send($page, $plus, $minus = '')
{
	global $script, $trackback;

	if (! $trackback) return;

	// Disable 'max execution time' (php.ini: max_execution_time)
	if (ini_get('safe_mode') == '0') set_time_limit(0);

	// Get URLs from <a>(anchor) tag from convert_html()
	$links = array();
	$plus  = convert_html($plus); // WARNING: heavy and may cause side-effect
	preg_match_all('#href="(https?://[^"]+)"#', $plus, $links, PREG_PATTERN_ORDER);
	$links = array_unique($links[1]);

	// Reject from minus list
	if ($minus != '') {
		$links_m = array();
		$minus = convert_html($minus); // WARNING: heavy and may cause side-effect
		preg_match_all('#href="(https?://[^"]+)"#', $minus, $links_m, PREG_PATTERN_ORDER);
		$links_m = array_unique($links_m[1]);

		$links = array_diff($links, $links_m);
	}

	// Reject own URL (Pattern _NOT_ started with '$script' and '?')
	$links = preg_grep('|^(?!' . preg_quote($script) . '\?).|', $links);

	// No link, END
	if (! is_array($links) || empty($links)) return;

	$r_page  = rawurlencode($page);
	$excerpt = strip_htmltag(convert_html(get_source($page)));

	// Sender's information
	$putdata = array(
		'title'     => $page, // Title = It's page name
		'url'       => "$script?$r_page", // will be rawurlencode() at send phase
		'excerpt'   => mb_strimwidth(preg_replace("/[\r\n]/", ' ', $excerpt), 0, 255, '...'),
		'blog_name' => PLUGIN_TRACKBACK_VERSION,
		'charset'   => SOURCE_ENCODING // Ping text encoding (Not defined)
	);

	foreach ($links as $link) {
		$tb_id = tb_get_url($link);  // Get Trackback ID from the URL
		if (empty($tb_id)) continue; // Trackback is not supported

		$result = http_request($tb_id, 'POST', '', $putdata);
		// FIXME: Create warning notification space at pukiwiki.skin!
	}
}

// Remove TrackBack Ping data
function tb_delete($page)
{
	$filename = tb_get_filename($page);
	if (file_exists($filename)) @unlink($filename);
}

// TrackBack Ping データ入力
function tb_get($file, $key = 1)
{
	if (! file_exists($file)) return array();

	$result = array();
	$fp = @fopen($file, 'r');
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	while ($data = @fgetcsv($fp, 8192, ',')) {
		// $data[$key] = URL
		$result[rawurldecode($data[$key])] = $data;
	}
	flock($fp, LOCK_UN);
	fclose ($fp);

	return $result;
}

// 文章中に trackback:ping を埋め込むためのデータを生成
function tb_get_rdf($page)
{
	global $trackback;

	if (! $trackback) return '';

	$r_page = rawurlencode($page);
	$tb_id  = tb_get_id($page);
	// $dcdate = substr_replace(get_date('Y-m-d\TH:i:sO', $time), ':', -2, 0);
	// dc:date="$dcdate"

	$_script = get_script_uri(); // Get absolute path

	return <<<EOD
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
 <rdf:Description
   rdf:about="$_script?$r_page"
   dc:identifier="$_script?$r_page"
   dc:title="$page"
   trackback:ping="$_script?tb_id=$tb_id" />
</rdf:RDF>
-->
EOD;
}

// 文書をGETし、埋め込まれたTrackBack Ping urlを取得
function tb_get_url($url)
{
	// プロキシを経由する必要があるホストにはpingを送信しない
	$parse_url = parse_url($url);
	if (empty($parse_url['host']) || via_proxy($parse_url['host']))
		return '';

	$data = http_request($url);
	if ($data['rc'] !== 200) return '';

	$matches = array();
	if (! preg_match_all('#<rdf:RDF[^>]*>(.*?)</rdf:RDF>#si', $data['data'],
	    $matches, PREG_PATTERN_ORDER))
		return '';

	$obj = new TrackBack_XML();
	foreach ($matches[1] as $body) {
		$tb_url = $obj->parse($body, $url);
		if ($tb_url !== FALSE) return $tb_url;
	}

	return '';
}

// 埋め込まれたデータから TrackBack Ping urlを取得するクラス
class TrackBack_XML
{
	var $url;
	var $tb_url;

	function parse($buf, $url)
	{
		// Init
		$this->url    = $url;
		$this->tb_url = FALSE;

		$xml_parser = xml_parser_create();
		if ($xml_parser === FALSE) return FALSE;

		xml_set_element_handler($xml_parser, array(& $this, 'start_element'),
			array(& $this, 'end_element'));

		if (! xml_parse($xml_parser, $buf, TRUE)) {
/*			die(sprintf('XML error: %s at line %d in %s',
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser),
				$buf));
*/
			return FALSE;
		}

		return $this->tb_url;
	}

	function start_element($parser, $name, $attrs)
	{
		if ($name !== 'RDF:DESCRIPTION') return;

		$about = $url = $tb_url = '';
		foreach ($attrs as $key=>$value) {
			switch ($key) {
			case 'RDF:ABOUT'     : $about  = $value; break;
			case 'DC:IDENTIFER'  : /*FALLTHROUGH*/
			case 'DC:IDENTIFIER' : $url    = $value; break;
			case 'TRACKBACK:PING': $tb_url = $value; break;
			}
		}
		if ($about == $this->url || $url == $this->url)
			$this->tb_url = $tb_url;
	}

	function end_element($parser, $name) {}
}

// Save or update referer data
function ref_save($page)
{
	global $referer;

	if (! $referer || empty($_SERVER['HTTP_REFERER'])) return TRUE;

	$url = $_SERVER['HTTP_REFERER'];

	// Validate URI (Ignore own)
	$parse_url = parse_url($url);
	if (empty($parse_url['host']) || $parse_url['host'] == $_SERVER['HTTP_HOST'])
		return TRUE;

	if (! is_dir(TRACKBACK_DIR))      die('No such directory: TRACKBACK_DIR');
	if (! is_writable(TRACKBACK_DIR)) die('Permission denied: TRACKBACK_DIR');

	// Update referer data
	if (ereg("[,\"\n\r]", $url))
		$url = '"' . str_replace('"', '""', $url) . '"';

	$filename = tb_get_filename($page, '.ref');
	$data     = tb_get($filename, 3);
	$d_url    = rawurldecode($url);
	if (! isset($data[$d_url])) {
		// 0:最終更新日時, 1:初回登録日時, 2:参照カウンタ,
		// 3:Referer ヘッダ, 4:利用可否フラグ(1は有効)
		$data[$d_url] = array(UTIME, UTIME, 0, $url, 1);
	}
	$data[$d_url][0] = UTIME;
	$data[$d_url][2]++;

	$fp = fopen($filename, 'w');
	if ($fp === FALSE) return FALSE;	
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	foreach ($data as $line)
		fwrite($fp, join(',', $line) . "\n");
	flock($fp, LOCK_UN);
	fclose($fp);

	return TRUE;
}
?>
