<?php
// $Id: img.inc.php,v 1.5.2.3 2003/06/09 08:09:45 arino Exp $
function plugin_img_convert()
{
	if(func_num_args()!=2) {
		return;
	}
	$aryargs = func_get_args();
	$url = $aryargs[0];
	$align = strtoupper($aryargs[1]);
	if($align == 'R' || $align == 'RIGHT') {
		$align = 'right';
	}
	else if($align == 'L' || $align == 'LEFT') {
		$align = 'left';
	}
	else {
		return "<div style=\"clear:both\"></div>";
	}
	if (!is_url($url) or !preg_match('/(\.gif|\.png|\.jpeg|\.jpg)$/i', $url))
	{
		return;
	}
	return "<div style=\"float:$align;padding:.5em 1.5em .5em 1.5em\"><img src=\"$url\" alt=\"\" /></div>";
}
?>
