<?
// $Id: update.inc.php,v 1.2 2002/06/26 06:23:57 masui Exp $

///////////////////////
//
define(PLUGIN_UPDATE_SECRET,'GReenOrRed');


function plugin_update_action()
{
  global $vars,$script,$do_backup,$del_backup;

  $page = $vars["page"];
 
  header("Content-type: text/plain");
 
  // 差分ファイルの作成
  if(is_page($vars["page"]))
    $oldpostdata = join("",get_source($page));
  else
    $oldpostdata = "";

  list($postdata,$auto) = plugin_update_diff($oldpostdata,$vars['body']);
  $postdata = user_rules_str($postdata);
  if($postdata == $oldpostdata) {
    print "status noupdate\n\n";
    die;
  }
  
  if($postdata)
    $diffdata = do_diff($oldpostdata,$postdata);
  file_write(DIFF_DIR,$page,$diffdata);
  
  // バックアップの作成
  if(is_page($page))
    $oldposttime = filemtime(get_filename(encode($page)));
  else
    $oldposttime = time();
  
  // 編集内容が何も書かれていないとバックアップも削除する?しないですよね。
  if(!$postdata && $del_backup)
    backup_delete(BACKUP_DIR.encode($vars["page"]).".txt");
  else if($do_backup && is_page($vars["page"]))
    make_backup(encode($vars["page"]).".txt",$oldpostdata,$oldposttime);
  
  // ファイルの書き込み
  file_write(DATA_DIR,$vars["page"],$postdata);
  
  // is_pageのキャッシュをクリアする。
  is_page($vars["page"],true);
  
  if($auto) {
    print "status updated\n";
  }
  else {
    print "status collided\n";
  }
  print "\n".$postdata;
  
  die();
}

// 差分の作成
function plugin_update_diff($oldstr,$newstr)
{
  $oldlines = split("\n",ereg_replace("[\r\n\t ]+$",'',$oldstr));
  $newlines = split("\n",ereg_replace("[\r\n\t ]+$",'',$newstr));

  if(sizeof($oldlines)==1 && $oldlines[0]=='') {
    $oldlines = array();
  }
  if(sizeof($newlines)==1 && $newlines[0]=='') {
    $newlines = array();
  }
 
  $retdiff = $props = array();
  $auto = true;
  
  foreach($newlines as $newline) {
    $flg = false;
    $cnt = 0;
    foreach($oldlines as $oldline) {
      if($oldline == $newline) {
	if($cnt>0) {
	  for($i=0; $i<$cnt; ++$i) {
	    array_push($retdiff,array_shift($oldlines));
	    array_push($props,'! ');
	    $auto = false;
	  }
	}
	array_push($retdiff,array_shift($oldlines));
	array_push($props,'');
	$flg = true;
	break;
      }
      $cnt++;
    }
    if(!$flg) {
      array_push($retdiff,$newline);
#      array_push($props,'+ ');
      array_push($props,'');
    }
  }
  foreach($oldlines as $oldline) {
    array_push($retdiff,$oldline);
    array_push($props,'! ');
    $auto = false;
  }
  if($auto) {
    return array(join("\n",$retdiff),$auto);
  }
  
  $ret = '';
  foreach($retdiff as $line) {
    $ret .= array_shift($props) . $line . "\n";
  }
  return array($ret,$auto);
}

?>
