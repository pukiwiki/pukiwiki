<?
function plugin_source_action()
{
	global $post,$vars,$script,$InterWikiName,$WikiName,$BracketName,$defaultpage;

	header("Content-type: text/plain");
	readfile(get_filename(encode($vars["page"])));

	die();
}

?>
