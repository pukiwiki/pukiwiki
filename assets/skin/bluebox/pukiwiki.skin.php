<?php
/////////////////////////////////////////////////
// BlueBox for PukiWiki
// Designed by ari- <http://youjing.ws/>
// $Revision: 1.2 $
// $Date: 2004/11/21 12:48:16 $
//
# KsuWiki BEGIN
$GLOBALS['_LINK']['login']   = SITE_URL ."?cmd=site&amp;act=login";
$GLOBALS['_LINK']['logout']  = SITE_URL ."?cmd=site&amp;act=logout";
$flag = defined('SITE_ADMIN') ? SITE_ADMIN : false; 
if (! defined('PKWK_SKIN_SHOW_FOOTER'))
	define('PKWK_SKIN_SHOW_FOOTER', $flag); // 1, 0
$enable_login = !$flag;
$enable_logout = $flag;
# KsuWiki END

// HTTP headers
pkwk_common_headers();
header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=' . CONTENT_CHARSET);

// MenuBar
$menu = arg_check('read') && exist_plugin_convert('menu') ? do_plugin_convert('menu') : FALSE;
if (!$menu) {
	$menu = FALSE;
}
// RightBar
$rightbar = FALSE;
if (arg_check('read') && exist_plugin_convert('rightbar')) {
	$rightbar = do_plugin_convert('rightbar');
}

?>
<!DOCTYPE html>
<html lang="<?php echo LANG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CONTENT_CHARSET ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php if ($nofollow || ! $is_read)  { ?> <meta name="robots" content="NOINDEX,NOFOLLOW" /><?php } ?>
	<?php if ($html_meta_referrer_policy) { ?> <meta name="referrer" content="<?php echo htmlsc(html_meta_referrer_policy) ?>" /><?php } ?>
	<link rel="stylesheet" href="<?=PKWK_HOME.SKIN_DIR?>default.ja.css" type="text/css" media="screen,print" charset="Shift_JIS" />
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $link['rss'] ?>" /><?php // RSS auto-discovery ?>
	<title><?php echo "$title - $page_title" ?></title>
	<script type="text/javascript">
	function external_link(){
		var host_Name = location.host;
		var host_Check;
		var link_Href;

		for(var i=0; i < document.links.length; ++i)
		{
			link_Href = document.links[i].host;
			host_Check = link_Href.indexOf(host_Name,0);

			if(host_Check == -1){
				document.links[i].innerHTML = document.links[i].innerHTML 
				+ "<img src=\"<?=PKWK_HOME.SKIN_DIR?>external_link.gif\" height=\"11px\" width=\"11px\" alt=\"[紊・・・・・・潟・・\" class=\"external_link\">";
			}

		}
	}
	window.onload = external_link;
	</script>
	<script type="text/javascript" src="<?=PKWK_HOME?>assets/skin/main.js" defer></script>
	<script type="text/javascript" src="<?=PKWK_HOME?>assets/skin/search2.js" defer></script>
	<?php echo $head_tag ?>
</head>
<body>
<div id="base"><!-- ■BEGIN id:base -->
<!-- ◆ Header ◆ ========================================================== -->
<div id="header"><!-- ■BEGIN id:header -->
<div id="logo"><a href="<?php echo $link_top ?>"><?php echo $page_title ?></a></div>
</div><!-- □END id:header -->
<div id="navigator"><!-- ■BEGIN id:navigator -->
	<?php echo convert_html(get_source('SiteNavigator')) ?>
</div><!-- □END id:navigator -->
<!-- ◆ CenterBar ◆ ======================================================= -->
<div id="wrapper">
<div id="center_bar"><!-- ■BEGIN id:center_bar -->
<div id="content"><!-- ■BEGIN id:content -->
<div id="page_navigator"><!-- ■BEGIN id:page_navigator -->
	<?php echo convert_html(get_source('PageNavigator')) ?>
