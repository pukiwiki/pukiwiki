<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: md5.inc.php,v 1.8 2005/03/28 14:23:41 henoheno Exp $
//  MD5 plugin

define('PLUGIN_MD5_LIMIT_LENGTH', 512);

function plugin_md5_action()
{
	global $get, $post;

	if (PKWK_SAFE_MODE || PKWK_READONLY) die_message('Prohibited');

	// Wait POST
	$key    = isset($post['key']) ? $post['key'] : '';
	$submit = isset($post['key']);
	if ($key != '') {
		// Compute (Don't show its $key at the same time)
		$scheme = isset($post['scheme']) ? $post['scheme'] : '';
		$prefix = isset($post['prefix']);
		$body   = plugin_md5_compute($scheme, $key, $prefix);
		return array('msg'=>'MD5', 'body'=>$body);

	} else {
		// If plugin=md4&md5=password, only set it (Don't compute)
		$value = isset($get['md5']) ? $get['md5'] : '';

		plugin_md5_checklimit($value);
		if ($value != '') $value  = 'value="' . htmlspecialchars($value) . '" ';
		$self = get_script_uri();
		$form = '';
		if ($submit) $form .= '<strong>NO PHRASE</strong><br />';
		$form .= <<<EOD
<form action="$self" method="post">
 <div>
  <input type="hidden" name="plugin" value="md5" />
  <label for="_p_md5_phrase">Phrase:</label>
  <input type="text"  name="key"    id="_p_md5_phrase" size="30" $value/><br />
  <input type="radio" name="scheme" id="_p_md5_sha1" value="php_sha1" />
  <label for="_p_md5_sha1">PHP sha1()</label><br />
  <input type="radio" name="scheme" id="_p_md5_md5"  value="php_md5" checked="checked" />
  <label for="_p_md5_md5">PHP md5()</label><br />
  <input type="radio" name="scheme" id="_p_md5_crpt" value="php_crypt" />
  <label for="_p_md5_crpt">PHP crypt()</label><br />
  <input type="radio" name="scheme" id="_p_md5_lmd5" value="ldap_md5" />
  <label for="_p_md5_lmd5">OpenLDAP MD5</label><br />
  <input type="radio" name="scheme" id="_p_md5_lsha" value="ldap_sha" />
  <label for="_p_md5_lsha">OpenLDAP SHA (sha1)</label><br />
  <input type="checkbox" name="prefix" id="_p_md5_prefix" checked="checked" />
  <label for="_p_md5_prefix">Add scheme prefix (RFC2307, Using LDAP as NIS)</label><br />
  <input type="submit" value="Compute" />
 </div>
</form>
EOD;
		return array('msg'=>'MD5', 'body'=>$form);
	}
}

// Compute hash with php-functions, or compute like slappasswd (OpenLDAP)
function plugin_md5_compute($scheme = 'php_md5', $key = '', $prefix = FALSE)
{
	plugin_md5_checklimit($key);

	switch (strtolower($scheme)) {
	case 'x-php-crypt' : /* FALLTHROUGH */
	case 'php_crypt'   :
		$hash = ($prefix ? '{x-php-crypt}' : '') . crypt($key); break;
	case 'x-php-md5'   : /* FALLTHROUGH */
	case 'php_md5'     :
		$hash = ($prefix ? '{x-php-md5}'   : '') . md5($key);  break;
	case 'x-php-sha1'  : /* FALLTHROUGH */
	case 'php_sha1'    :
		$hash = ($prefix ? '{x-php-sha1}'  : '') . sha1($key); break;
	case 'md5'         : /* FALLTHROUGH */
	case 'ldap_md5'    :
		$hash = ($prefix ? '{MD5}' : '') . base64_encode(hex2bin(md5($key)));  break;
	case 'sha'         : /* FALLTHROUGH */
	case 'ldap_sha'    :
		$hash = ($prefix ? '{SHA}' : '') . base64_encode(hex2bin(sha1($key))); break;
	default: $hash = ''; break;
	}

	return $hash;
}

function plugin_md5_checklimit($text)
{
	if (strlen($text) > PLUGIN_MD5_LIMIT_LENGTH)
		die_message('Limit: malicious message length');
}
?>
