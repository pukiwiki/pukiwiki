<?php
// $Id: includesubmenu.inc.php,v 1.2 2003/01/27 05:38:46 panda Exp $

function plugin_includesubmenu_convert()
{
  global $vars,$script;
  $ShowPageName = FALSE;
  if (func_num_args()) {
    $aryargs = func_get_args();
    if ($aryargs[0] == "showpagename") $ShowPageName = TRUE;
  }else{
    $ShowPageName = FALSE;
  }

  $SubMenuPageName = "";

  $tmppage = strip_bracket($vars["page"]);
  //下階層のSubMenuページ名
  $SubMenuPageName1 = $tmppage . "/SubMenu";

  //同階層のSubMenuページ名
  $LastSlash= strrpos($tmppage,"/");
  if ($LastSlash === false){
    $SubMenuPageName2 = "SubMenu";
  }else{
    $SubMenuPageName2 = substr($tmppage,0,$LastSlash)."/SubMenu";
  }
  //echo "$SubMenuPageName1 <br>";
  //echo "$SubMenuPageName2 <br>";
  //下階層にSubMenuがあるかチェック
  //あれば、それを使用
  if (is_page($SubMenuPageName1)){
    //下階層にSubMenu有り
    $SubMenuPageName=$SubMenuPageName1;
  }elseif (is_page($SubMenuPageName2)){
    //同階層にSubMenu有り
    $SubMenuPageName=$SubMenuPageName2;
  }else{
    //SubMenu無し
    return "";
  }
  
  $link = "<a href=\"$script?cmd=edit&page=".rawurlencode($SubMenuPageName)."\">".strip_bracket($SubMenuPageName)."</a>";

  $body = convert_html(join('',get_source($SubMenuPageName)));
  
  if ($ShowPageName == TRUE) {
    $head = "<h1>$link</h1>\n";
    $body = "$head\n$body\n";
  }
  return $body;
}
?>
