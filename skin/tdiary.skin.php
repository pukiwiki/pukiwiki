<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: tdiary.skin.php,v 1.11 2005/01/16 04:18:16 henoheno Exp $
//
// tDiary-wrapper skin

// Select theme
if (! defined('TDIARY_THEME')) define('TDIARY_THEME', 'loose-leaf'); // Default

// Show link(s) at your choice, with <div class="calendar"> design
// NOTE: Some theme become looking worse with this!
//   NULL = Show nothing
//   0    = Show topicpath
//   1    = Show reload URL
if (! defined('TDIARY_CALENDAR_DESIGN'))
	define('TDIARY_CALENDAR_DESIGN', NULL); // NULL, 0, 1

// Show toolbar at your choice, with <div class="footer"> design
// NOTE: Some theme become looking worse with this!
if (! defined('PKWK_SKIN_SHOW_TOOLBAR'))
	define('PKWK_SKIN_SHOW_TOOLBAR', 0); // 0, 1

// --------
// Prohibit direct access
if (! defined('UI_LANG')) die('UI_LANG is not set');
if (! isset($_LANG)) die('$_LANG is not set');

// Check theme
$theme = TDIARY_THEME;
if ($theme == '' || $theme == 'TDIARY_THEME') {
	die('Theme is not specified. Set "TDIARY_THEME" correctly');
} else {
	$theme = rawurlencode($theme); // Supress all nasty letters
	$theme_css = SKIN_DIR . 'theme/' . $theme . '/' . $theme . '.css';
	if (! file_exists($theme_css)) {
		echo 'tDiary theme wrapper: ';
		echo 'Theme not found: ' . htmlspecialchars($theme_css) . '<br/>';
		echo 'You can get tdiary-theme from: ';
		echo 'http://sourceforge.net/projects/tdiary/';
		exit;
	 }
}

if (defined('TDIARY_SIDEBAR_POSITION')) {
	$sidebar = TDIARY_SIDEBAR_POSITION;
} else {
	// Themes including sidebar CSS < (AllTheme / 2)
	// $ grep div.sidebar */*.css | cut -d: -f1 | cut -d/ -f1 | sort | uniq
	// $ wc -l *.txt
	//     75 list-sidebar.txt
	//    193 list-all.txt
	$sidebar = 'another'; // Default: Show as an another page below
	switch(TDIARY_THEME){
	case '3minutes':	/*FALLTHROUGH*/
	case '3pink':
	case 'aoikuruma':
	case 'arrow':
	case 'autumn':
	case 'babypink':
	case 'bill':
	case 'bistro_menu':
	case 'bluely':
	case 'book':
	case 'book2-feminine':
	case 'book3-sky':
	case 'bright-green':
	case 'bubble':
	case 'candy':
	case 'cat':
	case 'cherry':
	case 'citrus':
	case 'clover':
	case 'cool_ice':
	case 'cosmos':
	case 'darkness-pop':
	case 'diamond_dust':
	case 'dice':
	case 'emboss':
	case 'flower':
	case 'gear':
	case 'germany':
	case 'gray2':
	case 'happa':
	case 'hatena':
	case 'himawari':
	case 'kaeru':
	case 'kotatsu':
	case 'light-blue':
	case 'loose-leaf':
	case 'marguerite':
	case 'matcha':
	case 'mizu':
	case 'momonga':
	case 'mono':
	case 'moo':
	case 'nippon':
	case 'note':
	case 'old-pavement':
	case 'pain':
	case 'pale':
	case 'paper':
	case 'parabola':
	case 'pettan':
	case 'pink-border':
	case 'plum':
	case 'puppy':
	case 'purple_sun':
	case 'rainy-season':
	case 'rectangle':
	case 'repro':
	case 'russet':
	case 's-blue':
	case 'sagegreen':
	case 'savanna':
	case 'scarlet':
	case 'sepia':
	case 'simple':
	case 'smoking_black':
	case 'smoking_white':
	case 'spring':
	case 'sunset':
	case 'teacup':
	case 'thin':
	case 'tile':
	case 'tinybox':
	case 'tinybox_green':
	case 'wine':
	case 'yukon':
		$sidebar = 'bottom';	// This is the default position of tDiary's.
		break;
	}

	// Adjust sidebar's default position
	switch(TDIARY_THEME){

	// Assuming sidebar is above of the body
	case 'autumn':	/*FALLTHROUGH*/
	case 'cosmos':
	case 'dice':	// Sidebar text (white) seems unreadable
	case 'happa':
	case 'kaeru':
	case 'note':
	case 'paper':	// Sidebar text (white) seems unreadable
	case 'sunset':
	case 'tinybox':	// For MSIE with narrow window width, seems meanless
	case 'tinybox_green':	// The same
		$sidebar = 'top';
		break;

	// Strict separation between sidebar and main contents needed
	case '3minutes':	/*FALLTHROUGH*/
	case '3pink':
	case 'aoikuruma':
	case 'bill':
	case 'candy':
	case 'cat':
	case 'clover':
	case 'cool_ice':
	case 'flower':
	case 'germany':
	case 'himawari':
	case 'kotatsu':
	case 'light-blue':
	case 'loose-leaf':
	case 'marguerite':
	case 'matcha':
	case 'mizu':
	case 'mono':
	case 'moo':	// For MSIE, strict seems meanless
	case 'puppy':
	case 'rainy-season':
	case 's-blue':	// For MSIE, strict seems meanless
	case 'sagegreen':
	case 'savanna':
	case 'scarlet':
	case 'sepia':
	case 'simple':
	case 'spring':
	case 'teacup':
	case 'wine':
		$sidebar = 'strict';
		break;

	// They have sidevar-design, but can not show it at the 'side' of the contents
	case 'babypink':	/*FALLTHROUGH*/
	case 'bubble':
	case 'cherry':
	case 'darkness-pop':
	case 'diamond_dust':
	case 'gear':
	case 'pale':
	case 'pink-border':
	case 'rectangle':
	case 'russet':
	case 'smoking_black':
		$sidebar = 'another'; // Show as an another page below
		break;
	}
}
// Check menu (sidebar) is ready and $menubar is there
$menu = (arg_check('read') && is_page($GLOBALS['menubar']) &&
	exist_plugin_convert('menu'));
