<?php
/*
 * PukiWiki 最新の?件を表示するプラグイン
 *
 * CopyRight 2002 Y.MASUI GPL2
 * http://masui.net/pukiwiki/ masui@masui.net
 * 
 * 変更履歴:
 *  2002.04.08: patさん、みのるさんの指摘により、リンク先が日本語の場合に
 *              化けるのを修正
 * 
 *  2002.06.17: plugin_recent_init()を設定
 *  2002.07.02: <ul>による出力に変更し構造化
 *
 * $id$
 */

function plugin_recent_convert()
{
	global $script,$BracketName,$whatsnew,$date_format;
	global $_recent_plugin_frame;
	
	$recent_lines = 10;
	if (func_num_args())
		list($recent_lines) = func_get_args();
	
	$date = $items = '';
	$lines = array_splice(preg_grep('/^\/\//',get_source($whatsnew)),0,$recent_lines);
	foreach($lines as $line) {
		if (!preg_match("/^\/\/(\d+)\s($BracketName)$/",$line,$match))
			continue; // fatal error, die?
		
		$page = $match[2];
		$_date = get_date($date_format,$match[1]);
		if ($date != $_date) {
			if ($date != '')
				$items .= '</ul>';
			$date = $_date;
			$items .= "<strong>$date</strong>\n<ul class=\"recent_list\">";
		}
		$s_page = htmlspecialchars($page);
		$r_page = rawurlencode($page);
		$pg_passage = get_pg_passage($page,FALSE);
		$items .=" <li><a href=\"$script?$r_page\" title=\"$s_page $pg_passage\">$s_page</a></li>\n";
	}
	if (count($lines)) {
		$items .='</ul>';
	}
	return sprintf($_recent_plugin_frame,count($lines),$items);
}
?>
