<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: ruby.inc.php,v 1.2 2003/04/24 14:42:29 arino Exp $
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
	
	return "<ruby><rb>$body</rb><rp>(</rp><rt>$ruby</rt><rp>)</rp></ruby>";
}
?>
