<?
// $Id: source.inc.php,v 1.2 2002/06/26 06:23:57 masui Exp $

function plugin_source_action()
{
	global $post,$vars,$script,$InterWikiName,$WikiName,$BracketName,$defaultpage;

	header("Content-type: text/plain");
	readfile(get_filename(encode($vars["page"])));

	die();
}

?>
