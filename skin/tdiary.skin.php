<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: tdiary.skin.php,v 1.36 2007/06/24 14:01:21 henoheno Exp $
// Copyright (C)
//   2002-2007 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// tDiary-wrapper skin (Updated for tdiary-theme 2.1.2)

// ------------------------------------------------------------
// Settings (define before here, if you want)

// Set site identities
$_IMAGE['skin']['favicon']  = ''; // Sample: 'image/favicon.ico';

// Select theme
if (! defined('TDIARY_THEME'))
	define('TDIARY_THEME', 'loose-leaf'); // Default

// Show link(s) at your choice, with <div class="calendar"> design
// NOTE: Some theme become looking worse with this!
//   NULL = Show nothing
//   0    = Show topicpath
//   1    = Show reload URL
if (! defined('TDIARY_CALENDAR_DESIGN'))
	define('TDIARY_CALENDAR_DESIGN', NULL); // NULL, 0, 1

// Show / Hide navigation bar UI at your choice
// NOTE: This is not stop their functionalities!
if (! defined('PKWK_SKIN_SHOW_NAVBAR'))
	define('PKWK_SKIN_SHOW_NAVBAR', 1); // 1, 0

// Show toolbar at your choice, with <div class="footer"> design
// NOTE: Some theme become looking worse with this!
if (! defined('PKWK_SKIN_SHOW_TOOLBAR'))
	define('PKWK_SKIN_SHOW_TOOLBAR', 0); // 0, 1

// TDIARY_SIDEBAR_POSITION: See below

// ------------------------------------------------------------
// Code start

// Prohibit direct access
if (! defined('UI_LANG')) die('UI_LANG is not set');
if (! isset($_LANG)) die('$_LANG is not set');
if (! defined('PKWK_READONLY')) die('PKWK_READONLY is not set');

// ------------------------------------------------------------
// Check tDiary theme

if (! defined('TDIARY_THEME') || TDIARY_THEME == '') {
	die('Theme is not specified. Set "TDIARY_THEME" correctly');
} else {
	$theme = rawurlencode(TDIARY_THEME); // Supress all nasty letters
	$theme_css = SKIN_DIR . 'theme/' . $theme . '/' . $theme . '.css';
	if (! file_exists($theme_css)) {
		echo 'tDiary theme wrapper: ';
		echo 'Theme not found: ' . htmlspecialchars($theme_css) . '<br />';
		echo 'You can get tdiary-theme from: ';
		echo 'http://sourceforge.net/projects/tdiary/';
		exit;
	 }
}

// ------------------------------------------------------------
// tDiary theme: Exception

// Adjust DTD (bug between these theme(=CSS) and MSIE)
// NOTE:
//    PukiWiki default: PKWK_DTD_XHTML_1_1
//    tDiary's default: PKWK_DTD_HTML_4_01_STRICT
switch(TDIARY_THEME){
case 'christmas':
	$pkwk_dtd = PKWK_DTD_HTML_4_01_STRICT; // or centering will be ignored via MSIE
	break;
}

// Adjust reverse-link default design manually
$disable_backlink = FALSE;
switch(TDIARY_THEME){
case 'hatena':		/* FALLTHROUGH */
case 'hatena-black':
case 'hatena-brown':
case 'hatena-darkgray':
case 'hatena-green':
case 'hatena-lightblue':
case 'hatena-lightgray':
case 'hatena-purple':
case 'hatena-red':
case 'hatena-white':
case 'hatena_cinnamon':
case 'hatena_japanese':
case 'hatena_leaf':
case 'hatena_water':
	$disable_backlink = TRUE; // or very viewable title color
	break;
}

// ------------------------------------------------------------
// tDiary theme: Select CSS color theme (Now testing:black only)

