<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: img.inc.php,v 1.8 2003/05/29 08:25:35 arino Exp $
//
// 画像を表示
function plugin_img_convert()
{
	if (func_num_args() != 2)
	{
		return FALSE;
	}
	$aryargs = func_get_args();
	$url = $aryargs[0];
	$align = strtoupper($aryargs[1]);
	if ($align == 'R' || $align == 'RIGHT')
	{
		$align = 'right';
	}
	else if ($align == 'L' || $align == 'LEFT')
	{
		$align = 'left';
	}
	else
	{
		return '<div style="clear:both"></div>';
	}
	if (!is_url($url) or !preg_match('/\.(jpe?g|gif|png)$/i', $url))
	{
		return FALSE;
	}
	return <<<EOD

<div style="float:$align;padding:.5em 1.5em .5em 1.5em">
 <img src="$url" alt="" />
</div>
EOD;
}
?>
