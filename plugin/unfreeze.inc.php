<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: unfreeze.inc.php,v 1.1 2003/01/27 05:38:47 panda Exp $
//
// Åà·ë²ò½ü
function plugin_unfreeze_action()
{
	global $script,$post,$vars,$function_freeze,$adminpass;
	global $_title_isunfreezed,$_title_unfreezed,$_title_unfreeze,$_msg_invalidpass,$_msg_unfreezing,$_btn_unfreeze;
	
	$msg = $body = '';
	
	if (!$function_freeze or !is_page($vars['page']))
		return array('msg'=>$msg,'body'=>$body);
	
	$pass = array_key_exists('pass',$post) ? $post['pass'] : NULL;
	
	if (!is_freeze($vars['page']))
	{
		$msg = $_title_isunfreezed;
		$body = str_replace('$1',htmlspecialchars(strip_bracket($vars['page'])),$_title_isunfreezed);
	}
	else if (md5($pass) == $adminpass)
	{
		$postdata = get_source($post['page']);
		array_shift($postdata);
		$postdata = join('',$postdata);
		
		$file = get_filename($vars['page']);
		$time = get_filetime($vars['page']);
		file_write(DATA_DIR,$vars['page'],$postdata);
		touch($file,$time + LOCALZONE);
		
		$vars['cmd'] = 'read';
		$msg = $_title_unfreezed;
		$body = '';
	}
	else
	{
		$msg = $_title_unfreeze;

		$body = "<br />\n";
		
		if ($pass !== NULL)
			$body .= "<strong>$_msg_invalidpass</strong><br />\n";
		
		$body.= "$_msg_unfreezing<br />\n";
		
		$s_page = htmlspecialchars($vars['page']);
		$body .= <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="cmd" value="unfreeze" />
  <input type="hidden" name="page" value="$s_page" />
  <input type="password" name="pass" size="12" />
  <input type="submit" name="ok" value="$_btn_unfreeze" />
 </div>
</form>
EOD;
	}
	
	return array('msg'=>$msg,'body'=>$body);
}
?>
