<?php
// PukiWiki - Yet another WikiWikiWeb clone
// passage.inc.php
// Copyright 2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Show passage by Client JavaScript

function plugin_passage_inline()
{
	list($date_atom) = func_get_args();
	$time = strtotime($date_atom);
	$yyyyMMdd = date('Y-m-d', $time);
	return '<span class="simple_passage" data-mtime="' .
		get_date_atom($time) . '">' . $yyyyMMdd . '</span>';
}
