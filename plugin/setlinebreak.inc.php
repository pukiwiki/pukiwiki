<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: setlinebreak.inc.php,v 1.4 2005/04/02 06:27:38 henoheno Exp $
//
// Set linebreak plugin - on/of linebreak-to-'<br />' conversion
//
// Usage:
//	#setlinebreak          : Invert on/off
//	#setlinebreak(on)      : ON  (from this line)
//	#setlinebreak(off)     : OFF (from this line)
//	#setlinebreak(default) : Reset

function plugin_setlinebreak_convert()
{
	global $line_break;
	static $default;

	if (! isset($default)) $default = $line_break;

	if (func_num_args() == 0) {
		// Invert
		$line_break = ! $line_break;
	} else {
		$args = func_get_args();
		switch (strtolower($args[0])) {
		case 'on':	/*FALLTHROUGH*/
		case 'true':	/*FALLTHROUGH*/
		case '1':
			$line_break = 1;
			break;

		case 'off':	/*FALLTHROUGH*/
		case 'false':	/*FALLTHROUGH*/
		case '0':
			$line_break = 0;
			break;

		case 'default':
			$line_break = $default;
			break;

		default:
			return '#setlinebreak: Invalid argument: ' .
				htmlspecialchars($args[0]) . '<br />';
		}
	}
	return '';
}
?>
