<?php
// $Id: newpage.inc.php,v 1.5 2003/01/27 05:38:46 panda Exp $

function plugin_newpage_init()
{
	$messages = array(
		'_msg_newpage' => 'ページ新規作成'
	);
	set_plugin_messages($messages);
}

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
	$s_page = htmlspecialchars($vars['page']);
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
	global $vars,$script,$_btn_edit,$_msg_newpage;
	
	if ($vars['page'] == '') {
		$retvars['msg'] = $_msg_newpage;
		$retvars['body'] = plugin_newpage_convert();
		return $retvars;
	}

	$r_page = rawurlencode(strip_bracket($vars['page']));
	$r_refer = rawurlencode($vars['refer']);
	
	header("Location: $script?cmd=edit&page=$r_page&refer=$r_refer");
	die();
}
?>
