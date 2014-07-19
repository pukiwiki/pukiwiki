<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: vote.inc.php,v 1.27 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C) 2002-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Vote box plugin

function plugin_vote_action()
{
	global $vars, $script, $cols,$rows;
	global $_title_collided, $_msg_collided, $_title_updated;
	global $_vote_plugin_votes;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');

	$postdata_old  = get_source($vars['refer']);

	$vote_no = 0;
	$title = $body = $postdata = $postdata_input = $vote_str = '';
	$matches = array();
	foreach($postdata_old as $line) {

		if (! preg_match('/^#vote(?:\((.*)\)(.*))?$/i', $line, $matches) ||
		    $vote_no++ != $vars['vote_no']) {
			$postdata .= $line;
			continue;
		}
		$args  = explode(',', $matches[1]);
		$lefts = isset($matches[2]) ? $matches[2] : '';

		foreach($args as $arg) {
			$cnt = 0;
			if (preg_match('/^(.+)\[(\d+)\]$/', $arg, $matches)) {
				$arg = $matches[1];
				$cnt = $matches[2];
			}
			$e_arg = encode($arg);
			if (! empty($vars['vote_' . $e_arg]) && $vars['vote_' . $e_arg] == $_vote_plugin_votes)
				++$cnt;

			$votes[] = $arg . '[' . $cnt . ']';
		}

		$vote_str       = '#vote(' . @join(',', $votes) . ')' . $lefts . "\n";
		$postdata_input = $vote_str;
		$postdata      .= $vote_str;
	}

	if (md5(@join('', get_source($vars['refer']))) != $vars['digest']) {
		$title = $_title_collided;

		$s_refer          = htmlsc($vars['refer']);
		$s_digest         = htmlsc($vars['digest']);
		$s_postdata_input = htmlsc($postdata_input);
		$body = <<<EOD
$_msg_collided
<form action="$script?cmd=preview" method="post">
 <div>
  <input type="hidden" name="refer"  value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols" id="textarea">$s_postdata_input</textarea><br />
 </div>
</form>

EOD;
	} else {
		page_write($vars['refer'], $postdata);
		$title = $_title_updated;
	}

	$vars['page'] = $vars['refer'];

	return array('msg'=>$title, 'body'=>$body);
}

function plugin_vote_convert()
{
	global $script, $vars,  $digest;
	global $_vote_plugin_choice, $_vote_plugin_votes;
	static $number = array();

	$page = isset($vars['page']) ? $vars['page'] : '';
	
	// Vote-box-id in the page
	if (! isset($number[$page])) $number[$page] = 0; // Init
	$vote_no = $number[$page]++;

	if (! func_num_args()) return '#vote(): No arguments<br />' . "\n";

	if (PKWK_READONLY) {
		$_script = '';
		$_submit = 'hidden';
	} else {
		$_script = $script;
		$_submit = 'submit';
	}

	$args     = func_get_args();
	$s_page   = htmlsc($page);
	$s_digest = htmlsc($digest);

	$body = <<<EOD
<form action="$_script" method="post">
 <table cellspacing="0" cellpadding="2" class="style_table" summary="vote">
  <tr>
   <td align="left" class="vote_label" style="padding-left:1em;padding-right:1em"><strong>$_vote_plugin_choice</strong>
    <input type="hidden" name="plugin"  value="vote" />
    <input type="hidden" name="refer"   value="$s_page" />
    <input type="hidden" name="vote_no" value="$vote_no" />
    <input type="hidden" name="digest"  value="$s_digest" />
   </td>
   <td align="center" class="vote_label"><strong>$_vote_plugin_votes</strong></td>
  </tr>

EOD;

	$tdcnt = 0;
	$matches = array();
	foreach($args as $arg) {
		$cnt = 0;

		if (preg_match('/^(.+)\[(\d+)\]$/', $arg, $matches)) {
			$arg = $matches[1];
			$cnt = $matches[2];
		}
		$e_arg = encode($arg);

		$link = make_link($arg);

		$cls = ($tdcnt++ % 2)  ? 'vote_td1' : 'vote_td2';

		$body .= <<<EOD
  <tr>
   <td align="left"  class="$cls" style="padding-left:1em;padding-right:1em;">$link</td>
   <td align="right" class="$cls">$cnt&nbsp;&nbsp;
    <input type="$_submit" name="vote_$e_arg" value="$_vote_plugin_votes" class="submit" />
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
