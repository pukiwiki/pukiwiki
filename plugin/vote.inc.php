<?

function plugin_vote_action()
{
	global $post,$vars,$script,$cols,$rows,$del_backup,$do_backup;
	global $_title_collided,$_msg_collided,$_title_updated;

	$postdata_old  = file(get_filename(encode($post["refer"])));
	$vote_no = 0;

	foreach($postdata_old as $line)
	{
		if(preg_match("/^#vote\((.*)\)$/",$line,$arg))
		{
			if($vote_no == $post["vote_no"])
			{
				$args = explode(",",$arg[1]);

				foreach($args as $arg)
				{
					if(preg_match("/^(.+)\[(\d+)\]$/",$arg,$match))
					{
						$arg = $match[1];
						$cnt = $match[2];
					}
					else
					{
						$cnt = 0;
					}

					if($post["vote"][preg_replace("/\]\]$/","",$arg)]) $cnt++;

					$votes[] = $arg.'['.$cnt.']';
				}

				$vote_str = "#vote(" . @join(",",$votes) . ")\n";

				$postdata_input = $vote_str;
				$postdata .= $vote_str;
				$line = "";
			}
			$vote_no++;
		}
		$postdata .= $line;
	}

	if(md5(@join("",@file(get_filename(encode($post["refer"]))))) != $post["digest"])
	{
		$title = $_title_collided;

		$body = "$_msg_collided\n";

		$body .= "<form action=\"$script?cmd=preview\" method=\"post\">\n"
			."<input type=\"hidden\" name=\"refer\" value=\"".$post["refer"]."\">\n"
			."<input type=\"hidden\" name=\"digest\" value=\"".$post["digest"]."\">\n"
			."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\" id=\"textarea\">$postdata_input</textarea><br>\n"
			."</form>\n";
	}
	else
	{
		// ?¡¦?a?t?@?C???I?i?¢Ì
		if(is_page($post["refer"]))
			$oldpostdata = join("",file(get_filename(encode($post["refer"]))));
		else
			$oldpostdata = "\n";
		if($postdata)
			$diffdata = do_diff($oldpostdata,$postdata);
		file_write(DIFF_DIR,$post["refer"],$diffdata);

		// ?o?b?N?A?b?v?I?i?¢Ì
		if(is_page($post["refer"]))
			$oldposttime = filemtime(get_filename(encode($post["refer"])));
		else
			$oldposttime = time();

		// ?O?W¡Èa?e?a¢ó??a?¡Æ?c?e?A?¡ñ?E?¡ñ?A?o?b?N?A?b?v?a?i???¡¦?e??¦Ì?E?¡ñ?A?¡¦?a?E?B
		if(!$postdata && $del_backup)
			backup_delete(BACKUP_DIR.encode($post["refer"]).".txt");
		else if($do_backup && is_page($post["refer"]))
			make_backup(encode($post["refer"]).".txt",$oldpostdata,$oldposttime);

		// ?t?@?C???I?¡Æ?¢ã???Y
		file_write(DATA_DIR,$post["refer"],$postdata);

		// is_page?I?L???b?V?¡Ä?d?N???A?¡¦?e?B
		is_page($post["refer"],true);

		$title = $_title_updated;
	}

	$retvars["msg"] = $title;
	$retvars["body"] = $body;

	$post["page"] = $post["refer"];
	$vars["page"] = $post["refer"];

	return $retvars;
}
function plugin_vote_convert()
{
	global $script,$vars,$vote_no,$digest;

	$args = func_get_args();

	if(!func_num_args()) return FALSE;

	$string = "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"

		. "<form action=\"$script\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"plugin\" value=\"vote\">\n"
		. "<input type=\"hidden\" name=\"refer\" value=\"$vars[page]\">\n"
		. "<input type=\"hidden\" name=\"vote_no\" value=\"$vote_no\">\n"
		. "<input type=\"hidden\" name=\"digest\" value=\"$digest\">\n"

		. "<tr>\n"
		. "<td align=\"left\" class=\"vote_label\"><b>The choices</b></td>\n"
		. "<td align=\"center\" class=\"vote_label\"><b>Votes</b></td>\n"
		. "</tr>\n";

	$tdcnt = 0;
	foreach($args as $arg)
	{
		$cnt = 0;

		if(preg_match("/^(.+)\[(\d+)\]$/",$arg,$match))
		{
			$arg = $match[1];
			$cnt = $match[2];
		}

		$link = make_link($arg);

		if($tdcnt++ % 2) $cls = "vote_td1";
		else           $cls = "vote_td2";

		$string .= "<tr>"
			.  "<td width=\"80%\" class=\"$cls\" nowrap>$link</td>"
			.  "<td class=\"$cls\" nowrap>$cnt&nbsp;&nbsp;<input type=\"submit\" name=\"vote[$arg]\" value=\"Vote\"></td>"
			.  "</tr>\n";
	}

	$string .= "</form>\n"
		.  "</table>\n";

	$vote_no++;

	return $string;
}
?>

