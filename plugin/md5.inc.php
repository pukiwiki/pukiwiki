<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: md5.inc.php,v 1.13 2005/04/05 13:44:05 henoheno Exp $
//
//  MD5 plugin

// User interface of pkwk_hash_compute() for system admin
function plugin_md5_action()
{
	global $get, $post;

	if (PKWK_SAFE_MODE || PKWK_READONLY) die_message('Prohibited');

	// Wait POST
	$key    = isset($post['key']) ? $post['key'] : '';
	$submit = isset($post['key']);
	if ($key != '') {
		// Compute (Don't show its $key at the same time)

		$prefix = isset($post['prefix']);
		$salt   = isset($post['salt']) ? $post['salt'] : '';

		// With scheme-prefix or not
		if (! preg_match('/^\{.+\}.*$/', $salt)) {
			$scheme = isset($post['scheme']) ? '{' . $post['scheme'] . '}': '';
			$salt   = $scheme . $salt;
		}

		return array('msg'=>'MD5',
			'body'=>pkwk_hash_compute($salt, $key, $prefix));

	} else {
		// If plugin=md5&md5=password, only set it (Don't compute)
		$value = isset($get['md5']) ? $get['md5'] : '';
		if (strlen($value) > PKWK_PASSPHRASE_LIMIT_LENGTH)
			die_message('Limit: malicious message length');
		if ($value != '') $value  = 'value="' . htmlspecialchars($value) . '" ';

		$self = get_script_uri();
		$form = '';
		if ($submit) $form .= '<strong>NO PHRASE</strong><br />';
		$form .= <<<EOD
<form action="$self" method="post">
 <div>
  <input type="hidden" name="plugin" value="md5" />
  <label for="_p_md5_phrase">Phrase:</label>
  <input type="text" name="key"  id="_p_md5_phrase" size="60" $value/><br />

  <input type="radio" name="scheme" id="_p_md5_sha1" value="php_sha1" />
  <label for="_p_md5_sha1">PHP sha1()</label><br />
  <input type="radio" name="scheme" id="_p_md5_md5"  value="php_md5" checked="checked" />
  <label for="_p_md5_md5">PHP md5()</label><br />
  <input type="radio" name="scheme" id="_p_md5_crpt" value="php_crypt" />
  <label for="_p_md5_crpt">PHP crypt() *</label><br />

  <input type="radio" name="scheme" id="_p_md5_lssha" value="ldap_ssha" />
  <label for="_p_md5_lssha">LDAP SSHA (sha-1 with a seed)</label><br />
  <input type="radio" name="scheme" id="_p_md5_lsha" value="ldap_sha" />
  <label for="_p_md5_lsha">LDAP SHA (sha-1)</label><br />

  <input type="radio" name="scheme" id="_p_md5_lsmd5" value="ldap_smd5" />
  <label for="_p_md5_lsmd5">LDAP SMD5 (md5 with a seed)</label><br />
  <input type="radio" name="scheme" id="_p_md5_lmd5" value="ldap_md5" />
  <label for="_p_md5_lmd5">LDAP MD5</label><br />

  <input type="radio" name="scheme" id="_p_md5_lcrpt" value="ldap_crypt" />
  <label for="_p_md5_lcrpt">LDAP CRYPT *</label><br />

  <input type="checkbox" name="prefix" id="_p_md5_prefix" checked="checked" />
  <label for="_p_md5_prefix">Add scheme prefix (RFC2307, Using LDAP as NIS)</label><br />

  <label for="_p_md5_salt">*Salt or userPassword itself:</label>
  <input type="text" name="salt" id="_p_md5_salt" size="60" /><br />

  <input type="submit" value="Compute" />
 </div>
</form>
EOD;
		return array('msg'=>'MD5', 'body'=>$form);
	}
}
?>
