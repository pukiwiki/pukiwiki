<?php
// $Id: template.inc.php,v 1.13 2003/07/03 05:27:08 arino Exp $

define('MAX_LEN',60);

function plugin_template_action()
{
	global $script,$vars;
	global $_title_edit;
	global $_msg_template_start,$_msg_template_end,$_msg_template_page,$_msg_template_refer;
	global $_btn_template_create,$_title_template;
	
	if (!is_page($vars['refer']))
	{
		return; 
	}
	
	$lines = get_source($vars['refer']);
	
	// #freezeを削除
	if (count($lines) and rtrim($lines[0]) == '#freeze')
	{
		array_shift($lines);
	} 
	
	// edit
	if (array_key_exists('begin',$vars) and is_numeric($vars['begin'])
		and array_key_exists('end',$vars) and is_numeric($vars['end']))
	{
		$postdata = '';
		if ($vars['begin'] <= $vars['end'])
		{
			for ($i = $vars['begin']; $i <= $vars['end']; $i++)
			{
				$postdata .= $lines[$i];
			}
		}
		
		$retvar['msg'] = $_title_edit;
		$vars['refer'] = ''; // edit_formにはreferを見せたくない
		$retvar['body'] = edit_form($vars['page'],$postdata);
		$vars['refer'] = $vars['page'];
		return $retvar;
	}
	// input mb_strwidth()
	else
	{
		$begin_select = $_msg_template_start."<select name=\"begin\" size=\"10\">\n";
		for ($i = 0; $i < count($lines); $i++)
		{
			$lines[$i] = mb_strimwidth($lines[$i],0,MAX_LEN,'...');
			
			$tag = ($i==0) ? ' selected="selected"' : '';
			$line = htmlspecialchars($lines[$i]);
			$begin_select .= "<option value=\"$i\"$tag>$line</option>\n";
		}
		$begin_select.= "</select><br />\n<br />\n";
		
		$end_select = $_msg_template_end."<select name=\"end\" size=\"10\">\n";
		for ($i = 0; $i < count($lines); $i++)
		{
			$tag = ($i == count($lines) - 1) ? ' selected="selected"' : '';
			$line = htmlspecialchars($lines[$i]);
			$end_select .= "<option value=\"$i\"$tag>$line</option>\n";
		}
		$end_select.= "</select><br />\n<br />\n";
		
/*
		$lines = get_source($vars['refer']);
		
		$select = <<<EOD
<table width="100%" cellspacing="0" cellpadding="2" border="0">
 <tr>
  <td width="40" style="background-color:#ddeeff">開始</td>
  <td width="40" style="background-color:#ddeeff">終了</td>
  <td style="background-color:#ddeeff">&nbsp;</td>
 </tr>
EOD;
		for ($i = 0; $i < count($lines); $i++)
		{
			//$lines[$i] = mb_strimwidth($lines[$i],0,MAX_LEN,"...");
			
			$begin_tag = ($i == 0)                 ? ' checked="checked"' : '';
			$end_tag   = ($i == count($lines) - 1) ? ' checked="checked"' : '';
			$color = ($i % 2) ? ' style="background-color:#F0FFFA"' : '';
			$select .= <<<EOD
<tr>
 <td$color><input type="radio" name="begin" value="$i"$begin_tag /></td>
 <td$color><input type="radio" name="end" value="$i"$end_tag /></td>
 <td$color>{$lines[$i]}</td>
</tr>
EOD;
		}
		$select.= "</table><br />\n";
*/
	}
	$s_refer = str_replace('$1',htmlspecialchars($vars['refer']),$_msg_template_page);
	$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="template" />
  <input type="hidden" name="refer" value="$s_refer" />
  $begin_select
  $end_select
  $_msg_template_refer <input type="text" name="page" value="$s_refer" />
  <input type="submit" name="submit" value="$_btn_template_create" />
 </div>
</form>
EOD;
	
	$retvar['msg'] = $_title_template;
	$retvar['body'] = $ret;
	
	return $retvar;
}
?>
