<?
function plugin_aname_convert()
{
	if(func_num_args())
		$aryargs = func_get_args();
	else
		$aryargs = array();

	return "<a name=\"$aryargs[0]\"></a>";
}
?>
