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
 * $Id: recent.inc.php,v 1.5.2.2 2004/07/31 03:15:07 henoheno Exp $
 */

function plugin_recent_init()
{
  $_plugin_recent_messages = array(
    '_recent_plugin_frame '=>'<h5 class="side_label" style="margin:auto;margin-top:0px;margin-bottom:.5em">最新の%d件</h5><div class="small" style="margin-left:.8em;margin-right:.8em">%s</div>');
  set_plugin_messages($_plugin_recent_messages);
}

function plugin_recent_convert()
{
	global $_recent_plugin_frame;
	global $WikiName,$BracketName,$script,$whatsnew;

	$recent_lines = 10;
	if(func_num_args()>0) {
		$array = func_get_args();
		$recent_lines = $array[0];
	}

	$lines = file(get_filename(encode($whatsnew)));
	$date = $items = "";
	$cnt = 0;
	foreach($lines as $line)
	{
		if($cnt > $recent_lines - 1) break;
		if(preg_match("/(($WikiName)|($BracketName))/",$line,$match))
		{
			$name = $match[1];
			if($match[2])
			{
				$title = $match[1];
			}
			else
			{
				$title = strip_bracket($match[1]);
 			}
			if(preg_match("/([0-9]{4}-[0-9]{2}-[0-9]{2})/",$line,$match)) {
				if($date != $match[0]) {
					if($date != '') {
						$items .= "</ul>";
					}
					$items .= "<strong>".$match[0]."</strong><ul class=\"recent_list\">";
					$date = $match[0];
				}
			}
			$title = htmlspecialchars($title);
			$items .="<li><a href=\"".$script."?".rawurlencode($name)."\" title=\"$title ".get_pg_passage($name,false)."\">".$title."</a></li>\n";
			$cnt++;
		}
	}
	$items .="</ul>";
	return sprintf($_recent_plugin_frame,$cnt,$items);
}
?>
