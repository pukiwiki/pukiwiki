<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: plugin.php,v 1.9 2003/09/27 15:26:19 arino Exp $
//

// プラグイン用に未定義の変数を設定
function set_plugin_messages($messages)
{
	foreach ($messages as $name=>$val)
	{
		global $$name;
		
		if (!isset($$name))
		{
			$$name = $val;
		}
	}
}

//プラグインが存在するか
function exist_plugin($name)
{
	$name = strtolower($name);	//Ryuji_edit(2003-03-18) add 大文字と小文字を区別しないファイルシステム対策
	if (preg_match('/^\w{1,64}$/',$name)
		and file_exists(PLUGIN_DIR.$name.'.inc.php'))
	{
		require_once(PLUGIN_DIR.$name.'.inc.php');
		return TRUE;
	}
	return FALSE;
}

//プラグイン(action)が存在するか
function exist_plugin_action($name)
{
	if (!exist_plugin($name))
	{
		return FALSE;
	}
	return function_exists('plugin_'.$name.'_action');
}

//プラグイン(convert)が存在するか
function exist_plugin_convert($name)
{
	if (!exist_plugin($name))
	{
		return FALSE;
	}
	return function_exists('plugin_'.$name.'_convert');
}

//プラグイン(inline)が存在するか
function exist_plugin_inline($name)
{
	if (!exist_plugin($name))
	{
		return FALSE;
	}
	return function_exists('plugin_'.$name.'_inline');
}

//プラグインの初期化を実行
function do_plugin_init($name)
{
	static $check = array();
	
	if (array_key_exists($name,$check))
	{
		return $check[$name];
	}
	
	$func = 'plugin_'.$name.'_init';
	if ($check[$name] = function_exists($func))
	{
		@call_user_func($func);
		return TRUE;
	}
	return FALSE;
}

//プラグイン(action)を実行
function do_plugin_action($name)
{
	if (!exist_plugin_action($name))
	{
		return array();
	}
	
	do_plugin_init($name);
	$retvar = call_user_func('plugin_'.$name.'_action');
	
	// 文字エンコーディング検出用 hidden フィールドを挿入する
	return preg_replace('/(<form[^>]*>)/',"$1\n<div><input type=\"hidden\" name=\"encode_hint\" value=\"ぷ\" /></div>",$retvar);
}

//プラグイン(convert)を実行
function do_plugin_convert($name,$args='')
{
	global $digest;
	
	// digestを退避
	$_digest = $digest;
	
	$aryargs = ($args !== '') ? explode(',',$args) : array();

	do_plugin_init($name);
	$retvar = call_user_func_array('plugin_'.$name.'_convert',$aryargs);
	
	// digestを復元
	$digest = $_digest;
	
	if ($retvar === FALSE)
	{
		return htmlspecialchars('#'.$name.($args ? "($args)" : ''));
	}
	
	// 文字エンコーディング検出用 hidden フィールドを挿入する
	return preg_replace('/(<form[^>]*>)/',"$1\n<div><input type=\"hidden\" name=\"encode_hint\" value=\"ぷ\" /></div>",$retvar);
}

//プラグイン(inline)を実行
function do_plugin_inline($name,$args,$body)
{
	global $digest;
	
	// digestを退避
	$_digest = $digest;
	
	$aryargs = ($args !== '') ? explode(',',$args) : array();
	$aryargs[] =& $body;

	do_plugin_init($name);
	$retvar = call_user_func_array('plugin_'.$name.'_inline',$aryargs);
	
	// digestを復元
	$digest = $_digest;
	
	if($retvar === FALSE)
	{
		return htmlspecialchars("&${name}" . ($args ? "($args)" : '') . ';');
	}
	
	return $retvar;
}
?>
