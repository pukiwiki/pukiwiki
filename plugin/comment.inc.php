<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: comment.inc.php,v 1.25 2004/11/23 01:19:59 henoheno Exp $
//
// Comment plugin

define('PLUGIN_COMMENT_DIRECTION_DEFAULT', '1'); // 1: above 0: below
define('PLUGIN_COMMENT_SIZE_MSG',  70);
define('PLUGIN_COMMENT_SIZE_NAME', 15);

// ----
define('PLUGIN_COMMENT_FORMAT_MSG',  '$msg');
define('PLUGIN_COMMENT_FORMAT_NAME', '[[$name]]');
define('PLUGIN_COMMENT_FORMAT_NOW',  '&new{$now};');
define('PLUGIN_COMMENT_FORMAT_STRING', "\x08MSG\x08 -- \x08NAME\x08 \x08NOW\x08");
function plugin_comment_action()
{
	global $script, $vars, $now, $_title_updated, $_no_name;
	global $_msg_comment_collided, $_title_comment_collided;

	if (! isset($vars['msg']) || $vars['msg'] == '')
		return array('msg'=>'', 'body'=>'');

	$vars['msg'] = preg_replace("/\n/", '', $vars['msg']);

	$head = '';
	$match = array();
	if (preg_match('/^(-{1,2})(.*)/', $vars['msg'], $match)) {
		$head        = $match[1];
		$vars['msg'] = $match[2];
	}
	unset($match);

	$_msg  = str_replace('$msg', $vars['msg'], PLUGIN_COMMENT_FORMAT_MSG);

	$_name = (! isset($vars['name']) || $vars['name'] == '') ? $_no_name : $vars['name'];
	$_name = ($_name == '') ? '' : str_replace('$name', $_name, PLUGIN_COMMENT_FORMAT_NAME);

	$_now  = ($vars['nodate'] == '1') ? '' : str_replace('$now', $now, PLUGIN_COMMENT_FORMAT_NOW);

	$comment = str_replace("\x08MSG\x08",  $_msg,  PLUGIN_COMMENT_FORMAT_STRING);
	$comment = str_replace("\x08NAME\x08", $_name, $comment);
	$comment = str_replace("\x08NOW\x08",  $_now,  $comment);
	$comment = $head . $comment;

	$postdata = '';
	$postdata_old  = get_source($vars['refer']);
	$comment_no = 0;
	$comment_ins = ($vars['above'] == '1');

	foreach ($postdata_old as $line) {
		if (! $comment_ins) $postdata .= $line;
		if (preg_match('/^#comment/i', $line) && $comment_no++ == $vars['comment_no']) {
			$postdata = rtrim($postdata) . "\n-$comment\n";
			if ($comment_ins) $postdata .= "\n";
		}
		if ($comment_ins) $postdata .= $line;
	}

	$title = $_title_updated;
	$body = '';
	if (md5(@join('', get_source($vars['refer']))) != $vars['digest']) {
		$title = $_title_comment_collided;
		$body  = $_msg_comment_collided . make_pagelink($vars['refer']);
	}

	page_write($vars['refer'], $postdata);

	$retvars['msg']  = $title;
	$retvars['body'] = $body;

	$vars['page'] = $vars['refer'];

	return $retvars;
}

function plugin_comment_convert()
{
	global $script, $vars, $digest;
	global $_btn_comment, $_btn_name, $_msg_comment;
	static $numbers = array();

	if (! isset($numbers[$vars['page']])) $numbers[$vars['page']] = 0;
	$comment_no = $numbers[$vars['page']]++;

	$options = func_num_args() ? func_get_args() : array();

	if (in_array('noname', $options)) {
		$nametags = $_msg_comment;
	} else {
		$nametags = $_btn_name .
			'<input type="text" name="name" size="' . PLUGIN_COMMENT_SIZE_NAME . "\" />\n";
	}

	$nodate = in_array('nodate', $options) ? '1' : '0';
	$above  = in_array('above',  $options) ? '1' :
		(in_array('below', $options) ? '0' : PLUGIN_COMMENT_DIRECTION_DEFAULT);

	$s_page = htmlspecialchars($vars['page']);
	static $comment_cols = PLUGIN_COMMENT_SIZE_MSG;
	$string = <<<EOD
<br />
<form action="$script" method="post">
 <div>
  <input type="hidden" name="comment_no" value="$comment_no" />
  <input type="hidden" name="refer"  value="$s_page" />
  <input type="hidden" name="plugin" value="comment" />
  <input type="hidden" name="nodate" value="$nodate" />
  <input type="hidden" name="above"  value="$above" />
  <input type="hidden" name="digest" value="$digest" />
  $nametags
  <input type="text"   name="msg" size="$comment_cols" />
  <input type="submit" name="comment" value="$_btn_comment" />
 </div>
</form>
EOD;

	return $string;
}
?>
