<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: lookup.inc.php,v 1.17 2005/02/27 08:16:30 henoheno Exp $
//
// InterWiki lookup plugin

define('PLUGIN_LOOKUP_USAGE', '#lookup(interwikiname[,button_name[,default]])');
function plugin_lookup_convert()
{
	global $script, $vars;

	$num = func_num_args();
	if ($num == 0 || $num > 3) return PLUGIN_LOOKUP_USAGE;

	$args = func_get_args();
	$interwiki = htmlspecialchars(trim($args[0]));
	$button    = isset($args[1]) ? trim($args[1]) : '';
	$button    = ($button != '') ? htmlspecialchars($button) : 'lookup';
	$default   = ($num > 2) ? htmlspecialchars(trim($args[2])) : '';
	$s_page    = htmlspecialchars($vars['page']);

	$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="lookup" />
  <input type="hidden" name="refer"  value="$s_page" />
  <input type="hidden" name="inter"  value="$interwiki" />
  <label for="_p_lookup_interwiki">$interwiki:</label>
  <input type="text" name="page" id="_p_lookup_interwiki" size="30" value="$default" />
  <input type="submit" value="$button" />
 </div>
</form>
EOD;
	return $ret;
}

function plugin_lookup_action()
{
	global $post; // Deny GET method to avlid GET loop

	$page  = isset($post['page'])  ? $post['page']  : '';
	$inter = isset($post['inter']) ? $post['inter'] : '';
	if ($page == '') return FALSE; // Do nothing
	if ($inter == '') return array(msg=>'Invalid access', body=>'');

	$url = get_interwiki_url($inter, $page);
	if ($url === FALSE) {
		$msg = sprintf('InterWikiName "%s" not found', $inter);
		$msg = htmlspecialchars($msg);
		return array(msg=>'Not found', body=>$msg);
	}

	pkwk_headers_sent();
	header('Location: ' . $url); // Publish as GET method
	exit;
}
?>
