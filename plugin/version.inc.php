<?php
// $Id: version.inc.php,v 1.6 2003/01/27 05:38:47 panda Exp $

function plugin_version_convert()
{
	return '<p>'.plugin_version_inline().'</p>';
}
function plugin_version_inline()
{
	return S_VERSION;
}
?>
