<?php
// $Id: source.inc.php,v 1.5 2003/01/27 05:38:47 panda Exp $

function plugin_source_action()
{
	global $vars;
	
	header('Content-type: text/plain; charset="'.ENCODING.'"');
	echo join('',get_source($vars['page']));
	
	die();
}

?>
