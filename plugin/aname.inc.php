<?
// $Id: aname.inc.php,v 1.4 2002/08/21 17:32:04 masui Exp $

function plugin_aname_convert()
{
  if (!func_num_args()) return "Aname no argument!!\n";
  $aryargs = func_get_args();
  if (eregi("^[A-Z][A-Z0-9\-_]*$", $aryargs[0]))
    return "<a name=\"$aryargs[0]\" id=\"$aryargs[0]\"></a>";
  else
    return "Bad Aname!! -- ".$aryargs[0]."\n";
}
?>