if (defined('TDIARY_COLOR_THEME')) {
	$css_theme = rawurlencode(TDIARY_COLOR_THEME);
} else {
	$css_theme = '';

	switch(TDIARY_THEME){
	case 'alfa':
	case 'bill':
	case 'black-lingerie':
	case 'blackboard':
	case 'bubble':
	case 'cosmos':
	case 'darkness-pop':
	case 'digital_gadgets':
	case 'fine':
	case 'fri':
	case 'giza':
	case 'hatena-black':
	case 'hatena_savanna-blue':
	case 'hatena_savanna-green':
	case 'hatena_savanna-red':
	case 'kaizou':
	case 'lightning':
	case 'lime':
	case 'line':
	case 'midnight':
	case 'moo':
	case 'nachtmusik':
	case 'nebula':
	case 'nippon':
	case 'noel':
	case 'petith-b':
	case 'quiet_black':
	case 'redgrid':
	case 'starlight':
	case 'tinybox_green':
	case 'white-lingerie':
	case 'white_flower':
	case 'whiteout':
	case 'wine':
	case 'wood':
	case 'xmastree':
	case 'yukon':
		$css_theme = 'black';

	// Another theme needed?
	case 'bluely':
	case 'brown':
	case 'deepblue':
	case 'scarlet':
	case 'smoking_black':
		;
	}
}

// ------------------------------------------------------------
// tDiary theme: Page title design (which is fancy, date and text?)

if (defined('TDIARY_TITLE_DESIGN_DATE') &&
    (TDIARY_TITLE_DESIGN_DATE  == 0 ||
     TDIARY_TITLE_DESIGN_DATE  == 1 ||
     TDIARY_TITLE_DESIGN_DATE  == 2)) {
	$title_design_date = TDIARY_TITLE_DESIGN_DATE;
} else {
	$title_design_date = 1; // Default: Select the date desin, or 'the same design'
	switch(TDIARY_THEME){
	case '3minutes':	/* FALLTHROUGH */
	case '90':
	case 'aoikuruma':
	case 'black-lingerie':
	case 'blog':
	case 'book':
	case 'book2-feminine':
	case 'book3-sky':
	case 'candy':
	case 'cards':
	case 'desert':
	case 'dot':
	case 'himawari':
	case 'kitchen-classic':
	case 'kitchen-french':
	case 'kitchen-natural':
	case 'light-blue':
	case 'lovely':
	case 'lovely_pink':
	case 'lr':
	case 'magic':
	case 'maroon':
	case 'midnight':
	case 'momonga':
	case 'nande-ya-nen':
	case 'narrow':
	case 'natrium':
	case 'nebula':
	case 'orange':
	case 'parabola':
	case 'plum':
	case 'pool_side':
	case 'rainy-season':
	case 'right':
	case 's-blue':
	case 's-pink':
	case 'sky':
	case 'sleepy_kitten':
	case 'snow_man':
	case 'spring':
	case 'tag':
	case 'tdiarynet':
	case 'treetop':
	case 'white-lingerie':
	case 'white_flower':
	case 'whiteout':
	case 'wood':
		$title_design_date = 0; // Select text design	
		break;

	case 'aqua':
	case 'arrow':
	case 'fluxbox':
	case 'fluxbox2':
	case 'fluxbox3':
	case 'ymck':
		$title_design_date = 2; // Show both :)
		break;
	}
}

// ------------------------------------------------------------
// tDiary 'Sidebar' position

