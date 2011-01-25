<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: freeze.inc.php,v 1.12 2011/01/25 15:01:01 henoheno Exp $
// Copyright: 2003-2004, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Freeze(Lock) plugin

// Reserve 'Do nothing'. '^#freeze' is for internal use only.
function plugin_freeze_convert() { return ''; }

function plugin_freeze_action()
{
	global $script, $vars, $function_freeze;
	global $_title_isfreezed, $_title_freezed, $_title_freeze;
	global $_msg_invalidpass, $_msg_freezing, $_btn_freeze;

	$page = isset($vars['page']) ? $vars['page'] : '';
	if (! $function_freeze || ! is_page($page))
		return array('msg' => '', 'body' => '');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	$msg = $body = '';
	if (is_freeze($page)) {
		// Freezed already
		$msg  = & $_title_isfreezed;
		$body = str_replace('$1', htmlsc(strip_bracket($page)),
			$_title_isfreezed);

	} else if ($pass !== NULL && pkwk_login($pass)) {
		// Freeze
		$postdata = get_source($page);
		array_unshift($postdata, "#freeze\n");
		file_write(DATA_DIR, $page, join('', $postdata), TRUE);

		// Update
		is_freeze($page, TRUE);
		$vars['cmd'] = 'read';
		$msg  = & $_title_freezed;
		$body = '';

	} else {
		// Show a freeze form
		$msg    = & $_title_freeze;
		$s_page = htmlsc($page);
		$body   = ($pass === NULL) ? '' : "<p><strong>$_msg_invalidpass</strong></p>\n";
		$body  .= <<<EOD
<p>$_msg_freezing</p>
<form action="$script" method="post">
 <div>
  <input type="hidden"   name="cmd"  value="freeze" />
  <input type="hidden"   name="page" value="$s_page" />
  <input type="password" name="pass" size="12" />
  <input type="submit"   name="ok"   value="$_btn_freeze" />
 </div>
</form>
EOD;
	}

	return array('msg'=>$msg, 'body'=>$body);
}
?>
