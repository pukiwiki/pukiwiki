<?php
// $Id: vote.inc.php,v 1.11 2003/01/27 05:38:47 panda Exp $

function plugin_vote_init()
{
	if (LANG == 'ja')
		$messages = array(
			'_vote_plugin_choice' => 'ÁªÂò»è',
			'_vote_plugin_votes' => 'ÅêÉ¼',
		);
	else
		$messages = array(
			'_vote_plugin_choice' => 'Selection',
			'_vote_plugin_votes' => 'Vote',
		);
	set_plugin_messages($messages);
}

function plugin_vote_action()
{
	global $post,$vars,$script,$cols,$rows;
	global $_title_collided,$_msg_collided,$_title_updated;
	global $_vote_plugin_choice, $_vote_plugin_votes;

	$postdata_old  = get_source($post['refer']);
	$vote_no = 0;
	$title = $body = $postdata = '';

	foreach($postdata_old as $line) {
		if (!preg_match("/^#vote\((.*)\)\s*$/",$line,$arg)) {
			$postdata .= $line;
			continue;
		}
		
		if (++$vote_no != $post['vote_no']) {
			$postdata .= $line;
			continue;
		}
		$args = explode(',',$arg[1]);
		
		foreach($args as $arg) {
			$cnt = 0;
			if (preg_match("/^(.+)\[(\d+)\]$/",$arg,$match)) {
				$arg = $match[1];
				$cnt = $match[2];
			}
			$e_arg = encode($arg);
			if (!empty($post["vote_$e_arg"]) and $post["vote_$e_arg"] == $_vote_plugin_votes) {
				$cnt++;
			}
			
			$votes[] = $arg.'['.$cnt.']';
		}
		
		$vote_str = '#vote('.@join(',',$votes).")\n";
		
		$postdata_input = $vote_str;
		$postdata .= $vote_str;
	}

	if (md5(@join('',get_source($post['refer']))) != $post['digest']) {
		$title = $_title_collided;
		
		$s_refer = htmlspecialchars($post['refer']);
		$s_digest = htmlspecialchars($post['digest']);
		$s_postdata_input = htmlspecialchars($postdata_input);
		$body = <<<EOD
$_msg_collided
<form action="$script?cmd=preview" method="post">
 <div>
  <input type="hidden" name="refer" value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols" id="textarea">$s_postdata_input</textarea><br />
 </div>
</form>

EOD;
	}
	else
	{
		page_write($post['refer'],$postdata);
		
		$title = $_title_updated;
	}

	$retvars['msg'] = $title;
	$retvars['body'] = $body;

	$post['page'] = $post['refer'];
	$vars['page'] = $post['refer'];

	return $retvars;
}
function plugin_vote_convert()
{
	global $script,$vars,$digest;
	global $_vote_plugin_choice, $_vote_plugin_votes;
	static $vote_no = 0;
	
	$vote_no++;
	
	if (!func_num_args()) {
		return '';
	}

	$args = func_get_args();
	$s_page = htmlspecialchars($vars['page']);
	$s_digest = htmlspecialchars($digest);

	$body = <<<EOD
<form action="$script" method="post">
 <table cellspacing="0" cellpadding="2" class="style_table" summary="vote">
  <tr>
   <td align="left" class="vote_label" style="padding-left:1em;padding-right:1em"><strong>$_vote_plugin_choice</strong>
    <input type="hidden" name="plugin" value="vote" />
    <input type="hidden" name="refer" value="$s_page" />
    <input type="hidden" name="vote_no" value="$vote_no" />
    <input type="hidden" name="digest" value="$s_digest" />
   </td>
   <td align="center" class="vote_label"><strong>$_vote_plugin_votes</strong></td>
  </tr>

EOD;
	
	$tdcnt = 0;
	foreach($args as $arg) {
		$cnt = 0;
		
		if (preg_match("/^(.+)\[(\d+)\]$/",$arg,$match)) {
			$arg = $match[1];
			$cnt = $match[2];
		}
		$e_arg = encode($arg);
		
		$link = make_link($arg);
		
		$cls = ($tdcnt++ % 2)  ? 'vote_td1' : 'vote_td2';
		
		$body .= <<<EOD
  <tr>
   <td align="left" class="$cls" style="padding-left:1em;padding-right:1em;">$link</td>
   <td align="right" class="$cls">$cnt&nbsp;&nbsp;
    <input type="submit" name="vote_$e_arg" value="$_vote_plugin_votes" class="submit" />
   </td>
  </tr>

EOD;
	}
	
	$body .= <<<EOD
 </table>
</form>

EOD;
	
	return $body;
}
?>
