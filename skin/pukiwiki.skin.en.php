<?php global $page_title; header("Content-Type: text/html; charset=iso-8859-1") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="content-style-type" content="text/css">
	<meta http-equiv="content-script-type" content="text/javascript">
<?php if (! ( ($vars['cmd']==''||$vars['cmd']=='read') && $is_page) ) { ?>
	<meta name="robots" content="noindex,nofollow" />
<?php } ?>
	<title><?php echo $page_title ?> - <?php echo $title ?></title>
	<link rel="stylesheet" href="skin/default.en.css" type="text/css" media="screen" charset="iso-8859-1">
	<script language=javascript src="skin/default.js"></script>
</head>
<body>
<div>
	<table border="0">
		<tr>
		<td rowspan="2">
			<a href="http://pukiwiki.org/"><img src="pukiwiki.png" width="80" height="80" border="0" alt="[PukiWiki]" /></a><br />
		</td>
		<td rowspan="2" style="width:20px">
		</td>
		<td valign="bottom">
			<strong style="font-size:30px"><?php echo $page ?></strong><br />
		</td></tr>
		<tr><td valign="top">
			<?php if($is_page) { ?>
			<a style="font-size:8px" href="<?php echo $script ?>?<?php echo rawurlencode($vars["page"]) ?>"><?php echo $script ?>?<?php echo rawurlencode($vars["page"]) ?></a><br />
			<?php } ?>
		</td></tr>
	</table>
	<br />
	<?php if($is_page) { ?>
		[ <a href="<?php echo $script ?>?<?php echo rawurlencode($vars[page]) ?>">Reload</a> ]
		&nbsp;
		[ <a href="<?php echo $script ?>?plugin=newpage">New</a>
		| <a href="<?php echo $link_edit ?>">Edit</a>
		| <a href="<?php echo $link_diff ?>">Diff</a>
		| <a href="<?php echo $script ?>?plugin=attach&amp;pcmd=upload&amp;page=<?php echo rawurlencode($vars[page]) ?>">Upload</a>
		]
		&nbsp;
	<?php } ?>
	[ <a href="<?php echo $link_top ?>">Front page</a>
	| <a href="<?php echo $link_list ?>">List of pages</a>
	<?php if(arg_check("list")) { ?>
		| <a href="<?php echo $link_filelist ?>">List of page files</a>
	<?php } ?>
	| <a href="<?php echo $link_search ?>">Search</a>
	| <a href="<?php echo $link_whatsnew ?>">Recent changes</a>
	<?php if($do_backup) { ?>
		| <a href="<?php echo $link_backup ?>">Backup</a>
	<?php } ?>
	| <a href="<?php echo "$script?".rawurlencode("[[Help]]") ?>">Help</a>
	]<br />
	<?php echo $hr ?>
	<?php if($is_page){ ?>
		<table cellspacing="1" cellpadding="0" border="0" width="100%">
			<tr>
			<td valign="top" style="width:120px;word-break:break-all;">
				<?php echo convert_html(@join("",@file(get_filename(encode("MenuBar"))))) ?>
			</td>
			<td style="width:10px">
			</td>
			<td valign="top">
	<?php } ?>
	<?php echo $body ?>
	<?php if($is_page){ ?>
			</td>
			</tr>
		</table>
	<?php } ?>
	<?php echo $hr ?>
	<?php
		if(file_exists(PLUGIN_DIR."attach.inc.php") && $is_read)
		{
			require_once(PLUGIN_DIR."attach.inc.php");
			$attaches = attach_filelist();
			if($attaches)
			{
				print $attaches;
				print $hr;
			}
		}
	?>
	<div style="text-align:right">
		<?php if($is_page) { ?>
			<a href="<?php echo $script;?>?<?php echo rawurlencode($vars[page]) ?>"><img src="./image/reload.gif" width="20" height="20" border="0" alt="Reload" /></a>
			&nbsp;
			<a href="<?php echo $script ?>?plugin=newpage"><img src="./image/new.gif" width="20" height="20" border="0" alt="New" /></a>
			<a href="<?php echo $link_edit ?>"><img src="./image/edit.gif" width="20" height="20" border="0" alt="Edit" /></a>
			<a href="<?php echo $link_diff ?>"><img src="./image/diff.gif" width="20" height="20" border="0" alt="Diff" /></a>
			&nbsp;
		<?php } ?>
		<a href="<?php echo $link_top ?>"><img src="./image/top.gif" width="20" height="20" border="0" alt="Front page" /></a>
		<a href="<?php echo link_list ?>"><img src="./image/list.gif" width="20" height="20" border="0" alt="List of pages" /></a>
		<a href="<?php echo $link_search ?>"><img src="./image/search.gif" width="20" height="20" border="0" alt="Search" /></a>
		<a href="<?php echo $link_whatsnew ?>"><img src="./image/recentchanges.gif" width="20" height="20" border="0" alt="Recent changes" /></a>
		<?php if($do_backup) { ?>
			<a href="<?php echo $link_backup ?>"><img src="./image/backup.gif" width="20" height="20" border="0" alt="Backup" /></a>
		<?php } ?>
		&nbsp;
		<a href="<?php echo "$script?".rawurlencode("[[Help]]") ?>"><img src="./image/help.gif" width="20" height="20" border="0" alt="Help" /></a>
		&nbsp;
		<a href="<?php echo $script ?>?cmd=rss"><img src="./image/rss.gif" width="36" height="14" border="0" alt="RSS of recent changes" /></a>
	</div>
	<?php if($fmt) { ?>
		 <span class="small">Last-modified: <?php echo date("D, d M Y H:i:s T",$fmt) ?></span> <?php echo get_pg_passage($vars["page"]) ?><br />
	<?php } ?>
	<?php if($related) { ?>
		 <span class="small">Link: <?php echo $related ?></span><br />
	<?php } ?>
	<br />
	<address>
		Modified by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a><br /><br />
		<?php echo S_COPYRIGHT ?><br />
		Powered by PHP <?php echo PHP_VERSION ?><br /><br />
		HTML convert time to <?php echo $taketime ?> sec.
	</address>
</div>
</body>
</html>
