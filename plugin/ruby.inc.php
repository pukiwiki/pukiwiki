<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: ruby.inc.php,v 1.1 2003/04/23 08:06:15 arino Exp $
//

function plugin_ruby_inline()
{
	if (func_num_args() != 2)
	{
		return FALSE; 
	}
	
	list($ruby,$body) = func_get_args();
	
	if ($ruby == '' or $body == '')
	{
		return FALSE;
	}
	$body = make_link($body);
	
	return "<ruby><rb>$body</rb><rp>(</rp><rt>$ruby</rt><rp>)</rp></ruby>";
}
?>
