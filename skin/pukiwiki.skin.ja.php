<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: pukiwiki.skin.ja.php,v 1.37 2004/09/30 12:24:47 henoheno Exp $
//
if (!defined('DATA_DIR')) { exit; }

require_once(SKIN_DIR . 'skin.' . SKIN_LANG . '.lng');
$lang = $_LANG['skin'];

$css_charset = 'iso-8859-1';
switch(SKIN_LANG){
	case 'ja': $css_charset = 'Shift_JIS'; break;
}

header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=' . CONTENT_CHARSET);
echo '<?xml version="1.0" encoding="' . CONTENT_CHARSET . '"?>';
?>

<?php if ($html_transitional) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANG ?>" lang="<?php echo LANG ?>">
<?php } else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANG ?>">
<?php } ?>
<head>
 <meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo CONTENT_CHARSET ?>" />
 <meta http-equiv="content-style-type" content="text/css" />

<?php if (!$is_read) { ?>
 <meta name="robots" content="NOINDEX,NOFOLLOW" />
<?php } ?>

 <title><?php echo "$title - $page_title" ?></title>
 <link rel="stylesheet" href="skin/pukiwiki.css.php?charset=<?php echo $css_charset ?>" type="text/css" media="screen" charset="<?php echo $css_charset ?>" />
 <link rel="stylesheet" href="skin/pukiwiki.css.php?charset=<?php echo $css_charset ?>&media=print" type="text/css" media="print" charset="<?php echo $css_charset ?>" />
<?php
  global $trackback, $referer;
  if ($trackback) {
?>
 <meta http-equiv="Content-Script-Type" content="text/javascript" />
 <script type="text/javascript" src="skin/trackback.js"></script>
<?php } ?>
<?php echo $head_tag ?>
</head>
<body>

<div id="header">
 <a href="<?php echo $modifierlink ?>"><img id="logo" src="<?php echo IMAGE_DIR ?>pukiwiki.png" width="80" height="80" alt="[PukiWiki]" title="[PukiWiki]" /></a>
 <h1 class="title"><?php echo $page ?></h1>

<?php if ($is_page) { ?>
 <a href="<?php echo "$script?$r_page" ?>"><span class="small"><?php echo "$script?$r_page" ?></span></a>
<?php } ?>

</div>


<div id="navigator">

<?php if ($is_page) { ?>
 [ <a href="<?php echo "$script?$r_page" ?>"><?php echo $lang['reload'] ?></a> ]
 &nbsp;
 [ <a href="<?php echo "$script?plugin=newpage&amp;refer=$r_page" ?>"><?php echo $lang['new'] ?></a>
 | <a href="<?php echo $link_edit ?>"><?php echo $lang['edit'] ?></a>
<?php   if ($is_read and $function_freeze) { ?>
<?php     if ($is_freeze) { ?>
 | <a href="<?php echo $link_unfreeze ?>"><?php echo $lang['unfreeze'] ?></a>
<?php     } else { ?>
 | <a href="<?php echo $link_freeze ?>"><?php echo $lang['freeze'] ?></a>
<?php     } ?>
<?php   } ?>

 | <a href="<?php echo $link_diff ?>"><?php echo $lang['diff'] ?></a>

<?php   if ((bool)ini_get('file_uploads')) { ?>
 | <a href="<?php echo $link_upload ?>"><?php echo $lang['upload'] ?></a>
<?php   } ?>

 ]
 &nbsp;
<?php } ?>

 [ <a href="<?php echo $link_top ?>"><?php echo $lang['top'] ?></a>
 | <a href="<?php echo $link_list ?>"><?php echo $lang['list'] ?></a>

<?php if (arg_check('list')) { ?>
 | <a href="<?php echo $link_filelist ?>"><?php echo $lang['filelist'] ?></a>
<?php } ?>

 | <a href="<?php echo $link_search ?>"><?php echo $lang['search'] ?></a>
 | <a href="<?php echo $link_whatsnew ?>"><?php echo $lang['recent'] ?></a>

<?php if ($do_backup) { ?>
 | <a href="<?php echo $link_backup ?>"><?php echo $lang['backup'] ?></a>
<?php } ?>

 | <a href="<?php echo $link_help ?>"><?php echo $lang['help'] ?></a>
 ]
<?php
  if ($trackback) {
    $tb_id = tb_get_id($_page);
?>
 &nbsp;
 [ <a href="<?php echo "$script?plugin=tb&amp;__mode=view&amp;tb_id=$tb_id" ?>"><?php echo $lang['trackback'] ?>(<?php echo tb_count($_page) ?>)</a> ]
<?php } ?>

<?php
  if ($referer) {
?>
 [ <a href="<?php echo "$script?plugin=referer&amp;page=$r_page" ?>"><?php echo $lang['refer'] ?></a> ]
<?php } ?>

</div>
<?php echo $hr ?>


