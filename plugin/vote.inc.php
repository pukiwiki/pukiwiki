<?php
// $Id: vote.inc.php,v 1.8.2.1 2003/01/22 05:41:14 panda Exp $

function plugin_vote_init()
{
  $_plugin_vote_messages = array(
    '_vote_plugin_choice' => '¡™¬ÚªË',
    '_vote_plugin_votes' => '≈Í…º',
    );
  set_plugin_messages($_plugin_vote_messages);
}

function plugin_vote_action()
{
	global $post,$vars,$script,$cols,$rows,$del_backup,$do_backup;
	global $_title_collided,$_msg_collided,$_title_updated;
	global $_vote_plugin_choice, $_vote_plugin_votes;

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

					$e_arg = encode($arg);
					if($post["vote_$e_arg"]==$_vote_plugin_votes) $cnt++;

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
			."<div>\n"
			."<input type=\"hidden\" name=\"refer\" value=\"".htmlspecialchars($post["refer"])."\" />\n"
			."<input type=\"hidden\" name=\"digest\" value=\"".htmlspecialchars($post["digest"])."\" />\n"
			."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\" id=\"textarea\">".htmlspecialchars($postdata_input)."</textarea><br />\n"
			."</div>\n"
			."</form>\n";
	}
	else
	{
		if(is_page($post["refer"]))
			$oldpostdata = join("",file(get_filename(encode($post["refer"]))));
		else
			$oldpostdata = "\n";
		if($postdata)
			$diffdata = do_diff($oldpostdata,$postdata);
		file_write(DIFF_DIR,$post["refer"],$diffdata);

		if(is_page($post["refer"]))
			$oldposttime = filemtime(get_filename(encode($post["refer"])));
		else
			$oldposttime = time();

		if(!$postdata && $del_backup)
			backup_delete(BACKUP_DIR.encode($post["refer"]).".txt");
		else if($do_backup && is_page($post["refer"]))
			make_backup(encode($post["refer"]).".txt",$oldpostdata,$oldposttime);

		file_write(DATA_DIR,$post["refer"],$postdata);

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
	global $script,$vars,$digest;
	global $_vote_plugin_choice, $_vote_plugin_votes;
	static $vote_no = 0;

	$args = func_get_args();

	if(!func_num_args()) return FALSE;

	$string = ""
		. "<form action=\"$script\" method=\"post\">\n"
 		. "<table cellspacing=\"0\" cellpadding=\"2\" class=\"style_table\">\n"
 		. "<tr>\n"
 		. "<td align=\"left\" class=\"vote_label\" style=\"padding-left:1em;padding-right:1em\"><strong>$_vote_plugin_choice</strong>"
		. "<input type=\"hidden\" name=\"plugin\" value=\"vote\" />\n"
		. "<input type=\"hidden\" name=\"refer\" value=\"".htmlspecialchars($vars["page"])."\" />\n"
		. "<input type=\"hidden\" name=\"vote_no\" value=\"".htmlspecialchars($vote_no)."\" />\n"
		. "<input type=\"hidden\" name=\"digest\" value=\"".htmlspecialchars($digest)."\" />\n"
		. "</td>\n"
		. "<td align=\"center\" class=\"vote_label\"><strong>$_vote_plugin_votes</strong></td>\n"
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
		$e_arg = encode($arg);

		if($tdcnt++ % 2) $cls = "vote_td1";
		else           $cls = "vote_td2";

		$string .= "<tr>"
			.  "<td align=\"left\" class=\"$cls\" style=\"padding-left:1em;padding-right:1em;\" nowrap=\"nowrap\">$link</td>"
			.  "<td align=\"right\" class=\"$cls\" nowrap=\"nowrap\">$cnt&nbsp;&nbsp;<input type=\"submit\" name=\"vote_".htmlspecialchars($e_arg)."\" value=\"$_vote_plugin_votes\" class=\"submit\" /></td>"
			.  "</tr>\n";
	}

	$string .= "</table></form>\n";

	$vote_no++;

	return $string;
}
?>
