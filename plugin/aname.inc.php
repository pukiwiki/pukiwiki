<?
// $Id: aname.inc.php,v 1.3 2002/06/26 06:23:57 masui Exp $

function plugin_aname_convert()
{
	if(func_num_args())
		$aryargs = func_get_args();
	else
		$aryargs = array();

	return "<a name=\"$aryargs[0]\"></a>";
}
?>
