<?php
// $Id: aname.inc.php,v 1.6.2.1 2003/03/22 15:12:35 panda Exp $

function plugin_aname_convert()
{
  if (!func_num_args()) return "Aname no argument!!\n";
  $aryargs = func_get_args();
  if (eregi("^[A-Z][A-Z0-9\-_]*$", $aryargs[0]))
    return "<a name=\"$aryargs[0]\" id=\"$aryargs[0]\"></a>";
  else
    return "Bad Aname!! -- ".htmlspecialchars($aryargs[0])."\n";
}
?>