if ($menu) {
	$menu_body = preg_replace('#<h2 ([^>]*)>(.*?)</h2>#',
		'<h3 $1><span class="sanchor"></span> $2</h3>',
		do_plugin_convert('menu'));
}

// Adjust reverse-link default design manually
$disable_reverse_link = FALSE;
switch(TDIARY_THEME){
case 'hatena':	/*FALLTHROUGH*/
case 'repro':
case 'yukon':
	$disable_reverse_link = TRUE;
	break;
}

// Adjust DTD (between theme(=CSS) and MSIE bug)
// NOTE:
//    PukiWiki default: PKWK_DTD_XHTML_1_1
//    tDiary's default: PKWK_DTD_HTML_4_01_STRICT
switch(TDIARY_THEME){
case 'christmas':
	$pkwk_dtd = PKWK_DTD_HTML_4_01_STRICT; // or centering will be ignored via MSIE
	break;
}

$lang  = & $_LANG['skin'];
$link  = & $_LINK;

// Decide charset for CSS
$css_charset = 'iso-8859-1';
switch(UI_LANG){
	case 'ja': $css_charset = 'Shift_JIS'; break;
}

// Output HTTP headers
pkwk_common_headers();
header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=' . CONTENT_CHARSET);

// Output HTML DTD, <html>, and receive content-type
if (isset($pkwk_dtd)) {
	$meta_content_type = pkwk_output_dtd($pkwk_dtd);
} else {
	$meta_content_type = pkwk_output_dtd();
}

?>
<head>
 <?php echo $meta_content_type ?>
 <meta http-equiv="content-style-type" content="text/css" />
<?php if (! $is_read)  { ?> <meta name="robots" content="NOINDEX,NOFOLLOW" /><?php } ?>
<?php if (PKWK_ALLOW_JAVASCRIPT && isset($javascript)) { ?> <meta http-equiv="Content-Script-Type" content="text/javascript" /><?php } ?>

 <title><?php echo "$title - $page_title" ?></title>

 <link rel="stylesheet" href="skin/theme/base.css" type="text/css" media="all" />
 <link rel="stylesheet" href="skin/theme/<?php echo $theme ?>/<?php echo $theme ?>.css" type="text/css" media="all" />
 <link rel="stylesheet" href="skin/tdiary.css.php?charset=<?php echo $css_charset ?>" type="text/css" media="screen" charset="<?php echo $css_charset ?>" />
 <link rel="stylesheet" href="skin/tdiary.css.php?charset=<?php echo $css_charset ?>&amp;media=print" type="text/css" media="print" charset="<?php echo $css_charset ?>" />

 <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $link['rss'] ?>" /><?php // RSS auto-discovery ?>

