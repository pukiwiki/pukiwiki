<?php
/*
 * PukiWiki versionlistプラグイン
 *
 * CopyRight 2002 S.YOSHIMURA GPL2
 * http://masui.net/pukiwiki/ yosimura@excellence.ac.jp
 *
 * $Id: versionlist.inc.php,v 1.2.2.4 2004/06/20 06:31:07 henoheno Exp $
 */

function plugin_versionlist_convert()
{
  global $vars, $script;

  /* 探索ディレクトリ設定 */
  $SCRIPT_DIR = array(SUB_DIR, PLUGIN_DIR, SKIN_DIR);
  if (SUB_DIR != './'){ array_unshift($SCRIPT_DIR, './'); }

  if(func_num_args())
    $aryargs = func_get_args();
  else
    $aryargs = array();

  $lst = $comment = '';

  foreach($SCRIPT_DIR as $sdir){
    if ($dir = @dir($sdir)){
      while($file = $dir->read()){
        if($file == ".." || $file == ".") continue;
        if(!preg_match("/\.(php|lng|css|js)$/i",$file)) continue;
        
        $comment = '';
        $filenp = $sdir . $file;
        $fd = fopen($filenp,'r');
        while(!feof ($fd)){
          if(preg_match('/Id:(.+),v (\d+\.\d+)/',fgets($fd,1024),$match)){
            $comment = trim($match[1] . " -&gt; " .  $match[2]) ;
            break;
          }else {
            continue;
          }
        }
        fclose($fd);
        if($comment != '')
          $lst .= "<li>$filenp =&gt; $comment\n";
      }
    }
    $dir->close();
  }
  if($lst=='') {
    return '';
  }

  return "<ul>$lst</ul>";
}
?>
