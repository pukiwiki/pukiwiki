<?php
/**
 *
 * showrss プラグイン
 * 
 * ライセンスは PukiWiki 本体と同じく GNU General Public License (GPL) です。
 * http://www.gnu.org/licenses/gpl.txt
 *
 * pukiwiki用のプラグインです。
 * pukiwiki1.3.2以上で動くと思います。
 * 
 * 今のところ動作させるためにはPHP の xml extension が必須です。PHPに組み込まれてない場合はそっけないエラーが出ると思います。
 * 正規表現 or 文字列関数でなんとかならなくもなさげなんですが需要ってどれくらいあるのかわからいので保留です。
 * mbstring もあるほうがいいです。
 * 
 * ない場合は、 jcode.phps をちょこっといじって mb_convert_encoding という関数を宣言しておけばとりあえずそれっぽく変換できるかもです。
 * http://www.spencernetwork.org/
 * 
 * ご連絡先:
 * do3ob wiki   ->   http://do3ob.com/
 * email        ->   hiro_do3ob@yahoo.co.jp
 * 
 * 避難所       ->   http://do3ob.s20.xrea.com/
 *
 * version: $Id: showrss.inc.php,v 1.4 2003/02/18 04:30:23 panda Exp $
 * 
 */

// RSS中の "&lt; &gt; &amp;" などを 一旦 "< > &" に戻すか？      ＜ "&amp;" が "&amp;amp;" になっちゃうの対策
if (!defined('SHOWRSS_VALUE_UNESCAPE')) {
	define('SHOWRSS_VALUE_UNESCAPE', true);
}

// その後もっかい"< > &"などを"&lt; &gt; &amp;"にするか？        ＜ XSS対策？
if (!defined('SHOWRSS_VALUE_ESCAPE')) {
	define('SHOWRSS_VALUE_ESCAPE'  , true);
}


function plugin_showrss_init() {

	global $_plugin_showrss_tmpl;

	$_plugin_showrss_tmpl = array();
	$_plugin_showrss_tmpl['default'] = array(
		'main' => '<p>{list}</p>',
		'list' => "<a href=\"{link}\" title=\"{description}\">{title}</a><br />\n",
		'lastmodified' => "<p style=\"font-size:10px\"><strong>Last-Modified:{timestamp}</strong></p>\n"
	);
	$_plugin_showrss_tmpl['menubar'] = array(
		'main' => "<ul class=\"recent_list\">\n{list}</ul>\n",
		'list' => " <li><a href=\"{link}\" title=\"{title} ({description})\">{title}</a></li>\n",
		'lastmodified' => "<p style=\"font-size:10px\"><strong>Last-Modified:{timestamp}</strong></p>\n"
	);
	$_plugin_showrss_tmpl['recent'] = array(
		'main' => "<ul class=\"recent_list\">\n{list}</ul>\n",
		'list' => " <li><a href=\"{link}\" title=\"{title} ({description})\">{title}</a></li>\n",
		'lastmodified' => "<p style=\"font-size:10px\"><strong>Last-Modified:{timestamp}</strong></p>\n"
	);
}

