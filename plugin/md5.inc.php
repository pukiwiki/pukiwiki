<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: md5.inc.php,v 1.7 2005/01/23 05:29:10 henoheno Exp $
//  MD5 plugin

define('PLUGIN_MD5_LIMIT_LENGTH', 512);

function plugin_md5_action()
{
	global $script, $get, $post;

	if (PKWK_SAFE_MODE || PKWK_READONLY) die_message('Prohibited');

	// Wait POST
	$key = isset($post['key']) ? $post['key'] : '';
	if ($key != '') {
		plugin_md5_checklimit($key);
		// Compute (Don't show its $key at the same time)
		return array('msg'=>'MD5', 'body'=>'Hash: ' . md5($key));
	} else {
		// If cmd=md5&md5=password, set only (Don't compute)
		$value = isset($get['md5']) ? $get['md5'] : '';
		if ($value != '') {
			plugin_md5_checklimit($value);
			$value  = 'value="' . htmlspecialchars($value) . '" ';
		}
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

function plugin_md5_checklimit($text)
{
	if (strlen($text) > PLUGIN_MD5_LIMIT_LENGTH)
		die_message('Limit: malicious message length');
}
?>
