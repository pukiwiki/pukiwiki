<?php
// $Id: template.inc.php,v 1.18 2004/10/09 08:25:29 henoheno Exp $

define('MAX_LEN',60);

function plugin_template_action()
{
	global $script,$vars;
	global $_title_edit;
	global $_msg_template_start,$_msg_template_end,$_msg_template_page,$_msg_template_refer;
	global $_btn_template_create,$_title_template;
	global $_err_template_already,$_err_template_invalid,$_msg_template_force;

	if (!array_key_exists('refer',$vars) or !is_page($vars['refer']))
	{
		return FALSE;
	}

	$lines = get_source($vars['refer']);

	// #freeze¤òºï½ü
	if (! empty($lines) && strtolower(rtrim($lines[0])) == '#freeze')
		array_shift($lines);

	$begin = (array_key_exists('begin',$vars) and is_numeric($vars['begin'])) ? $vars['begin'] : 0;
	$end = (array_key_exists('end',$vars) and is_numeric($vars['end'])) ? $vars['end'] : count($lines) - 1;
	if ($begin > $end)
	{
		$temp = $begin;
		$begin = $end;
		$end = $temp;
	}
	$page = array_key_exists('page',$vars) ? $vars['page'] : '';
	$is_page = is_page($page);

	// edit
	if ($is_pagename = is_pagename($page) and (!$is_page or !empty($vars['force'])))
	{
		$postdata = join('',array_splice($lines,$begin,$end - $begin + 1));
		$retvar['msg'] = $_title_edit;
		$retvar['body'] = edit_form($vars['page'],$postdata);
		$vars['refer'] = $vars['page'];
		return $retvar;
	}
	$begin_select = $end_select = '';
	for ($i = 0; $i < count($lines); $i++)
	{
		$line = htmlspecialchars(mb_strimwidth($lines[$i],0,MAX_LEN,'...'));

		$tag = ($i == $begin) ? ' selected="selected"' : '';
		$begin_select .= "<option value=\"$i\"$tag>$line</option>\n";

		$tag = ($i == $end) ? ' selected="selected"' : '';
		$end_select .= "<option value=\"$i\"$tag>$line</option>\n";
	}

	$_page = htmlspecialchars($page);
	$msg = $tag = '';
	if ($is_page)
	{
		$msg = $_err_template_already;
		$tag = '<input type="checkbox" name="force" value="1" />'.$_msg_template_force;
	}
	else if ($page != '' and !$is_pagename)
	{
		$msg = str_replace('$1',$_page,$_err_template_invalid);
	}

	$s_refer = htmlspecialchars($vars['refer']);
	$s_page = ($page == '') ? str_replace('$1',$s_refer,$_msg_template_page) : $_page;
	$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="template" />
  <input type="hidden" name="refer" value="$s_refer" />
  $_msg_template_start <select name="begin" size="10">$begin_select</select><br /><br />
  $_msg_template_end <select name="end" size="10">$end_select</select><br /><br />
  $_msg_template_refer <input type="text" name="page" value="$s_page" />
  <input type="submit" name="submit" value="$_btn_template_create" /> $tag
 </div>
</form>
EOD;

	$retvar['msg'] = ($msg == '') ? $_title_template : $msg;
	$retvar['body'] = $ret;

	return $retvar;
}
?>
