<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: br.inc.php,v 1.5 2007/04/08 10:22:18 henoheno Exp $
// Copyright (C) 2003-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// "Forcing one line-break" plugin

// Escape using <br /> in <blockquote> (BugTrack/583)
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