<?php if (PKWK_ALLOW_JAVASCRIPT && $trackback_javascript) { ?> <script type="text/javascript" src="skin/trackback.js"></script><?php } ?>

<?php echo $head_tag ?>
</head>
<body><!-- Theme:<?php echo htmlspecialchars($theme) . ' Sidebar:' . $sidebar ?> -->

<?php if ($menu && $sidebar == 'strict') { ?>
<!-- Sidebar top -->
<div class="sidebar">
	<div id="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->

<div class="pkwk_body">
<div class="main">
<?php } // if ($menu && $sidebar == 'strict') ?>

<!-- Navigation buttuns -->
<div id="navigator"></div>
<div class="adminmenu">
<?php
function _navigator($key, $value = '', $javascript = ''){
	$lang = $GLOBALS['_LANG']['skin'];
	$link = $GLOBALS['_LINK'];
	if (! isset($lang[$key])) { echo 'LANG NOT FOUND'; return FALSE; }
	if (! isset($link[$key])) { echo 'LINK NOT FOUND'; return FALSE; }
	if (! PKWK_ALLOW_JAVASCRIPT) $javascript = '';

	echo '<span class="adminmenu"><a href="' . $link[$key] . '" ' . $javascript . '>' .
		(($value === '') ? $lang[$key] : $value) .
		'</a></span>';

	return TRUE;
}
?>
 <?php _navigator('top') ?> &nbsp;

<?php if ($is_page) { ?>
   <?php _navigator('edit')   ?>
 <?php if ($is_read && $function_freeze) { ?>
    <?php (! $is_freeze) ? _navigator('freeze') : _navigator('unfreeze') ?>
 <?php } ?>
   <?php _navigator('diff') ?>
 <?php if ($do_backup) { ?>
   <?php _navigator('backup') ?>
 <?php } ?>
 <?php if ((bool)ini_get('file_uploads')) { ?>
   <?php _navigator('upload') ?>
 <?php } ?>
   <?php _navigator('reload')    ?>
   &nbsp;
<?php } ?>

   <?php _navigator('new')  ?>
   <?php _navigator('list') ?>
 <?php if (arg_check('list')) { ?>
   <?php _navigator('filelist') ?>
 <?php } ?>
   <?php _navigator('search') ?>
   <?php _navigator('recent') ?>
   <?php _navigator('help')   ?>

<?php if ($trackback) { ?> &nbsp;
   <?php _navigator('trackback', $lang['trackback'] . '(' . tb_count($_page) . ')',
 	($trackback_javascript == 1) ? 'onClick="OpenTrackback(this.href); return false"' : '') ?>
<?php } ?>
<?php if ($referer)   { ?> &nbsp;
   <?php _navigator('refer') ?>
<?php } ?>
</div>

<h1><?php echo $page_title ?></h1>

<div class="calendar">
<?php if ($is_page && TDIARY_CALENDAR_DESIGN !== NULL) { ?>
	<?php if(TDIARY_CALENDAR_DESIGN) { ?>
		<a href="<?php echo $link['reload'] ?>"><span class="small"><?php echo $link['reload'] ?></span></a>
	<?php } else { ?>
		<?php require_once(PLUGIN_DIR . 'topicpath.inc.php'); echo plugin_topicpath_inline(); ?>
	<?php } ?>
<?php } ?>
</div>


<?php if ($menu && $sidebar == 'top') { ?>
<!-- Sidebar compat top -->
<div class="sidebar">
	<div id="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->
<?php } // if ($menu && $sidebar == 'top') ?>


<?php if ($menu && ($sidebar == 'top' || $sidebar == 'bottom')) { ?>
<div class="pkwk_body">
<div class="main">
<?php } ?>

<hr class="sep" />

<div class="day">

<h2><span class="date"></span> <span class="title"><?php
if ($disable_reverse_link === TRUE) {
	if ($_page != '') {
		echo htmlspecialchars($_page);
	} else {
		echo $page; // Search, or something message
	}
} else {
	if ($page != '') {
		echo $page;
	} else {
		echo htmlspecialchars($_page);
	}
}
?></span></h2>

