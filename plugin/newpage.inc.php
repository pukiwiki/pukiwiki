<?php
// $Id: newpage.inc.php,v 1.10 2004/10/10 10:20:59 henoheno Exp $

function plugin_newpage_convert()
{
	global $script,$vars,$_btn_edit,$_msg_newpage,$BracketName;

	$newpage = '';
	if (func_num_args()) {
		list($newpage) = func_get_args();
	}
	if (!preg_match("/^$BracketName$/",$newpage)) {
		$newpage = '';
	}
	$s_page = htmlspecialchars(array_key_exists('refer',$vars) ? $vars['refer'] : $vars['page']);
	$s_newpage = htmlspecialchars($newpage);
	$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="newpage" />
  <input type="hidden" name="refer" value="$s_page" />
  $_msg_newpage:
  <input type="text" name="page" size="30" value="$s_newpage" />
  <input type="submit" value="$_btn_edit" />
 </div>
</form>
EOD;

	return $ret;
}

function plugin_newpage_action()
{
	global $vars, $_btn_edit, $_msg_newpage;

	if ($vars['page'] == '') {
		$retvars['msg'] = $_msg_newpage;
		$retvars['body'] = plugin_newpage_convert();
		return $retvars;
	}
	$page = strip_bracket($vars['page']);
	$r_page = rawurlencode(array_key_exists('refer',$vars) ?
		get_fullname($page,$vars['refer']) : $page);
	$r_refer = rawurlencode($vars['refer']);

	header('Location: ' . get_script_uri() . '?cmd=read&page=' . $r_page . '&refer=' . $r_refer);
	die();
}
?>
