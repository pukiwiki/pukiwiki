<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: br.inc.php,v 1.4 2005/03/19 01:26:53 henoheno Exp $
//
// Forcing a line break plugin

// Escape using <br> in <blockquote> (BugTrack/583)
define('PLUGIN_BR_ESCAPE_BLOCKQUOTE', 1);

// ----

define('PLUGIN_BR_TAG', '<br class="spacer" />');

function plugin_br_convert()
{
	if (PLUGIN_BR_ESCAPE_BLOCKQUOTE) {
		return '<div class="spacer">&nbsp;</div>';
	} else {
		return PLUGIN_BR_TAG;
	}
}

function plugin_br_inline()
{
	return PLUGIN_BR_TAG;
}
?>