// Default position
if (defined('TDIARY_SIDEBAR_POSITION')) {
	$sidebar = TDIARY_SIDEBAR_POSITION;
} else {
	$sidebar = 'another'; // Default: Show as an another page below

	// List of themes having sidebar CSS < (AllTheme / 2)
	// $ grep div.sidebar */*.css | cut -d: -f1 | cut -d/ -f1 | sort | uniq
	// $ wc -l *.txt
	//    142 list-sidebar.txt
	//    286 list-all.txt
	switch(TDIARY_THEME){
	case '3minutes':	/*FALLTHROUGH*/
	case '3pink':
	case 'aoikuruma':
	case 'aqua':
	case 'arrow':
	case 'artnouveau-blue':
	case 'artnouveau-green':
	case 'artnouveau-red':
	case 'asterisk-blue':
	case 'asterisk-lightgray':
	case 'asterisk-maroon':
	case 'asterisk-orange':
	case 'asterisk-pink':
	case 'autumn':
	case 'babypink':
	case 'be_r5':
	case 'bill':
	case 'bistro_menu':
	case 'bluely':
	case 'book':
	case 'book2-feminine':
	case 'book3-sky':
	case 'bright-green':
	case 'britannian':
	case 'bubble':
	case 'candy':
	case 'cat':
	case 'cherry':
	case 'cherry_blossom':
	case 'chiffon_leafgreen':
	case 'chiffon_pink':
	case 'chiffon_skyblue':
	case 'citrus':
	case 'clover':
	case 'colorlabel':
	case 'cool_ice':
	case 'cosmos':
	case 'curtain':
	case 'darkness-pop':
	case 'delta':
	case 'diamond_dust':
	case 'dice':
	case 'digital_gadgets':
	case 'dot-lime':
	case 'dot-orange':
	case 'dot-pink':
	case 'dot-sky':
	case 'dotted_line-blue':
	case 'dotted_line-green':
	case 'dotted_line-red':
	case 'emboss':
	case 'flower':
	case 'gear':
	case 'germany':
	case 'gray2':
	case 'green_leaves':
	case 'happa':
	case 'hatena':
	case 'hatena-black':
	case 'hatena-brown':
	case 'hatena-darkgray':
	case 'hatena-green':
	case 'hatena-lightblue':
	case 'hatena-lightgray':
	case 'hatena-lime':
	case 'hatena-orange':
	case 'hatena-pink':
	case 'hatena-purple':
	case 'hatena-red':
	case 'hatena-sepia':
	case 'hatena-tea':
	case 'hatena-white':
	case 'hatena_cinnamon':
	case 'hatena_japanese':
	case 'hatena_leaf':
	case 'hatena_rainyseason':
	case 'hatena_savanna-blue':
	case 'hatena_savanna-green':
	case 'hatena_savanna-red':
	case 'hatena_savanna-white':
	case 'hatena_water':
	case 'himawari':
	case 'jungler':
	case 'kaeru':
	case 'kitchen-classic':
	case 'kitchen-french':
	case 'kitchen-natural':
	case 'kotatsu':
	case 'light-blue':
	case 'loose-leaf':
	case 'marguerite':
	case 'matcha':
	case 'mizu':
	case 'momonga':
	case 'mono':
	case 'moo':
	case 'natrium':
	case 'nippon':
	case 'note':
	case 'old-pavement':
	case 'orange_flower':
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
	case 'rim-daidaiiro':
	case 'rim-fujiiro':
	case 'rim-mizuiro':
	case 'rim-sakurairo':
	case 'rim-tanpopoiro':
	case 'rim-wakabairo':
	case 'russet':
	case 's-blue':
	case 'sagegreen':
	case 'savanna':
	case 'scarlet':
	case 'sepia':
	case 'simple':
	case 'sleepy_kitten':
	case 'smoking_black':
	case 'smoking_white':
	case 'spring':
	case 'sunset':
	case 'tdiarynet':
	case 'teacup':
	case 'thin':
	case 'tile':
	case 'tinybox':
	case 'tinybox_green':
	case 'treetop':
	case 'white_flower':
	case 'wine':
	case 'yukon':
	case 'zef':
		$sidebar = 'bottom'; // This is the default position of tDiary's.
		break;
	}

	// Manually adjust sidebar's default position
	switch(TDIARY_THEME){

	// 'bottom'
	case '90': // But upper navigatin UI will be hidden by sidebar
	case 'blackboard':
	case 'quirky':
	case 'quirky2':
		$sidebar = 'bottom';
		break;

	// 'top': Assuming sidebar is above of the body
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
	case 'ymck':
		$sidebar = 'top';
		break;

	// 'strict': Strict separation between sidebar and main contents needed
	case '3minutes':	/*FALLTHROUGH*/
	case '3pink':
	case 'aoikuruma':
	case 'aqua':
	case 'artnouveau-blue':
	case 'artnouveau-green':
	case 'artnouveau-red':
	case 'asterisk-blue':
	case 'asterisk-lightgray':
	case 'asterisk-maroon':
	case 'asterisk-orange':
	case 'asterisk-pink':
	case 'bill':
	case 'candy':
	case 'cat':
	case 'chiffon_leafgreen':
	case 'chiffon_pink':
	case 'chiffon_skyblue':
	case 'city':
	case 'clover':
	case 'colorlabel':
	case 'cool_ice':
	case 'dot-lime':
	case 'dot-orange':
	case 'dot-pink':
	case 'dot-sky':
	case 'dotted_line-blue':
	case 'dotted_line-green':
	case 'dotted_line-red':
	case 'flower':
	case 'germany':
	case 'green-tea':
	case 'hatena':
	case 'hatena-black':
	case 'hatena-brown':
	case 'hatena-darkgray':
	case 'hatena-green':
	case 'hatena-lightblue':
	case 'hatena-lightgray':
	case 'hatena-lime':
	case 'hatena-orange':
	case 'hatena-pink':
	case 'hatena-purple':
	case 'hatena-red':
	case 'hatena-sepia':
	case 'hatena-tea':
	case 'hatena-white':
	case 'hiki':
	case 'himawari':
	case 'kasumi':
	case 'kitchen-classic':
	case 'kitchen-french':
	case 'kitchen-natural':
	case 'kotatsu':
	case 'kurenai':
	case 'light-blue':
	case 'loose-leaf':
	case 'marguerite':
	case 'matcha':
	case 'memo':
	case 'memo2':
	case 'memo3':
	case 'mirage':
	case 'mizu':
	case 'mono':
	case 'moo':	// For MSIE, strict seems meanless
	case 'navy':
	case 'pict':
	case 'pokke-blue':
	case 'pokke-orange':
	case 'query000':
	case 'query011':
	case 'query101':
	case 'query110':
	case 'query111or':
	case 'puppy':
	case 'rainy-season':
	case 's-blue':	// For MSIE, strict seems meanless
	case 'sagegreen':
	case 'savanna':
	case 'scarlet':
	case 'sepia':
	case 'simple':
	case 'smoking_gray':
	case 'spring':
	case 'teacup':
	case 'wine':
		$sidebar = 'strict';
		break;

	// 'another': They have sidebar-design, but can not show it
	//  at the 'side' of the contents
	case 'babypink':	/*FALLTHROUGH*/
	case 'bubble':
	case 'cherry':
	case 'darkness-pop':
	case 'diamond_dust':
	case 'gear':
	case 'necktie':
	case 'pale':
	case 'pink-border':
	case 'rectangle':
	case 'russet':
	case 'smoking_black':
	case 'zef':
		$sidebar = 'another'; // Show as an another page below
		break;
	}

	// 'none': Show no sidebar
}
// Check menu (sidebar) is ready and $menubar is there
if ($sidebar == 'none') {
	$menu = FALSE;
} else {
	$menu = (arg_check('read') && is_page($GLOBALS['menubar']) &&
		exist_plugin_convert('menu'));
	if ($menu) {
		$menu_body = preg_replace('#<h2 ([^>]*)>(.*?)</h2>#',
			'<h3 $1><span class="sanchor"></span> $2</h3>',
			do_plugin_convert('menu'));
	}
}

