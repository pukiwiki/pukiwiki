<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: plugin.php,v 1.4 2004/10/31 03:45:52 henoheno Exp $
//

// プラグイン用に未定義のグローバル変数を設定
function set_plugin_messages($messages)
{
	foreach ($messages as $name=>$val) {
		if (! isset($GLOBALS[$name])) $GLOBALS[$name] = $val;
	}
}

//プラグインが存在するか
function exist_plugin($name)
{
	$name = strtolower($name);	// 大文字と小文字を区別しないファイルシステム対策
	if (preg_match('/^\w{1,64}$/', $name) &&
	    file_exists(PLUGIN_DIR . $name . '.inc.php')) {
		require_once(PLUGIN_DIR . $name . '.inc.php');
		return TRUE;
	} else {
		return FALSE;
	}
}

//プラグイン関数(action)が存在するか
function exist_plugin_action($name) {
	return	function_exists('plugin_' . $name . '_action') ? TRUE : exist_plugin($name) ?
		function_exists('plugin_' . $name . '_action') : FALSE;
}

//プラグイン関数(convert)が存在するか
function exist_plugin_convert($name) {
	return	function_exists('plugin_' . $name . '_convert') ? TRUE : exist_plugin($name) ?
		function_exists('plugin_' . $name . '_convert') : FALSE;
}

//プラグイン関数(inline)が存在するか
function exist_plugin_inline($name) {
	return	function_exists('plugin_' . $name . '_inline') ? TRUE : exist_plugin($name) ?
		function_exists('plugin_' . $name . '_inline') : FALSE;
}

//プラグインの初期化を実行
function do_plugin_init($name)
{
	static $checked = array();

	if (! isset($checked[$name])) {
		$func = 'plugin_' . $name . '_init';
		if (function_exists($func)) {
			// TRUE or FALSE or NULL (return nothing)
			$checked[$name] = call_user_func($func);
		} else {
			// Not exists
			$checked[$name] = null;
		}
	}
	return $checked[$name];
}

//プラグイン(action)を実行
function do_plugin_action($name)
{
	if (! exist_plugin_action($name)) return array();

	if(do_plugin_init($name) === FALSE)
		die_message("Plugin init failed: $name");

	$retvar = call_user_func('plugin_' . $name . '_action');

	// Insert a hidden field, supports idenrtifying text enconding
	if (PKWK_ENCODING_HINT != '')
		$retvar =  preg_replace('/(<form[^>]*>)/', "$1\n" .
			'<div><input type="hidden" name="encode_hint" value="' . PKWK_ENCODING_HINT . '" /></div>',
			$retvar);

	return $retvar;
}

//プラグイン(convert)を実行
function do_plugin_convert($name, $args = '')
{
	global $digest;

	if(do_plugin_init($name) === FALSE)
		return "[Plugin init failed: $name]";

	if ($args !== '') {
		$aryargs = csv_explode(',', $args);
	} else {
		$aryargs = array();
	}

	$_digest = $digest;  // 退避
	$retvar  = call_user_func_array('plugin_' . $name . '_convert', $aryargs);
	$digest  = $_digest; // 復元

	if ($retvar === FALSE) {
		$retvar =  htmlspecialchars('#' . $name . ($args != '' ? "($args)" : ''));
	} else if (PKWK_ENCODING_HINT != '') {
		// Insert a hidden field, supports idenrtifying text enconding
		$retvar =  preg_replace('/(<form[^>]*>)/', "$1\n" .
			'<div><input type="hidden" name="encode_hint" value="' . PKWK_ENCODING_HINT . '" /></div>',
			$retvar);

	}

	return $retvar;
}

//プラグイン(inline)を実行
function do_plugin_inline($name, $args, & $body)
{
	global $digest;

	if(do_plugin_init($name) === FALSE)
		return "[Plugin init failed: $name]";

	if ($args !== '') {
		$aryargs = csv_explode(',', $args);
	} else {
		$aryargs = array();
	}
	$aryargs[] = & $body; // Added reference of $body

	$_digest = $digest;  // 退避
	$retvar  = call_user_func_array('plugin_' . $name . '_inline', $aryargs);
	$digest  = $_digest; // 復元

	if($retvar === FALSE) {
		return htmlspecialchars("&${name}" . ($args ? "($args)" : '') . ';');
	} else {
		return $retvar;
	}
}
?>
