<?
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
	else {
		$align = 'left';
	}
	
	return "<div style=\"float:$align\"><img src=\"$url\"></div>";
}
?>