</div><!-- □END id:PageNavigator -->
<h1 class="title"><?php echo $page ?></h1>
<?php if ($lastmodified) { ?><!-- ■BEGIN id:lastmodified -->
<div id="lastmodified">
	<?php echo $lastmodified ?>
</div>
<?php } ?><!-- □END id:lastmodified -->
<div id="body"><!-- ■BEGIN id:body -->
<?php echo $body ?>
</div><!-- □END id:body -->
<div id="summary"><!-- ■BEGIN id:summary -->
<?php if ($notes) { ?><!-- ■BEGIN id:note -->
<div id="note">
<?php echo $notes ?>
</div>
<?php } ?><!-- □END id:note -->
<?php if ($related) { ?><!-- ■ BEGIN id:related -->
<div id="related">
 Link: <?php echo $related ?>
</div>
<?php } ?><!-- □ END id:related -->
<?php if ($attaches) { ?><!-- ■ BEGIN id:attach -->
<div id="attach">
<?php echo $hr ?>
<?php echo $attaches ?>
</div>
<?php } ?><!-- □ END id:attach -->
</div><!-- □ END id:summary -->
</div><!-- □END id:content -->
</div><!-- □ END id:center_bar -->
<!-- ◆RightBar◆ ========================================================== -->
<div id="right_bar"><!-- ■BEGIN id:right_bar -->
<div id="rightbar1" class="side_bar"><!-- ■BEGIN id:rightbar1 -->
<h2>検索</h2>
<form action="<?php echo $script ?>" method="post">
<div><input name="encode_hint" value="ぷ" type="hidden" /></div>
<div>
<input name="plugin" value="lookup" type="hidden" />
<input name="refer" value="<?php echo $title ?>" type="hidden" />
<input name="page" size="20" value="" type="text" accesskey="s" title="serch box"/>
<input value="Go!" type="submit" accesskey="g"/><br/>
<input name="inter" value="検索" type="radio" checked="checked" id="serch_site" /><label for="serch_site">サイト内</label>
<input name="inter" value="Google.jp" type="radio" accesskey="w" id="serch_web"/><label for="serch_web">Web</label>
</div>
</form></div><!-- END id:rightbar1 -->
<div id="rightbar2" class="side_bar"><!-- ■BEGIN id:rightbar2 -->
<h2>編集操作</h2>
<ul>
<?php if ($is_page) { ?>
	<li><a href="<?php echo $link_edit ?>"><img src="<?php echo IMAGE_DIR ?>edit.png" width="20" height="20" alt="編集" title="編集" />編集</a></li>
<?php   if ((bool)ini_get('file_uploads')) { ?>
	<li><a href="<?php echo $link_upload ?>"><img src="<?php echo IMAGE_DIR ?>file.png" width="20" height="20" alt="添付" title="添付" />添付</a></li>
<?php   } ?>
	<li><a href="<?php echo $link_diff ?>"><img src="<?php echo IMAGE_DIR ?>diff.png" width="20" height="20" alt="差分" title="差分" />差分</a></li>
<?php } ?>
<?php if ($do_backup) { ?>
	<li><a href="<?php echo $link_backup ?>"><img src="<?php echo IMAGE_DIR ?>backup.png" width="20" height="20" alt="バックアップ" title="バックアップ" />バックアップ</a></li>
<?php } ?>
</ul>
</div><!-- □END id:rightbar2 -->
<?php if ($rightbar) { ?><!-- ■BEGIN id:rightbar3 -->
<div id="rightbar3" class="side_bar">
	<?php echo $rightbar ?>
</div>
<?php } ?><!-- □END id:rightbar3 -->
</div><!-- □END id:right_bar -->
<!-- ◆ LeftBar ◆ ========================================================= -->
<div id="left_bar"><!-- ■BEGIN id:left_bar -->
<?php if ($menu !== FALSE) { ?>
<div id="menubar" class="side_bar"><!-- ■BEGIN id:menubar -->
	<?php echo $menu ?>
</div><!-- □END id:menubar -->
<?php } ?>
</div><!-- □END id:left_bar -->
<!-- ◆ Footer ◆ ========================================================== -->
<div id="footer"><!-- ■BEGIN id:footer -->
<div id="copyright"><!-- ■BEGIN id:copyright -->
	Modified by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a><br />
	<?php echo S_COPYRIGHT ?>

</div><!-- □END id:copyright -->
</div><!-- □END id:footer -->
<!-- ◆ END ◆ ============================================================= -->
</div><!-- END id:wrapper -->
</div><!-- □END id:base -->
</body>
</html>