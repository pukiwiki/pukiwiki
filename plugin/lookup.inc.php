<?php
// $Id: lookup.inc.php,v 1.7 2003/01/27 05:38:46 panda Exp $

function plugin_lookup_convert()
{
	global $script,$vars;
	
	$args = func_get_args();
	
	if (func_num_args() < 2) return FALSE;
	
	$iwn = trim(strip_tags($args[0]));
	$btn = trim(strip_tags($args[1]));
	
	$default = '';
	if (func_num_args() > 2)
		$default = trim(strip_tags($args[2]));
	
	$s_page = htmlspecialchars($vars['page']);
	
	$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="lookup" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="inter" value="$iwn" />
  $iwn:
  <input type="text" name="page" size="30" value="$default" />
  <input type="submit" value="$btn" />
 </div>
</form>
EOD;
	return $ret;
}
function plugin_lookup_action()
{
	global $script,$vars;
	
	if (!$vars['inter'] or !$vars['page']) return;
	
	$interwikiname = rawurlencode("[[{$vars['inter']}:{$vars['page']}]]");
	
	header("Location: $script?$interwikiname");
	die();
}
?>
