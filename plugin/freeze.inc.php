<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: freeze.inc.php,v 1.8 2004/12/16 13:09:48 henoheno Exp $
//
// 凍結
function plugin_freeze_convert()
{
	return '';
}

function plugin_freeze_action()
{
	global $script, $vars, $function_freeze;
	global $_title_isfreezed, $_title_freezed, $_title_freeze;
	global $_msg_invalidpass, $_msg_freezing, $_btn_freeze;

	$page = isset($vars['page']) ? $vars['page'] : '';

	if (!$function_freeze or !is_page($page))
		return array('msg' => '', 'body' => '');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	if (is_freeze($page)) {
		return array(
			'msg'  => $_title_isfreezed,
			'body' => str_replace('$1', htmlspecialchars(strip_bracket($page)), $_title_isfreezed)
		);
	} else if ($pass !== NULL && pkwk_login($pass)) {
		$postdata = get_source($page);
		array_unshift($postdata, "#freeze\n");
		$postdata = join('', $postdata);

		file_write(DATA_DIR,$page, $postdata, TRUE);

		is_freeze($page, TRUE);
		$vars['cmd'] = 'read';
		return array('msg' => $_title_freezed, 'body' => '');
	}
	// 凍結フォームを表示
	$s_page = htmlspecialchars($page);

	$body = ($pass === NULL) ? '' : "<p><strong>$_msg_invalidpass</strong></p>\n";
	$body .= <<<EOD
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

	return array('msg'=>$_title_freeze, 'body'=>$body);
}
?>
