<?php
// $Id: source.inc.php,v 1.4 2002/12/05 05:02:27 panda Exp $

function plugin_source_action()
{
	global $vars;

	header('Content-type: text/plain');
	readfile(get_filename(encode($vars['page'])));

	die();
}

?>
