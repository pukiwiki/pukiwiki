<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: setlinebreak.inc.php,v 1.1 2004/03/18 10:45:47 arino Exp $
//
// 改行を<br />に置換するフラグ($line_break)を設定する
//
// #setlinebreak(on) : これ以降、改行を<br />に置換する
// #setlinebreak(off) : これ以降、改行を<br />に置換しない
// #setlinebreak : これ以降、改行を<br />に置換する/しないを切り替え
// #setlinebreak(default) : これ以降、改行の扱いをシステム設定に戻す

function plugin_setlinebreak_convert()
{
	global $line_break;
	static $default;
	
	if (!isset($default))
	{
		$default = $line_break;
	}
	if (func_num_args() == 0)
	{
		$line_break = !$line_break;
		return '';
	}
	
	$args = func_get_args();
	
	switch (strtolower($args[0]))
	{
		case 'on':
		case 'true':
		case '1':
			$line_break = 1;
			break;
		
		case 'off':
		case 'false':
		case '0':
			$line_break = 0;
			break;
		case 'default':
			$line_break = $default;
			break;
		
		default:
			return FALSE;
	}
	return '';
}
?>
