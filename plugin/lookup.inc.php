<?
// $Id: lookup.inc.php,v 1.5 2002/07/12 11:53:56 masui Exp $

function plugin_lookup_convert()
{
	global $script,$vars;
	
	$args = func_get_args();
	
	if(func_num_args() < 2) return FALSE;
	
	$iwn = trim(strip_tags($args[0]));
	$btn = trim(strip_tags($args[1]));
	$default = trim(strip_tags($args[2]));
	
	$ret = "<form action=\"$script\" method=\"post\">\n";
	$ret.= "<div>\n";
	$ret.= "<input type=\"hidden\" name=\"plugin\" value=\"lookup\" />\n";
	$ret.= "<input type=\"hidden\" name=\"refer\" value=\"".htmlspecialchars($vars["page"])."\" />\n";
	$ret.= "<input type=\"hidden\" name=\"inter\" value=\"$iwn\" />\n";
	$ret.= "$iwn:\n";
	$ret.= "<input type=\"text\" name=\"page\" size=\"30\" value=\"$default\" />\n";
	$ret.= "<input type=\"submit\" value=\"$btn\" />\n";
	$ret.= "</div>\n";
	$ret.= "</form>\n";

	return $ret;
}
function plugin_lookup_action()
{
	global $vars,$script;
	
	if(!$vars["inter"] || !$vars["page"]) return;
	
	$interwikiname = "[[".$vars["inter"].":".$vars["page"]."]]";
	$interwikiname = rawurlencode($interwikiname);
	
	header("Location: $script?$interwikiname");
	die();
}
?>
