<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: mail.php,v 1.1 2004/08/01 01:54:35 henoheno Exp $
//

// APOP/POP Before SMTP
function pop_before_smtp($pop_userid = '', $pop_passwd = '',
	$pop_server = 'localhost', $pop_port = 110)
{
	$pop_auth_use_apop = TRUE;	// Always try APOP, by default
	$must_use_apop     = FALSE;	// Always try POP for APOP-disabled server
	if (isset($GLOBALS['pop_auth_use_apop'])) {
		// Force APOP only, or POP only
		$pop_auth_use_apop = $must_use_apop = $GLOBALS['pop_auth_use_apop'];
	}

	// Compat: GLOBALS > function arguments
	foreach(array('pop_userid', 'pop_passwd', 'pop_server', 'pop_port') as $global) {
		if(isset($GLOBALS[$global]) && $GLOBALS[$global] !== '')
			${$global} = $GLOBALS[$global];
	}

	// Check
	$die = '';
	foreach(array('pop_userid', 'pop_server', 'pop_port') as $global)
		if(${$global} == '') $die .= "pop_before_smtp(): \$$global seems blank\n";
	if ($die) return ($die);

	// Connect
	$errno = 0; $errstr = '';
	$fp = @fsockopen($pop_server, $pop_port, $errno, $errstr, 30);
	if (! $fp) return ("pop_before_smtp(): $errstr ($errno)");

	// Greeting message from server, may include <challenge-string> of APOP
	$message = fgets($fp, 1024); // 512byte max
	if (! preg_match('/^\+OK/', $message)) {
		fclose($fp);
		return ("pop_before_smtp(): Greeting message seems invalid");
	}

	$challenge = array();
	if ($pop_auth_use_apop &&
	   (preg_match('/<.*>/', $message, $challenge) || $must_use_apop)) {
		$method = 'APOP'; // APOP auth
		if (! isset($challenge[0])) {
			$response = md5(time()); // Someting worthless but variable
		} else {
			$response = md5($challenge[0] . $pop_passwd);
		}
		fputs($fp, 'APOP ' . $pop_userid . ' ' . $response . "\r\n");
	} else {
		$method = 'POP'; // POP auth
		fputs($fp, 'USER ' . $pop_userid . "\r\n");
		$message = fgets($fp, 1024); // 512byte max
		if (! preg_match('/^\+OK/', $message)) {
			fclose($fp);
			return ("pop_before_smtp(): USER seems invalid");
		}
		fputs($fp, 'PASS ' . $pop_passwd . "\r\n");
	}

	$result = fgets($fp, 1024); // 512byte max, auth result
	$auth   = preg_match('/^\+OK/', $result);
	if ($auth) {
		fputs($fp, "STAT\r\n"); // STAT, trigger SMTP relay!
		$message = fgets($fp, 1024); // 512byte max
	}

	// Disconnect
	fputs($fp, "QUIT\r\n");
	$message = fgets($fp, 1024); // 512byte max, last "+OK"
	fclose($fp);

	if (! $auth) {
		return ("pop_before_smtp(): $method authentication failed");
	} else {
		return TRUE;	// Success
	}
}
?>
