<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: mail.php,v 1.3 2004/07/17 10:37:33 henoheno Exp $
//

// APOP/POP Before SMTP
function pop_before_smtp($pop_userid = '', $pop_passwd = '',
	$pop_server = 'localhost', $pop_port = 110)
{
	// Always try APOP, by default
	$pop_auth_use_apop =
		(! isset($GLOBALS['pop_auth_use_apop']) || $GLOBALS['pop_auth_use_apop'] !== FALSE);

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
	if (! preg_match('/^\+OK /', $message)) {
		fclose($fp);
		return ("pop_before_smtp(): Greeting message seems invalid");
	}
	$challenge = array();
	if ($pop_auth_use_apop && preg_match('/<.*>/', $message, $challenge)) {
		$method = 'APOP'; // APOP auth
		$response = md5($challenge[0] . $pop_passwd);
		fputs($fp, 'APOP ' . $pop_userid . ' ' . $response . "\r\n");
	} else {
		$method = 'POP'; // POP auth
		fputs($fp, 'USER ' . $pop_userid . "\r\n");
		$message = fgets($fp, 1024); // 512byte max
		if (! preg_match('/^\+OK /', $message)) {
			fclose($fp);
			return ("pop_before_smtp(): USER seems invalid");
		}
		fputs($fp, 'PASS ' . $pop_passwd . "\r\n");
	}
	$result = fgets($fp, 1024); // 512byte max, auth result

	// Disconnect
	// fputs($fp, "QUIT\r\n");
	// $message = fgets($fp, 1024); // 512byte max, last "+OK"
	fclose($fp);

	if (! preg_match('/^\+OK /', $result)) {
		return ("pop_before_smtp(): $method authentication failed");
	} else {
		return TRUE;	// Success
	}
}
?>
