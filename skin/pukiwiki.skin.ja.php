<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: pukiwiki.skin.ja.php,v 1.15.2.6 2004/08/08 05:25:23 henoheno Exp $
//

if (!defined('DATA_DIR')) { exit; }
header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=euc-jp');

global $page_title;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=euc-jp">
	<meta http-equiv="content-style-type" content="text/css">
	<meta http-equiv="content-script-type" content="text/javascript">
<?php if (! ( ($vars['cmd']==''||$vars['cmd']=='read') && $is_page) ) { ?>
	<meta name="robots" content="noindex,nofollow" />
<?php } ?>

	<title><?php echo $page_title ?> - <?php echo $title?></title>
	<link rel="stylesheet" href="skin/default.ja.css" type="text/css" media="screen" charset="shift_jis">
	<script language=javascript src="skin/default.js"></script>
</head>
<body>
<div>
	<table border="0">
		<tr>
		<td rowspan="2">
			<a href="http://pukiwiki.org/"><img src="<?php echo IMAGE_DIR ?>pukiwiki.png" width="80" height="80" border="0" alt="[PukiWiki]" /></a><br />
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
		[ <a href="<?php echo "$script?".rawurlencode($vars[page]) ?>">リロード</a> ]
		&nbsp;
		[ <a href="<?php echo $script ?>?plugin=newpage">新規</a>
		| <a href="<?php echo $link_edit ?>">編集</a>
		| <a href="<?php echo $link_diff ?>">差分</a>
		| <a href="<?php echo $script ?>?plugin=attach&amp;pcmd=upload&amp;page=<?php echo rawurlencode($vars[page]) ?>">添付</a>
		]
		&nbsp;
	<?php } ?>
	[ <a href="<?php echo $link_top ?>">トップ</a>
	| <a href="<?php echo $link_list ?>">一覧</a>
	<?php if(arg_check("list")) { ?>
		| <a href="<?php echo $link_filelist ?>">ファイル名一覧</a>
	<?php } ?>
	| <a href="<?php echo $link_search ?>">単語検索</a>
	| <a href="<?php echo $link_whatsnew ?>">最終更新</a>
	<?php if($do_backup) { ?>
		| <a href="<?php echo $link_backup ?>">バックアップ</a>
	<?php } ?>
	| <a href="<?php echo "$script?".rawurlencode("[[ヘルプ]]") ?>">ヘルプ</a>
	]<br />
	<?php echo $hr ?>
	<?php if($is_page) { ?>
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
	<?php if($is_page) { ?>
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
			<a href="<?php echo "$script?".rawurlencode($vars[page]) ?>"><img src="<?php echo IMAGE_DIR ?>reload.gif" width="20" height="20" border="0" alt="リロード" /></a>
			&nbsp;
			<a href="<?php echo $script ?>?plugin=newpage"><img src="<?php echo IMAGE_DIR ?>new.gif" width="20" height="20" border="0" alt="新規" /></a>
			<a href="<?php echo $link_edit ?>"><img src="<?php echo IMAGE_DIR ?>edit.gif" width="20" height="20" border="0" alt="編集" /></a>
			<a href="<?php echo $link_diff ?>"><img src="<?php echo IMAGE_DIR ?>diff.gif" width="20" height="20" border="0" alt="差分" /></a>
			&nbsp;
		<?php } ?>
		<a href="<?php echo $link_top ?>"><img src="<?php echo IMAGE_DIR ?>top.gif" width="20" height="20" border="0" alt="トップ" /></a>
		<a href="<?php echo $link_list ?>"><img src="<?php echo IMAGE_DIR ?>list.gif" width="20" height="20" border="0" alt="一覧" /></a>
		<a href="<?php echo $link_search ?>"><img src="<?php echo IMAGE_DIR ?>search.gif" width="20" height="20" border="0" alt="検索" /></a>
		<a href="<?php echo $link_whatsnew ?>"><img src="<?php echo IMAGE_DIR ?>recentchanges.gif" width="20" height="20" border="0" alt="最終更新" /></a>
		<?php if($do_backup) { ?>
			<a href="<?php echo $link_backup ?>"><img src="<?php echo IMAGE_DIR ?>backup.gif" width="20" height="20" border="0" alt="バックアップ" /></a>
		<?php } ?>
		&nbsp;
		<a href="<?php echo "$script?".rawurlencode("[[ヘルプ]]") ?>"><img src="<?php echo IMAGE_DIR ?>help.gif" width="20" height="20" border="0" alt="ヘルプ" /></a>
		&nbsp;
		<a href="<?php echo $script ?>?cmd=rss"><img src="<?php echo IMAGE_DIR ?>rss.gif" width="36" height="14" border="0" alt="最終更新のRSS" /></a>
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
