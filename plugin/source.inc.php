<?php
// $Id: source.inc.php,v 1.3 2002/11/29 00:09:01 panda Exp $

function plugin_source_action()
{
	global $post,$vars,$script,$InterWikiName,$WikiName,$BracketName,$defaultpage;

	header("Content-type: text/plain");
	readfile(get_filename(encode($vars["page"])));

	die();
}

?>
