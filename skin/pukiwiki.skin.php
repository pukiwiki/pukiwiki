<? header("Content-Type: text/html; charset=EUC_JP") ?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=euc-jp">
<title><?=$page_title ?> - <?=$title?></title>
<style>
<!--
pre, dl, ol, p, blockquote { line-height:130%; }

body,td
{
	color: black;
	margin-left: 2%;
	margin-right: 2%;
	font-size: 10.5pt;
	font-family: verdana, arial, helvetica, Sans-Serif;
}

a:link
{
	color: #215dc6;
	text-decoration: none;
}

a:active
{
	background: #CCDDEE;
	color: #215dc6;
	text-decoration: none;
}

a:visited
{
	color: #a63d21;
	text-decoration: none;
}

a:hover
{
	background: #CCDDEE;
	color: #215dc6;
	text-decoration: underline;
	position:relative;
	top:1px;
	left:1px;
}

h1, h2, h3, h4, h5, h6
{
	font-family: verdana, arial, helvetica, Sans-Serif;
	background-color: #DDEEFF;
	padding: 0.3em;
}

dt {
	font-weight: bold;
	margin-top: 2ex;
	margin-left: 1em;
}

pre {
	border-top:    #DDDDEE 1px solid;
	border-bottom: #888899 1px solid;
	border-left:   #DDDDEE 1px solid;
	border-right:  #888899 1px solid;
	padding: 0.5em;
	margin-left: 1em;
	margin-right: 2em;
	white-space: pre;
	background-color: #F0F8FF;
	color: black;
}

img {
	border: none;
	vertical-align: middle;
}

small {
	font-size:8.5pt;
}

sup {
	color: #DD3333;
	font-weight: bold;
}

ul {
	margin-top: 5px;
	margin-bottom: 5px;
	line-height:130%;
}

.noexists {
	background-color:#FFFACC;
}

.style_table {
	border: 0px;
	background-color: #CCD5DD;
	margin: 5px;
	margin-left: 3em;
	padding: 0px;
}

.style_td {
	background-color: #EEF5FF;
	padding: 5px;
	margin: 1px;
}

.style_td_caltop {
	background-color: #EEF5FF;
	padding: 5px;
	margin: 1px;
}

.style_td_today {
	background-color: #FFFFDD;
	padding: 5px;
	margin: 1px;
}

.style_td_sat {
	background-color: #DDE5FF;
	padding: 5px;
	margin: 1px;
}

.style_td_sun {
	background-color: #FFEEEE;
	padding: 5px;
	margin: 1px;
}

.style_td_blank {
	background-color: #EEF5FF;
	padding: 5px;
	margin: 1px;
}

.style_td_week {
	background-color: #DDE5EE;
	padding: 5px;
	margin: 1px;
}

//-->
</style>
<script language="JavaScript">
<!--
  function open_mini(URL,width,height){
    aWindow = window.open(URL, "mini", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes,resizable=no,width="+width+",height="+height);
  }
//-->
</script>
</head>

<body>

<table>
 <tr>
  <td>
   <a href="http://pukiwiki.org/"><img src="pukiwiki.png" width="80" height="80" border="0" alt="[PukiWiki]"></a><br>
  </td>
  <td width="20">
  </td>
  <td>
   <b style="font-size:30px"><?=$page?></b><br>
   <? if($is_page) { ?>
   <font size="1"><a href="<?=$script?>?<?=rawurlencode($vars["page"])?>"><?=$script?>?<?=rawurlencode($vars["page"])?></a></font><br>
   <? } ?>
  </td>
 </tr>
</table>

<br>

<? if($is_page) { ?>
[ <a href="<?=$script?>?<?=rawurlencode($vars[page])?>">リロード</a> ]
&nbsp;
[ <a href="<?=$script?>?plugin=newpage">新規</a>
| <a href="<?=$link_edit?>">編集</a>
| <a href="<?=$link_diff?>">差分</a>
]
&nbsp;
<? } ?>

 [ <a href="<?=$link_top?>">トップ</a>
 | <a href="<?=$link_list?>">一覧</a>
 | <a href="<?=$link_search?>">単語検索</a>
 | <a href="<?=$link_whatsnew?>">最終更新</a>
<? if($do_backup) { ?>
 | <a href="<?=$link_backup?>">バックアップ</a>
<? } ?>
 | <a href="<?="$script?".rawurlencode("[[ヘルプ]]")?>">ヘルプ</a>
 ]<br>

<?=$hr?>

<?=$body?>

<?=$hr?>

<div align="right">

<? if($is_page) { ?>
<a href="<?=$script?>?<?=rawurlencode($vars[page])?>"><img src="./image/reload.gif" width="20" height="20" border="0" alt="リロード"></a>

&nbsp;

<a href="<?=$script?>?plugin=newpage"><img src="./image/new.gif" width="20" height="20" border="0" alt="新規"></a>
<a href="<?=$link_edit?>"><img src="./image/edit.gif" width="20" height="20" border="0" alt="編集"></a>
<a href="<?=$link_diff?>"><img src="./image/diff.gif" width="20" height="20" border="0" alt="差分"></a>
&nbsp;

<? } ?>

<a href="<?=$link_top?>"><img src="./image/top.gif" width="20" height="20" border="0" alt="トップ"></a>
<a href="<?=$link_list?>"><img src="./image/list.gif" width="20" height="20" border="0" alt="一覧"></a>
<a href="<?=$link_search?>"><img src="./image/search.gif" width="20" height="20" border="0" alt="検索"></a>
<a href="<?=$link_whatsnew?>"><img src="./image/recentchanges.gif" width="20" height="20" border="0" alt="最終更新"></a>
<? if($do_backup) { ?>
<a href="<?=$link_backup?>"><img src="./image/backup.gif" width="20" height="20" border="0" alt="バックアップ"></a>
<? } ?>
&nbsp;
<a href="<?="$script?".rawurlencode("[[ヘルプ]]")?>"><img src="./image/help.gif" width="20" height="20" border="0" alt="ヘルプ"></a>
&nbsp;

<a href="<?=$script?>?cmd=rss"><img src="./image/rss.gif" width="36" height="14" border="0" alt="最終更新のRSS"></a>

</div>

<? if($fmt) { ?>
 <small>Last-modified: <?=date("D, d M Y H:i:s T",$fmt)?></small> <?=get_pg_passage($vars["page"])?><br>
<? } ?>
<? if($related) { ?>
 <small>Link: <?=$related?></small><br>
<? } ?>

<br>

<font face="Verdana" size="1">
Modified by <a href="<?=$modifierlink?>"><?=$modifier?></a><br>
<br>
<?=S_COPYRIGHT?><br>
Powered by PHP <?=PHP_VERSION?><br>
<br>
HTML convert time to <?=$taketime?> sec.
</font>

</body>
</html>
