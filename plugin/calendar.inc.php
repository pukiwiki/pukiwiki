<?php
// $Id: calendar.inc.php,v 1.9 2002/11/29 00:09:01 panda Exp $

function plugin_calendar_convert()
{
	global $script,$weeklabels,$vars,$command,$WikiName,$BracketName;
	
	$args = func_get_args();
	
	if(func_num_args() == 0)
	{
		$date_str = date("Ym");
		$pre = $vars[page];
		$prefix = preg_replace("/^\[\[(.*)\]\]$/","$1",$vars[page])."/";
	}
	else if(func_num_args() == 1)
	{
		if(is_numeric($args[0]) && strlen($args[0]) == 6)
		{
			$date_str = $args[0];
			$pre = $vars[page];
			$prefix = preg_replace("/^\[\[(.*)\]\]$/","$1",$vars[page])."/";
		}
		else
		{
			$date_str = date("Ym");
			$pre = $args[0];
			$prefix = $args[0]."/";
		}
	}
	else if(func_num_args() == 2)
	{
		if(is_numeric($args[0]) && strlen($args[0]) == 6)
		{
			$date_str = $args[0];
			$pre = $args[1];
			$prefix = $args[1]."/";
		}
		else if(is_numeric($args[1]) && strlen($args[1]) == 6)
		{
			$date_str = $args[1];
			$pre = $args[0];
			$prefix = $args[0]."/";
		}
		else
		{
			$date_str = date("Ym");
			$pre = $vars[page];
			$prefix = preg_replace("/^\[\[(.*)\]\]$/","$1",$vars[page])."/";
		}
	}
	else
	{
		return FALSE;
	}

	if(!$command) $cmd = "read";
	else          $cmd = $command;
	
	$prefix = strip_tags($prefix);
	
	$yr = substr($date_str,0,4);
	$mon = substr($date_str,4,2);
	if($yr != date("Y") || $mon != date("m"))
	{
		$now_day = 1;
		$other_month = 1;
	}
	else
	{
		$now_day = date("d");
		$other_month = 0;
	}
	$today = getdate(mktime(0,0,0,$mon,$now_day,$yr));
	
	$m_num = $today[mon];
	$d_num = $today[mday];
	$year = $today[year];
	$f_today = getdate(mktime(0,0,0,$m_num,1,$year));
	$wday = $f_today[wday];
	$day = 1;
	$fweek = true;

	$m_name = "$year.$m_num ($cmd)";

	if(!preg_match("/^(($WikiName)|($BracketName))$/",$pre))
		$prefix_url = "[[".$pre."]]";
	else
		$prefix_url = $pre;

	$prefix_url = rawurlencode($prefix_url);
	$pre = strip_bracket($pre);

	$ret .= '
<table class="style_calendar" cellspacing="1" width="150" border="0">
  <tr>
    <td align="middle" class="style_td_caltop" colspan="7">
      <div class="small" style="text-align:center"><strong>'.$m_name.'</strong><br />[<a href="'.$script.'?'.$prefix_url.'">'.$pre.'</a>]</div>
    </td>
  </tr>
  <tr>
';

	foreach($weeklabels as $label)
	{
		$ret .= '
    <td align="middle" class="style_td_week">
      <div class="small" style="text-align:center"><strong>'.$label.'</strong></div>
    </td>';
	}

	$ret .= "</tr>\n<tr>\n";

	while(checkdate($m_num,$day,$year))
	{
		$dt = sprintf("%4d%02d%02d", $year, $m_num, $day);
		$name = "$prefix$dt";
		$page = "[[$prefix$dt]]";
		$page_url = rawurlencode("[[$prefix$dt]]");
		
		if($cmd == "edit") $refer = "&amp;refer=$page_url";
		else               $refer = "";
		
		if($cmd == "read" && !is_page($page))
			$link = "<strong>$day</strong>";
		else
			$link = "<a href=\"$script?cmd=$cmd&amp;page=$page_url$refer\" title=\"$name\"><strong>$day</strong></a>";

		if($fweek)
		{
			for($i=0;$i<$wday;$i++)
			{ // Blank 
				$ret .= "    <td align=\"center\" class=\"style_td_blank\">&nbsp;</td>\n"; 
			} 
		$fweek=false;
		}

		if($wday == 0) $ret .= "  </tr><tr>\n";
		if(!$other_month && ($day == $today[mday]) && ($m_num == $today[mon]) && ($year == $today[year]))
		{
			//  Today 
			$ret .= "    <td align=\"center\" class=\"style_td_today\"><span class=\"small\">$link</span></td>\n"; 
		}
		else if($wday == 0)
		{
			//  Sunday 
			$ret .= "    <td align=\"center\" class=\"style_td_sun\"><span class=\"small\">$link</span></td>\n";
		}
		else if($wday == 6)
		{
			//  Saturday 
			$ret .= "    <td align=\"center\" class=\"style_td_sat\"><span class=\"small\">$link</span></td>\n";
		}
		else
		{
			// Weekday 
			$ret .= "    <td align=\"center\" class=\"style_td\"><span class=\"small\">$link</span></td>\n";
		}
		$day++;
		$wday++;
		$wday = $wday % 7;
	}
	if($wday > 0)
	{
		while($wday < 7)
		{ // Blank 
			$ret .= "    <td align=\"center\" class=\"style_td_blank\">&nbsp;</td>\n";
		$wday++;
		} 
	}

	$ret .= "  </tr>\n</table>\n";
	return $ret;
}
?>
