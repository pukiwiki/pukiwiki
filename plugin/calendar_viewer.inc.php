<?php
/*
 * PukiWiki calendar_viewer plubin
 * $Id: calendar_viewer.inc.php,v 1.26 2004/12/30 09:21:05 henoheno Exp $
 * Based on calendarrecent plugin
 */

/*
 * 概要
 * calendarプラグインやcalendar2プラグインで作成したページを一覧表示するためのプラグインです。
 * 使い方
 *  #calendar_viewer(pagename,(this|yyyy-mm|n|x*y),[mode],[separater])
 *
 ** pagename
 * - calendar or calendar2プラグインを記述してるページ名
 *
 ** (yyyy-mm|n|this)
 * this    - 今月のページを一覧表示
 * yyyy-mm - yyyy-mmで指定した年月のページを一覧表示
 * n       - 先頭からn件を一覧表示
 * x*n     - 先頭より数えて x ページ目(先頭は0)から、y件づつ一覧表示
 *
 ** [mode]
 * Default:past
 * past   - 今日以前のページの一覧表示モード。更新履歴や日記向き
 * future - 今日以降のページの一覧表示モード。イベント予定やスケジュール向き
 * view   - 過去から未来への一覧表示モード。表示抑止するページはありません
 *
 ** [separater]
 * - 年月日を区切るセパレータを指定。
 * - 省略可能。デフォルトは-。（calendar2なら省略でOK）
 *
 * TODO
 *  past or future で月単位表示するときに、それぞれ来月、先月の一覧へのリンクを表示しないようにする
 */

