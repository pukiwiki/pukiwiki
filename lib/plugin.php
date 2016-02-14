<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// plugin.php
// Copyright
//   2002-2016 PukiWiki Development Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Plugin related functions

define('PKWK_PLUGIN_CALL_TIME_LIMIT', 768);

// Set global variables for plugins
function set_plugin_messages($messages)
{
	foreach ($messages as $name=>$val)
		if (! isset($GLOBALS[$name]))
			$GLOBALS[$name] = $val;
}

// Check plugin '$name' is here
function exist_plugin($name)
{
	global $vars;
	static $exist = array(), $count = array();

	$name = strtolower($name);
	if(isset($exist[$name])) {
		if (++$count[$name] > PKWK_PLUGIN_CALL_TIME_LIMIT)
			die('Alert: plugin "' . htmlsc($name) .
			'" was called over ' . PKWK_PLUGIN_CALL_TIME_LIMIT .
			' times. SPAM or someting?<br />' . "\n" .
			'<a href="' . get_script_uri() . '?cmd=edit&amp;page='.
			rawurlencode($vars['page']) . '">Try to edit this page</a><br />' . "\n" .
			'<a href="' . get_script_uri() . '">Return to frontpage</a>');
		return $exist[$name];
	}

	if (preg_match('/^\w{1,64}$/', $name) &&
	    file_exists(PLUGIN_DIR . $name . '.inc.php')) {
	    	$exist[$name] = TRUE;
	    	$count[$name] = 1;
		require_once(PLUGIN_DIR . $name . '.inc.php');
		return TRUE;
	} else {
	    	$exist[$name] = FALSE;
	    	$count[$name] = 1;
		return FALSE;
	}
}

// Check if plugin API 'action' exists
function exist_plugin_action($name) {
	return	function_exists('plugin_' . $name . '_action') ? TRUE : exist_plugin($name) ?
		function_exists('plugin_' . $name . '_action') : FALSE;
}

// Check if plugin API 'convert' exists
function exist_plugin_convert($name) {
	return	function_exists('plugin_' . $name . '_convert') ? TRUE : exist_plugin($name) ?
		function_exists('plugin_' . $name . '_convert') : FALSE;
}

// Check if plugin API 'inline' exists
function exist_plugin_inline($name) {
	return	function_exists('plugin_' . $name . '_inline') ? TRUE : exist_plugin($name) ?
		function_exists('plugin_' . $name . '_inline') : FALSE;
}

// Call 'init' function for the plugin
// NOTE: Returning FALSE means "An erorr occurerd"
function do_plugin_init($name)
{
	static $done = array();

	if (! isset($done[$name])) {
		$func = 'plugin_' . $name . '_init';
		$done[$name] = (! function_exists($func) || call_user_func($func) !== FALSE);
	}

	return $done[$name];
}

// Call API 'action' of the plugin
function do_plugin_action($name)
{
	if (! exist_plugin_action($name)) return array();

	if (do_plugin_init($name) === FALSE) {
		die_message('Plugin init failed: ' . htmlsc($name));
	}

	$retvar = call_user_func('plugin_' . $name . '_action');

	// Insert a hidden field, supports idenrtifying text enconding
	if (PKWK_ENCODING_HINT != '')
		$retvar =  preg_replace('/(<form[^>]*>)/', '$1' . "\n" .
			'<div><input type="hidden" name="encode_hint" value="' .
			PKWK_ENCODING_HINT . '" /></div>', $retvar);

	return $retvar;
}

// Call API 'convert' of the plugin
function do_plugin_convert($name, $args = '')
{
	global $digest;

	if (do_plugin_init($name) === FALSE) {
		return '[Plugin init failed: ' . htmlsc($name) . ']';
	}

	if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK) {
		// Multiline plugin?
		$pos  = strpos($args, "\r"); // "\r" is just a delimiter
		if ($pos !== FALSE) {
			$body = substr($args, $pos + 1);
			$args = substr($args, 0, $pos);
		}
	}

	if ($args === '') {
		$aryargs = array();                 // #plugin()
	} else {
		$aryargs = csv_explode(',', $args); // #plugin(A,B,C,D)
	}
	if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK) {
		if (isset($body)) $aryargs[] = & $body;     // #plugin(){{body}}
	}

	$_digest = $digest;
	$retvar  = call_user_func_array('plugin_' . $name . '_convert', $aryargs);
	$digest  = $_digest; // Revert

	if ($retvar === FALSE) {
		return htmlsc('#' . $name .
			($args != '' ? '(' . $args . ')' : ''));
	} else if (PKWK_ENCODING_HINT != '') {
		// Insert a hidden field, supports idenrtifying text enconding
		return preg_replace('/(<form[^>]*>)/', '$1 ' . "\n" .
			'<div><input type="hidden" name="encode_hint" value="' .
			PKWK_ENCODING_HINT . '" /></div>', $retvar);
	} else {
		return $retvar;
	}
}

// Call API 'inline' of the plugin
function do_plugin_inline($name, $args, & $body)
{
	global $digest;

	if (do_plugin_init($name) === FALSE) {
		return '[Plugin init failed: ' . htmlsc($name) . ']';
	}

	if ($args === '') {
		$aryargs = array();
	} else {
		$aryargs = csv_explode(',', $args);
	}

	// NOTE: A reference of $body is always the last argument
	$aryargs[] = & $body; // func_num_args() != 0

	$_digest = $digest;
	$retvar  = call_user_func_array('plugin_' . $name . '_inline', $aryargs);
	$digest  = $_digest; // Revert

	if($retvar === FALSE) {
		// Do nothing
		return htmlsc('&' . $name . ($args ? '(' . $args . ')' : '') . ';');
	} else {
		return $retvar;
	}
}
