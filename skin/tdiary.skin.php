<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: tdiary.skin.php,v 1.1 2004/12/24 14:41:12 henoheno Exp $
//
// tDiary-wrapper skin

// Select theme
//define('TDIARY_THEME', '3minutes');
//define('TDIARY_THEME', 'aoikuruma');
//define('TDIARY_THEME', 'bill');
//define('TDIARY_THEME', 'black-lingerie');
//define('TDIARY_THEME', 'black_mamba');
//define('TDIARY_THEME', 'blog');
//define('TDIARY_THEME', 'bubble');
//define('TDIARY_THEME', 'cards');
//define('TDIARY_THEME', 'cat');
//define('TDIARY_THEME', 'christmas');
//define('TDIARY_THEME', 'clover');
//define('TDIARY_THEME', 'dot');
//define('TDIARY_THEME', 'gear');
//define('TDIARY_THEME', 'gingham-gray');
//define('TDIARY_THEME', 'green-border');	// With frogs
//define('TDIARY_THEME', 'himawari');
//define('TDIARY_THEME', 'hatena');
//define('TDIARY_THEME', 'kaeru');
//define('TDIARY_THEME', 'loose-leaf');
//define('TDIARY_THEME', 'petith');
//define('TDIARY_THEME', 'piyo-family');
//define('TDIARY_THEME', 'plum');
//define('TDIARY_THEME', 'puppy');
//define('TDIARY_THEME', 'snowy');
if (! defined('TDIARY_THEME')) define('TDIARY_THEME', 'loose-leaf'); // Default

// SKIN_DEFAULT_DISABLE_REVERSE_LINK
if(! defined('SKIN_DEFAULT_DISABLE_REVERSE_LINK'))
	define('SKIN_DEFAULT_DISABLE_REVERSE_LINK', 1);


// SKIN_DEFAULT_DISABLE_TOPICPATH
//   1    = Show reload URL
//   0    = Show topicpath
//   NULL = Show nothing
if (! defined('SKIN_DEFAULT_DISABLE_TOPICPATH'))
	define('SKIN_DEFAULT_DISABLE_TOPICPATH', NULL);

// --------
// Prohibit direct access
if (! defined('UI_LANG')) die('UI_LANG is not set');
if (! isset($_LANG)) die('$_LANG is not set');

// Check theme
$theme = rawurlencode(TDIARY_THEME);
if ($theme == '') {
	echo '/* Theme is not specified. Set "TDIARY_THEME" */';
	exit;
} else {
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
	$sidebar = FALSE; // Disabled
	switch($theme){
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
		$sidebar = TRUE; // Compatible
		break;
	}

	// Adjust sidebar's default design manually
	switch($theme){
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
	case 'puppy':
	case 'rainy-season':
	case 's-blue':
	case 'sagegreen':
	case 'savanna':
	case 'scarlet':
	case 'sepia':
	case 'simple':
	case 'spring':
	case 'teacup':
		$sidebar = 'top'; // Strict separation between sidebar and main
		break;

	case 'babypink':	/*FALLTHROUGH*/
	case 'bubble':
	case 'blog':
	case 'gear':
	case 'purple_sun':
	case 'rectangle':
	case 'russet':
	case 'smoking_black':
		$sidebar = FALSE; // Show as an another page below
		break;

	case 'be_r5':
		$sidebar = TRUE; // Not included officially but works
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
switch($theme){
case 'hatena':	/*FALLTHROUGH*/
case 'repro':
case 'yukon':
	$disable_reverse_link = TRUE;
	break;
}

$lang  = $_LANG['skin'];
$link  = $_LINK;

// Decide charset for CSS
$css_charset = 'iso-8859-1';
switch(UI_LANG){
	case 'ja': $css_charset = 'Shift_JIS'; break;
}

// Output header
pkwk_common_headers();
header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=' . CONTENT_CHARSET);

// Output body
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="<?php echo LANG ?>">
<head>
 <meta http-equiv="content-type" content="text/html; charset=<?php echo CONTENT_CHARSET ?>" />
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
<body><!-- Theme: <?php echo htmlspecialchars($theme) . ' Sidebar:' . $sidebar ?> -->

<?php if ($menu && $sidebar === 'top') { ?>
<!-- Sidebar top -->
<div class="sidebar">
	<div id="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->

<div class="pkwk_body">
<div class="main">
<?php } // if ($menu && $sidebar === 'top') ?>

<!-- Navigation buttuns -->
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
<?php if ($is_page && SKIN_DEFAULT_DISABLE_TOPICPATH !== NULL) { ?>
	<?php if(SKIN_DEFAULT_DISABLE_TOPICPATH) { ?>
		<a href="<?php echo $link['reload'] ?>"><span class="small"><?php echo $link['reload'] ?></span></a>
	<?php } else { ?>
		<?php require_once(PLUGIN_DIR . 'topicpath.inc.php'); echo plugin_topicpath_inline(); ?>
	<?php } ?>
<?php } ?>
</div>

<?php if ($menu && $sidebar === TRUE) { ?>
<!-- Sidebar compat -->
<div class="sidebar">
	<div id="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->

<div class="pkwk_body">
<div class="main">
<?php } // if ($menu && $sidebar === TRUE) ?>

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
	$body = preg_replace('#<h2 ([^>]*)>(.*?)</h2>#',
		'<h3 $1><span class="sanchor">_</span> $2</h3>', $body);
	if ($is_read) {
		// Read
		echo $body;
	} else {
		// Edit and preview
		echo preg_replace('/(<form) (action="' . preg_quote($script, '/') .
			'" method="post">)/', '$1 class="update" $2', $body);
	}
?>
	</div>
</div><!-- class="body" -->

<?php if ($notes) { ?>
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

<?php if ($attaches) { ?>
<div class="comment">
	<div class="caption">&nbsp;</div>
	<div class="commentshort">
		<?php echo $attaches ?>
	</div>
</div>
<?php } ?>

<?php if ($related) { ?>
<div class="comment">
	<div class="caption">&nbsp;</div>
	<div class="commentshort">
		Link: <?php echo $related ?>
	</div>
</div>
<?php } ?>

<!-- Design for tDiary "Today's referrer" -->
<div class="referer"><?php if ($lastmodified) echo 'Last-modified: ' . $lastmodified; ?></div>

</div><!-- class="day" -->

<hr class="sep" />

<?php if ($menu && $sidebar === FALSE) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->

<!-- Sidebar bottom -->
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
</div><!-- class="sidebar" -->

<div class="pkwk_body">
<div class="main">
<?php } // if ($menu && $sidebar === FALSE) ?>

<?php if ($menu && $sidebar === TRUE) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->
<?php } ?>

<!-- Copyright etc -->
<div class="footer">
 Modified by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a><br />
 <?php echo S_COPYRIGHT ?><br />
 Powered by PHP <?php echo PHP_VERSION ?><br />
 HTML convert time to <?php echo $taketime ?> sec.
</div>

<?php if ($menu && $sidebar !== TRUE) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->
<?php } ?>

</body>
</html>
