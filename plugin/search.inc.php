<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: search.inc.php,v 1.2 2004/07/31 03:09:20 henoheno Exp $
//
// ¸¡º÷
function plugin_search_action()
{
	global $script,$vars;
	global $_title_result,$_title_search,$_msg_searching,$_btn_and,$_btn_or,$_btn_search;

	$s_word = array_key_exists('word',$vars) ? htmlspecialchars($vars['word']) : '';
	$type = array_key_exists('type',$vars) ? $vars['type'] : '';

	if ($s_word != '')
	{
		$msg = str_replace('$1',$s_word,$_title_result);
		$body = do_search($vars['word'],$type);
	}
	else
	{
		$msg = $_title_search;
		$body = "<br />\n$_msg_searching\n";
	}

	$and_check = $or_check = '';
	if ($type == 'OR')
		$or_check = ' checked="checked"';
	else
		$and_check = ' checked="checked"';

	$body .= <<<EOD
<form action="$script?cmd=search" method="post">
 <div>
  <input type="text" name="word" size="20" value="$s_word" />
  <input type="radio" name="type" value="AND" $and_check />$_btn_and
  <input type="radio" name="type" value="OR" $or_check />$_btn_or
  &nbsp;<input type="submit" value="$_btn_search" />
 </div>
</form>
EOD;

	return array('msg'=>$msg,'body'=>$body);
}
