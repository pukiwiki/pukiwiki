<?php
// $Id: calendar2.inc.php,v 1.24 2011/01/25 15:01:01 henoheno Exp $
//
// Calendar2 plugin
//
// Usage:
//	#calendar2({[pagename|*],[yyyymm],[off]})
//	off: Don't view today's

function plugin_calendar2_convert()
{
	global $script, $vars, $post, $get, $weeklabels, $WikiName, $BracketName;
	global $_calendar2_plugin_edit, $_calendar2_plugin_empty;

	$date_str = get_date('Ym');
	$base     = strip_bracket($vars['page']);

	$today_view = TRUE;
	if (func_num_args()) {
		$args = func_get_args();
		foreach ($args as $arg) {
			if (is_numeric($arg) && strlen($arg) == 6) {
				$date_str = $arg;
			} else if ($arg == 'off') {
				$today_view = FALSE;
			} else {
				$base = strip_bracket($arg);
			}
		}
	}
	if ($base == '*') {
		$base   = '';
		$prefix = '';
	} else {
		$prefix = $base . '/';
	}
	$r_base   = rawurlencode($base);
	$s_base   = htmlsc($base);
	$r_prefix = rawurlencode($prefix);
	$s_prefix = htmlsc($prefix);

	$yr  = substr($date_str, 0, 4);
	$mon = substr($date_str, 4, 2);
	if ($yr != get_date('Y') || $mon != get_date('m')) {
		$now_day = 1;
		$other_month = 1;
	} else {
		$now_day = get_date('d');
		$other_month = 0;
	}

	$today = getdate(mktime(0, 0, 0, $mon, $now_day, $yr) - LOCALZONE + ZONETIME);

	$m_num = $today['mon'];
	$d_num = $today['mday'];
	$year  = $today['year'];

	$f_today = getdate(mktime(0, 0, 0, $m_num, 1, $year) - LOCALZONE + ZONETIME);
	$wday = $f_today['wday'];
	$day  = 1;

	$m_name = $year . '.' . $m_num;

	$y = substr($date_str, 0, 4) + 0;
	$m = substr($date_str, 4, 2) + 0;

	$prev_date_str = ($m == 1) ?
		sprintf('%04d%02d', $y - 1, 12) : sprintf('%04d%02d', $y, $m - 1);

	$next_date_str = ($m == 12) ?
		sprintf('%04d%02d', $y + 1, 1) : sprintf('%04d%02d', $y, $m + 1);

	$ret = '';
	if ($today_view) {
		$ret = '<table border="0" summary="calendar frame">' . "\n" .
			' <tr>' . "\n" .
			'  <td valign="top">' . "\n";
	}
	$ret .= <<<EOD
   <table class="style_calendar" cellspacing="1" width="150" border="0" summary="calendar body">
    <tr>
     <td class="style_td_caltop" colspan="7">
      <a href="$script?plugin=calendar2&amp;file=$r_base&amp;date=$prev_date_str">&lt;&lt;</a>
      <strong>$m_name</strong>
      <a href="$script?plugin=calendar2&amp;file=$r_base&amp;date=$next_date_str">&gt;&gt;</a>
EOD;

	if ($prefix) $ret .= "\n" .
		'      <br />[<a href="' . $script . '?' . $r_base . '">' . $s_base . '</a>]';

	$ret .= "\n" .
		'     </td>' . "\n" .
		'    </tr>'  . "\n" .
		'    <tr>'   . "\n";

	foreach($weeklabels as $label)
		$ret .= '     <td class="style_td_week">' . $label . '</td>' . "\n";

	$ret .= '    </tr>' . "\n" .
		'    <tr>'  . "\n";
	// Blank
	for ($i = 0; $i < $wday; $i++)
		$ret .= '     <td class="style_td_blank">&nbsp;</td>' . "\n";

	while (checkdate($m_num, $day, $year)) {
		$dt     = sprintf('%4d-%02d-%02d', $year, $m_num, $day);
		$page   = $prefix . $dt;
		$r_page = rawurlencode($page);
		$s_page = htmlsc($page);

		if ($wday == 0 && $day > 1)
			$ret .=
			'    </tr>' . "\n" .
			'    <tr>' . "\n";

		$style = 'style_td_day'; // Weekday
		if (! $other_month && ($day == $today['mday']) && ($m_num == $today['mon']) && ($year == $today['year'])) { // Today
			$style = 'style_td_today';
		} else if ($wday == 0) { // Sunday
			$style = 'style_td_sun';
		} else if ($wday == 6) { //  Saturday
			$style = 'style_td_sat';
		}

		if (is_page($page)) {
			$link = '<a href="' . $script . '?' . $r_page . '" title="' . $s_page .
				'"><strong>' . $day . '</strong></a>';
		} else {
			if (PKWK_READONLY) {
				$link = '<span class="small">' . $day . '</small>';
			} else {
				$link = $script . '?cmd=edit&amp;page=' . $r_page . '&amp;refer=' . $r_base;
				$link = '<a class="small" href="' . $link . '" title="' . $s_page . '">' . $day . '</a>';
			}
		}

		$ret .= '     <td class="' . $style . '">' . "\n" .
			'      ' . $link . "\n" .
			'     </td>' . "\n";
		++$day;
		$wday = ++$wday % 7;
	}

	if ($wday > 0)
		while ($wday++ < 7) // Blank
			$ret .= '     <td class="style_td_blank">&nbsp;</td>' . "\n";

	$ret .= '    </tr>'   . "\n" .
		'   </table>' . "\n";

	if ($today_view) {
		$tpage = $prefix . sprintf('%4d-%02d-%02d', $today['year'],
			$today['mon'], $today['mday']);
		$r_tpage = rawurlencode($tpage);
		if (is_page($tpage)) {
			$_page = $vars['page'];
			$get['page'] = $post['page'] = $vars['page'] = $tpage;
			$str = convert_html(get_source($tpage));
			$str .= '<hr /><a class="small" href="' . $script .
				'?cmd=edit&amp;page=' . $r_tpage . '">' .
				$_calendar2_plugin_edit . '</a>';
			$get['page'] = $post['page'] = $vars['page'] = $_page;
		} else {
			$str = sprintf($_calendar2_plugin_empty,
				make_pagelink(sprintf('%s%4d-%02d-%02d', $prefix,
				$today['year'], $today['mon'], $today['mday'])));
		}
		$ret .= '  </td>' . "\n" .
			'  <td valign="top">' . $str . '</td>' . "\n" .
			' </tr>'   . "\n" .
			'</table>' . "\n";
	}

	return $ret;
}

function plugin_calendar2_action()
{
	global $vars;

	$page = strip_bracket($vars['page']);
	$vars['page'] = '*';
	if ($vars['file']) $vars['page'] = $vars['file'];

	$date = $vars['date'];

	if ($date == '') $date = get_date('Ym');
	$yy = sprintf('%04d.%02d', substr($date, 0, 4),substr($date, 4, 2));

	$aryargs = array($vars['page'], $date);
	$s_page  = htmlsc($vars['page']);

	$ret['msg']  = 'calendar ' . $s_page . '/' . $yy;
	$ret['body'] = call_user_func_array('plugin_calendar2_convert', $aryargs);

	$vars['page'] = $page;

	return $ret;
}
?>
