<?php
// PukiWiki - Yet another WikiWikiWeb clone
// popular.inc.php
// Copyright
//   2003-2019 PukiWiki Development Team
//   2002 Kazunori Mizushima <kazunori@uc.netyou.jp>
// License: WHERE IS THE RECORD?
//
// Popular pages plugin: Show an access ranking of this wiki
// -- like recent plugin, using counter plugin's count --

/**
 *
 * 通算および今日に別けて一覧を作ることができます。
 *
 * [Usage]
 *   #popular
 *   #popular(20)
 *   #popular(20,FrontPage|MenuBar)
 *   #popular(20,FrontPage|MenuBar,true)
 *
 * [Arguments]
 *   1 - 表示する件数                             default 10
 *   2 - 表示させないページの正規表現             default なし
 *   3 - 通算(true)か今日(false)の一覧かのフラグ  default false
 */

define('PLUGIN_POPULAR_DEFAULT', 10);

function plugin_popular_convert()
{
	global $vars;
	global $_popular_plugin_frame, $_popular_plugin_today_frame;

	$max    = PLUGIN_POPULAR_DEFAULT;
	$except = '';

	$array = func_get_args();
	$today = FALSE;
	$today_param = $array[2];
	switch (func_num_args()) {
	case 3: if ($today_param && $today_param !== 'false') $today = get_date('Y/m/d');
	case 2: $except = $array[1];
	case 1: $max    = (int)$array[0];
	}
	if (exist_plugin('counter')) {
		$counters = plugin_counter_get_popular_list($today, $except, $max);
	} else {
		$counters = array();
	}

	$items = '';
	if (! empty($counters)) {
		$items = '<ul class="popular_list">' . "\n";

		foreach ($counters as $page=>$count) {
			$page = substr($page, 1);

			$s_page = htmlsc($page);
			if ($page === $vars['page']) {
				// No need to link itself, notifies where you just read
				$attrs = get_page_link_a_attrs($page);
				$items .= ' <li><span class="' .
					$attrs['class'] . '" data-mtime="' . $attrs['data_mtime'] .
					'">' . $s_page . '<span class="counter">(' . $count .
					')</span></span></li>' . "\n";
			} else {
				$items .= ' <li>' . make_pagelink($page,
					$s_page . '<span class="counter">(' . $count . ')</span>') .
					'</li>' . "\n";
			}
		}
		$items .= '</ul>' . "\n";
	}

	return sprintf($today ? $_popular_plugin_today_frame : $_popular_plugin_frame, count($counters), $items);
}
