<?
function plugin_newpage_init()
{
  $_plugin_recent_messages = array(
    '_msg_newpage' => 'ページ新規作成'
  );
  set_plugin_messages($_plugin_recent_messages);
}

function plugin_newpage_convert()
{
	global $script,$vars,$_btn_edit,$_msg_newpage;
	
	$ret = "<form action=\"$script\" method=\"post\">\n";
	$ret.= "<input type=\"hidden\" name=\"plugin\" value=\"newpage\">\n";
	$ret.= "<input type=\"hidden\" name=\"refer\" value=\"$vars[page]\">\n";
	$ret.= "$_msg_newpage: ";
	$ret.= "<input type=\"text\" name=\"page\" size=\"30\" value=\"\">\n";
	$ret.= "<input type=\"submit\" value=\"$_btn_edit\">\n";
	$ret.= "</form>\n";

	return $ret;
}

function plugin_newpage_action()
{
	global $vars,$script,$_btn_edit,$_msg_newpage;
	
	if(!$vars["page"]) {
		$retvars["msg"] = $_msg_newpage;
		$retvars["body"] = "<form action=\"$script\" method=\"post\">\n";
		$retvars["body"].= "<input type=\"hidden\" name=\"plugin\" value=\"newpage\">\n";
		$retvars["body"].= "<input type=\"hidden\" name=\"refer\" value=\"$vars[page]\">\n";
		$retvars["body"].= "$_msg_newpage: ";
		$retvars["body"].= "<input type=\"text\" name=\"page\" size=\"30\" value=\"\">\n";
		$retvars["body"].= "<input type=\"submit\" value=\"$_btn_edit\">\n";
		$retvars["body"].= "</form>\n";

		return $retvars;
	}
	
	if(!preg_match("/^($BracketName)|($InterWikiName)$/",$vars["page"]))
	{
		$vars["page"] = "[[$vars[page]]]";
	}

	$wikiname = rawurlencode($vars["page"]);
	
	header("Location: $script?$wikiname");
	die();
}
?>
