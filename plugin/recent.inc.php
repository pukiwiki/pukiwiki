<?
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
 *
 * $Id: recent.inc.php,v 1.2 2002/06/26 06:23:57 masui Exp $
 */

function plugin_recent_init()
{
  $_plugin_recent_messages = array(
    '_recent_plugin_li'=>'・',
    '_recent_plugin_frame '=>'<span align="center"><h5 class="side_label">最新の%d件</h5></span><small>%s</small>');
  set_plugin_messages($_plugin_recent_messages);
}

function plugin_recent_convert()
{
	global $_recent_plugin_li,$_recent_plugin_frame;
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
						$items .= "<br>";
					}
					$items .= "<b>".$match[0]."</b><br>";
					$date = $match[0];
				}
			}
			$items .= $_recent_plugin_li."<a href=\"".$script."?".rawurlencode($name)."\" title=\"$title ".get_pg_passage($name,false)."\">".$title."</a><BR>\n";
			$cnt++;
		}
	}
	return sprintf($_recent_plugin_frame,$cnt,$items);
}
?>
