<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: freeze.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//
// Åà·ë
function plugin_freeze_action()
{
	global $script,$post,$vars,$function_freeze,$adminpass;
	global $_title_isfreezed,$_title_freezed,$_title_freeze,$_msg_invalidpass,$_msg_freezing,$_btn_freeze;
	
	$msg = $body = '';
	
	if (!$function_freeze or !is_page($vars['page']))
		return array('msg'=>$msg,'body'=>$body);
	
	$pass = array_key_exists('pass',$post) ? $post['pass'] : NULL;
	
	if (is_freeze($vars['page']))
	{
		$msg = $_title_isfreezed;
		$body = str_replace('$1',htmlspecialchars(strip_bracket($vars['page'])),$_title_isfreezed);
	}
	else if (md5($pass) == $adminpass)
	{
		$postdata = get_source($post['page']);
		array_unshift($postdata,"#freeze\n");
		$postdata = join('',$postdata);
		
		$file = get_filename($vars['page']);
		$time = get_filetime($vars['page']);
		file_write(DATA_DIR,$vars['page'],$postdata);
		touch($file,$time + LOCALZONE);
		
		$vars['cmd'] = 'read';
		$msg = $_title_freezed;
		$body = '';
	}
	else
	{
		$msg = $_title_freeze;

		$body = "<br />\n";
		
		if ($pass !== NULL)
			$body .= "<strong>$_msg_invalidpass</strong><br />\n";
		
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