function plugin_showrss_convert() {

	global $_plugin_showrss_tmpl;

	$local_tmpl = $_plugin_showrss_tmpl; // timestamp付加用

	if (!extension_loaded('xml')) {
		// xml 拡張機能が有効でない場合。
		// http://www18.tok2.com/home/koumori27/xml/phpsax/phpsax_menu.html を使用すると同じことできそうだけどニーズあるかな？
		return plugin_showrss_private_error_message('xml extension is not loaded');
	}

	if (func_num_args() == 0) {
		// 引数がない場合はエラー
		return plugin_showrss_private_error_message('wrong parameter');
	}
	
	$array = func_get_args();
	$rssurl = $tmplname = $usecache = $usetimestamp = '';
	
	switch (func_num_args()) {
	case 4:
		$usetimestamp = $array[3];
	case 3:
		$usecache = $array[2];
	case 2:
		$tmplname = $array[1];
	case 1:
		$rssurl = $array[0];
	}

	// 空白を排除
	$rssurl       = trim($rssurl);
	$tmplname     = trim($tmplname);
	$usetimestamp = trim($usetimestamp);

	if ($tmplname == '' or (is_array($local_tmpl[$tmplname]) === false)) {
		$tmplname = 'default';
	}

	// RSS パスの値チェック
	if (plugin_showrss_private_check_url($rssurl) == false) {
		// url(ローカルファイルパス)が不正な場合
		return plugin_showrss_private_error_message("syntax error '$rssurl'");
	}

	if ($usecache > 0) {
		if (file_exists(CACHE_DIR) === false) {
			// キャッシュを使おうと思ったけどキャッシュディレクトリが存在しない。
			return plugin_showrss_private_error_message("don't exist:" . CACHE_DIR);
		}

		if (is_writable(CACHE_DIR) === false) {
			// キャッシュディレクトリは書き込み可能か？
			return plugin_showrss_private_error_message("don't have permission to access :" . CACHE_DIR);
		}

		$expire = 60 * 60 * $usecache;
		if (($filename = plugin_showrss_private_cache_rss($rssurl, $expire)) !== false && filesize($filename) !== 0) {
			// キャッシュで対処できた場合は url をキャッシュに書き換える。
			$rssurl = $filename;
		}
		else {
			// キャッシュの生成に失敗した場合は何もなかったのごとく振舞う・・・ ＜エラー起こすべき？
			$usecache = 0;
		}
	}

	// タイムスタンプつけるんだけど。もーちょいスマートに書きたいな、、
	$timestamp = '';
	if ($usetimestamp > 0) {
		if ($usecache > 0) {
			$timestamp = filemtime($rssurl);
		}
		else {
			$timestamp = time();
		}
		$timestamp = date('Y/m/d H:i:s', $timestamp);
		$timestamp = str_replace('{timestamp}', $timestamp, $local_tmpl[$tmplname]['lastmodified']);
	}

	$parsed_rss_array = plugin_showrss_private_get_rss_array($rssurl);

	if (is_string($parsed_rss_array)) {
		// 戻り値が文字列だとエラーメッセージ
		return plugin_showrss_private_error_message($parsed_rss_array);
	}

	if (function_exists('mb_convert_encoding')) {
		// エンコードできる場合はSOURCE_ENCODINGに。
		foreach ($parsed_rss_array as $index => $parsed_rss) {
			foreach ($parsed_rss as $parsed_rss_key => $parsed_rss_value) {
				$parsed_rss_array[$index][$parsed_rss_key] = mb_convert_encoding($parsed_rss_value,SOURCE_ENCODING,'auto');
			}
		}
	}
	return plugin_showrss_private_make_html($tmplname, $local_tmpl, $parsed_rss_array) . $timestamp;
}

// 以下、showrss プライベートな関数とか

// エラーメッセージ（簡易）
function plugin_showrss_private_error_message($msg) {
	return '<strong>showrss:</strong>' . $msg;
}

// urlチェック
// ローカルファイルの場合は showrss??????.tmp みたいなファイル名じゃないとエラーになります。
// ereg("showrss[a-z0-9_-]+\\.tmp") ←これにマッチすればOK!
function plugin_showrss_private_check_url($rssurl) {
	// parse_urlをかまして配列化
	$parsed = parse_url(strtolower(trim($rssurl)));

	// schemeがhttp,https,ftpなら無条件でOK
	$scheme = array('http', 'https', 'ftp');
	if (in_array($parsed['scheme'], $scheme)) {
		return true;
	}
	elseif (isset($parsed['scheme']) == true) {
		// それ以外のschemeはとりあえずエラーにしてみる。
		return false;
	}

	$filename = basename($parsed['path']);
	if (ereg("showrss[a-z0-9_\\.-]+\\.tmp", $filename)) {
		return true;
	}

	// すべての条件に引っ掛からない場合は false
	return false;
}
// テンプレートをつかってrss配列からhtmlを作る
function plugin_showrss_private_make_html($tmplname, $showrss_tmpl, $parsed_rss_array) {

	// テンプレート特有の関数がある場合、そいつを使う。
	if (function_exists("plugin_showrss_private_make_html_" . $tmplname) === true) {
		$makehtml = "plugin_showrss_private_make_html_" . $tmplname;
	}
	else {
		$makehtml = "plugin_showrss_private_make_html_default";
	}
	return $makehtml($tmplname, $showrss_tmpl, $parsed_rss_array);
}

