<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: pukiwiki.skin.php,v 1.29 2004/12/11 08:53:24 henoheno Exp $
//

// Prohibit direct access
if (! defined('UI_LANG')) exit;

// Set skin-specific images
$_IMAGE['skin']['logo']     = 'pukiwiki.png';
$_IMAGE['skin']['reload']   = 'reload.png';
$_IMAGE['skin']['new']      = 'new.png';
$_IMAGE['skin']['edit']     = 'edit.png';
$_IMAGE['skin']['freeze']   = 'freeze.png';
$_IMAGE['skin']['unfreeze'] = 'unfreeze.png';
$_IMAGE['skin']['diff']     = 'diff.png';
$_IMAGE['skin']['upload']   = 'file.png';
$_IMAGE['skin']['copy']     = 'copy.png';
$_IMAGE['skin']['rename']   = 'rename.png';
$_IMAGE['skin']['top']      = 'top.png';
$_IMAGE['skin']['list']     = 'list.png';
$_IMAGE['skin']['search']   = 'search.png';
$_IMAGE['skin']['recent']   = 'recentchanges.png';
$_IMAGE['skin']['backup']   = 'backup.png';
$_IMAGE['skin']['help']     = 'help.png';
$_IMAGE['skin']['rss']      = 'rss.png';
$_IMAGE['skin']['rss10']    = & $_IMAGE['skin']['rss'];
$_IMAGE['skin']['rss20']    = 'rss20.png';
$_IMAGE['skin']['rdf']      = 'rdf.png';

$lang  = $_LANG['skin'];
$link  = $_LINK;
$image = $_IMAGE['skin'];

// Decide charset for CSS
$css_charset = 'iso-8859-1';
switch(UI_LANG){
	case 'ja': $css_charset = 'Shift_JIS'; break;
}

// Output header
pkwk_headers_sent();
header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=' . CONTENT_CHARSET);
if(ini_get('zlib.output_compression') && preg_match('/\bgzip\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'])) {
	header('Content-Encoding: gzip');
	header('Vary: Accept-Encoding');
}
echo '<?xml version="1.0" encoding="' . CONTENT_CHARSET . '"?>';

// Output body
if ($html_transitional) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANG ?>" lang="<?php echo LANG ?>">
<?php } else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANG ?>">
<?php } ?>
<head>
 <meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo CONTENT_CHARSET ?>" />
 <meta http-equiv="content-style-type" content="text/css" />
<?php if (! $is_read)  { ?> <meta name="robots" content="NOINDEX,NOFOLLOW" /><?php } ?>
<?php if (PKWK_ALLOW_JAVASCRIPT && isset($javascript)) { ?> <meta http-equiv="Content-Script-Type" content="text/javascript" /><?php } ?>

 <title><?php echo "$title - $page_title" ?></title>
 <link rel="stylesheet" href="skin/pukiwiki.css.php?charset=<?php echo $css_charset ?>" type="text/css" media="screen" charset="<?php echo $css_charset ?>" />
 <link rel="stylesheet" href="skin/pukiwiki.css.php?charset=<?php echo $css_charset ?>&amp;media=print" type="text/css" media="print" charset="<?php echo $css_charset ?>" />
  <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $link['rss'] ?>" /><?php // RSS auto-discovery ?>

<?php if (PKWK_ALLOW_JAVASCRIPT && $trackback_javascript) { ?> <script type="text/javascript" src="skin/trackback.js"></script><?php } ?>

<?php echo $head_tag ?>
</head>
<body>

<div id="header">
 <a href="<?php echo $link['top'] ?>"><img id="logo" src="<?php echo IMAGE_DIR . $image['logo'] ?>" width="80" height="80" alt="[PukiWiki]" title="[PukiWiki]" /></a>

 <h1 class="title"><?php echo $page ?></h1>

<?php if ($is_page) { ?>
 <?php if(TRUE) { ?>
   <a href="<?php echo $link['reload'] ?>"><span class="small"><?php echo $link['reload'] ?></span></a>
 <?php } else { ?>
   <span class="small">
   <?php require_once(PLUGIN_DIR . 'topicpath.inc.php'); echo plugin_topicpath_inline(); ?>
   </span>
 <?php } ?>
<?php } ?>

</div>

<div id="navigator">
<?php
function _navigator($key, $value = '', $javascript = ''){
	$lang = $GLOBALS['_LANG']['skin'];
	$link = $GLOBALS['_LINK'];
	if (! isset($lang[$key])) { echo 'LANG NOT FOUND'; return FALSE; }
	if (! isset($link[$key])) { echo 'LINK NOT FOUND'; return FALSE; }
	if (! PKWK_ALLOW_JAVASCRIPT) $javascript = '';

	echo '<a href="' . $link[$key] . '" ' . $javascript . '>' .
		(($value === '') ? $lang[$key] : $value) .
		'</a>';

	return TRUE;
}
?>
 [ <?php _navigator('top') ?> ] &nbsp;

