<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: pukiwiki.ini.php,v 1.149 2007/08/26 15:19:48 henoheno Exp $
// Copyright (C)
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki main setting file

/////////////////////////////////////////////////
// Functionality settings

// PKWK_OPTIMISE - Ignore verbose but understandable checking and warning
//   If you end testing this PukiWiki, set '1'.
//   If you feel in trouble about this PukiWiki, set '0'.
if (! defined('PKWK_OPTIMISE'))
	define('PKWK_OPTIMISE', 0);

/////////////////////////////////////////////////
// Security settings

// PKWK_READONLY - Prohibits editing and maintain via WWW
//   NOTE: Counter-related functions will work now (counter, attach count, etc)
if (! defined('PKWK_READONLY'))
	define('PKWK_READONLY', 0); // 0 or 1

// PKWK_SAFE_MODE - Prohibits some unsafe(but compatible) functions 
if (! defined('PKWK_SAFE_MODE'))
	define('PKWK_SAFE_MODE', 0);

// PKWK_DISABLE_INLINE_IMAGE_FROM_URI - Disallow using inline-image-tag for URIs
//   Inline-image-tag for URIs may allow leakage of Wiki readers' information
//   (in short, 'Web bug') or external malicious CGI (looks like an image's URL)
//   attack to Wiki readers, but easy way to show images.
if (! defined('PKWK_DISABLE_INLINE_IMAGE_FROM_URI'))
	define('PKWK_DISABLE_INLINE_IMAGE_FROM_URI', 0);

// PKWK_QUERY_STRING_MAX
//   Max length of GET method, prohibits some worm attack ASAP
//   NOTE: Keep (page-name + attach-file-name) <= PKWK_QUERY_STRING_MAX
define('PKWK_QUERY_STRING_MAX', 640); // Bytes, 0 = OFF

/////////////////////////////////////////////////
// Experimental features

// Multiline plugin hack (See BugTrack2/84)
// EXAMPLE(with a known BUG):
//   #plugin(args1,args2,...,argsN){{
//   argsN+1
//   argsN+1
//   #memo(foo)
//   argsN+1
//   }}
//   #memo(This makes '#memo(foo)' to this)
define('PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK', 1); // 1 = Disabled

/////////////////////////////////////////////////
// Language / Encoding settings

// LANG - Internal content encoding ('en', 'ja', or ...)
define('LANG', 'ja');

// UI_LANG - Content encoding for buttons, menus,  etc
define('UI_LANG', LANG); // 'en' for Internationalized wikisite

/////////////////////////////////////////////////
// Directory settings I (ended with '/', permission '777')

// You may hide these directories (from web browsers)
// by setting DATA_HOME at index.php.

define('DATA_DIR',      DATA_HOME . 'wiki/'     ); // Latest wiki texts
define('DIFF_DIR',      DATA_HOME . 'diff/'     ); // Latest diffs
define('BACKUP_DIR',    DATA_HOME . 'backup/'   ); // Backups
define('CACHE_DIR',     DATA_HOME . 'cache/'    ); // Some sort of caches
define('UPLOAD_DIR',    DATA_HOME . 'attach/'   ); // Attached files and logs
define('COUNTER_DIR',   DATA_HOME . 'counter/'  ); // Counter plugin's counts
define('PLUGIN_DIR',    DATA_HOME . 'plugin/'   ); // Plugin directory

/////////////////////////////////////////////////
// Directory settings II (ended with '/')

// Skins / Stylesheets
define('SKIN_DIR', 'skin/');
// Skin files (SKIN_DIR/*.skin.php) are needed at
// ./DATAHOME/SKIN_DIR from index.php, but
// CSSs(*.css) and JavaScripts(*.js) are needed at
// ./SKIN_DIR from index.php.

// Static image files
define('IMAGE_DIR', 'image/');
// Keep this directory shown via web browsers like
// ./IMAGE_DIR from index.php.

