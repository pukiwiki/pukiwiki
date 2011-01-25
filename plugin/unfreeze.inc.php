<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: unfreeze.inc.php,v 1.14 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C) 2003-2004, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Unfreeze(Unlock) plugin

// Show edit form when unfreezed
define('PLUGIN_UNFREEZE_EDIT', TRUE);

function plugin_unfreeze_action()
{
	global $script, $vars, $function_freeze;
	global $_title_isunfreezed, $_title_unfreezed, $_title_unfreeze;
	global $_msg_invalidpass, $_msg_unfreezing, $_btn_unfreeze;

	$page = isset($vars['page']) ? $vars['page'] : '';
	if (! $function_freeze || ! is_page($page))
		return array('msg' => '', 'body' => '');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	$msg = $body = '';
	if (! is_freeze($page)) {
		// Unfreezed already
		$msg  = & $_title_isunfreezed;
		$body = str_replace('$1', htmlsc(strip_bracket($page)),
			$_title_isunfreezed);

	} else if ($pass !== NULL && pkwk_login($pass)) {
		// Unfreeze
		$postdata = get_source($page);
		array_shift($postdata);
		$postdata = join('', $postdata);
		file_write(DATA_DIR, $page, $postdata, TRUE);

		// Update 
		is_freeze($page, TRUE);
		if (PLUGIN_UNFREEZE_EDIT) {
			$vars['cmd'] = 'read'; // To show 'Freeze' link
			$msg  = & $_title_unfreezed;
			$body = edit_form($page, $postdata);
		} else {
			$vars['cmd'] = 'read';
			$msg  = & $_title_unfreezed;
			$body = '';
		}

	} else {
		// Show unfreeze form
		$msg    = & $_title_unfreeze;
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
?>
