<?
define("MAX_LEN",60);
function plugin_template_action()
{
	global $vars,$script,$non_list,$whatsnew,$_btn_template;
	
	global $script,$rows,$cols,$hr,$vars,$function_freeze,$WikiName,$BracketName;
	global $_btn_addtop,$_btn_preview,$_btn_update,$_btn_freeze,$_msg_help,$_btn_notchangetimestamp;
	global $whatsnew,$_btn_template,$_btn_load,$non_list,$load_template_func;

	$ret = "";
	
	// edit
	if($vars["refer"] &&  $vars["page"] && $vars["submit"] && !is_page($vars["refer"]))
	{
		// ページ名がWikiNameでなく、BracketNameでなければBracketNameとして解釈
		if(!preg_match("/^(($WikiName)|($BracketName))$/",$vars["refer"]))
		{
			$vars["refer"] = "[[$vars[refer]]]";
		}
		
		$page = $vars["refer"];
		
		$lines = @file(get_filename(encode($vars["page"])));
		
		if($vars["begin"] <= $vars["end"])
		{
			for($i=$vars["begin"];$i<=$vars["end"];$i++)
			{
				$postdata.= $lines[$i];
			}
		}
		
		if($vars["help"] == "true")
			$help = $hr.catrule();
		else
			$help = "<br>\n<ul><li><a href=\"$script?cmd=edit&amp;help=true&amp;page=".rawurlencode($page)."\">$_msg_help</a></ul></li>\n";

		if($function_freeze)
			$str_freeze = '<input type="submit" name="freeze" value="'.$_btn_freeze.'" accesskey="f">';
$retvar["body"] =  '
<form action="'.$script.'" method="post">
<input type="hidden" name="page" value="'.$page.'">
<input type="hidden" name="digest" value="'.$digest.'">
<table cellspacing="3" cellpadding="0" border="0">
 <tr>
  <td colspan="2" align="right">
'.$template.'
  </td>
 </tr>
 <tr>
  <td colspan="2" align="right">
   <textarea name="msg" rows="'.$rows.'" cols="'.$cols.'" wrap="virtual">
'.$postdata.'</textarea>
  </td>
 </tr>
 <tr>
  <td>
   <input type="submit" name="preview" value="'.$_btn_preview.'" accesskey="p">
   <input type="submit" name="write" value="'.$_btn_update.'" accesskey="s">
   '.$add_top.'
   <input type="checkbox" name="notimestamp" value="true"><small>'.$_btn_notchangetimestamp.'</small>
  </td>
  </form>
  <form action="'.$script.'?cmd=freeze" method="post">
   <input type="hidden" name="page" value="'.$vars["page"].'">
  <td align="right">
   '.$str_freeze.'
  </td>
  </form>
 </tr>
</table>
' . $help;

		$retvar["msg"] = "$1 の編集";
		
		$vars["page"] = $vars["refer"];
		return $retvar;
	}
	// input mb_strwidth()
	else if($vars["refer"])
	{
		if(is_page($vars["refer"]))
		{
			
			$begin_select = "";
			$end_select = "";
			$lines = @file(get_filename(encode($vars["refer"])));
			$begin_select.= "開始行:<br><select name=\"begin\" size=\"10\">\n";
			for($i=0;$i<count($lines);$i++)
			{
				$lines[$i] = mb_strimwidth($lines[$i],0,MAX_LEN,"...");
				
				if($i==0) $tag = "selected";
				else      $tag = "";
				$begin_select.= "<option value=\"$i\" $tag>$lines[$i]</option>\n";
			}
			$begin_select.= "</select><br>\n<br>\n";
			
			$end_select.= "終了行:<br><select name=\"end\" size=\"10\">\n";
			for($i=0;$i<count($lines);$i++)
			{
				if($i==count($lines)-1) $tag = "selected";
				else                    $tag = "";
				$end_select.= "<option value=\"$i\" $tag>$lines[$i]</option>\n";
			}
			$end_select.= "</select><br>\n<br>\n";
			
			
			/*
			$select = "";
			$lines = @file(get_filename(encode($vars["refer"])));
			$select.= "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
			$select.= "<tr><td bgcolor=\"#DDEEFF\" width=\"40\">開始</td><td bgcolor=\"#DDEEFF\" width=\"40\">終了</td><td bgcolor=\"#DDEEFF\">&nbsp;</td></tr>\n";
			
			for($i=0;$i<count($lines);$i++)
			{
				//$lines[$i] = mb_strimwidth($lines[$i],0,MAX_LEN,"...");
				
				if($i==0)
				{
					$begin_tag = "checked";
					$end_tag = "";
				}
				else if($i==count($lines)-1)
				{
					$begin_tag = "";
					$end_tag = "checked";
				}
				else
				{
					$begin_tag = "";
					$end_tag = "";
				}
				
				if($i%2) $color = "bgcolor=\"#F0FFFA\"";
				else     $color = "";
				$select.= "<tr>";
				$select.= "<td $color>";
				$select.= "<input type=\"radio\" name=\"begin\" value=\"$i\" $begin_tag>\n";
				$select.= "</td><td $color>";
				$select.= "<input type=\"radio\" name=\"end\" value=\"$i\" $end_tag>\n";
				$select.= "</td><td $color>";
				$select.= "$lines[$i]";
				$select.= "</td>";
				$select.= "</tr>";
			}
			$select.= "</table><br>\n";
			*/
		}
		
		$ret.= "<form action=\"$script\" method=\"post\">\n";
		$ret.= "<input type=\"hidden\" name=\"plugin\" value=\"template\">\n";
		$ret.= "<input type=\"hidden\" name=\"page\" value=\"$vars[refer]\">\n";
		//$ret.= "ページ名: <input type=\"text\" name=\"refer\" value=\"$vars[refer]/複製\">\n";
		//$ret.= "<input type=\"submit\" name=\"submit\" value=\"作成\"><br>\n<br>\n";
		$ret.= $begin_select;
		$ret.= $end_select;
		//$ret.= $select;
		$ret.= "ページ名: <input type=\"text\" name=\"refer\" value=\"$vars[refer]/複製\">\n";
		$ret.= "<input type=\"submit\" name=\"submit\" value=\"作成\">\n";
		$ret.= "</form>\n";
		
		$retvar["msg"] = "$1 をテンプレートにして作成";
		$retvar["body"] = $ret;
		
		return $retvar;
	}

}
?>