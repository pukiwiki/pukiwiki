<?php
// $Id: template.inc.php,v 1.6 2003/01/27 05:38:47 panda Exp $

define('MAX_LEN',60);

function plugin_template_action()
{
	global $vars,$script,$non_list,$whatsnew,$_btn_template;
	
	global $script,$vars;
	global $_title_edit;
	
	if (!is_page($vars['refer'])) { return; }
	
	// edit
	if (array_key_exists('begin',$vars) and is_numeric($vars['begin']) and array_key_exists('end',$vars) and is_numeric($vars['end']))
	{
		$lines = get_source($vars['refer']);
		
		if ($vars['begin'] <= $vars['end'])
			for($i = $vars['begin']; $i <= $vars['end']; $i++)
				$postdata.= $lines[$i];
		
		$retvar['msg'] = $_title_edit;
		$vars['refer'] = ''; // edit_formにはreferを見せたくない
		$retvar['body'] = edit_form($vars['page'],$postdata);
		$vars['refer'] = $vars['page'];
		return $retvar;
	}
	// input mb_strwidth()
	else
	{
		$lines = get_source($vars['refer']);
		
		$begin_select = "開始行:<br /><select name=\"begin\" size=\"10\">\n";
		for ($i = 0; $i < count($lines); $i++)
		{
			$lines[$i] = mb_strimwidth($lines[$i],0,MAX_LEN,'...');
			
			$tag = ($i==0) ? ' selected="selected"' : '';
			$begin_select.= "<option value=\"$i\"$tag>$lines[$i]</option>\n";
		}
		$begin_select.= "</select><br />\n<br />\n";
		
		$end_select = "終了行:<br /><select name=\"end\" size=\"10\">\n";
		for ($i = 0; $i < count($lines); $i++)
		{
			$tag = ($i == count($lines) - 1) ? ' selected="selected"' : '';
			$end_select.= "<option value=\"$i\"$tag>$lines[$i]</option>\n";
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
	
	$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="template" />
  <input type="hidden" name="refer" value="{$vars['refer']}" />
  $begin_select
  $end_select
  ページ名: <input type="text" name="page" value="{$vars['refer']}/複製" />
  <input type="submit" name="submit" value="作成" />
 </div>
</form>
EOD;
	
	$retvar['msg'] = '$1 をテンプレートにして作成';
	$retvar['body'] = $ret;
	
	return $retvar;
}
?>
