<?php
// $Id: referer.inc.php,v 1.1 2003/07/03 04:56:04 arino Exp $
/*
 * PukiWiki Referer プラグイン(リンク元表示プラグイン)
 * (C) 2003, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
*/

// 構成定義ファイル
define('CONFIG_REFERER','plugin/referer/config');

function plugin_referer_action() {
  global $script,$vars,$post,$referer;
  global $_referer_msg;

  // 許可していないのに呼ばれた場合の対応
  if (!$referer) {
    // デフォルトは、PukiWiki を表示
    header("Location: $script");
    die();
  }

  // ページ指定なし
  if (empty($vars["page"])) {
    header("Location: $script");
    die();
  }

  // 整列順
  $sort = $vars["sort"];
  if (is_null($sort) || empty($sort)) {
    $sort = "0d";
  }

  $retval['msg']  = $_referer_msg['msg_H0_Refer'];
  $retval['body'] = referer_edit($vars["page"],$sort);
  return $retval;
}

function referer_edit($page,$sort) {

  $r_page = rawurlencode($page);
  $file   = TRACKBACK_DIR.md5($r_page).".ref";
  $data = tb_get($file);
  if ($data === false) return "";

  switch ($sort) {
    case "0d":
      usort($data, 'referer_sort_by_LastDate_d'); // 0d 最終更新日時(新着順)
      break;
    case "0a":
      usort($data, 'referer_sort_by_LastDate_a'); // 0a 最終更新日時(日付順)
      break;
    case "1d":
      usort($data, 'referer_sort_by_RegDate_d');  // 1d 初回登録日時(新着順)
      break;
    case "1a":
      usort($data, 'referer_sort_by_RegDate_a');  // 1a 初回登録日時(日付順)
      break;
    case "2d":
      usort($data, 'referer_sort_by_Counter_d');  // 2d カウンタ(大きい順)
      break;
    case "2a":
      usort($data, 'referer_sort_by_Counter_a');  // 2a カウンタ(小さい順)
      break;
    case "3":
      usort($data, 'referer_sort_by_Referer');    // 3 Referer
      break;
    default:
      $sort = "0d";
      usort($data, 'referer_sort_by_LastDate_d'); // 0d 最終更新日時(新着順)
  }

  $msg .= referer_body($data,$page,$sort);
  return $msg;
}

