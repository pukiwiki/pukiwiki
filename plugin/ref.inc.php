<?php
// $Id: ref.inc.php,v 1.2.2.3 2004/06/22 12:08:32 henoheno Exp $
/*
Last-Update:2002-10-29 rev.33

*プラグイン ref
ページに添付されたファイルを展開する

*Usage
 #ref(filename[,Page][[,{Left|Center|Right}]|[,{Wrap|Nowrap}]|[,Around]]{}[,comments])

*パラメータ
-filename~
 添付ファイル名、あるいはURL
-Page~
 WikiNameまたはBracketNameを指定すると、そのページの添付ファイルを参照する
-Left|Center|Right~
 横の位置合わせ
-Wrap|Nowrap~
 テーブルタグで囲む/囲まない
-Around~
 テキストの回り込み

*/

// file icon image
if (!defined('FILE_ICON'))
{
	define('FILE_ICON','<img src="' . IMAGE_DIR . 'file.gif" width="20" height="20" alt="file" style="border-width:0px" />');
}

// default alignment
define('REF_DEFAULT_ALIGN','left'); // 'left','center','right'

// force wrap on default
define('REF_WRAP_TABLE',FALSE); // TRUE,FALSE

function plugin_ref_convert() {

	global $script,$vars;
	global $WikiName, $BracketName;

	//戻り値
	$ret = '';

	//エラーチェック
	if (!func_num_args()) return 'no argument(s).';

	//添付ファイル名を取得
	$args = func_get_args();
	$name = array_shift($args);

// $nameをもとに以下の変数を設定
// $url : URL
// $title :タイトル
// $ext : 拡張子判別用文字列
// $icon : アイコンのimgタグ
// $size : 画像ファイルのときサイズ
// $info : 画像ファイル以外のファイルの情報
//  添付ファイルのとき : ファイルの最終更新日とサイズ
//  URLのとき : URLそのもの

	if (is_url($name)) { //URL
		$url = $ext = $info = htmlspecialchars($name);
		$icon = $size = '';
		if (preg_match('/([^\/]+)$/', $name, $match)) { $ext = $match[1]; }
	} else { //添付ファイル
		$icon = FILE_ICON;
		if (!is_dir(UPLOAD_DIR)) return 'no UPLOAD_DIR.';
		//ページ指定のチェック
		$page = $vars['page'];
		if (count($args) > 0) {
			$_page = get_fullname($args[0],$vars['page']);
			if (is_page($_page)) {
				$page = $_page;
				array_shift($args);
			}
		}
		if (!is_page($page)) { return 'page not found.'; }

		$ext = $name;
		$file = UPLOAD_DIR.encode($page).'_'.encode($name);
		if (!is_file($file)) { return 'not found.'; }

		if (is_picture($ext)) {
			$url = $file;
			$size = getimagesize($file);
			$size = $size[3];
		} else {
			$url = $script.'?plugin=attach&amp;openfile='.rawurlencode($name).'&amp;refer='.rawurlencode($page);
			$lastmod = date('Y/m/d H:i:s',filemtime($file));
			$size = sprintf('%01.1f',round(filesize($file)/1000,1)).'KB';
			$info = "$lastmod $size";
		}
	}

	//パラメータ変換
	$params = array('left'=>FALSE,'center'=>FALSE,'right'=>FALSE,'wrap'=>FALSE,'nowrap'=>FALSE,'around'=>FALSE,'_args'=>array(),'_done'=>FALSE);
	array_walk($args, 'ref_check_arg', &$params);
	if (count($params['_args']) > 0) { $title = join(',', $params['_args']); }

	//タイトルを決定
	if (!isset($title) or $title == '') { $title = $ext; }
	$title = htmlspecialchars($title);

	//アラインメント判定
	if ($params['right'])
		$align = 'right';
	else if ($params['left'])
		$align = 'left';
	else if ($params['center'])
		$align = 'center';
	else
		$align = REF_DEFAULT_ALIGN;

	// ファイル種別判定
	if (is_picture($ext)) { // 画像
		$ret .= "<img src=\"$url\" alt=\"$title\" title=\"$title\" $size />";
	} else { // 通常ファイル
		$ret .= "<a href=\"$url\" title=\"$info\">$icon$title</a>\n";
	}

	if ((REF_WRAP_TABLE and !$params['nowrap']) or $params['wrap']) {
		$ret = wrap_table($ret, $align, $params['around']);
	}
	$ret = wrap_div($ret, $align, $params['around']);
	return $ret;
}

//-----------------------------------------------------------------------------
// 画像かどうか
function is_picture($text) {
	return preg_match('/(\.gif|\.png|\.jpeg|\.jpg)$/i', $text);
}
// divで包む
function wrap_div($text, $align, $around) {
	if ($around) {
		$style = ($align == 'right') ? 'float:right' : 'float:left';
	} else {
		$style = "text-align:$align";
	}
	return "<div class=\"img_margin\" style=\"$style\">$text</div>\n";
}
// 枠で包む
// margin:auto Moz1=x(wrap,aroundが効かない),op6=oNN6=x(wrap,aroundが効かない)IE6=x(wrap,aroundが効かない)
// margin:0px Moz1=x(wrapで寄せが効かない),op6=x(wrapで寄せが効かない),nn6=x(wrapで寄せが効かない),IE6=o
function wrap_table($text, $align, $around) {
	$margin = ($around ? '0px' : 'auto');
	$margin_align = ($align == 'center') ? '' : ";margin-$align:0px";
	return "<table class=\"style_table\" style=\"margin:$margin$margin_align\">\n<tr><td class=\"style_td\">\n$text\n</td></tr>\n</table>\n";
}
//オプションを解析する
function ref_check_arg($val, $_key, &$params) {
	if ($val == '') { $params['_done'] = TRUE; return; }
	if (!$params['_done']) {
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
