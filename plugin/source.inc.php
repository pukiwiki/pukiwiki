<?php
// $Id: source.inc.php,v 1.6 2003/02/18 04:30:32 panda Exp $

function plugin_source_action()
{
	global $vars;
	
	header('Content-type: text/plain; charset="'.SOURCE_ENCODING.'"');
	echo join('',get_source($vars['page']));
	
	die();
}

?>
