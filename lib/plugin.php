<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: plugin.php,v 1.6 2004/12/30 13:50:54 henoheno Exp $
//
// Plugin related functions

// プラグイン用に未定義のグローバル変数を設定
function set_plugin_messages($messages)
{
	foreach ($messages as $name=>$val) {
		if (! isset($GLOBALS[$name])) $GLOBALS[$name] = $val;
	}
}

// Check plugin '$name' is here
function exist_plugin($name)
{
	static $exists = array();

	$name = strtolower($name);
	if(isset($exists[$name])) return $exists[$name];

	if (preg_match('/^\w{1,64}$/', $name) &&
	    file_exists(PLUGIN_DIR . $name . '.inc.php')) {
		$exists[$name] = TRUE;
		require_once(PLUGIN_DIR . $name . '.inc.php');
		return TRUE;
	} else {
		$exists[$name] = FALSE;
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

// Do init the plugin
function do_plugin_init($name)
{
	static $checked = array();

	if (isset($checked[$name])) return $checked[$name];

	$func = 'plugin_' . $name . '_init';
	if (function_exists($func)) {
		// TRUE or FALSE or NULL (return nothing)
		$checked[$name] = call_user_func($func);
	} else {
		$checked[$name] = NULL; // Not exist
	}

	return $checked[$name];
}

// Call API 'action' of the plugin
function do_plugin_action($name)
{
	if (! exist_plugin_action($name)) return array();

	if(do_plugin_init($name) === FALSE)
		die_message('Plugin init failed: ' . $name);

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

	if(do_plugin_init($name) === FALSE)
		return '[Plugin init failed: ' . $name . ']';

	if ($args !== '') {
		$aryargs = csv_explode(',', $args);
	} else {
		$aryargs = array();
	}

	$_digest = $digest;
	$retvar  = call_user_func_array('plugin_' . $name . '_convert', $aryargs);
	$digest  = $_digest; // Revert

	if ($retvar === FALSE) {
		return htmlspecialchars('#' . $name .
			($args != '' ? '(' . $args . ')' : ''));
	} else if (PKWK_ENCODING_HINT != '') {
		// Insert a hidden field, supports idenrtifying text enconding
		return preg_replace('/(<form[^>]*>)/', '$1 ' . "\n" .
			'<div><input type="hidden" name="encode_hint" value="' .
			PKWK_ENCODING_HINT . '" /></div>', $retvar);
	}
}

// Call API 'inline' of the plugin
function do_plugin_inline($name, $args, & $body)
{
	global $digest;

	if(do_plugin_init($name) === FALSE)
		return '[Plugin init failed: ' . $name . ']';

	if ($args !== '') {
		$aryargs = csv_explode(',', $args);
	} else {
		$aryargs = array();
	}

	// NOTE: A reference of $body is always the last argument
	$aryargs[] = & $body; // func_num_args() != 0

	$_digest = $digest;
	$retvar  = call_user_func_array('plugin_' . $name . '_inline', $aryargs);
	$digest  = $_digest; // Revert

	if($retvar === FALSE) {
		// Do nothing
		return htmlspecialchars('&' . $name . ($args ? '(' . $args . ')' : '') . ';');
	} else {
		return $retvar;
	}
}
?>
