<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: search.inc.php,v 1.3 2005/01/02 05:38:44 henoheno Exp $
//
// Search plugin

function plugin_search_action()
{
	global $script, $post, $vars, $_title_result, $_title_search;
	global $_msg_searching, $_btn_and, $_btn_or, $_btn_search;

	$s_word = isset($post['word']) ? htmlspecialchars($post['word']) : '';
	$type   = isset($vars['type']) ? $vars['type'] : '';

	if ($s_word != '') {
		$msg  = str_replace('$1', $s_word, $_title_result);
		$body = do_search($vars['word'], $type);
	} else {
		$msg  = $_title_search;
		$body = '<br />' . "\n" . $_msg_searching . "\n";
	}

	$and_check = $or_check = '';
	if ($type == 'OR') {
		$or_check  = ' checked="checked"';
	} else {
		$and_check = ' checked="checked"';
	}

	$body .= <<<EOD
<form action="$script?cmd=search" method="post">
 <div>
  <input type="text"  name="word" value="$s_word" size="20" />
  <input type="radio" name="type" value="AND" $and_check />$_btn_and
  <input type="radio" name="type" value="OR"  $or_check  />$_btn_or
  &nbsp;<input type="submit" value="$_btn_search" />
 </div>
</form>
EOD;

	return array('msg'=>$msg, 'body'=>$body);
}