/////////////////////////////////////////////////
// Local time setting

switch (LANG) { // or specifiy one
case 'ja':
	define('ZONE', 'JST');
	define('ZONETIME', 9 * 3600); // JST = GMT + 9
	break;
default  :
	define('ZONE', 'GMT');
	define('ZONETIME', 0);
	break;
}

/////////////////////////////////////////////////
// Title of your Wikisite (Name this)
// Also used as RSS feed's channel name etc
$page_title = 'PukiWiki';

// Specify PukiWiki URL (default: auto)
//$script = 'http://example.com/pukiwiki/';

// Shorten $script: Cut its file name (default: not cut)
//$script_directory_index = 'index.php';

// Site admin's name (CHANGE THIS)
$modifier = 'anonymous';

// Site admin's Web page (CHANGE THIS)
$modifierlink = 'http://pukiwiki.example.com/';

// Default page name
$defaultpage  = 'FrontPage';     // Top / Default page
$whatsnew     = 'RecentChanges'; // Modified page list
$whatsdeleted = 'RecentDeleted'; // Removeed page list
$interwiki    = 'InterWikiName'; // Set InterWiki definition here
$aliaspage    = 'AutoAliasName'; // Set AutoAlias definition here
$menubar      = 'MenuBar';       // Menu

/////////////////////////////////////////////////
// Change default Document Type Definition

// Some web browser's bug, and / or Java apprets may needs not-Strict DTD.
// Some plugin (e.g. paint) set this PKWK_DTD_XHTML_1_0_TRANSITIONAL.

//$pkwk_dtd = PKWK_DTD_XHTML_1_1; // Default
//$pkwk_dtd = PKWK_DTD_XHTML_1_0_STRICT;
//$pkwk_dtd = PKWK_DTD_XHTML_1_0_TRANSITIONAL;
//$pkwk_dtd = PKWK_DTD_HTML_4_01_STRICT;
//$pkwk_dtd = PKWK_DTD_HTML_4_01_TRANSITIONAL;

/////////////////////////////////////////////////
// Always output "nofollow,noindex" attribute

$nofollow = 0; // 1 = Try hiding from search engines

/////////////////////////////////////////////////

// PKWK_ALLOW_JAVASCRIPT - Allow / Prohibit using JavaScript
define('PKWK_ALLOW_JAVASCRIPT', 0);

/////////////////////////////////////////////////
// Spam URI insertion filtering

$spam = 1;	// 1 = On

if ($spam) {
	$spam = array();

	// Threshold and rules for insertion (default)
	$spam['method']['_default'] = array(
		'_comment'     => '_default',
		'quantity'     =>  8,
		//'non_uniquri'  =>  3,
		'non_uniqhost' =>  3,
		'area_anchor'  =>  0,
		'area_bbcode'  =>  0,
		'uniqhost'     => TRUE,
		'badhost'      => TRUE,
		'asap'         => TRUE, // Stop as soon as possible (quick but less-info)
	);

	// For editing
	// NOTE:
	// Any thresholds may LOCK your contents by
	// "posting one URL" many times.
	// Any rules will lock contents that have NG things already.
	$spam['method']['edit'] = array(
		// Supposed_by_you(n) * Margin(1.5)
		'_comment'     => 'edit',
		//'quantity'     => 60 * 1.5,
		//'non_uniquri'  =>  5 * 1.5,
		//'non_uniqhost' => 50 * 1.5,
		//'area_anchor'  => 30 * 1.5,
		//'area_bbcode'  => 15 * 1.5,
		'uniqhost'     => TRUE,
		'badhost'      => TRUE,
		'asap'         => TRUE,
	);

	//$spam['exitmode'] = 'dump'; // Dump progress
}

/////////////////////////////////////////////////
// _Disable_ WikiName auto-linking
$nowikiname = 0;

