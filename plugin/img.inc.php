<?
// $Id: img.inc.php,v 1.3 2002/07/01 07:08:57 masui Exp $

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
		return "<br style=\"clear:both\">";
	}
	return "<div style=\"float:$align;padding:.5em 1.5em .5em 1.5em\"><img src=\"$url\" alt=\"\" /></div>";
}
?>