// ------------------------------------------------------------
// Code continuing ...

$lang  = & $_LANG['skin'];
$link  = & $_LINK;
$image = & $_IMAGE['skin'];
$rw    = ! PKWK_READONLY;

// Decide charset for CSS
$css_charset = 'iso-8859-1';
switch(UI_LANG){
	case 'ja': $css_charset = 'Shift_JIS'; break;
}

// ------------------------------------------------------------
// Output

// HTTP headers
pkwk_common_headers();
header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=' . CONTENT_CHARSET);

// HTML DTD, <html>, and receive content-type
if (isset($pkwk_dtd)) {
	$meta_content_type = pkwk_output_dtd($pkwk_dtd);
} else {
	$meta_content_type = pkwk_output_dtd();
}

?>
<head>
 <?php echo $meta_content_type ?>
 <meta http-equiv="content-style-type" content="text/css" />
<?php if ($nofollow || ! $is_read)  { ?> <meta name="robots" content="NOINDEX,NOFOLLOW" /><?php } ?>
<?php if (PKWK_ALLOW_JAVASCRIPT && isset($javascript)) { ?> <meta http-equiv="Content-Script-Type" content="text/javascript" /><?php } ?>

 <title><?php echo $title ?> - <?php echo $page_title ?></title>

 <link rel="SHORTCUT ICON" href="<?php echo $image['favicon'] ?>" />
 <link rel="stylesheet" type="text/css" media="all" href="<?php echo SKIN_DIR ?>theme/base.css" />
 <link rel="stylesheet" type="text/css" media="all" href="<?php echo SKIN_DIR ?>theme/<?php echo $theme ?>/<?php echo $theme ?>.css" />
 <link rel="stylesheet" type="text/css" media="screen" href="<?php echo SKIN_DIR ?>tdiary.css.php?charset=<?php echo $css_charset ?>&amp;color=<?php echo $css_theme ?>" charset="<?php echo $css_charset ?>" />
 <link rel="stylesheet" type="text/css" media="print"  href="<?php echo SKIN_DIR ?>tdiary.css.php?charset=<?php echo $css_charset ?>&amp;color=<?php echo $css_theme ?>&amp;media=print" charset="<?php echo $css_charset ?>" />
 <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $link['rss'] ?>" /><?php // RSS auto-discovery ?>

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
<?php if (PKWK_SKIN_SHOW_NAVBAR) { ?>
<div class="adminmenu"><div id="navigator">
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
  <?php if ($rw) { ?>
	<?php _navigator('edit') ?>
	<?php if ($is_read && $function_freeze) { ?>
		<?php (! $is_freeze) ? _navigator('freeze') : _navigator('unfreeze') ?>
	<?php } ?>
 <?php } ?>
   <?php _navigator('diff') ?>
 <?php if ($do_backup) { ?>
	<?php _navigator('backup') ?>
 <?php } ?>
 <?php if ($rw && (bool)ini_get('file_uploads')) { ?>
	<?php _navigator('upload') ?>
 <?php } ?>
   <?php _navigator('reload') ?>
   &nbsp;
<?php } ?>

 <?php if ($rw) { ?>
	<?php _navigator('new') ?>
 <?php } ?>
   <?php _navigator('list') ?>
 <?php if (arg_check('list')) { ?>
   <?php _navigator('filelist') ?>
 <?php } ?>
   <?php _navigator('search') ?>
   <?php _navigator('recent') ?>
   <?php _navigator('help')   ?>
