<?php
 /*

 PukiWiki サーバー情報表示プラグイン

 by Reimy
 http://pukiwiki.reimy.com/

 $Id: server.inc.php,v 1.4 2004/07/31 03:09:20 henoheno Exp $

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
