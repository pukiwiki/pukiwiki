<?
 /*
 
 PukiWiki サーバー情報表示プラグイン
 
 by Reimy
 http://pukiwiki.reimy.com/
 
 
 init.php の16行目あたりに下記の行を追加してからご使用ください
 define("SERVER_NAME",$HTTP_SERVER_VARS["SERVER_NAME"]);
 define("SERVER_SOFTWARE",$HTTP_SERVER_VARS["SERVER_SOFTWARE"]);
 define("SERVER_ADMIN",$HTTP_SERVER_VARS["SERVER_ADMIN"]);

 ※SERVER_NAMEはinit.phpで既に設定されているはずですので、残り2行を追加してください
 
 $Id: server.inc.php,v 1.1 2002/12/05 05:02:27 panda Exp $
 
 */

 function plugin_server_convert()
 {
   $string = "<dl><dt>Server Name</dt>\n<dd>"
   .SERVER_NAME
   ."</dd>\n<dt>Server Software</dt>\n<dd>"
   .SERVER_SOFTWARE
   ."</dd>\n<dt>Server Admin</dt>\n<dd>"
   ."<a href=\"mailto:"
   .SERVER_ADMIN
   ."\">"
   .SERVER_ADMIN
   ."</a></dd></dl>\n";
   return $string;
 }
?>