// Referer 明細行編集
// 0:最終更新日時, 1:初回登録日時, 2:参照カウンタ, 3:Referer ヘッダ, 4:利用可否フラグ(1は有効)
function referer_body($data,$page,$sort) {
  global $_referer_msg, $pconfig_color;

  $rc     = "";
  $bg = referer_set_color();

  $hed0   = "<td style=\"";
  $hed1   = "\" colspan=\"2\">";
  $hed2   = "<a href=\"./?plugin=referer&amp;page=".$page."&amp;sort=";

  $hed_last = $hed0.$bg["etc"].$hed1.$hed2."0d\">".$_referer_msg['msg_Chr_darr']."</a>".$_referer_msg['msg_Hed_LastUpdate'].$hed2."0a\">".$_referer_msg['msg_Chr_uarr']."</a></td>\n";
  $hed_1st  = $hed0.$bg["etc"].$hed1.$hed2."1d\">".$_referer_msg['msg_Chr_darr']."</a>".$_referer_msg['msg_Hed_1stDate'].$hed2."1a\">".$_referer_msg['msg_Chr_uarr']."</a></td>\n";
  $hed_ctr  = $hed0.$bg["etc"]."text-align:right;\">".$hed2."2d\">".$_referer_msg['msg_Chr_darr']."</a>".$_referer_msg['msg_Hed_RefCounter'].$hed2."2a\">".$_referer_msg['msg_Chr_uarr']."</a></td>\n";
  $hed_ref  = $hed0.$bg["etc"]."\">".$hed2."3\">".$_referer_msg['msg_Hed_Referer']."</a></td>\n";

  switch ($sort) {
    case "0d":
      $hed_last = $hed0.$bg["cur"].$hed1.$_referer_msg['msg_Chr_darr'].$hed2."0d\">".$_referer_msg['msg_Hed_LastUpdate'].$hed2."0a\">".$_referer_msg['msg_Chr_uarr']."</a></td>\n";
      break;
    case "0a":
      $hed_last = $hed0.$bg["cur"].$hed1.$_referer_msg['msg_Chr_uarr'].$hed2."0a\">".$_referer_msg['msg_Hed_LastUpdate'].$hed2."0d\">".$_referer_msg['msg_Chr_darr']."</a></td>\n";
      break;
    case "1d":
      $hed_1st  = $hed0.$bg["cur"].$hed1.$_referer_msg['msg_Chr_darr'].$hed2."1d\">".$_referer_msg['msg_Hed_1stDate'].$hed2."1a\">".$_referer_msg['msg_Chr_uarr']."</a></td>\n";
      break;
    case "1a":
      $hed_1st  = $hed0.$bg["cur"].$hed1.$_referer_msg['msg_Chr_uarr'].$hed2."1a\">".$_referer_msg['msg_Hed_1stDate'].$hed2."1d\">".$_referer_msg['msg_Chr_darr']."</a></td>\n";
      break;
    case "2d":
      $hed_ctr  = $hed0.$bg["cur"]."text-align:right;\">".$_referer_msg['msg_Chr_darr'].$hed2."2d\">".$_referer_msg['msg_Hed_RefCounter'].$hed2."2a\">".$_referer_msg['msg_Chr_uarr']."</a></td>\n";
      break;
    case "2a":
      $hed_ctr  = $hed0.$bg["cur"]."text-align:right;\">".$_referer_msg['msg_Chr_uarr'].$hed2."2a\">".$_referer_msg['msg_Hed_RefCounter'].$hed2."2d\">".$_referer_msg['msg_Chr_darr']."</a></td>\n";
      break;
    case "3":
      $hed_ref  = $hed0.$bg["cur"]."\">".$hed2."3\">".$_referer_msg['msg_Hed_Referer']."</a></td>\n";
      break;
  }

  $rc .= "<table border=\"1\" cellspacing=\"1\" summary=\"Referer\">\n<tr>\n".
    $hed_last.$hed_1st.$hed_ctr.$hed_ref."</tr>\n";

  foreach ($data as $x) {
    // 0:最終更新日時, 1:初回登録日時, 2:参照カウンタ, 3:Referer ヘッダ, 4:利用可否フラグ(1は有効)
    $progress0 = ereg_replace("[()]", "", get_passage($x[0])); // 最終更新日時からの経過時間
    $progress1 = ereg_replace("[()]", "", get_passage($x[1])); // 初回登録日時からの経過時間
    $x[0] = date($_referer_msg['msg_Fmt_Date'], $x[0]+LOCALZONE); // 最終更新日時文字列
    $x[1] = date($_referer_msg['msg_Fmt_Date'], $x[1]+LOCALZONE); // 初回登録日時文字列
    $url  = htmlspecialchars(rawurldecode(rawurldecode($x[3]))); // URL
    $x[3] = htmlspecialchars(rawurldecode($x[3])); // URL
    $rc .= "<tr>\n".
	"<td>".$x[0]."</td>\n".
	"<td>".$progress0."</td>\n";
    if ($x[2] == 1) {
      $rc .= "<td colspan=\"2\">N/A</td>\n";
    } else {
      $rc .= "<td>".$x[1]."</td>\n".
	"<td>".$progress1."</td>\n";
    }
    $rc .= "<td style=\"text-align:right;\">".$x[2]."</td>\n";

    if (referer_ignore_check($x[3])) {
      // 適用不可データのため、アンカー抹殺
      $rc .= "<td>".$url."</td>\n";
    } else {
      $rc .= "<td><a href=\"$x[3]\">".$url."</a></td>\n";
    }
    $rc .= "</tr>\n";
  }

  $rc .= "</table>\n";
  return $rc;
}

function referer_set_color() {
  global $pconfig_color;

  // config.php
  if (!count($pconfig_color)) {
    $config = new Config(CONFIG_REFERER);
    $config->read();
    $pconfig_color = $config->get('COLOR');
    unset($config);
  }

  // デフォルトカラー
  $rc["cur"] = "background-color:#88ff88;";
  $rc["etc"] = "background-color:#cccccc;";

  foreach ($pconfig_color as $x) {
    // BGCOLOR(#88ff88)
    preg_match("'BGCOLOR\((.*)\)'si", $x[1], $regs);
    if (!empty($regs[1])) $x[1] = $regs[1];
    $rc[ $x[0] ] = "background-color:".$x[1].";";
  }
  return $rc;
}

function referer_ignore_check($url) {
  global $pconfig_ignore_url;

  // config.php
  if (!count($pconfig_ignore_url)) {
    $config = new Config(CONFIG_REFERER);
    $config->read();
    $pconfig_ignore_url = $config->get('IGNORE');
    unset($config);
  }

  foreach ($pconfig_ignore_url as $x) {
    if (strpos($url,$x) === 0) return 1;
  }
  return 0;
}

// データを整列
// 0:最終更新日時, 1:初回登録日時, 2:参照カウンタ, 3:Referer
function referer_sort_by_LastDate_d($p1, $p2) { return ($p2['0'] - $p1['0']); }
function referer_sort_by_LastDate_a($p1, $p2) { return ($p1['0'] - $p2['0']); }
function referer_sort_by_RegDate_d($p1, $p2)  { return ($p2['1'] - $p1['1']); }
function referer_sort_by_RegDate_a($p1, $p2)  { return ($p1['1'] - $p2['1']); }
function referer_sort_by_Counter_d($p1, $p2)  { return ($p2['2'] - $p1['2']); }
function referer_sort_by_Counter_a($p1, $p2)  { return ($p1['2'] - $p2['2']); }
function referer_sort_by_Referer($p1, $p2) {
  if ($p1['3'] == $p2['3']) return 0;
  return ($p1['3'] > $p2['3']) ? 1 : -1;
}

?>