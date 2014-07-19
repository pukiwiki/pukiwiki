<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: ref.inc.php,v 1.51 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C)
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Image refernce plugin
// Include an attached image-file as an inline-image

// File icon image
if (! defined('FILE_ICON'))
	define('FILE_ICON',
	'<img src="' . IMAGE_DIR . 'file.png" width="20" height="20"' .
	' alt="file" style="border-width:0px" />');

/////////////////////////////////////////////////
// Default settings

// Horizontal alignment
define('PLUGIN_REF_DEFAULT_ALIGN', 'left'); // 'left', 'center', 'right'

// Text wrapping
define('PLUGIN_REF_WRAP_TABLE', FALSE); // TRUE, FALSE

// URL指定時に画像サイズを取得するか
define('PLUGIN_REF_URL_GET_IMAGE_SIZE', FALSE); // FALSE, TRUE

// UPLOAD_DIR のデータ(画像ファイルのみ)に直接アクセスさせる
define('PLUGIN_REF_DIRECT_ACCESS', FALSE); // FALSE or TRUE
// - これは従来のインラインイメージ処理を互換のために残すもので
//   あり、高速化のためのオプションではありません
// - UPLOAD_DIR をWebサーバー上に露出させており、かつ直接アクセス
//   できる(アクセス制限がない)状態である必要があります
// - Apache などでは UPLOAD_DIR/.htaccess を削除する必要があります
// - ブラウザによってはインラインイメージの表示や、「インライン
//   イメージだけを表示」させた時などに不具合が出る場合があります

/////////////////////////////////////////////////

// Image suffixes allowed
define('PLUGIN_REF_IMAGE', '/\.(gif|png|jpe?g)$/i');

