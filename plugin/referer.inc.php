<?php
// $Id: referer.inc.php,v 1.10 2005/01/23 05:20:02 henoheno Exp $
/*
 * PukiWiki Referer プラグイン(リンク元表示プラグイン)
 * (C) 2003, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
*/

define('CONFIG_REFERER', 'plugin/referer/config');

function plugin_referer_action()
{
	global $vars, $referer;
	global $_referer_msg;

	// Setting: Off
	if (! $referer) return array('msg'=>'','body'=>'');

	if (isset($vars['page']) && is_page($vars['page'])) {
		$sort = (empty($vars['sort'])) ? '0d' : $vars['sort'];
		return array(
			'msg'  => $_referer_msg['msg_H0_Refer'],
			'body' => plugin_referer_body($vars['page'], $sort));
	}
	$pages = get_existpages(TRACKBACK_DIR, '.ref');

	if (empty($pages)) {
		return array('msg'=>'', 'body'=>'');
	} else {
		return array(
			'msg'  => 'referer list',
			'body' => page_list($pages, 'referer', FALSE));
	}
}

// Referer 明細行編集
function plugin_referer_body($page, $sort)
{
	global $script, $_referer_msg;

	$data = tb_get(tb_get_filename($page, '.ref'));
	if (empty($data)) return '<p>no data.</p>';

	$bg = plugin_referer_set_color();

	$arrow_last = $arrow_1st = $arrow_ctr = '';
	$color_last = $color_1st = $color_ctr = $color_ref = $bg['etc'];
	$sort_last = '0d';
	$sort_1st  = '1d';
	$sort_ctr  = '2d';

	switch ($sort) {
	case '0d': // 0d 最終更新日時(新着順)
		usort($data, create_function('$a,$b', 'return $b[0] - $a[0];'));
		$color_last = $bg['cur'];
		$arrow_last = $_referer_msg['msg_Chr_darr'];
		$sort_last = '0a';
		break;
	case '0a': // 0a 最終更新日時(日付順)
		usort($data, create_function('$a,$b', 'return $a[0] - $b[0];'));
		$color_last = $bg['cur'];
		$arrow_last = $_referer_msg['msg_Chr_uarr'];
//		$sort_last = '0d';
		break;
	case '1d': // 1d 初回登録日時(新着順)
		usort($data, create_function('$a,$b', 'return $b[1] - $a[1];'));
		$color_1st = $bg['cur'];
		$arrow_1st = $_referer_msg['msg_Chr_darr'];
		$sort_1st = '1a';
		break;
	case '1a': // 1a 初回登録日時(日付順)
		usort($data, create_function('$a,$b', 'return $a[1] - $b[1];'));
		$color_1st = $bg['cur'];
		$arrow_1st = $_referer_msg['msg_Chr_uarr'];
//		$sort_1st = '1d';
		break;
	case '2d': // 2d カウンタ(大きい順)
		usort($data, create_function('$a,$b', 'return $b[2] - $a[2];'));
		$color_ctr = $bg['cur'];
		$arrow_ctr = $_referer_msg['msg_Chr_darr'];
		$sort_ctr = '2a';
		break;
	case '2a': // 2a カウンタ(小さい順)
		usort($data, create_function('$a,$b', 'return $a[2] - $b[2];'));
		$color_ctr = $bg['cur'];
		$arrow_ctr = $_referer_msg['msg_Chr_uarr'];
//		$sort_ctr = '2d';
		break;
	case '3': // 3 Referer
		usort($data, create_function('$a,$b',
			'return ($a[3] == $b[3]) ? 0 : (($a[3] > $b[3]) ? 1 : -1);'));
		$color_ref = $bg['cur'];
		break;
	}

	$body = '';
	foreach ($data as $arr) {
		// 0:最終更新日時, 1:初回登録日時, 2:参照カウンタ, 3:Referer ヘッダ, 4:利用可否フラグ(1は有効)
		list($ltime, $stime, $count, $url, $enable) = $arr;

		// 非ASCIIキャラクタ(だけ)をURLエンコードしておく BugTrack/440
		$e_url = htmlsc(preg_replace('/([" \x80-\xff]+)/e', 'rawurlencode("$1")', $url));
		$s_url = htmlsc(mb_convert_encoding(rawurldecode($url), SOURCE_ENCODING, 'auto'));

		$lpass = get_passage($ltime, FALSE); // 最終更新日時からの経過時間
		$spass = get_passage($stime, FALSE); // 初回登録日時からの経過時間
		$ldate = get_date($_referer_msg['msg_Fmt_Date'], $ltime); // 最終更新日時文字列
		$sdate = get_date($_referer_msg['msg_Fmt_Date'], $stime); // 初回登録日時文字列

		$body .=
			' <tr>' . "\n" .
			'  <td>' . $ldate . '</td>' . "\n" .
			'  <td>' . $lpass . '</td>' . "\n";

		$body .= ($count == 1) ?
			'  <td colspan="2">N/A</td>' . "\n" :
			'  <td>' . $sdate . '</td>' . "\n" .
			'  <td>' . $spass . '</td>' . "\n";

		$body .= '  <td style="text-align:right;">' . $count . '</td>' . "\n";

		// 適用不可データのときはアンカーをつけない
		$body .= plugin_referer_ignore_check($url) ?
			'  <td>' . $s_url . '</td>' . "\n" :
			'  <td><a href="' . $e_url . '" rel="nofollow">' . $s_url . '</a></td>' . "\n";

		$body .= ' </tr>' . "\n";
	}
	$href = $script . '?plugin=referer&amp;page=' . rawurlencode($page);
	return <<<EOD
<table border="1" cellspacing="1" summary="Referer">
 <tr>
  <td style="background-color:$color_last" colspan="2">
   <a href="$href&amp;sort=$sort_last">{$_referer_msg['msg_Hed_LastUpdate']}$arrow_last</a>
  </td>
  <td style="background-color:$color_1st" colspan="2">
   <a href="$href&amp;sort=$sort_1st">{$_referer_msg['msg_Hed_1stDate']}$arrow_1st</a>
  </td>
  <td style="background-color:$color_ctr;text-align:right">
   <a href="$href&amp;sort=$sort_ctr">{$_referer_msg['msg_Hed_RefCounter']}$arrow_ctr</a>
  </td>
  <td style="background-color:$color_ref">
   <a href="$href&amp;sort=3">{$_referer_msg['msg_Hed_Referer']}</a>
   </td>
 </tr>
 $body
</table>
EOD;
}

function plugin_referer_set_color()
{
	static $color;

	if (! isset($color)) {
		// Default color
		$color = array('cur' => '#88ff88', 'etc' => '#cccccc');

		$config = new Config(CONFIG_REFERER);
		$config->read();
		$pconfig_color = $config->get('COLOR');
		unset($config);

		// BGCOLOR(#88ff88)
		$matches = array();
		foreach ($pconfig_color as $x)
			$color[$x[0]] = htmlsc(
				preg_match('/BGCOLOR\(([^)]+)\)/si', $x[1], $matches) ?
					$matches[1] : $x[1]);
	}
	return $color;
}

function plugin_referer_ignore_check($url)
{
	static $ignore_url;

	// config.php
	if (! isset($ignore_url)) {
		$config = new Config(CONFIG_REFERER);
		$config->read();
		$ignore_url = $config->get('IGNORE');
		unset($config);
	}

	foreach ($ignore_url as $x)
		if (strpos($url, $x) !== FALSE)
			return 1;
	return 0;
}
?>
