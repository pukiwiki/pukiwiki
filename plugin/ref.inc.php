<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: ref.inc.php,v 1.27 2004/08/19 11:55:19 henoheno Exp $
//

/*

* プラグイン ref
ページに添付されたファイルを展開する
URLを展開する

* Usage
 #ref(filename[,page][,parameters][,title])

* パラメータ
- filename
添付ファイル名、あるいはURL

'ページ名/添付ファイル名'を指定すると、そのページの添付ファイルを参照する

- page
ファイルを添付したページ名(省略可)

- Left|Center|Right
横の位置合わせ

- Wrap|Nowrap
テーブルタグで囲む/囲まない

- Around
テキストの回り込み

- noicon
アイコンを表示しない

- nolink
元ファイルへのリンクを張らない

- noimg
画像を展開しない

- zoom
縦横比を保持する

- 999x999
サイズを指定(幅x高さ)

- 999%
サイズを指定(拡大率)

- その他の文字列
imgのalt/hrefのtitleとして使用~
ページ名やパラメータに見える文字列を使用するときは、#ref(hoge.png,,zoom)のように
タイトルの前にカンマを余分に入れる

*/

define('PLUGIN_REF_USAGE', "(attached-file-name[,page][,parameters][,title])");

// File icon image
if (! defined('FILE_ICON')) {
	define('FILE_ICON',
	'<img src="' . IMAGE_DIR . 'file.png" width="20" height="20"' .
	' alt="file" style="border-width:0px" />');
}

// Default alignment
define('REF_DEFAULT_ALIGN', 'left'); // 'left', 'center', 'right'

// Force wrap on default
define('REF_WRAP_TABLE', FALSE); // TRUE,FALSE

// URL指定時に画像サイズを取得するか
define('REF_URL_GETIMAGESIZE', FALSE);

function plugin_ref_inline()
{
	// Not reached, because of "$aryargs[] = & $body" at plugin.php
	// if (! func_num_args())
	//	return '&amp;ref(): Usage:' . PLUGIN_REF_USAGE . ';';

	$params = plugin_ref_body(func_get_args());

	if (isset($params['_error']) && $params['_error'] != '') {
		// Error
		return '&amp;ref(): ' . $params['_error'] . ';';
	} else {
		return $params['_body'];
	}
}

function plugin_ref_convert()
{
	if (! func_num_args())
		return '<p>#ref(): Usage:' . PLUGIN_REF_USAGE . "</p>\n";

	$params = plugin_ref_body(func_get_args());

	if (isset($params['_error']) && $params['_error'] != '') {
		return "<p>#ref(): {$params['_error']}</p>";
	}

	if ((REF_WRAP_TABLE && ! $params['nowrap']) || $params['wrap']) {
		// 枠で包む
		// margin:auto
		//	Mozilla 1.x  = x (wrap,aroundが効かない)
		//	Opera 6      = o
		//	Netscape 6   = x (wrap,aroundが効かない)
		//	IE 6         = x (wrap,aroundが効かない)
		// margin:0px
		//	Mozilla 1.x  = x (wrapで寄せが効かない)
		//	Opera 6      = x (wrapで寄せが効かない)
		//	Netscape 6   = x (wrapで寄せが効かない)
		//	IE6          = o
		$margin = ($params['around'] ? '0px' : 'auto');
		$margin_align = ($params['_align'] == 'center') ? '' : ";margin-{$params['_align']}:0px";
		$params['_body'] = <<<EOD
<table class="style_table" style="margin:$margin$margin_align">
 <tr>
  <td class="style_td">{$params['_body']}</td>
 </tr>
</table>
EOD;
	}

	if ($params['around']) {
		$style = ($params['_align'] == 'right') ? 'float:right' : 'float:left';
	} else {
		$style = "text-align:{$params['_align']}";
	}

	// divで包む
	return "<div class=\"img_margin\" style=\"$style\">{$params['_body']}</div>\n";
}

