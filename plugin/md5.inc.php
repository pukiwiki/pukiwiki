<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: md5.inc.php,v 1.19 2005/06/04 01:59:52 henoheno Exp $
//
//  MD5 plugin

// User interface of pkwk_hash_compute() for system admin
function plugin_md5_action()
{
	global $get, $post;

	if (PKWK_SAFE_MODE || PKWK_READONLY) die_message('Prohibited');

	// Wait POST
	$submit = isset($post['key']);
	$key    = isset($post['key']) ? $post['key'] : '';
	if ($key != '') {
		// Compute (Don't show its $key at the same time)

		$prefix = isset($post['prefix']);
		$salt   = isset($post['salt']) ? $post['salt'] : '';

		// With scheme-prefix or not
		if (! preg_match('/^\{.+\}.*$/', $salt)) {
			$scheme = isset($post['scheme']) ? '{' . $post['scheme'] . '}': '';
			$salt   = $scheme . $salt;
		}

		return array(
			'msg' =>'Result',
			'body'=>
				//($prefix ? 'userPassword: ' : '') .
				pkwk_hash_compute($salt, $key, $prefix, TRUE));

	} else {
		// If plugin=md5&md5=password, only set it (Don't compute)
		$value = isset($get['md5']) ? $get['md5'] : '';
		return array(
			'msg' =>'Compute userPassword',
			'body'=>plugin_md5_show_form($submit, $value));
	}
}

// $phrase = Passphrase is here or not
// $value  = Default passphrase value
function plugin_md5_show_form($phrase = FALSE, $value = '')
{
	if (PKWK_SAFE_MODE || PKWK_READONLY) die_message('Prohibited');
	if (strlen($value) > PKWK_PASSPHRASE_LIMIT_LENGTH)
		die_message('Limit: malicious message length');

	if ($value != '') $value = 'value="' . htmlspecialchars($value) . '" ';
	$self = get_script_uri();

	$form = <<<EOD
<p><strong>NOTICE: Don't use this feature via untrustful or unsure network</strong></p>
<hr>
EOD;

	if ($phrase) $form .= '<strong>NO PHRASE</strong><br />';

	$form .= <<<EOD
<form action="$self" method="post">
 <div>
  <input type="hidden" name="plugin" value="md5" />
  <label for="_p_md5_phrase">Phrase:</label>
  <input type="text" name="key"  id="_p_md5_phrase" size="60" $value/><br />

  <input type="radio" name="scheme" id="_p_md5_sha1" value="x-php-sha1" />
  <label for="_p_md5_sha1">PHP sha1()</label><br />
  <input type="radio" name="scheme" id="_p_md5_md5"  value="x-php-md5" checked="checked" />
  <label for="_p_md5_md5">PHP md5()</label><br />
  <input type="radio" name="scheme" id="_p_md5_crpt" value="x-php-crypt" />
  <label for="_p_md5_crpt">PHP crypt() *</label><br />

  <input type="radio" name="scheme" id="_p_md5_lssha" value="SSHA" />
  <label for="_p_md5_lssha">LDAP SSHA (sha-1 with a seed) *</label><br />
  <input type="radio" name="scheme" id="_p_md5_lsha" value="SHA" />
  <label for="_p_md5_lsha">LDAP SHA (sha-1)</label><br />

  <input type="radio" name="scheme" id="_p_md5_lsmd5" value="SMD5" />
  <label for="_p_md5_lsmd5">LDAP SMD5 (md5 with a seed) *</label><br />
  <input type="radio" name="scheme" id="_p_md5_lmd5" value="MD5" />
  <label for="_p_md5_lmd5">LDAP MD5</label><br />

  <input type="radio" name="scheme" id="_p_md5_lcrpt" value="CRYPT" />
  <label for="_p_md5_lcrpt">LDAP CRYPT *</label><br />

  <input type="checkbox" name="prefix" id="_p_md5_prefix" checked="checked" />
  <label for="_p_md5_prefix">Add scheme prefix (RFC2307, Using LDAP as NIS)</label><br />

  <label for="_p_md5_salt">Salt, '{scheme}', '{scheme}salt', or userPassword itself to specify:</label><br />
  <input type="text" name="salt" id="_p_md5_salt" size="60" /><br />

  <input type="submit" value="Compute" /><br />

  <hr>
  <p>* = Salt enabled<p/>
 </div>
</form>
EOD;

	return $form;
}
?>