// Usage (a part of)
define('PLUGIN_REF_USAGE', "([pagename/]attached-file-name[,parameters, ... ][,title])");

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
		return "<p>#ref(): {$params['_error']}</p>\n";
	}

	if ((PLUGIN_REF_WRAP_TABLE && ! $params['nowrap']) || $params['wrap']) {
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
	global $script, $vars;
	global $WikiName, $BracketName; // compat

	// 戻り値
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

	// 添付ファイルのあるページ: defaultは現在のページ名
	$page = isset($vars['page']) ? $vars['page'] : '';

	// 添付ファイルのファイル名
	$name = '';

	// 添付ファイルまでのパスおよび(実際の)ファイル名
	$file = '';

	// 第一引数: "[ページ名および/]添付ファイル名"、あるいは"URL"を取得
	$name = array_shift($args);
	$is_url = is_url($name);

	if(! $is_url) {
		// 添付ファイル
		if (! is_dir(UPLOAD_DIR)) {
			$params['_error'] = 'No UPLOAD_DIR';
			return $params;
		}

		$matches = array();
		// ファイル名にページ名(ページ参照パス)が合成されているか
		//   (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
		if (preg_match('#^(.+)/([^/]+)$#', $name, $matches)) {
			if ($matches[1] == '.' || $matches[1] == '..') {
				$matches[1] .= '/'; // Restore relative paths
			}
			$name = $matches[2];
			$page = get_fullname(strip_bracket($matches[1]), $page); // strip is a compat
			$file = UPLOAD_DIR . encode($page) . '_' . encode($name);
			$is_file = is_file($file);

		// 第二引数以降が存在し、それはrefのオプション名称などと一致しない
		} else if (isset($args[0]) && $args[0] != '' && ! isset($params[$args[0]])) {
			$e_name = encode($name);

			// Try the second argument, as a page-name or a path-name
			$_arg = get_fullname(strip_bracket($args[0]), $page); // strip is a compat
			$file = UPLOAD_DIR .  encode($_arg) . '_' . $e_name;
			$is_file_second = is_file($file);

			// If the second argument is WikiName, or double-bracket-inserted pagename (compat)
			$is_bracket_bracket = preg_match("/^($WikiName|\[\[$BracketName\]\])$/", $args[0]);

			if ($is_file_second && $is_bracket_bracket) {
				// Believe the second argument (compat)
				array_shift($args);
				$page = $_arg;
				$is_file = TRUE;
			} else {
				// Try default page, with default params
				$is_file_default = is_file(UPLOAD_DIR . encode($page) . '_' . $e_name);

				// Promote new design
				if ($is_file_default && $is_file_second) {
					// Because of race condition NOW
					$params['_error'] = htmlsc('The same file name "' .
						$name . '" at both page: "' .  $page . '" and "' .  $_arg .
						'". Try ref(pagename/filename) to specify one of them');
				} else {
					// Because of possibility of race condition, in the future
					$params['_error'] = 'The style ref(filename,pagename) is ambiguous ' .
						'and become obsolete. ' .
						'Please try ref(pagename/filename)';
				}
				return $params;
			}
		} else {
			// Simple single argument
			$file = UPLOAD_DIR . encode($page) . '_' . encode($name);
			$is_file = is_file($file);
		}
		if (! $is_file) {
			$params['_error'] = htmlsc('File not found: "' .
				$name . '" at page "' . $page . '"');
			return $params;
		}
	}

	// 残りの引数の処理
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
	$title = $url = $url2 = $info = '';
	$width = $height = 0;
	$matches = array();

	if ($is_url) {	// URL
		if (PKWK_DISABLE_INLINE_IMAGE_FROM_URI) {
			//$params['_error'] = 'PKWK_DISABLE_INLINE_IMAGE_FROM_URI prohibits this';
			//return $params;
			$url = htmlsc($name);
			$params['_body'] = '<a href="' . $url . '">' . $url . '</a>';
			return $params;
		}

		$url = $url2 = htmlsc($name);
		$title = htmlsc(preg_match('/([^\/]+)$/', $name, $matches) ? $matches[1] : $url);

		$is_image = (! $params['noimg'] && preg_match(PLUGIN_REF_IMAGE, $name));

		if ($is_image && PLUGIN_REF_URL_GET_IMAGE_SIZE && (bool)ini_get('allow_url_fopen')) {
			$size = @getimagesize($name);
			if (is_array($size)) {
				$width  = $size[0];
				$height = $size[1];
				$info   = $size[3];
			}
		}

	} else { // 添付ファイル

		$title = htmlsc($name);

		$is_image = (! $params['noimg'] && preg_match(PLUGIN_REF_IMAGE, $name));

		// Count downloads with attach plugin
		$url = $script . '?plugin=attach' . '&amp;refer=' . rawurlencode($page) .
			'&amp;openfile=' . rawurlencode($name); // Show its filename at the last

		if ($is_image) {
			// Swap $url
			$url2 = $url;

			// URI for in-line image output
			if (! PLUGIN_REF_DIRECT_ACCESS) {
				// With ref plugin (faster than attach)
				$url = $script . '?plugin=ref' . '&amp;page=' . rawurlencode($page) .
					'&amp;src=' . rawurlencode($name); // Show its filename at the last
			} else {
				// Try direct-access, if possible
				$url = $file;
			}

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
			$title = htmlsc(join(',', $_title));
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
	$params['_align'] = PLUGIN_REF_DEFAULT_ALIGN;
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

// Output an image (fast, non-logging <==> attach plugin)
function plugin_ref_action()
{
	global $vars;

	$usage = 'Usage: plugin=ref&amp;page=page_name&amp;src=attached_image_name';

	if (! isset($vars['page']) || ! isset($vars['src']))
		return array('msg'=>'Invalid argument', 'body'=>$usage);

	$page     = $vars['page'];
	$filename = $vars['src'] ;

	$ref = UPLOAD_DIR . encode($page) . '_' . encode(preg_replace('#^.*/#', '', $filename));
	if(! file_exists($ref))
		return array('msg'=>'Attach file not found', 'body'=>$usage);

	$got = @getimagesize($ref);
	if (! isset($got[2])) $got[2] = FALSE;
	switch ($got[2]) {
	case 1: $type = 'image/gif' ; break;
	case 2: $type = 'image/jpeg'; break;
	case 3: $type = 'image/png' ; break;
	case 4: $type = 'application/x-shockwave-flash'; break;
	default:
		return array('msg'=>'Seems not an image', 'body'=>$usage);
	}

	// Care for Japanese-character-included file name
	if (LANG == 'ja') {
		switch(UA_NAME . '/' . UA_PROFILE){
		case 'Opera/default':
			// Care for using _auto-encode-detecting_ function
			$filename = mb_convert_encoding($filename, 'UTF-8', 'auto');
			break;
		case 'MSIE/default':
			$filename = mb_convert_encoding($filename, 'SJIS', 'auto');
			break;
		}
	}
	$utf8filename = mb_convert_encoding($filename, 'UTF-8', 'auto');
	$size = filesize($ref);

	// Output
	pkwk_common_headers();
	header('Content-Disposition: inline; filename="' . $filename . '"; filename*=utf-8\'\'' . rawurlencode($utf8filename));
	header('Content-Length: ' . $size);
	header('Content-Type: '   . $type);
	@readfile($ref);
	exit;
}
?>
