<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: popular.inc.php,v 1.6 2003/08/20 02:38:30 arino Exp $
//

/*
 * PukiWiki popular プラグイン
 * (C) 2002, Kazunori Mizushima <kazunori@uc.netyou.jp>
 *
 * 人気のある(アクセス数の多い)ページの一覧を recent プラグインのように表示します。
 * 通算および今日に別けて一覧を作ることができます。
 * counter プラグインのアクセスカウント情報を使っています。
 *
 * [使用例]
 * #popular
 * #popular(20)
 * #popular(20,FrontPage|MenuBar)
 * #popular(20,FrontPage|MenuBar,true)
 *
 * [引数]
 * 1 - 表示する件数                             default 10
 * 2 - 表示させないページの正規表現             default なし
 * 3 - 通算(true)か今日(false)の一覧かのフラグ  default false
 */

// counter file : counter プラグインで設定しているものと同じにして下さい。
if (!defined('COUNTER_DIR'))
	define('COUNTER_DIR', './counter/');

function plugin_popular_convert()
{
	global $_popular_plugin_frame, $_popular_plugin_today_frame;
	global $script,$whatsnew,$non_list;
	
	$max = 10;
	$except = '';

	$array = func_get_args();
	$today = FALSE;

	switch (func_num_args()) {
	case 3:
		if ($array[2])
			$today = get_date('Y/m/d');
	case 2:
		$except = $array[1];
	case 1:
		$max = $array[0];
	}

	$counters = array();

	foreach (get_existpages(COUNTER_DIR,'.count') as $file=>$page) {
		if ($except != '' and ereg($except,$page)) {
			continue;
		}
		if ($page == $whatsnew or preg_match("/$non_list/",$page) or !is_page($page)) {
			continue;
		}
		
		$array = file($file);
		$count = rtrim($array[0]);
		$date = rtrim($array[1]);
		$today_count = rtrim($array[2]);
		$yesterday_count = rtrim($array[3]);
		
		if ($today) {
			if ($today == $date) {
				// $pageが数値に見える(たとえばencode('BBS')=424253)とき、
				// array_splice()によってキー値が変更されてしまうのを防ぐ
				$counters["_$page"] = $today_count;
			}
		}
		else {
			$counters["_$page"] = $count;
		}
	}
	
	asort($counters, SORT_NUMERIC);
	$counters = array_splice(array_reverse($counters,TRUE),0,$max);
	
	$items = '';
	if (count($counters)) {
		$items = '<ul class="popular_list">';
		
		foreach ($counters as $page=>$count) {
			$page = substr($page,1);
			
			$s_page = htmlspecialchars($page);
			$items .= " <li>".make_pagelink($page,"$s_page<span class=\"counter\">($count)</span>")."</li>\n";
		}
		$items .= '</ul>';
	}
	return sprintf($today ? $_popular_plugin_today_frame : $_popular_plugin_frame,count($counters),$items);
}

?>