<?php if ($is_page) { ?>
 [ <?php _navigator('edit')   ?>
 <?php if ($is_read && $function_freeze) { ?>
 |  <?php (! $is_freeze) ? _navigator('freeze') : _navigator('unfreeze') ?>
 <?php } ?>
 | <?php _navigator('diff') ?>
 <?php if ($do_backup) { ?>
 | <?php _navigator('backup') ?>
 <?php } ?>
 <?php if ((bool)ini_get('file_uploads')) { ?>
 | <?php _navigator('upload') ?>
 <?php } ?>
 | <?php _navigator('reload')    ?>
 ] &nbsp;
<?php } ?>

 [ <?php _navigator('new')  ?>
 | <?php _navigator('list') ?>
 <?php if (arg_check('list')) { ?>
 | <?php _navigator('filelist') ?>
 <?php } ?>
 | <?php _navigator('search') ?>
 | <?php _navigator('recent') ?>
 | <?php _navigator('help')   ?>
 ]

<?php if ($trackback) { ?> &nbsp;
 [ <?php _navigator('trackback', $lang['trackback'] . '(' . tb_count($_page) . ')',
 	($trackback_javascript == 1) ? 'onClick="OpenTrackback(this.href); return false"' : '') ?> ]
<?php } ?>
<?php if ($referer)   { ?> &nbsp;
 [ <?php _navigator('refer') ?> ]
<?php } ?>

</div>

<?php echo $hr ?>

<?php if (arg_check('read') && exist_plugin_convert('menu')) { ?>
<table border="0" style="width:100%">
 <tr>
  <td class="menubar">
   <div id="menubar"><?php echo do_plugin_convert('menu') ?></div>
  </td>
  <td valign="top">
   <div id="body"><?php echo $body ?></div>
  </td>
 </tr>
</table>
<?php } else { ?>
<div id="body"><?php echo $body ?></div>
<?php } ?>

<?php if ($notes) { ?>
<div id="note"><?php echo $notes ?></div>
<?php } ?>

<?php if ($attaches) { ?>
<div id="attach">
<?php echo $hr ?>
<?php echo $attaches ?>
</div>
<?php } ?>

<?php echo $hr ?>

<div id="toolbar">
<?php
function _toolbar($key, $x = 20, $y = 20){
	$lang  = $GLOBALS['_LANG']['skin'];
	$link  = $GLOBALS['_LINK'];
	$image = $GLOBALS['_IMAGE']['skin'];
	if (! isset($lang[$key]) ) { echo 'LANG NOT FOUND';  return FALSE; }
	if (! isset($link[$key]) ) { echo 'LINK NOT FOUND';  return FALSE; }
	if (! isset($image[$key])) { echo 'IMAGE NOT FOUND'; return FALSE; }

	echo '<a href="' . $link[$key] . '">' .
		'<img src="' . IMAGE_DIR . $image[$key] . '" width="' . $x . '" height="' . $y . '" ' .
			'alt="' . $lang[$key] . '" title="' . $lang[$key] . '" />' .
		'</a>';
	return TRUE;
}
?>
 <?php _toolbar('top') ?>

<?php if ($is_page) { ?>
 &nbsp;
 <?php _toolbar('edit') ?>
 <?php if ($is_read && $function_freeze) { ?>
  <?php if (! $is_freeze) { _toolbar('freeze'); } else { _toolbar('unfreeze'); } ?>
 <?php } ?>
 <?php _toolbar('diff') ?>
<?php if ($do_backup) { ?>
  <?php _toolbar('backup') ?>
<?php } ?>
 <?php if ((bool)ini_get('file_uploads')) { ?>
  <?php _toolbar('upload') ?>
 <?php } ?>
 <?php _toolbar('copy') ?>
 <?php _toolbar('rename') ?>
 <?php _toolbar('reload') ?>
<?php } ?>
 &nbsp;
 <?php _toolbar('new')    ?>
 <?php _toolbar('list')   ?>
 <?php _toolbar('search') ?>
 <?php _toolbar('recent') ?>
 &nbsp; <?php _toolbar('help') ?>
 &nbsp; <?php _toolbar('rss10', 36, 14) ?>
</div>

<?php if ($lastmodified) { ?>
<div id="lastmodified">Last-modified: <?php echo $lastmodified ?></div>
<?php } ?>

<?php if ($related) { ?>
<div id="related">Link: <?php echo $related ?></div>
<?php } ?>

<div id="footer">
 Modified by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a>
 <br /><br />
 <?php echo S_COPYRIGHT ?>
 <br />
 Powered by PHP <?php echo PHP_VERSION ?>
 <br /><br />
 HTML convert time to <?php echo $taketime ?> sec.
</div>

</body>
</html>
