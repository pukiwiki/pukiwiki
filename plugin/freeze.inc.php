<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: freeze.inc.php,v 1.3 2003/06/05 05:00:45 arino Exp $
//
// Åà·ë
function plugin_freeze_convert()
{
	return '';
}

function plugin_freeze_action()
{
	global $script,$post,$vars,$function_freeze,$adminpass;
	global $_title_isfreezed,$_title_freezed,$_title_freeze;
	global $_msg_invalidpass,$_msg_freezing,$_btn_freeze;
	
	$msg = $body = '';
	
	if (!$function_freeze or !is_page($vars['page'])) {
		return array('msg'=>$msg,'body'=>$body);
	}
	
	$pass = array_key_exists('pass',$post) ? $post['pass'] : NULL;
	
	if (is_freeze($vars['page'])) {
		$msg = $_title_isfreezed;
		$body = str_replace('$1',htmlspecialchars(strip_bracket($vars['page'])),$_title_isfreezed);
	}
	else if (md5($pass) == $adminpass) {
		$postdata = get_source($post['page']);
		array_unshift($postdata,"#freeze\n");
		$postdata = join('',$postdata);
		
		file_write(DATA_DIR,$vars['page'],$postdata,TRUE);
		
		$vars['cmd'] = 'read';
		$msg = $_title_freezed;
		$body = '';
	}
	else {
		$msg = $_title_freeze;

		$body = "<br />\n";
		
		if ($pass !== NULL) {
			$body .= "<strong>$_msg_invalidpass</strong><br />\n";
		}
		
		$body.= "$_msg_freezing<br />\n";
		
		$s_page = htmlspecialchars($vars['page']);
		$body .= <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="cmd" value="freeze" />
  <input type="hidden" name="page" value="$s_page" />
  <input type="password" name="pass" size="12" />
  <input type="submit" name="ok" value="$_btn_freeze" />
 </div>
</form>
EOD;
	}
	
	return array('msg'=>$msg,'body'=>$body);
}
?>
