<?php
// $Id: online.inc.php,v 1.4 2002/11/29 00:09:01 panda Exp $

// user list file
define(USR_LST, "counter/user.dat");
// time out sec
define(TIMEOUT, 300);

function plugin_online_convert()
{
	global $HTTP_SERVER_VARS;
	
	if(!file_exists(USR_LST))
	{
		$nf = fopen(USR_LST, "w");
		fclose($nf);
	}
	CheckUser($HTTP_SERVER_VARS["REMOTE_ADDR"]);
	return UserCount();
}

function CheckUser($addr)
{
	$usr_arr = file(USR_LST);
	$fp = fopen(USR_LST, "w");
	while(!flock($fp,LOCK_EX));
	$now = time();
	for($i = 0; $i < count($usr_arr); $i++)
	{
		list($ip_addr,$tim_stmp) = explode("|", $usr_arr[$i]);
		if(($now-$tim_stmp) < TIMEOUT)
		{
			if($ip_addr != $addr)
			{
				fputs($fp, "$ip_addr|$tim_stmp");
			}
		}
	}
	fputs($fp, "$addr|$now\n");
	flock($fp,LOCK_UN);
	fclose($fp);
}

function UserCount()
{
	$usr_arr = file(USR_LST);
	return count($usr_arr);
}
?>