/////////////////////////////////////////////////
// AutoLink feature
// Automatic link to existing pages (especially helpful for non-wikiword pages, but heavy)

// Minimum length of page name
$autolink = 0; // Bytes, 0 = OFF (try 8)

/////////////////////////////////////////////////
// AutoAlias feature
// Automatic link from specified word, to specifiled URI, page or InterWiki

// Minimum length of alias "from" word
$autoalias = 0; // Bytes, 0 = OFF (try 8)

// Limit loading valid alias pairs
$autoalias_max_words = 50; // pairs

/////////////////////////////////////////////////
// Enable Freeze / Unfreeze feature
$function_freeze = 1;

/////////////////////////////////////////////////
// Allow to use 'Do not change timestamp' checkbox
// (0:Disable, 1:For everyone,  2:Only for the administrator)
$notimeupdate = 1;

/////////////////////////////////////////////////
// Admin password for this Wikisite

// Default: always fail
$adminpass = '{x-php-md5}!';

// Sample:
//$adminpass = 'pass'; // Cleartext
//$adminpass = '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72'; // PHP md5()  'pass'
//$adminpass = '{CRYPT}$1$AR.Gk94x$uCe8fUUGMfxAPH83psCZG/';   // LDAP CRYPT 'pass'
//$adminpass = '{MD5}Gh3JHJBzJcaScd3wyUS8cg==';               // LDAP MD5   'pass'
//$adminpass = '{SMD5}o7lTdtHFJDqxFOVX09C8QnlmYmZnd2Qx';      // LDAP SMD5  'pass'

/////////////////////////////////////////////////
// Page-reading feature settings
// (Automatically creating pronounce datas, for Kanji-included page names,
//  to show sorted page-list correctly)

// Enable page-reading feature by calling ChaSen or KAKASHI command
// (1:Enable, 0:Disable)
$pagereading_enable = 0;

// Specify converter as ChaSen('chasen') or KAKASI('kakasi') or None('none')
$pagereading_kanji2kana_converter = 'none';

// Specify Kanji encoding to pass data between PukiWiki and the converter
$pagereading_kanji2kana_encoding = 'EUC'; // Default for Unix
//$pagereading_kanji2kana_encoding = 'SJIS'; // Default for Windows

// Absolute path of the converter (ChaSen)
$pagereading_chasen_path = '/usr/local/bin/chasen';
//$pagereading_chasen_path = 'c:\progra~1\chasen21\chasen.exe';

// Absolute path of the converter (KAKASI)
$pagereading_kakasi_path = '/usr/local/bin/kakasi';
//$pagereading_kakasi_path = 'c:\kakasi\bin\kakasi.exe';

// Page name contains pronounce data (written by the converter)
$pagereading_config_page = ':config/PageReading';

// Page name of default pronouncing dictionary, used when converter = 'none'
$pagereading_config_dict = ':config/PageReading/dict';

/////////////////////////////////////////////////
// User definition
$auth_users = array(
	// Username => password
	'foo'	=> 'foo_passwd', // Cleartext
	'bar'	=> '{x-php-md5}f53ae779077e987718cc285b14dfbe86', // PHP md5() 'bar_passwd'
	'hoge'	=> '{SMD5}OzJo/boHwM4q5R+g7LCOx2xGMkFKRVEx',      // LDAP SMD5 'hoge_passwd'
);

/////////////////////////////////////////////////
// Authentication method

$auth_method_type	= 'pagename';	// By Page name
//$auth_method_type	= 'contents';	// By Page contents

/////////////////////////////////////////////////
// Read auth (0:Disable, 1:Enable)
$read_auth = 0;