// デフォルトのテンプレート置き換え関数
function plugin_showrss_private_make_html_default($tmplname, $showrss_tmpl, $parsed_rss_array) {
	$linklist = '';
	// 置換え
	foreach ($parsed_rss_array as $index => $parsed_rss) {
		$linkhtml = $showrss_tmpl[$tmplname]["list"];
		foreach ($parsed_rss as $parsed_rss_key => $parsed_rss_value) {

			switch ($parsed_rss_key) {
			case "link":
				// リンクの場合
				// XSS 対策で "  > とか変換？
				break;
			case "description":
				if ($unixtime = strtotime(trim($parsed_rss_value))) {
					$parsed_rss_value = plugin_showrss_private_make_update_label($unixtime);
				}
				break;
			default:
				// なし
			}
			$parsed_rss_value = plugin_showrss_private_escape($parsed_rss_value);

			$linkhtml = str_replace("{" . $parsed_rss_key . "}", trim($parsed_rss_value), $linkhtml);
		}
		$linklist .= $linkhtml;
	}
	$linklist = str_replace("{list}", $linklist, $showrss_tmpl[$tmplname]["main"]);
	return $linklist;
}

// recent風に置き換える関数
function plugin_showrss_private_make_html_recent($tmplname, $showrss_tmpl, $parsed_rss_array) {

	$last = $linklist = $temp = '';
	// 置換え
	foreach ($parsed_rss_array as $index => $parsed_rss) {

		if (strtotime($parsed_rss['description']) !== false ) {
			if (date('Y-m-d', strtotime($parsed_rss['description'])) !== $last) {
				if ($temp != '') {
					$linklist .= "<p><strong>$last</strong></p>";
					$linklist .= str_replace('{list}', $temp, $showrss_tmpl[$tmplname]['main']);
					$temp = '';
				}
				$last = date('Y-m-d', strtotime($parsed_rss['description']));
			}
		}

		$linkhtml = $showrss_tmpl[$tmplname]["list"];
		foreach ($parsed_rss as $parsed_rss_key => $parsed_rss_value) {

			switch ($parsed_rss_key) {
			case "link":
				// リンクの場合
				// XSS 対策で "  > とか変換？
				break;
			case "description":
				if ($unixtime = strtotime(trim($parsed_rss_value))) {
					$parsed_rss_value = plugin_showrss_private_make_update_label($unixtime);
				}
				break;
			default:
				// なし
			}
			$parsed_rss_value = plugin_showrss_private_escape($parsed_rss_value);

			$linkhtml = str_replace("{" . $parsed_rss_key . "}", trim($parsed_rss_value), $linkhtml);
		}
		$temp .= $linkhtml;
	}
	if ($last != '')
		$linklist .= "<p><strong>$last</strong></p>";
	if ($temp != '')
		$linklist .= str_replace("{list}", $temp, $showrss_tmpl[$tmplname]["main"]);
	return $linklist;
}

// xss対策っぽいような
function plugin_showrss_private_escape($target) {

	if (SHOWRSS_VALUE_UNESCAPE) {
		$target = strtr($target, array_flip(get_html_translation_table(ENT_COMPAT)));
	}

	if (SHOWRSS_VALUE_ESCAPE) {
		$target = htmlspecialchars($target);
	}
	return $target;
}

// rssを取得・配列化
function plugin_showrss_private_get_rss_array($rss) {
	global $_plugin_showrss_insideitem,$_plugin_showrss_tag,$_plugin_showrss_title,
	$_plugin_showrss_description,$_plugin_showrss_link,$_plugin_showrss_parsed;

	// 初期化
	$_plugin_showrss_insideitem = false;
	$_plugin_showrss_tag = $_plugin_showrss_title = $_plugin_showrss_description = $_plugin_showrss_link = "";
	$_plugin_showrss_parsed = array();

	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "plugin_showrss_private_start_element", "plugin_showrss_private_end_element");
	xml_set_character_data_handler($xml_parser, "plugin_showrss_private_character_data");
	if (!($fp = @fopen($rss,"r"))) return("can't open $rss");
	while ($data = fread($fp, 4096))
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			return(sprintf("XML error: %s at line %d in %s",
				       xml_error_string(xml_get_error_code($xml_parser)),
				       xml_get_current_line_number($xml_parser), $rss));
		}
	fclose($fp);
	xml_parser_free($xml_parser);
	return $_plugin_showrss_parsed;
}


