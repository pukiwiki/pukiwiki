
<?
// $Id: calendar2.inc.php,v 1.13 2002/12/05 05:02:27 panda Exp $
// *引数にoffと書くことで今日の日記を表示しないようにした。
function plugin_calendar2_convert()
{
	global $script,$weeklabels,$vars,$command,$WikiName,$BracketName,$post,$get;
	global $_calendar2_plugin_edit, $_calendar2_plugin_empty;

	$today_view = true;
	$args = func_get_args();
	
	if(func_num_args() == 0)
	{
		$date_str = date("Ym");
		$pre = strip_bracket($vars['page']);
		$prefix = strip_bracket($vars['page'])."/";
	}
	else{
		foreach ($args as $arg){
			if(is_numeric($arg) && strlen($arg) == 6){
				$date_str = $arg;
			}
			else if($arg == "off"){
				$today_view = false;
			}
			else {
				$pre = strip_bracket($arg);
				$prefix = strip_bracket($arg)."/";
			}
		}
		if(empty($date_str)) $date_str = date("Ym");
		if(empty($pre)){
			$pre = strip_bracket($vars['page']);
			$prefix = strip_bracket($vars['page'])."/";
		}
	}
	/*
	else if(func_num_args() == 1)
	{
		if(is_numeric($args[0]) && strlen($args[0]) == 6)
		{
			//#calendar2(yyyymm)
			$date_str = $args[0];
			$pre = strip_bracket($vars['page']);
			$prefix = strip_bracket($vars['page'])."/";
		}
		else
		{
			//calendar2(pagename)
			$date_str = date("Ym");
			$pre = strip_bracket($args[0]);
			$prefix = strip_bracket($args[0])."/";
		}
	}
	else if(func_num_args() == 2)
	{
		if(is_numeric($args[0]) && strlen($args[0]) == 6)
		{
                      //#calendar2(yyyymm,pagename)
			$date_str = $args[0];
			$pre = strip_bracket($args[1]);
			$prefix = strip_bracket($args[1])."/";
		}
		else if(is_numeric($args[1]) && strlen($args[1]) == 6)
		{
                      //#calendar2(pagename,yyyymm)
			$date_str = $args[1];
			$pre = strip_bracket($args[0]);
			$prefix = strip_bracket($args[0]).'/';
		}
		else
		{
                      //#calendar2(?,?)
			$date_str = date("Ym");
			$pre = strip_bracket($vars[page]);
			$prefix = strip_bracket($vars[page])."/";
		}
	}
	else if(func_num_args() == 3){
		if(is_numeric($args[0]) && strlen($args[0]) == 6)
		{
                      //#calendar2(yyyymm,pagename)
			$date_str = $args[0];
			$pre = strip_bracket($args[1]);
			$prefix = strip_bracket($args[1])."/";
		}
		else if(is_numeric($args[1]) && strlen($args[1]) == 6)
		{
                      //#calendar2(pagename,yyyymm)
			$date_str = $args[1];
			$pre = strip_bracket($args[0]);
			$prefix = strip_bracket($args[0]).'/';
		}
		else
		{
                      //#calendar2(?,?)
			$date_str = date("Ym");
			$pre = strip_bracket($vars[page]);
			$prefix = strip_bracket($vars[page])."/";
		}
		if($args[2] == "off"){
			$today_view = false;
		}
	}
	else
	{
		return FALSE;
	}
  */
	if($pre == "*") {
		$prefix = '';
		$pre = '';
	}
	$prefix_ = rawurlencode($pre);
	$prefix = strip_tags($prefix);
	
	if(!$command) $cmd = "read";
	else          $cmd = $command;
	
	
	$yr = substr($date_str,0,4);
	$mon = substr($date_str,4,2);
	if($yr != date("Y") || $mon != date("m")) {
		$now_day = 1;
		$other_month = 1;
	}
	else {
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
	
	$ret .= '<table border="0" width="100%"><tr><td valign="top">';
	
	$y = substr($date_str,0,4)+0;
	$m = substr($date_str,4,2)+0;
	
	$prev_date_str = sprintf("%04d%02d",$y,$m-1);
	if($m-1<1) {
		$prev_date_str = sprintf("%04d%02d",$y-1,12);
	}
	$next_date_str = sprintf("%04d%02d",$y,$m+1);
	if($m+1>12) {
		$next_date_str = sprintf("%04d%02d",$y+1,1);
	}
	
	if($prefix == "") {
		$ret .= '
<table class="style_calendar" cellspacing="1" width="150" border="0">
  <tr>
    <td align="middle" class="style_td_caltop" colspan="7">
      <div class="small" style="text-align:center"><a href="'.$script.'?plugin=calendar2&amp;file='.$prefix_.'&amp;date='.$prev_date_str.'">&lt;&lt;</a> <strong>'.$m_name.'</strong> <a href="'.$script.'?plugin=calendar2&amp;file='.$prefix_.'&amp;date='.$next_date_str.'">&gt;&gt;</a></div>
    </td>
  </tr>
  <tr>
';
	}
	else {
		$ret .= '
<table class="style_calendar" cellspacing="1" width="150" border="0">
  <tbody>
  <tr>
    <td align="middle" class="style_td_caltop" colspan="7">
      <div class="small" style="text-align:center"><a href="'.$script.'?plugin=calendar2&amp;file='.$prefix_.'&amp;date='.$prev_date_str.'">&lt;&lt;</a> <strong>'.$m_name.'</strong> <a href="'.$script.'?plugin=calendar2&amp;file='.$prefix_.'&amp;date='.$next_date_str.'">&gt;&gt;</a><br />[<a href="'.$script.'?'.$prefix_url.'">'.$pre.'</a>]</div>
    </td>
  </tr>
  <tr>
';
	}
	
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
		$dt = sprintf("%4d-%02d-%02d", $year, $m_num, $day);
		$name = "$prefix$dt";
		$page = "[[$prefix$dt]]";
		$page_url = rawurlencode("[[$prefix$dt]]");
		
		if($cmd == "edit") $refer = "&amp;refer=$page_url";
		else               $refer = "";
		
		if($cmd == "read" && !is_page($page))
			$link = "<a href=\"$script?cmd=$cmd&amp;page=$page_url$refer\" title=\"$name\" class=\"small\">$day</a>";
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
			$ret .= "    <td align=\"center\" class=\"style_td_today\">$link</td>\n"; 
		}
		else if($wday == 0)
		{
			//  Sunday 
			$ret .= "    <td align=\"center\" class=\"style_td_sun\">$link</td>\n";
		}
		else if($wday == 6)
		{
			//  Saturday 
			$ret .= "    <td align=\"center\" class=\"style_td_sat\">$link</td>\n";
		}
		else
		{
			// Weekday 
			$ret .= "    <td align=\"center\" class=\"style_td_day\">$link</td>\n";
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
  if ($today_view == true){
	$page = sprintf("[[%s%4d-%02d-%02d]]", $prefix, $today[year], $today[mon], $today[mday]);
	$page_url = rawurlencode($page);
	if(is_page($page)) {
		$page_ = $vars['page'];
		$get['page'] = $post['page'] = $vars['page'] = $page;
		$str = convert_html(join("",file(get_filename(encode($page)))));
		$str .= "<hr /><a class=\"small\" href=\"$script?cmd=edit&amp;page=".rawurlencode($page)."\">$_calendar2_plugin_edit</a>";
		$get['page'] = $post['page'] = $vars['page'] = $page_;
	}
	else {
		$str = sprintf($_calendar2_plugin_empty,make_link(sprintf('[[%s%4d-%02d-%02d]]',$prefix, $today[year], $today[mon], $today[mday])));
	}
  }else{
    $str = "";
  }
	$ret .= "</td><td valign=\"top\">".$str."</td></tr></table>";
	
	return $ret;
}

function plugin_calendar2_action()
{
	global $command,$vars;
	
	$command = 'read';
	$page = strip_bracket($vars['page']);
	$vars['page'] = '*';
	if($vars['file']) $vars['page'] = $vars['file'];
	
	$date = $vars['date'];
	if($date=='') {
		$date = date("Ym");
	}
	$yy = sprintf("%04d.%02d",substr($date,0,4),substr($date,4,2));
	
	$aryargs = array($vars['page'],$date);
	$ret["msg"] = "calendar ".$vars['page']."/".$yy;
	$ret["body"] = call_user_func_array("plugin_calendar2_convert",$aryargs);
	
	$vars['page'] = $page;
	
	return $ret;
}

?>
