<?php
/*
 * PukiWiki カウンタープラグイン
 *
 * CopyRight 2002 Y.MASUI GPL2
 * http://masui.net/pukiwiki/ masui@masui.net
 *
 * $Id: counter.inc.php,v 1.6 2003/01/27 05:38:44 panda Exp $
 */

// counter file
if (!defined('COUNTER_DIR')) {
	define('COUNTER_DIR', './counter/');
}

if (!defined('COUNTER_EXT')) {
	define('COUNTER_EXT','.count');
}

function plugin_counter_convert()
{
	list($count,$today_count,$yesterday_count) = plugin_counter_count();
	return "<div class=\"counter\">Counter: $count, today: $today_count, yesterday: $yesterday_count</div>";
}

function plugin_counter_inline()
{
	$arg = '';
	if (func_num_args() > 0) {
		list($arg) = func_get_args();
	}
	
	list($count,$today_count,$yesterday_count) = plugin_counter_count();
	
	if ($arg == 'today') {
		return $today_count;
	}
	else if ($arg == 'yesterday') {
		return $yesterday_count;
	}
	return $count;
}
function plugin_counter_count()
{
	global $vars,$HTTP_SERVER_VARS;
	static $_counter_count;
	
	if (!is_page($vars['page'])) {
		return array(0,0,0);
	}
	if (isset($_counter_count)) {
		return $_counter_count;
	}
	
	$file = COUNTER_DIR.encode($vars['page']).COUNTER_EXT;
	if (!file_exists($file)) {
		$nf = fopen($file, 'w');
		fputs($nf,"0\n0\n0\n0\n\n");
		fclose($nf);
	}
	
	$array = file($file);
	$count = rtrim($array[0]);
	$today = rtrim($array[1]);
	$today_count = rtrim($array[2]);
	$yesterday_count = rtrim($array[3]);
	$ip = rtrim($array[4]);
	
	//前回とIPアドレスが異なった場合にカウンタを回す
	if ($ip != $HTTP_SERVER_VARS['REMOTE_ADDR']) {
		$t = get_date('Y/m/d');
		if ($t != $today) {
			$yesterday_count = $today_count;
			$today_count = 0;
			$today = $t;
		}
		++$count;
		++$today_count;
	}
	//ページ読み出し時のみファイルを更新
	if ($vars['cmd'] == 'read') {
		$ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		$nf = fopen($file, 'w');
		flock($nf,LOCK_EX);
		fputs($nf,"$count\n");
		fputs($nf,"$today\n");
		fputs($nf,"$today_count\n");
		fputs($nf,"$yesterday_count\n");
		fputs($nf,"$ip\n");
		flock($nf,LOCK_UN);
		fclose($nf);
	}
	
	$_counter_count = array($count,$today_count,$yesterday_count);
	
	return $_counter_count;
}
?>