// 更新時間をpukiwiki風に変換？
function plugin_showrss_private_make_update_label($time, $utime = UTIME) {
	$time = $utime - $time;

	if (ceil($time / 60) < 60)
		$result = ceil($time / 60)."m";
	else if (ceil($time / 60 / 60) < 24)
		$result = ceil($time / 60 / 60)."h";
	else
		$result = ceil($time / 60 / 60 / 24)."d";

	return $result;
}

// xml parserのハンドラ関数
function plugin_showrss_private_start_element($parser, $name, $attrs) {
	global $_plugin_showrss_insideitem, $_plugin_showrss_tag, $_plugin_showrss_title, $_plugin_showrss_description, $_plugin_showrss_link;
	if ($_plugin_showrss_insideitem) {
		$_plugin_showrss_tag = $name;
	}
	else if ($name == "ITEM") {
		$_plugin_showrss_insideitem = true;
	}
}
// xml parserのハンドラ関数
function plugin_showrss_private_end_element($parser, $name) {
	global $_plugin_showrss_insideitem, $_plugin_showrss_tag, $_plugin_showrss_title, $_plugin_showrss_description, $_plugin_showrss_link, $_plugin_showrss_parsed;
	if ($name == "ITEM") {

		$_plugin_showrss_parsed[] = array(
			"link"  =>  $_plugin_showrss_link,
			"title" =>  $_plugin_showrss_title,
			"description" => $_plugin_showrss_description
			);


		$_plugin_showrss_title = "";
		$_plugin_showrss_description = "";
		$_plugin_showrss_link = "";
		$_plugin_showrss_insideitem = false;
	}
}

// xml parser のハンドラ関数
function plugin_showrss_private_character_data($parser, $data) {
	global $_plugin_showrss_insideitem, $_plugin_showrss_tag, $_plugin_showrss_title, $_plugin_showrss_description, $_plugin_showrss_link;
	if ($_plugin_showrss_insideitem) {
		switch ($_plugin_showrss_tag) {
		case "TITLE":
			$_plugin_showrss_title .= $data;
			break;
		case "DESCRIPTION":
			$_plugin_showrss_description .= $data;
			break;
		case "LINK":
			$_plugin_showrss_link .= $data;
			break;
		}
	}
}

// -- キャッシュ周り -- //

// キャッシュをコントロール
function plugin_showrss_private_cache_rss($target, $expire) {
	// 期限切れのキャッシュをクリア
	plugin_showrss_private_cache_garbage_collection(CACHE_DIR, $expire);
	// キャッシュがあれば取得する
	if (($result = plugin_showrss_private_cache_fetch($target, CACHE_DIR, $expire)) !== false) {
		return $result;
	}

	$data = implode('', file($target));

	if (($filename = plugin_showrss_private_cache_save($data, $target, CACHE_DIR)) === false) {
		return false;
	}

	return $filename;

}

// キャッシュがあるか調べる。存在する場合ファイル名
function plugin_showrss_private_cache_fetch($target, $dir) {

	$filename = $dir . encode($target) . ".tmp";

	if (!is_readable($filename)) {
		return false;
	}

	return $filename;
}

// キャッシュを保存
function plugin_showrss_private_cache_save($data, $target, $dir) {
	$filename = $dir . encode($target) . ".tmp";
	// lockいらないかな？
	$fp = fopen($filename, "w");
	fwrite($fp, $data);
	fclose($fp);
	return $filename;
}

// 期限切れのファイルを削除
function plugin_showrss_private_cache_garbage_collection($dir, $expire) {

	$dh = dir($dir);
	while (($filename = $dh->read()) !== false) {
		if ($filename === '.' || $filename === '..') {
			continue;
		}

		$last = time() - filemtime($dir . $filename);

		if ($last > $expire) {
			unlink($dir . $filename);
		}
	}

	$dh->close();

}

?>