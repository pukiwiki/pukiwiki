<?php if (!defined('DATA_DIR')) { exit; } ?>
<?php header('Content-Type: text/html; charset=iso-8859-1') ?>
<?php echo '<?xml version="1.0" encoding="iso-8859-1"?>' ?>

<?php if ($html_transitional) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php } else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php } ?>
<head>
 <meta http-equiv="content-type" content="application/xhtml+xml; charset=iso-8859-1" />
 <meta http-equiv="content-style-type" content="text/css" />

<?php if (!$is_read) { ?>
 <meta name="robots" content="NOINDEX,NOFOLLOW" />
<?php } ?>

 <title><?php echo "$title - $page_title" ?></title>
 <link rel="stylesheet" href="skin/default.en.css" type="text/css" media="screen" charset="iso-8859-1" />
<?php echo $head_tag ?>
</head>
<body>

<div id="header">
 <a href="<?php echo $modifierlink ?>"><img id="logo" src="pukiwiki.png" width="80" height="80" alt="[PukiWiki]" /></a>
 <h1 class="title"><?php echo $page ?></h1>

<?php if ($is_page) { ?>
 <a href="<?php echo "$script?$r_page" ?>"><span class="small"><?php echo "$script?$r_page" ?></span></a>
<?php } ?>

</div>


<div id="navigator">

<?php if ($is_page) { ?>
 [ <a href="<?php echo "$script?$r_page" ?>">Reload</a> ]
 &nbsp;
 [ <a href="<?php echo "$script?plugin=newpage" ?>">New</a>
 | <a href="<?php echo $link_edit ?>">Edit</a>

<?php   if ($is_read and $function_freeze) { ?>
<?php     if ($is_freeze) { ?>
 | <a href="<?php echo $link_unfreeze ?>">Unfreeze</a>
<?php     } else { ?>
 | <a href="<?php echo $link_freeze ?>">Freeze</a>
<?php     } ?>
<?php   } ?>

 | <a href="<?php echo $link_diff ?>">Diff</a>

<?php   if ((bool)ini_get('file_uploads')) { ?>
 | <a href="<?php echo $link_upload ?>">Upload</a>
<?php   } ?>

 ]
 &nbsp;
<?php } ?>

 [ <a href="<?php echo $link_top ?>">Front page</a>
 | <a href="<?php echo $link_list ?>">List of pages</a>

<?php if (arg_check('list')) { ?>
 | <a href="<?php echo $link_filelist ?>">List of page files</a>
<?php } ?>

 | <a href="<?php echo $link_search ?>">Search</a>
 | <a href="<?php echo $link_whatsnew ?>">Recent changes</a>

<?php if ($do_backup) { ?>
 | <a href="<?php echo $link_backup ?>">Backup</a>
<?php } ?>

 | <a href="<?php echo $link_help ?>">Help</a>
 ]
<?php echo $hr ?>
</div>


<?php if (arg_check('read') and is_page('MenuBar')) { ?>
<table border="0" width="100%">
<tr><td valign="top" style="width:120px;word-break:break-all;padding:4px;">
<div id="menubar"><?php echo preg_replace('/<ul[^>]*>/','<ul>',convert_html(get_source('MenuBar'))) ?></div>
</td><td valign="top" style="padding-left:10px;">
<div><?php echo $body ?></div>
</td></tr>
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


<div id="toolbar">

<?php echo $hr ?>

<?php if ($is_page) { ?>
 <a href="<?php echo "$script?$r_page" ?>"><img src="./image/reload.png" width="20" height="20" alt="Reload" /></a>
 &nbsp;
 <a href="<?php echo $script ?>?plugin=newpage"><img src="./image/new.png" width="20" height="20" alt="New" /></a>
 <a href="<?php echo $link_edit ?>"><img src="./image/edit.png" width="20" height="20" alt="Edit" /></a>
 <a href="<?php echo $link_diff ?>"><img src="./image/diff.png" width="20" height="20" alt="Diff" /></a>
<?php   if ((bool)ini_get('file_uploads')) { ?>
 <a href="<?php echo $link_upload ?>"><img src="./image/file.png" width="20" height="20" alt="Upload" /></a>
<?php   } ?>
 &nbsp;
<?php } ?>

 <a href="<?php echo $link_top ?>"><img src="./image/top.png" width="20" height="20" alt="Front page" /></a>
 <a href="<?php echo $link_list ?>"><img src="./image/list.png" width="20" height="20" alt="List of pages" /></a>
 <a href="<?php echo $link_search ?>"><img src="./image/search.png" width="20" height="20" alt="Search" /></a>
 <a href="<?php echo $link_whatsnew ?>"><img src="./image/recentchanges.png" width="20" height="20" alt="Recent changes" /></a>

<?php if ($do_backup) { ?>
 <a href="<?php echo $link_backup ?>"><img src="./image/backup.png" width="20" height="20" alt="Backup" /></a>
<?php } ?>

 &nbsp;
 <a href="<?php echo $link_help ?>"><img src="./image/help.png" width="20" height="20" alt="Help" /></a>
 &nbsp;
 <a href="<?php echo $link_rss ?>"><img src="./image/rss.png" width="36" height="14" alt="RSS of recent changes" /></a>
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
