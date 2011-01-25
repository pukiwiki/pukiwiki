<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: search.inc.php,v 1.14 2011/01/25 15:01:01 henoheno Exp $
//
// Search plugin

// Allow search via GET method 'index.php?plugin=search&word=keyword'
// NOTE: Also allows DoS to your site more easily by SPAMbot or worm or ...
define('PLUGIN_SEARCH_DISABLE_GET_ACCESS', 1); // 1, 0

define('PLUGIN_SEARCH_MAX_LENGTH', 80);
define('PLUGIN_SEARCH_MAX_BASE',   16); // #search(1,2,3,...,15,16)

// Show a search box on a page
function plugin_search_convert()
{
	static $done;

	if (isset($done)) {
		return '#search(): You already view a search box<br />' . "\n";
	} else {
		$done = TRUE;
		$args = func_get_args();
		return plugin_search_search_form('', '', $args);
	}
}

function plugin_search_action()
{
	global $post, $vars, $_title_result, $_title_search, $_msg_searching;

	if (PLUGIN_SEARCH_DISABLE_GET_ACCESS) {
		$s_word = isset($post['word']) ? htmlsc($post['word']) : '';
	} else {
		$s_word = isset($vars['word']) ? htmlsc($vars['word']) : '';
	}
	if (strlen($s_word) > PLUGIN_SEARCH_MAX_LENGTH) {
		unset($vars['word']); // Stop using $_msg_word at lib/html.php
		die_message('Search words too long');
	}

	$type = isset($vars['type']) ? $vars['type'] : '';
	$base = isset($vars['base']) ? $vars['base'] : '';

	if ($s_word != '') {
		// Search
		$msg  = str_replace('$1', $s_word, $_title_result);
		$body = do_search($vars['word'], $type, FALSE, $base);
	} else {
		// Init
		unset($vars['word']); // Stop using $_msg_word at lib/html.php
		$msg  = $_title_search;
		$body = '<br />' . "\n" . $_msg_searching . "\n";
	}

	// Show search form
	$bases = ($base == '') ? array() : array($base);
	$body .= plugin_search_search_form($s_word, $type, $bases);

	return array('msg'=>$msg, 'body'=>$body);
}

function plugin_search_search_form($s_word = '', $type = '', $bases = array())
{
	global $script, $_btn_and, $_btn_or, $_btn_search;
	global $_search_pages, $_search_all;

	$and_check = $or_check = '';
	if ($type == 'OR') {
		$or_check  = ' checked="checked"';
	} else {
		$and_check = ' checked="checked"';
	}

	$base_option = '';
	if (!empty($bases)) {
		$base_msg = '';
		$_num = 0;
		$check = ' checked="checked"';
		foreach($bases as $base) {
			++$_num;
			if (PLUGIN_SEARCH_MAX_BASE < $_num) break;
			$label_id = '_p_search_base_id_' . $_num;
			$s_base   = htmlsc($base);
			$base_str = '<strong>' . $s_base . '</strong>';
			$base_label = str_replace('$1', $base_str, $_search_pages);
			$base_msg  .=<<<EOD
 <div>
  <input type="radio" name="base" id="$label_id" value="$s_base" $check />
  <label for="$label_id">$base_label</label>
 </div>
EOD;
			$check = '';
		}
		$base_msg .=<<<EOD
  <input type="radio" name="base" id="_p_search_base_id_all" value="" />
  <label for="_p_search_base_id_all">$_search_all</label>
EOD;
		$base_option = '<div class="small">' . $base_msg . '</div>';
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
$base_option
</form>
EOD;
}
?>