function plugin_ref_body($args)
{
	global $script, $vars, $WikiName, $BracketName;

	$params = array(); // 戻り値
	$page = isset($vars['page']) ? $vars['page'] : '';

	// 添付ファイル名を取得
	$name = array_shift($args);

	// 次の引数がページ名かどうか
	if (! empty($args) &&
		preg_match("/^($WikiName|\[\[$BracketName\]\])$/", $args[0]))
	{
		$_page = get_fullname(strip_bracket($args[0]), $page);
		if (is_pagename($_page)) {
			$page = $_page;
			array_shift($args);
		}
	}

	// パラメータ
	$params = array(
		'left'   => FALSE, // 左寄せ
		'center' => FALSE, // 中央寄せ
		'right'  => FALSE, // 右寄せ
		'wrap'   => FALSE, // TABLEで囲む
		'nowrap' => FALSE, // TABLEで囲まない
		'around' => FALSE, // 回り込み
		'noicon' => FALSE, // アイコンを表示しない
		'nolink' => FALSE, // 元ファイルへのリンクを張らない
		'noimg'  => FALSE, // 画像を展開しない
		'zoom'   => FALSE, // 縦横比を保持する
		'_size'  => FALSE, // サイズ指定あり
		'_w'     => 0,       // 幅
		'_h'     => 0,       // 高さ
		'_%'     => 0,     // 拡大率
		'_args'  => array(),
		'_done'  => FALSE,
		'_error' => ''
	);

	if (! empty($args))
		foreach ($args as $arg)
			ref_check_arg($arg, $params);

/*
 $nameをもとに以下の変数を設定
 $url,$url2 : URL
 $title :タイトル
 $is_image : 画像のときTRUE
 $info : 画像ファイルのときgetimagesize()の'size'
         画像ファイル以外のファイルの情報
         添付ファイルのとき : ファイルの最終更新日とサイズ
         URLのとき : URLそのもの
*/
	$file = $title = $url = $url2 = $info = '';
	$width = $height = 0;
	$matches = array();

	if (is_url($name)) {	// URL
		$url = $url2 = htmlspecialchars($name);
		$title = htmlspecialchars(preg_match('/([^\/]+)$/', $name, $matches) ? $matches[1] : $url);

		$is_image = (! $params['noimg'] && preg_match("/\.(gif|png|jpe?g)$/i",$name));

		if (REF_URL_GETIMAGESIZE && $is_image && (bool)ini_get('allow_url_fopen')) {
			$size = @getimagesize($name);
			if (is_array($size)) {
				$width  = $size[0];
				$height = $size[1];
				$info   = $size[3];
			}
		}

	} else { // 添付ファイル
		if (! is_dir(UPLOAD_DIR)) {
			$params['_error'] = 'No UPLOAD_DIR';
			return $params;
		} else {
			$file = UPLOAD_DIR . encode($page) . '_' . encode($name);
			if (! is_file($file)) {
				$params['_error'] = 'File not found';
				return $params;
			}
		}

		// ページ指定のチェック
		if (preg_match('/^(.+)\/([^\/]+)$/', $name, $matches)) {
			if ($matches[1] == '.' || $matches[1] == '..') {
				$matches[1] .= '/';
			}
			$page = get_fullname($matches[1], $page);
			$name = $matches[2];
		}
		$title = htmlspecialchars($name);

		$is_image = (! $params['noimg'] && preg_match('/\.(gif|png|jpe?g)$/i', $name));

		$url = $script . '?plugin=attach' . '&amp;refer=' . rawurlencode($page) .
			'&amp;openfile=' . rawurlencode($name); // Show its filename at the last

		if ($is_image) {
			// Swap $url
			$url2 = $url;
			$url  = $file;

			$width = $height = 0;
			$size = @getimagesize($file);
			if (is_array($size)) {
				$width  = $size[0];
				$height = $size[1];
			}
		} else {
			$info = get_date('Y/m/d H:i:s', filemtime($file) - LOCALZONE) .
				' ' . sprintf('%01.1f', round(filesize($file)/1024, 1)) . 'KB';
		}
	}

	// 拡張パラメータをチェック
	if (! empty($params['_args'])) {
		$_title = array();
		foreach ($params['_args'] as $arg) {
			if (preg_match('/^([0-9]+)x([0-9]+)$/', $arg, $matches)) {
				$params['_size'] = TRUE;
				$params['_w'] = $matches[1];
				$params['_h'] = $matches[2];

			} else if (preg_match('/^([0-9.]+)%$/', $arg, $matches) && $matches[1] > 0) {
				$params['_%'] = $matches[1];

			} else {
				$_title[] = $arg;
			}
		}

		if (! empty($_title)) {
			$title = htmlspecialchars(join(',', $_title));
			if ($is_image) $title = make_line_rules($title);
		}
	}

	// 画像サイズ調整
	if ($is_image) {
		// 指定されたサイズを使用する
		if ($params['_size']) {
			if ($width == 0 && $height == 0) {
				$width  = $params['_w'];
				$height = $params['_h'];
			} else if ($params['zoom']) {
				$_w = $params['_w'] ? $width  / $params['_w'] : 0;
				$_h = $params['_h'] ? $height / $params['_h'] : 0;
				$zoom = max($_w, $_h);
				if ($zoom) {
					$width  = (int)($width  / $zoom);
					$height = (int)($height / $zoom);
				}
			} else {
				$width  = $params['_w'] ? $params['_w'] : $width;
				$height = $params['_h'] ? $params['_h'] : $height;
			}
		}
		if ($params['_%']) {
			$width  = (int)($width  * $params['_%'] / 100);
			$height = (int)($height * $params['_%'] / 100);
		}
		if ($width && $height) $info = "width=\"$width\" height=\"$height\" ";
	}

	// アラインメント判定
	$params['_align'] = REF_DEFAULT_ALIGN;
	foreach (array('right', 'left', 'center') as $align) {
		if ($params[$align])  {
			$params['_align'] = $align;
			break;
		}
	}

	if ($is_image) { // 画像
		$params['_body'] = "<img src=\"$url\" alt=\"$title\" title=\"$title\" $info/>";
		if (! $params['nolink'] && $url2)
			$params['_body'] = "<a href=\"$url2\" title=\"$title\">{$params['_body']}</a>";
	} else {
		$icon = $params['noicon'] ? '' : FILE_ICON;
		$params['_body'] = "<a href=\"$url\" title=\"$info\">$icon$title</a>";
	}

	return $params;
}

//-----------------------------------------------------------------------------
// オプションを解析する
function ref_check_arg($val, & $params)
{
	if ($val == '') {
		$params['_done'] = TRUE;
		return;
	}

	if (! $params['_done']) {
		foreach (array_keys($params) as $key) {
			if (strpos($key, strtolower($val)) === 0) {
				$params[$key] = TRUE;
				return;
			}
		}
		$params['_done'] = TRUE;
	}

	$params['_args'][] = $val;
}
?>
