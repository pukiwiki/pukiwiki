<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// filelist.inc.php
// Copyright 2003-2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Filelist plugin: redirect to list plugin
// cmd=filelist

function plugin_filelist_action()
{
	return do_plugin_action('list');
}
