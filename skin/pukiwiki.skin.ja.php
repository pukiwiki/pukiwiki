<? global $page_title; header("Content-Type: text/html; charset=euc-jp") ?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=euc-jp">
	<meta http-equiv="content-style-type" content="text/css">
	<title><?=$page_title ?> - <?=$title?></title>
	<link rel="stylesheet" href="skin/default.ja.css" type="text/css" media="screen" charset="euc-jp">
	<script tyle="text/javascript">
	<!--
		function open_mini(URL,width,height){
			aWindow = window.open(URL, "mini", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes,resizable=no,width="+width+",height="+height);
		}
	//-->
	</script>
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
			<strong style="font-size:30px"><?=$page?></strong><br />
		</td></tr>
		<tr><td valign="top">
			<? if($is_page) { ?>
			<a style="font-size:8px" href="<?=$script?>?<?=rawurlencode($vars["page"])?>"><?=$script?>?<?=rawurlencode($vars["page"])?></a><br />
			<? } ?>
		</td></tr>
	</table>
	<br />
	<? if($is_page) { ?>
		[ <a href="<?=$script?>?<?=rawurlencode($vars[page])?>">リロード</a> ]
		&nbsp;
		[ <a href="<?=$script?>?plugin=newpage">新規</a>
		| <a href="<?=$link_edit?>">編集</a>
		| <a href="<?=$link_diff?>">差分</a>
		| <a href="<?=$script?>?plugin=attach&pcmd=upload&page=<?=rawurlencode($vars[page])?>">添付</a>
		]
		&nbsp;
	<? } ?>
	[ <a href="<?=$link_top?>">トップ</a>
	| <a href="<?=$link_list?>">一覧</a>
	<? if(arg_check("list")) { ?>
		| <a href="<?=$link_filelist?>">ファイル名一覧</a>
	<? } ?>
	| <a href="<?=$link_search?>">単語検索</a>
	| <a href="<?=$link_whatsnew?>">最終更新</a>
	<? if($do_backup) { ?>
		| <a href="<?=$link_backup?>">バックアップ</a>
	<? } ?>
	| <a href="<?="$script?".rawurlencode("[[ヘルプ]]")?>">ヘルプ</a>
	]<br />
	<?=$hr?>
	<?if($is_page){ ?>
		<table cellspacing="1" cellpadding="0" border="0" width="100%">
			<tr>
			<td valign="top" style="width:120px;word-break:break-all;">
				<? echo convert_html(@join("",@file(get_filename(encode("MenuBar"))))); ?>
			</td>
			<td style="width:10px">
			</td>
			<td valign="top">
	<? } ?>
	<?=$body?>
	<?if($is_page){ ?>
			</td>
			</tr>
		</table>
	<? } ?>
	<?=$hr?>
	<?
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
		<? if($is_page) { ?>
			<a href="<?=$script?>?<?=rawurlencode($vars[page])?>"><img src="./image/reload.gif" width="20" height="20" border="0" alt="リロード" /></a>
			&nbsp;
			<a href="<?=$script?>?plugin=newpage"><img src="./image/new.gif" width="20" height="20" border="0" alt="新規" /></a>
			<a href="<?=$link_edit?>"><img src="./image/edit.gif" width="20" height="20" border="0" alt="編集" /></a>
			<a href="<?=$link_diff?>"><img src="./image/diff.gif" width="20" height="20" border="0" alt="差分" /></a>
			&nbsp;
		<? } ?>
		<a href="<?=$link_top?>"><img src="./image/top.gif" width="20" height="20" border="0" alt="トップ" /></a>
		<a href="<?=$link_list?>"><img src="./image/list.gif" width="20" height="20" border="0" alt="一覧" /></a>
		<a href="<?=$link_search?>"><img src="./image/search.gif" width="20" height="20" border="0" alt="検索" /></a>
		<a href="<?=$link_whatsnew?>"><img src="./image/recentchanges.gif" width="20" height="20" border="0" alt="最終更新" /></a>
		<? if($do_backup) { ?>
			<a href="<?=$link_backup?>"><img src="./image/backup.gif" width="20" height="20" border="0" alt="バックアップ" /></a>
		<? } ?>
		&nbsp;
		<a href="<?="$script?".rawurlencode("[[ヘルプ]]")?>"><img src="./image/help.gif" width="20" height="20" border="0" alt="ヘルプ" /></a>
		&nbsp;
		<a href="<?=$script?>?cmd=rss"><img src="./image/rss.gif" width="36" height="14" border="0" alt="最終更新のRSS" /></a>
	</div>
	<? if($fmt) { ?>
		 <span class="small">Last-modified: <?=date("D, d M Y H:i:s T",$fmt)?></span> <?=get_pg_passage($vars["page"])?><br />
	<? } ?>
	<? if($related) { ?>
		 <span class="small">Link: <?=$related?></span><br />
	<? } ?>
	<br />
	<address>
		Modified by <a href="<?=$modifierlink?>"><?=$modifier?></a><br /><br />
		<?=S_COPYRIGHT?><br />
		Powered by PHP <?=PHP_VERSION?><br /><br />
		HTML convert time to <?=$taketime?> sec.
	</address>
</div>
</body>
</html>
