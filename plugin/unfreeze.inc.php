<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// unfreeze.inc.php
// Copyright 2003-2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Unfreeze(Unlock) plugin

// Show edit form when unfreezed
define('PLUGIN_UNFREEZE_EDIT', TRUE);

function plugin_unfreeze_action()
{
	global $vars, $function_freeze;
	global $_title_isunfreezed, $_title_unfreezed, $_title_unfreeze;
	global $_msg_invalidpass, $_msg_unfreezing, $_btn_unfreeze;

	$script = get_base_uri();
	$page = isset($vars['page']) ? $vars['page'] : '';
	if (! $function_freeze || ! is_page($page))
		return array('msg' => '', 'body' => '');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	$msg = $body = '';
	if (! is_freeze($page)) {
		// Unfreezed already
		$msg  = $_title_isunfreezed;
		$body = str_replace('$1', htmlsc(strip_bracket($page)),
			$_title_isunfreezed);

	} else if ($pass !== NULL && pkwk_login($pass)) {
		// Unfreeze
		$postdata = get_source($page);
		for ($i = count($postdata) - 1; $i >= 0; $i--) {
			if ("#freeze\n" === $postdata[$i]) {
				$postdata[$i] = '';
			}
		}
		$postdata = join('', $postdata);
		file_write(DATA_DIR, $page, $postdata, TRUE);

		// Update 
		is_freeze($page, TRUE);
		if (PLUGIN_UNFREEZE_EDIT) {
			$vars['cmd'] = 'edit'; // To show 'Freeze' link
			$msg  = $_title_unfreezed;
			$postdata = remove_author_info($postdata);
			$body = edit_form($page, $postdata);
		} else {
			$vars['cmd'] = 'read';
			$msg  = $_title_unfreezed;
			$body = '';
		}
	} else {
		// Show unfreeze form
		$msg    = $_title_unfreeze;
		$s_page = htmlsc($page);
		$body   = ($pass === NULL) ? '' : "<p><strong>$_msg_invalidpass</strong></p>\n";
		$body  .= <<<EOD
<p>$_msg_unfreezing</p>
<form action="$script" method="post">
 <div>
  <input type="hidden"   name="cmd"  value="unfreeze" />
  <input type="hidden"   name="page" value="$s_page" />
  <input type="password" name="pass" size="12" />
  <input type="submit"   name="ok"   value="$_btn_unfreeze" />
 </div>
</form>
EOD;
	}
	return array('msg'=>$msg, 'body'=>$body);
}
