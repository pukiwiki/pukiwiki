<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: mail.php,v 1.1 2003/09/24 00:37:27 arino Exp $
//

// POP Before SMTP
function pop_before_smtp()
{
	global $pop_auth_use_apop,$pop_port,$pop_server,$pop_userid,$pop_passwd;

	$fp = @fsockopen($pop_server,$pop_port,$errno,$errstr,30);
	if (!$fp)
	{
		return "$errstr ($errno)";
	}
	
	$rc = pop_result($fp);
	if ($rc !== 0)
	{
		return $rc;
	}
	
	// APOP 指定時の利用判定
	if ($pop_auth_use_apop)
	{
		// digest 抽出
		preg_match("(<.*>)", $buf, $regs);
		// digest が抽出できなかった場合は、強制的に POP に切り替え
		if (empty($regs[0]))
		{
			$pop_auth_use_apop = 0;
		}
	}
	
	// APOP または POP
	if ($pop_auth_use_apop)
	{
		fputs($fp, "APOP ".$pop_userid." ".md5($regs[0].$pop_passwd)."\r\n");
		$rc = pop_result($fp);
		if ($rc !== 0)
		{
			return $rc;
		}
	}
	else
	{
		fputs($fp, "USER ".$pop_userid."\r\n");
		$rc = pop_result($fp);
		if ($rc !== 0)
		{
			return $rc;
		}
		
		fputs($fp, "PASS ".$pop_passwd."\r\n");
		$rc = pop_result($fp);
		if ($rc !== 0)
		{
			return $rc;
		}
	}

	fputs($fp, "QUIT\r\n");
	fclose($fp);
	return 0;
}

// POP 処理の結果判定
function pop_result($fp)
{
	$buf = fgets($fp, 2048);
	$tok = strtok($buf,' ');
	if ($tok == '+OK')
	{
		return 0;
	}
	fclose($fp);
	return substr($buf, strlen($tok) + 1);
}
?>