<?php if (arg_check('read') and exist_plugin_convert('menu')) { ?>
<table border="0" style="width:100%">
 <tr>
  <td class="menubar">
   <div id="menubar">
    <?php echo do_plugin_convert('menu') ?>
   </div>
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
<div id="note">
<?php echo $notes ?>
</div>
<?php } ?>


<?php if ($attaches) { ?>
<div id="attach">
<?php echo $hr ?>
<?php echo $attaches ?>
</div>
<?php } ?>


<?php echo $hr ?>
<div id="toolbar">

<?php if ($is_page) { ?>
 <a href="<?php echo "$script?$r_page" ?>"><img src="<?php echo IMAGE_DIR ?>reload.png" width="20" height="20" alt="<?php echo $lang['reload'] ?>" title="<?php echo $lang['reload'] ?>" /></a>
 &nbsp;
 <a href="<?php echo $script ?>?plugin=newpage"><img src="<?php echo IMAGE_DIR ?>new.png" width="20" height="20" alt="<?php echo $lang['new'] ?>" title="<?php echo $lang['new'] ?>" /></a>
 <a href="<?php echo $link_edit ?>"><img src="<?php echo IMAGE_DIR ?>edit.png" width="20" height="20" alt="<?php echo $lang['edit'] ?>" title="<?php echo $lang['edit'] ?>" /></a>
<?php   if ($is_read and $function_freeze) { ?>
<?php     if ($is_freeze) { ?>
 <a href="<?php echo $link_unfreeze ?>"><img src="<?php echo IMAGE_DIR ?>unfreeze.png" width="20" height="20" alt="<?php echo $lang['unfreeze'] ?>" title="<?php echo $lang['unfreeze'] ?>" /></a>
<?php     } else { ?>
 <a href="<?php echo $link_freeze ?>"><img src="<?php echo IMAGE_DIR ?>freeze.png" width="20" height="20" alt="<?php echo $lang['freeze'] ?>" title="<?php echo $lang['freeze'] ?>" /></a>
<?php     } ?>
<?php   } ?>
 <a href="<?php echo $link_diff ?>"><img src="<?php echo IMAGE_DIR ?>diff.png" width="20" height="20" alt="<?php echo $lang['diff'] ?>" title="<?php echo $lang['diff'] ?>" /></a>
<?php   if ((bool)ini_get('file_uploads')) { ?>
 <a href="<?php echo $link_upload ?>"><img src="<?php echo IMAGE_DIR ?>file.png" width="20" height="20" alt="<?php echo $lang['upload'] ?>" title="<?php echo $lang['upload'] ?>" /></a>
<?php   } ?>
 <a href="<?php echo $link_template ?>"><img src="<?php echo IMAGE_DIR ?>copy.png" width="20" height="20" alt="<?php echo $lang['copy'] ?>" title="<?php echo $lang['copy'] ?>" /></a>
 <a href="<?php echo $link_rename ?>"><img src="<?php echo IMAGE_DIR ?>rename.png" width="20" height="20" alt="<?php echo $lang['rename'] ?>" title="<?php echo $lang['rename'] ?>" /></a>
 &nbsp;
<?php } ?>

 <a href="<?php echo $link_top ?>"><img src="<?php echo IMAGE_DIR ?>top.png" width="20" height="20" alt="<?php echo $lang['top'] ?>" title="<?php echo $lang['top'] ?>" /></a>
 <a href="<?php echo $link_list ?>"><img src="<?php echo IMAGE_DIR ?>list.png" width="20" height="20" alt="<?php echo $lang['list'] ?>" title="<?php echo $lang['list'] ?>" /></a>
 <a href="<?php echo $link_search ?>"><img src="<?php echo IMAGE_DIR ?>search.png" width="20" height="20" alt="<?php echo $lang['search'] ?>" title="<?php echo $lang['search'] ?>" /></a>
 <a href="<?php echo $link_whatsnew ?>"><img src="<?php echo IMAGE_DIR ?>recentchanges.png" width="20" height="20" alt="<?php echo $lang['recent'] ?>" title="<?php echo $lang['recent'] ?>" /></a>

<?php if ($do_backup) { ?>
 <a href="<?php echo $link_backup ?>"><img src="<?php echo IMAGE_DIR ?>backup.png" width="20" height="20" alt="<?php echo $lang['backup'] ?>" title="<?php echo $lang['backup'] ?>" /></a>
<?php } ?>

 &nbsp;
 <a href="<?php echo $link_help ?>"><img src="<?php echo IMAGE_DIR ?>help.png" width="20" height="20" alt="<?php echo $lang['help'] ?>" title="<?php echo $lang['help'] ?>" /></a>
 &nbsp;
 <a href="<?php echo $link_rss ?>"><img src="<?php echo IMAGE_DIR ?>rss.png" width="36" height="14" alt="<?php echo $lang['rss'] ?>" title="<?php echo $lang['rss'] ?>" /></a>
</div>


<?php if ($lastmodified) { ?>
<div id="lastmodified">
 Last-modified: <?php echo $lastmodified ?>
</div>
<?php } ?>


<?php if ($related) { ?>
<div id="related">
 Link: <?php echo $related ?>
</div>
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