function plugin_calendar_viewer_convert()
{
	global $vars, $get, $post, $hr, $script;
	global $_err_calendar_viewer_param, $_err_calendar_viewer_param2;
	global $_msg_calendar_viewer_right, $_msg_calendar_viewer_left;
	global $_msg_calendar_viewer_restrict;

	static $viewed = array();

	if (func_num_args() < 2)
		return '#calendar_viewer(): ' . $_err_calendar_viewer_param . '<br />' . "\n";

	$func_args = func_get_args();

	// Default values
	$pagename    = $func_args[0];	// 基準となるページ名
	$page_YM     = '';	// 一覧表示する年月
	$limit_base  = 0;	// 先頭から数えて何ページ目から表示するか (先頭)
	$limit_pitch = 0;	// 何件づつ表示するか
	$limit_page  = 0;	// サーチするページ数
	$mode        = 'past';	// 動作モード
	$date_sep    = '-';	// 日付のセパレータ calendar2なら '-', calendarなら ''

	// Check $func_args[1]
	if (preg_match('/[0-9]{4}' . $date_sep . '[0-9]{2}/', $func_args[1])) {
		// 指定年月の一覧表示
		$page_YM     = $func_args[1];
		$limit_page  = 31;
	} else if (preg_match('/this/si', $func_args[1])) {
		// 今月の一覧表示
		$page_YM     = get_date('Y' . $date_sep . 'm');
		$limit_page  = 31;
	} else if (preg_match('/^[0-9]+$/', $func_args[1])) {
		// n日分表示
		$limit_pitch = $func_args[1];
		$limit_page  = $func_args[1];
	} else if (preg_match('/(-?[0-9]+)\*([0-9]+)/', $func_args[1], $matches)) {
		// 先頭より数えて x ページ目から、y件づつ表示
		$limit_base  = $matches[1];
		$limit_pitch = $matches[2];
		$limit_page  = $matches[1] + $matches[2]; // 読み飛ばす + 表示する
	} else {
		return '#calendar_viewer(): ' . $_err_calendar_viewer_param2 . '<br />' . "\n";
	}

	// $func_args[2]: Mode setting
	if (isset($func_args[2]) && preg_match('/^(past|view|future)$/si', $func_args[2]))
		$mode = $func_args[2];

	// $func_args[3]: Change default delimiter
	if (isset($func_args[3])) $date_sep = $func_args[3];

	// Avoid Loop etc.
	if (isset($viewed[$pagename])) {
		$s_page = htmlspecialchars($pagename);
		return "#calendar_viewer(): You already view: $s_page<br />";
	} else {
		$viewed[$pagename] = TRUE; // Valid
	}

	// 一覧表示するページ名とファイル名のパターン　ファイル名には年月を含む
	if ($pagename == '') {
		// pagename無しのyyyy-mm-ddに対応するための処理
		$pagepattern     = '';
		$pagepattern_len = 0;
		$filepattern     = encode($page_YM);
		$filepattern_len = strlen($filepattern);
	} else {
		$pagepattern     = strip_bracket($pagename) . '/';
		$pagepattern_len = strlen($pagepattern);
		$filepattern     = encode($pagepattern . $page_YM);
		$filepattern_len = strlen($filepattern);
	}

	// ページリストの取得
	$pagelist = array();
	if ($dir = @opendir(DATA_DIR)) {
		$_date = get_date('Y' . $date_sep . 'm' . $date_sep . 'd');
		$page_date  = '';
		while($file = readdir($dir)) {
			if ($file == '..' || $file == '.') continue;
			if (substr($file, 0, $filepattern_len) != $filepattern) continue;

			$page      = decode(trim(preg_replace('/\.txt$/', ' ', $file)));
			$page_date = substr($page, $pagepattern_len);

			// Verify the $page_date pattern (Default: yyyy-mm-dd).
			// Past-mode hates the future, and
			// Future-mode hates the past.
			if ((plugin_calendar_viewer_isValidDate($page_date, $date_sep) == FALSE) || 
				($page_date > $_date && ($mode == 'past')) ||
				($page_date < $_date && ($mode == 'future')))
					continue;

			$pagelist[] = $page;
		}
	}
	closedir($dir);

	if ($mode == 'past') {
		rsort($pagelist);	// New => Old
	} else {
		sort($pagelist);	// Old => New
	}

	// Include start
	$tmppage     = $vars['page'];
	$return_body = '';

	// $limit_page の件数までインクルード
	$tmp = max($limit_base, 0); // Skip minus
	while ($tmp < $limit_page) {
		if (! isset($pagelist[$tmp])) break;

		$page = $pagelist[$tmp];
		$get['page'] = $post['page'] = $vars['page'] = $page;

		// 現状で閲覧許可がある場合だけ表示する
		if (check_readable($page, FALSE, FALSE)) {
			$body = convert_html(get_source($page));
		} else {
			$body = str_replace('$1', $page, $_msg_calendar_viewer_restrict);
		}

		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		$link   = '<a href="' . $script . '?cmd=edit&amp;page=' . $r_page . '">' . $s_page . '</a>';
		$head   = '<h1>' . $link . '</h1>' . "\n";
		$return_body .= $head . $body;

		++$tmp;
	}

	// ここで、前後のリンクを表示
	// ?plugin=calendar_viewer&file=ページ名&date=yyyy-mm
	$enc_pagename = rawurlencode(substr($pagepattern, 0, $pagepattern_len - 1));

	if ($page_YM != '') {
		// 年月表示時
		$date_sep_len = strlen($date_sep);
		$this_year    = substr($page_YM, 0, 4);
		$this_month   = substr($page_YM, 4 + $date_sep_len, 2);

		// 次月
		$next_year  = $this_year;
		$next_month = $this_month + 1;
		if ($next_month > 12) {
			++$next_year;
			$next_month = 1;
		}
		$next_YM = sprintf('%04d%s%02d', $next_year, $date_sep, $next_month);

		// 前月
		$prev_year  = $this_year;
		$prev_month = $this_month - 1;
		if ($prev_month < 1) {
			--$prev_year;
			$prev_month = 12;
		}
		$prev_YM = sprintf('%04d%s%02d', $prev_year, $date_sep, $prev_month);
		if ($mode == 'past') {
			$right_YM   = $prev_YM;
			$right_text = $prev_YM . '&gt;&gt;'; // >>
			$left_YM    = $next_YM;
			$left_text  = '&lt;&lt;' . $next_YM; // <<
		} else {
			$left_YM    = $prev_YM;
			$left_text  = '&lt;&lt;' . $prev_YM; // <<
			$right_YM   = $next_YM;
			$right_text = $next_YM . '&gt;&gt;'; // >>
		}
	} else {
		// n件表示時
		if ($limit_base < 0) {
			$left_YM = ''; // 表示しない (それより前の項目はない)
		} else {
			$left_YM   = $limit_base - $limit_pitch . '*' . $limit_pitch;
			$left_text = sprintf($_msg_calendar_viewer_left, $limit_pitch);

		}
		if ($limit_base + $limit_pitch >= count($pagelist)) {
			$right_YM = ''; // 表示しない (それより後の項目はない)
		} else {
			$right_YM   = $limit_base + $limit_pitch . '*' . $limit_pitch;
			$right_text = sprintf($_msg_calendar_viewer_right, $limit_pitch);
		}
	}

	// ナビゲート用のリンクを末尾に追加
	if ($left_YM != '' || $right_YM != '') {
		$s_date_sep = htmlspecialchars($date_sep);
		$left_link = $right_link = '';
		$link = $script . '?plugin=calendar_viewer&amp;mode=' . $mode .
			'&amp;file=' . $enc_pagename . '&amp;date_sep=' . $s_date_sep . '&amp;';
		if ($left_YM != '')
			$left_link = '<a href="' . $link .
				'date=' . $left_YM . '">' . $left_text . '</a>';
		if ($right_YM != '')
			$right_link = '<a href="' . $link .
				'date=' . $right_YM . '">' . $right_text . '</a>';
		// past modeは<<新 旧>> 他は<<旧 新>>
		$return_body .=
			'<table width ="100%"><tr>' .
			'<td align="left">'  . $left_link  . '</td>' .
			'<td align="right">' . $right_link . '</td>' .
			'</tr></table>';
	}

	$get['page'] = $post['page'] = $vars['page'] = $tmppage;

	return $return_body;
}

