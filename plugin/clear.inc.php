<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: clear.inc.php,v 1.4 2004/11/27 10:01:21 henoheno Exp $
//
// Clear plugin - inserts a CSS class 'clear', to set 'clear:both'

function plugin_clear_convert()
{
	return '<div class="clear"></div>';
}
?>
