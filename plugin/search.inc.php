<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: search.inc.php,v 1.8 2005/04/02 06:33:39 henoheno Exp $
//
// Search plugin

// Allow search via GET method 'index.php?plugin=search&word=keyword'
// NOTE: Also allows DoS to your site more easily by SPAMbot or worm or ...
define('PLUGIN_SEARCH_DISABLE_GET_ACCESS', 1); // 1, 0

define('PLUGIN_SEARCH_MAX_LENGTH', 80);

// Show a search box on a page
function plugin_search_convert()
{
	static $done;

	if (func_get_args()) {
		return '#search(): No argument<br />' . "\n";
	} else if (isset($done)) {
		return '#search(): You already view a search box<br />' . "\n";
	} else {
		$done = TRUE;
		return plugin_search_search_form();
	}
}

function plugin_search_action()
{
	global $post, $vars, $_title_result, $_title_search, $_msg_searching;

	if (PLUGIN_SEARCH_DISABLE_GET_ACCESS) {
		$s_word = isset($post['word']) ? htmlspecialchars($post['word']) : '';
	} else {
		$s_word = isset($vars['word']) ? htmlspecialchars($vars['word']) : '';
	}
	if (strlen($s_word) > PLUGIN_SEARCH_MAX_LENGTH) {
		unset($vars['word']); // Stop using $_msg_word at lib/html.php
		die_message('Search words too long');
	}

	$type = isset($vars['type']) ? $vars['type'] : '';

	if ($s_word != '') {
		// Search
		$msg  = str_replace('$1', $s_word, $_title_result);
		$body = do_search($vars['word'], $type);
	} else {
		// Init
		unset($vars['word']); // Stop using $_msg_word at lib/html.php
		$msg  = $_title_search;
		$body = '<br />' . "\n" . $_msg_searching . "\n";
	}

	// Show search form
	$body .= plugin_search_search_form($s_word, $type);

	return array('msg'=>$msg, 'body'=>$body);
}

function plugin_search_search_form($s_word = '', $type = '')
{
	global $script, $_btn_and, $_btn_or, $_btn_search;

	$and_check = $or_check = '';
	if ($type == 'OR') {
		$or_check  = ' checked="checked"';
	} else {
		$and_check = ' checked="checked"';
	}

	return <<<EOD
<form action="$script?cmd=search" method="post">
 <div>
  <input type="text"  name="word" value="$s_word" size="20" />
  <input type="radio" name="type" id="_p_search_AND" value="AND" $and_check />
  <label for="_p_search_AND">$_btn_and</label>
  <input type="radio" name="type" id="_p_search_OR"  value="OR"  $or_check  />
  <label for="_p_search_OR">$_btn_or</label>
  &nbsp;<input type="submit" value="$_btn_search" />
 </div>
</form>
EOD;
}
?>