</div></div>
<?php } else { ?>
<div id="navigator"></div>
<?php } // PKWK_SKIN_SHOW_NAVBAR ?>

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

<?php
// Page title (page name)
$title = '';
if ($disable_backlink) {
	if ($_page != '') {
		$title = htmlspecialchars($_page);
	} else {
		$title = $page; // Search, or something message
	}
} else {
	if ($page != '') {
		$title = $page;
	} else {
		$title =  htmlspecialchars($_page);
	}
}
$title_date = $title_text = '';
switch($title_design_date){
case 1: $title_date = & $title; break;
case 0: $title_text = & $title; break;
default:
	// Show both (for debug or someting)
	$title_date = & $title;
	$title_text = & $title;
	break;
}
?>
<h2><span class="date"><?php  echo $title_date ?></span>
    <span class="title"><?php echo $title_text ?></span></h2>

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
	<div class="commentbody"><br />
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
 <?php if ($rw) { ?>
	<?php _toolbar('edit') ?>
	<?php if ($is_read && $function_freeze) { ?>
		<?php if (! $is_freeze) { _toolbar('freeze'); } else { _toolbar('unfreeze'); } ?>
	<?php } ?>
 <?php } ?>
 <?php _toolbar('diff') ?>
<?php if ($do_backup) { ?>
	<?php _toolbar('backup') ?>
<?php } ?>
 <?php if ($rw && (bool)ini_get('file_uploads')) { ?>
	<?php _toolbar('upload') ?>
 <?php } ?>
 <?php if ($rw) { ?>
	<?php _toolbar('copy') ?>
	<?php _toolbar('rename') ?>
 <?php } ?>
 <?php _toolbar('reload') ?>
<?php } ?>
 &nbsp;
 <?php if ($rw) { ?>
	<?php _toolbar('new') ?>
 <?php } ?>
 <?php _toolbar('list')   ?>
 <?php _toolbar('search') ?>
 <?php _toolbar('recent') ?>
 &nbsp; <?php _toolbar('help') ?>
 &nbsp; <?php _toolbar('rss10', 36, 14) ?>
 <br />
<?php } // PKWK_SKIN_SHOW_TOOLBAR ?>

<!-- Copyright etc -->
 Site admin: <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a><p />
 <?php echo S_COPYRIGHT ?>.
 Powered by PHP <?php echo PHP_VERSION ?><br />
 HTML convert time: <?php echo elapsedtime() ?> sec.

</div><!-- class="footer" -->

<?php if ($menu && ($sidebar != 'top' && $sidebar != 'bottom')) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->
<?php } ?>


</body>
</html>
