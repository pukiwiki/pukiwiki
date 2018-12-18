<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// saml.inc.php
// Copyright
//   2018 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// PukiWiki SAML Plugin

require 'vendor/autoload.php';
require_once 'vendor/onelogin/php-saml/_toolkit_loader.php';

define('PLUGIN_SAML_AUTHUSER_ID_ATTR', 'UserId');
define('PLUGIN_SAML_AUTHUSER_DISPLAYNAME_ATTR', 'DisplayName');

/**
 *  SAML Handler
 */
function plugin_saml_action() {
	global $vars;
	require 'saml_settings.php';

	$auth = new OneLogin\Saml2\Auth($settingsInfo);

	if (isset($vars['sso'])) {
		// sso: Sign in endpoint before IdP
		$url_after_login = $vars['url_after_login'];
		$auth->login($url_after_login);
	} else if (isset($vars['slo'])) {
		// sso: Sign out endpoint before IdP
		$returnTo = null;
		$paramters = array();
		$nameId = null;
		$sessionIndex = null;
		if (isset($_SESSION['samlNameId'])) {
			$nameId = $_SESSION['samlNameId'];
		}
		if (isset($_SESSION['samlSessionIndex'])) {
			$sessionIndex = $_SESSION['samlSessionIndex'];
		}
		$auth->logout($returnTo, $paramters, $nameId, $sessionIndex);
	} else if (isset($vars['acs'])) {
		// acs: Sign in endpoint after IdP
		$auth->processResponse();

		$errors = $auth->getErrors();

		if (!empty($errors)) {
			return array('msg' => 'SAML Error', print_r('<p>'.implode(', ', $errors).'</p>'));
		}

		if (!$auth->isAuthenticated()) {
			return array('msg' => 'SAML sign in', 'body' => '<p>Not authenticated</p>');
		}
		$attrs = $auth->getAttributes();
		$_SESSION['samlUserdata'] = $attrs;
		$_SESSION['samlNameId'] = $auth->getNameId();
		$_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
		if (isset($attrs[PLUGIN_SAML_AUTHUSER_ID_ATTR][0])) {
			// PukiWiki ExternalAuth requirement
			$_SESSION['authenticated_user'] = $attrs[PLUGIN_SAML_AUTHUSER_ID_ATTR][0];
		} else {
			$_SESSION['authenticated_user'] = $auth->getNameId();
		}
		if (isset($attrs[PLUGIN_SAML_AUTHUSER_DISPLAYNAME_ATTR][0])) {
			// PukiWiki ExternalAuth requirement
			$_SESSION['authenticated_user_fullname'] = $attrs[PLUGIN_SAML_AUTHUSER_DISPLAYNAME_ATTR][0];
		}

		if (isset($_POST['RelayState']) && OneLogin\Saml2\Utils::getSelfURL() != $_POST['RelayState']) {
			$auth->redirectTo($_POST['RelayState']);
		}
		return array('msg' => 'SAML sign in', 'body' => 'SAML Sined in. but no redirection');
	} else if (isset($vars['sls'])) {
		// sls: Sign out endpoint after IdP
		// onelone/php-saml only supports Redirect SingleLogout
		$is_post = $_SERVER['REQUEST_METHOD'] === 'POST';
		if ($is_post) {
			session_destroy();
			$_SESSION = array();
		} else {
			$auth->processSLO();
			$errors = $auth->getErrors();
			$msg = '';
			if (empty($errors)) {
				$msg .= '<p>Sucessfully logged out</p>';
			} else {
				$msg .= '<p>'.implode(', ', $errors).'</p>';
			}
		}
		return array('msg' => 'SAML sign out', 'body' => 'SAML Sined out. ' . $msg);
	} else if (isset($vars['metadata'])) {
		// metadata: SP metadata endpoint
		try {
			$auth = new OneLogin\Saml2\Auth($settingsInfo);
			$settings = $auth->getSettings();
			$metadata = $settings->getSPMetadata();
			$errors = $settings->validateMetadata($metadata);
			if (empty($errors)) {
				header('Content-Type: text/xml');
				echo $metadata;
			} else {
				throw new OneLogin\Saml2\Error(
					'Invalid SP metadata: '.implode(', ', $errors),
					OneLogin\Saml2\Error::METADATA_SP_INVALID
				);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		exit;
	}
	return array('msg' => 'Error', 'body' => 'SAML Invalid state srror');
}
