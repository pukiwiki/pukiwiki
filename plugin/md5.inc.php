<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: md5.inc.php,v 1.5 2004/11/28 06:31:04 henoheno Exp $
//  MD5 plugin

function plugin_md5_action()
{
	global $script, $get, $post;

	// Wait POST
	$key = isset($post['key']) ? $post['key'] : '';
	if ($key != '') {
		// Compute (Don't show its $key at the same time)
		return array('msg'=>'MD5', 'body'=>'Hash: ' . md5($key));
	} else {
		// If cmd=md5&md5=password, set only (Don't compute)
		$value = isset($get['md5']) ? $get['md5'] : '';
		if ($value != '') $value  = 'value="' . htmlspecialchars($value) . '" ';
		$form = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="md5" />
  <input type="text"   name="key" size="30" $value/>
  <input type="submit" value="Compute" />
 </div>
</form>
<br/>
EOD;
		return array('msg'=>'MD5', 'body'=>$form);
	}
}
?>
