<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: online.inc.php,v 1.6 2003/05/16 05:55:20 arino Exp $
//

// user list file
define('USR_LST', 'counter/user.dat');
// time out sec
define('TIMEOUT', 300);

function plugin_online_inline()
{
	return plugin_online_convert();
}
function plugin_online_convert()
{
	if (!file_exists(USR_LST))
	{
		$nf = fopen(USR_LST, 'w');
		fclose($nf);
	}
	CheckUser($_SERVER['REMOTE_ADDR']);
	return UserCount();
}

function CheckUser($addr)
{
	$usr_arr = file(USR_LST);
	$fp = fopen(USR_LST, 'w');
	flock($fp,LOCK_EX);
	$now = UTIME;
	for ($i = 0; $i < count($usr_arr); $i++)
	{
		list($ip_addr,$tim_stmp) = explode('|', $usr_arr[$i]);
		if (($now - $tim_stmp) < TIMEOUT and $ip_addr != $addr)
		{
			fputs($fp, "$ip_addr|$tim_stmp");
		}
	}
	fputs($fp, "$addr|$now\n");
	flock($fp,LOCK_UN);
	fclose($fp);
}

function UserCount()
{
	return count(file(USR_LST));
}
?>