$read_auth_pages = array(
	// Regex		   Username
	'#HogeHoge#'		=> 'hoge',
	'#(NETABARE|NetaBare)#'	=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// Edit auth (0:Disable, 1:Enable)
$edit_auth = 0;

$edit_auth_pages = array(
	// Regex		   Username
	'#BarDiary#'		=> 'bar',
	'#HogeHoge#'		=> 'hoge',
	'#(NETABARE|NetaBare)#'	=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// Search auth
// 0: Disabled (Search read-prohibited page contents)
// 1: Enabled  (Search only permitted pages for the user)
$search_auth = 0;

/////////////////////////////////////////////////
// $whatsnew: Max number of RecentChanges
$maxshow = 60;

// $whatsdeleted: Max number of RecentDeleted
// (0 = Disabled)
$maxshow_deleted = 60;

/////////////////////////////////////////////////
// Page names can't be edit via PukiWiki
$cantedit = array( $whatsnew, $whatsdeleted );

/////////////////////////////////////////////////
// HTTP: Output Last-Modified header
$lastmod = 0;

/////////////////////////////////////////////////
// Date format
$date_format = 'Y-m-d';

// Time format
$time_format = 'H:i:s';

/////////////////////////////////////////////////
// Max number of RSS feed
$rss_max = 15;

/////////////////////////////////////////////////
// Backup related settings

// Enable backup
$do_backup = 1;

// When a page had been removed, remove its backup too?
$del_backup = 0;

// Bacukp interval and generation
$cycle  =   3; // Wait N hours between backup (0 = no wait)
$maxage = 120; // Stock latest N backups

// NOTE: $cycle x $maxage / 24 = Minimum days to lost your data
//          3   x   120   / 24 = 15

// Splitter of backup data (NOTE: Too dangerous to change)
define('PKWK_SPLITTER', '>>>>>>>>>>');

/////////////////////////////////////////////////
// Command execution per update

define('PKWK_UPDATE_EXEC', '');

// Sample: Namazu (Search engine)
//$target     = '/var/www/wiki/';
//$mknmz      = '/usr/bin/mknmz';
//$output_dir = '/var/lib/namazu/index/';
//define('PKWK_UPDATE_EXEC',
//	$mknmz . ' --media-type=text/pukiwiki' .
//	' -O ' . $output_dir . ' -L ja -c -K ' . $target);

/////////////////////////////////////////////////
// HTTP proxy setting

// Use HTTP proxy server to get remote data
$use_proxy = 0;

$proxy_host = 'proxy.example.com';
$proxy_port = 8080;

// Do Basic authentication
$need_proxy_auth = 0;
$proxy_auth_user = 'username';
$proxy_auth_pass = 'password';

// Hosts that proxy server will not be needed
$no_proxy = array(
	'localhost',	// localhost
	'127.0.0.0/8',	// loopback
//	'10.0.0.0/8'	// private class A
//	'172.16.0.0/12'	// private class B
//	'192.168.0.0/16'	// private class C
//	'no-proxy.com',
);

////////////////////////////////////////////////
// Mail related settings

// Send mail per update of pages
$notify = 0;

// Send diff only
$notify_diff_only = 1;

// SMTP server (Windows only. Usually specified at php.ini)
$smtp_server = 'localhost';

// Mail recipient (To:) and sender (From:)
$notify_to   = 'to@example.com';	// To:
$notify_from = 'from@example.com';	// From:

// Subject: ($page = Page name wll be replaced)
$notify_subject = '[PukiWiki] $page';

// Mail header
// NOTE: Multiple items must be divided by "\r\n", not "\n".
$notify_header = '';

/////////////////////////////////////////////////
// Mail: POP / APOP Before SMTP

// Do POP/APOP authentication before send mail
$smtp_auth = 0;

$pop_server = 'localhost';
$pop_port   = 110;
$pop_userid = '';
$pop_passwd = '';

// Use APOP instead of POP (If server uses)
//   Default = Auto (Use APOP if possible)
//   1       = Always use APOP
//   0       = Always use POP
// $pop_auth_use_apop = 1;

/////////////////////////////////////////////////
// Ignore list

// Regex of ignore pages
$non_list = '^\:';

// Search ignored pages
$search_non_list = 1;

/////////////////////////////////////////////////
// Template setting

$auto_template_func = 1;
$auto_template_rules = array(
	'((.+)\/([^\/]+))' => '\2/template'
);

/////////////////////////////////////////////////
// Automatically add fixed heading anchor
$fixed_heading_anchor = 1;

/////////////////////////////////////////////////
// Remove the first spaces from Preformatted text
$preformat_ltrim = 1;

/////////////////////////////////////////////////
// Convert linebreaks into <br />
$line_break = 0;

/////////////////////////////////////////////////
// Use date-time rules (See rules.ini.php)
$usedatetime = 1;

/////////////////////////////////////////////////
// User-Agent settings
//
// If you want to ignore embedded browsers for rich-content-wikisite,
// remove (or comment-out) all 'keitai' settings.
//
// If you want to to ignore desktop-PC browsers for simple wikisite,
// copy keitai.ini.php to default.ini.php and customize it.

$agents = array(
// pattern: A regular-expression that matches device(browser)'s name and version
// profile: A group of browsers

    // Embedded browsers (Rich-clients for PukiWiki)

	// Windows CE (Microsoft(R) Internet Explorer 5.5 for Windows(R) CE)
	// Sample: "Mozilla/4.0 (compatible; MSIE 5.5; Windows CE; sigmarion3)" (sigmarion, Hand-held PC)
	array('pattern'=>'#\b(?:MSIE [5-9]).*\b(Windows CE)\b#', 'profile'=>'default'),

	// ACCESS "NetFront" / "Compact NetFront" and thier OEM, expects to be "Mozilla/4.0"
	// Sample: "Mozilla/4.0 (PS2; PlayStation BB Navigator 1.0) NetFront/3.0" (PlayStation BB Navigator, for SONY PlayStation 2)
	// Sample: "Mozilla/4.0 (PDA; PalmOS/sony/model crdb/Revision:1.1.19) NetFront/3.0" (SONY Clie series)
	// Sample: "Mozilla/4.0 (PDA; SL-A300/1.0,Embedix/Qtopia/1.1.0) NetFront/3.0" (SHARP Zaurus)
	array('pattern'=>'#^(?:Mozilla/4).*\b(NetFront)/([0-9\.]+)#',	'profile'=>'default'),

    // Embedded browsers (Non-rich)

	// Windows CE (the others)
	// Sample: "Mozilla/2.0 (compatible; MSIE 3.02; Windows CE; 240x320 )" (GFORT, NTT DoCoMo)
	array('pattern'=>'#\b(Windows CE)\b#', 'profile'=>'keitai'),

	// ACCESS "NetFront" / "Compact NetFront" and thier OEM
	// Sample: "Mozilla/3.0 (AveFront/2.6)" ("SUNTAC OnlineStation", USB-Modem for PlayStation 2)
	// Sample: "Mozilla/3.0(DDIPOCKET;JRC/AH-J3001V,AH-J3002V/1.0/0100/c50)CNF/2.0" (DDI Pocket: AirH" Phone by JRC)
	array('pattern'=>'#\b(NetFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(CNF)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AveFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AVE-Front)/([0-9\.]+)#',	'profile'=>'keitai'), // The same?

	// NTT-DoCoMo, i-mode (embeded Compact NetFront) and FOMA (embedded NetFront) phones
	// Sample: "DoCoMo/1.0/F501i", "DoCoMo/1.0/N504i/c10/TB/serXXXX" // c以降は可変
	// Sample: "DoCoMo/2.0 MST_v_SH2101V(c100;TB;W22H12;serXXXX;iccxxxx)" // ()の中は可変
	array('pattern'=>'#^(DoCoMo)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Vodafone's embedded browser
	// Sample: "J-PHONE/2.0/J-T03"	// 2.0は"ブラウザの"バージョン
	// Sample: "J-PHONE/4.0/J-SH51/SNxxxx SH/0001a Profile/MIDP-1.0 Configuration/CLDC-1.0 Ext-Profile/JSCL-1.1.0"
	array('pattern'=>'#^(J-PHONE)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Openwave(R) Mobile Browser (EZweb, WAP phone, etc)
	// Sample: "OPWV-SDK/62K UP.Browser/6.2.0.5.136 (GUI) MMP/2.0"
	array('pattern'=>'#\b(UP\.Browser)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Opera, dressing up as other embedded browsers
	// Sample: "Mozilla/3.0(DDIPOCKET;KYOCERA/AH-K3001V/1.4.1.67.000000/0.1/C100) Opera 7.0" (Like CNF at 'keitai'-mode)
	array('pattern'=>'#\b(?:DDIPOCKET|WILLCOM)\b.+\b(Opera) ([0-9\.]+)\b#',	'profile'=>'keitai'),

	// Planetweb http://www.planetweb.com/
	// Sample: "Mozilla/3.0 (Planetweb/v1.07 Build 141; SPS JP)" ("EGBROWSER", Web browser for PlayStation 2)
	array('pattern'=>'#\b(Planetweb)/v([0-9\.]+)#', 'profile'=>'keitai'),

	// DreamPassport, Web browser for SEGA DreamCast
	// Sample: "Mozilla/3.0 (DreamPassport/3.0)"
	array('pattern'=>'#\b(DreamPassport)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Palm "Web Pro" http://www.palmone.com/us/support/accessories/webpro/
	// Sample: "Mozilla/4.76 [en] (PalmOS; U; WebPro)"
	array('pattern'=>'#\b(WebPro)\b#',	'profile'=>'keitai'),

	// ilinx "Palmscape" / "Xiino" http://www.ilinx.co.jp/
	// Sample: "Xiino/2.1SJ [ja] (v. 4.1; 153x130; c16/d)"
	array('pattern'=>'#^(Palmscape)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#^(Xiino)/([0-9\.]+)#',	'profile'=>'keitai'),

	// SHARP PDA Browser (SHARP Zaurus)
	// Sample: "sharp pda browser/6.1[ja](MI-E1/1.0) "
	array('pattern'=>'#^(sharp [a-z]+ browser)/([0-9\.]+)#',	'profile'=>'keitai'),

	// WebTV
	array('pattern'=>'#^(WebTV)/([0-9\.]+)#',	'profile'=>'keitai'),

    // Desktop-PC browsers

	// Opera (for desktop PC, not embedded) -- See BugTrack/743 for detail
	// NOTE: Keep this pattern above MSIE and Mozilla
	// Sample: "Opera/7.0 (OS; U)" (not disguise)
	// Sample: "Mozilla/4.0 (compatible; MSIE 5.0; OS) Opera 6.0" (disguise)
	array('pattern'=>'#\b(Opera)[/ ]([0-9\.]+)\b#',	'profile'=>'default'),

	// MSIE: Microsoft Internet Explorer (or something disguised as MSIE)
	// Sample: "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)"
	array('pattern'=>'#\b(MSIE) ([0-9\.]+)\b#',	'profile'=>'default'),

	// Mozilla Firefox
	// NOTE: Keep this pattern above Mozilla
	// Sample: "Mozilla/5.0 (Windows; U; Windows NT 5.0; ja-JP; rv:1.7) Gecko/20040803 Firefox/0.9.3"
	array('pattern'=>'#\b(Firefox)/([0-9\.]+)\b#',	'profile'=>'default'),

    	// Loose default: Including something Mozilla
	array('pattern'=>'#^([a-zA-z0-9 ]+)/([0-9\.]+)\b#',	'profile'=>'default'),

	array('pattern'=>'#^#',	'profile'=>'default'),	// Sentinel
);
?>