function plugin_calendar_viewer_action()
{
	global $vars, $get, $post, $hr, $script;

	$date_sep = '-';

	$return_vars_array = array();

	$page = strip_bracket($vars['page']);
	$vars['page'] = '*';
	if (isset($vars['file'])) $vars['page'] = $vars['file'];

	$date_sep = $vars['date_sep'];

	$page_YM = $vars['date'];
	if ($page_YM == '') $page_YM = get_date('Y' . $date_sep . 'm');
	$mode = $vars['mode'];

	$args_array = array($vars['page'], $page_YM, $mode, $date_sep);
	$return_vars_array['body'] = call_user_func_array('plugin_calendar_viewer_convert', $args_array);

	//$return_vars_array['msg'] = 'calendar_viewer ' . $vars['page'] . '/' . $page_YM;
	$return_vars_array['msg'] = 'calendar_viewer ' . htmlspecialchars($vars['page']);
	if ($vars['page'] != '') $return_vars_array['msg'] .= '/';
	if (preg_match('/\*/', $page_YM)) {
		// うーん、n件表示の時はなんてページ名にしたらいい？
	} else {
		$return_vars_array['msg'] .= htmlspecialchars($page_YM);
	}

	$vars['page'] = $page;
	return $return_vars_array;
}

function plugin_calendar_viewer_isValidDate($aStr, $aSepList = '-/ .')
{
	if ($aSepList == '') {
		// yyymmddとしてチェック（手抜き(^^;）
		return checkdate(substr($aStr, 4, 2), substr($aStr, 6, 2), substr($aStr, 0, 4));
	} else if (ereg("^([0-9]{2,4})[$aSepList]([0-9]{1,2})[$aSepList]([0-9]{1,2})$", $aStr, $m) ) {
		return checkdate($m[2], $m[3], $m[1]);
	} else {
		return FALSE;
	}
}

?>