<div class="body">
	<div class="section">
<?php
	// For read and preview: tDiary have no <h2> inside body
	$body = preg_replace('#<h2 ([^>]*)>(.*?)<a class="anchor_super" ([^>]*)>.*?</a></h2>#',
		'<h3 $1><a $3><span class="sanchor">_</span></a> $2</h3>', $body);
	$body = preg_replace('#<h([34]) ([^>]*)>(.*?)<a class="anchor_super" ([^>]*)>.*?</a></h\1>#',
		'<h$1 $2><a $4>_</a> $3</h$1>', $body);
	$body = preg_replace('#<h2 ([^>]*)>(.*?)</h2>#',
		'<h3 $1><span class="sanchor">_</span> $2</h3>', $body);
	if ($is_read) {
		// Read
		echo $body;
	} else {
		// Edit, preview, search, etc
		echo preg_replace('/(<form) (action="' . preg_quote($script, '/') .
			')/', '$1 class="update" $2', $body);
	}
?>
	</div>
</div><!-- class="body" -->


<?php if ($notes != '') { ?>
<div class="comment"><!-- Design for tDiary "Comments" -->
	<div class="caption">&nbsp;</div>
	<div class="commentbody"><br/>
		<?php
		$notes = preg_replace('#<span class="small">(.*?)</span>#', '<p>$1</p>', $notes);
		echo preg_replace('#<a (id="notefoot_[^>]*)>(.*?)</a>#',
			'<div class="commentator"><a $1><span class="canchor"></span> ' .
			'<span class="commentator">$2</span></a>' .
			'<span class="commenttime"></span></div>', $notes);
		?>
	</div>
</div>
<?php } ?>

<?php if ($attaches != '') { ?>
<div class="comment">
	<div class="caption">&nbsp;</div>
	<div class="commentshort">
		<?php echo $attaches ?>
	</div>
</div>
<?php } ?>

<?php if ($related != '') { ?>
<div class="comment">
	<div class="caption">&nbsp;</div>
	<div class="commentshort">
		Link: <?php echo $related ?>
	</div>
</div>
<?php } ?>

<!-- Design for tDiary "Today's referrer" -->
<div class="referer"><?php if ($lastmodified != '') echo 'Last-modified: ' . $lastmodified; ?></div>

</div><!-- class="day" -->

<hr class="sep" />


<?php if ($menu && $sidebar == 'another') { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->

<!-- Sidebar another -->
<div class="pkwk_body">
	<h1>&nbsp;</h1>
	<div class="calendar"></div>
	<hr class="sep" />
	<div class="day">
		<h2><span class="date"></span><span class="title">&nbsp;</span></h2>
		<div class="body">
			<div class="section">
				<?php echo $menu_body ?>
			</div>
		</div>
		<div class="referer"></div>
	</div>
	<hr class="sep" />
</div><!-- class="pkwk_body" -->

<div class="pkwk_body">
<div class="main">
<?php } // if ($menu && $sidebar == 'another') ?>


<?php if ($menu && ($sidebar == 'top' || $sidebar == 'bottom')) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->
<?php } ?>


<?php if ($menu && $sidebar == 'bottom') { ?>
<!-- Sidebar compat bottom -->
<div class="sidebar">
	<div id="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->
<?php } // if ($menu && $sidebar == 'bottom') ?>


<div class="footer">
<?php if (PKWK_SKIN_SHOW_TOOLBAR) { ?>
<!-- Toolbar -->
<?php

// Set toolbar-specific images
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

function _toolbar($key, $x = 20, $y = 20){
	$lang  = & $GLOBALS['_LANG']['skin'];
	$link  = & $GLOBALS['_LINK'];
	$image = & $GLOBALS['_IMAGE']['skin'];
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
 <br/>
<?php } // PKWK_SKIN_SHOW_TOOLBAR ?>

<!-- Copyright etc -->
 Site admin: <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a><p />
 <?php echo S_COPYRIGHT ?>.
 Powered by PHP <?php echo PHP_VERSION ?><br />
 HTML convert time: <?php echo $taketime ?> sec.

</div><!-- class="footer" -->

<?php if ($menu && ($sidebar != 'top' && $sidebar != 'bottom')) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->
<?php } ?>


</body>
</html>
