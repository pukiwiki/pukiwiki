<?
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: plugin.php,v 1.1 2002/06/21 05:21:46 masui Exp $
/////////////////////////////////////////////////

// プラグイン用に未定義の変数を設定
function set_plugin_messages($messages)
{
  foreach ($messages as $name=>$val) {
    global $$name;
    if(!isset($$name)) {
      $$name = $val;
    }
  }
}

//プラグイン(action)が存在するか
function exist_plugin_action($name) {
  if(!file_exists(PLUGIN_DIR.$name.".inc.php"))
    {
      return false;
    }
  else
    {
      require_once(PLUGIN_DIR.$name.".inc.php");
      if(!function_exists("plugin_".$name."_action"))
	{
	  return false;
	}
    }
  return true;
}

//プラグイン(convert)が存在するか
function exist_plugin_convert($name) {
  if(!file_exists(PLUGIN_DIR.$name.".inc.php"))
    {
      return false;
    }
  else
    {
      require_once(PLUGIN_DIR.$name.".inc.php");
      if(!function_exists("plugin_".$name."_convert"))
	{
	  return false;
	}
    }
  return true;
}

//プラグインの初期化を実行
function do_plugin_init($name) {
  $funcname = "plugin_".$name."_init";
  if(!function_exists($funcname))
    {
      return false;
    }
  
  $func_check = "_funccheck_".$funcname;
  global $$func_check;
  if($$func_check)
    {
      return false;
    }
  $$func_check = true;
  return @call_user_func($funcname);
}

//プラグイン(action)を実行
function do_plugin_action($name) {
  if(!exist_plugin_action($name)) {
    return array();
  }
  do_plugin_init($name);
  return @call_user_func("plugin_".$name."_action");
}

//プラグイン(convert)を実行
function do_plugin_convert($plugin_name,$plugin_args)
{
  $invalid_return = "#${plugin_name}(${plugin_args})";
  
  if($plugin_args !== "")
    $aryargs = explode(",",$plugin_args);
  else
    $aryargs = array();

  do_plugin_init($plugin_name);
  $retvar = call_user_func_array("plugin_${plugin_name}_convert",$aryargs);
  
  if($retvar === FALSE) return $invalid_return;
  else                  return $retvar;
}


?>