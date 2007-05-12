<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: map.inc.php,v 1.17 2007/05/12 09:17:14 henoheno Exp $
// Copyright (C) 2002-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Relation map plugin
//
// Usage :
//   ?plugin=map&refer=pagename
//   ?plugin=map&refer=pagename&reverse=true


// Show $non_list files
define('PLUGIN_MAP_SHOW_HIDDEN', 0); // 0, 1

function plugin_map_action()
{
	global $vars, $whatsnew, $defaultpage, $non_list;

	$reverse = isset($vars['reverse']);
	$refer   = isset($vars['refer']) ? $vars['refer'] : '';
	if ($refer == '' || ! is_page($refer)) {
		$vars['refer'] = $refer = $defaultpage;
	}

	$retval['msg']  = $reverse ? 'Relation map (link from)' : 'Relation map, from $1';

	// Get pages
	$pages = array_values(array_diff(get_existpages(), array($whatsnew)));
	if (! PLUGIN_MAP_SHOW_HIDDEN) {
		$pages = array_diff($pages, preg_grep('/' . $non_list . '/', $pages));
	}
	if (empty($pages)) {
		$retval['body'] = 'No page.';
		return $retval;
	}

	$body = array();

	// Generate a tree
	$nodes = array();
	foreach ($pages as $page) {
		$nodes[$page] = & new MapNode($page, $reverse);
	}

	// Node not found: Because of filtererd by $non_list
	if (! isset($nodes[$refer])) {
		$vars['refer'] = $refer = $defaultpage;
		$nodes[$refer] = & new MapNode($page, $reverse);
	}

	if ($reverse) {
		$keys = array_keys($nodes);
		sort($keys, SORT_STRING);
		$alone = array();
		$body[] = '<ul>';
		foreach ($keys as $page) {
			if (! empty($nodes[$page]->rels)) {
				$body[] = $nodes[$page]->toString($nodes, 1, $nodes[$page]->parent_id);
			} else {
				$alone[] = $page;
			}
		}
		$body[] = '</ul>';
		if (! empty($alone)) {
			$body[] = '<hr />';
			$body[] = '<p>No link from anywhere in this site.</p>';
			$body[] = '<ul>';
			foreach ($alone as $page) {
				$body[] = $nodes[$page]->toString($nodes, 1, $nodes[$page]->parent_id);
			}
			$body[] = '</ul>';
		}
	} else {
		$nodes[$refer]->chain($nodes);
		$body[] = '<ul>';
		$body[] = $nodes[$refer]->toString($nodes) . '</ul>';
		$body[] = '<hr />';
		$body[] = '<p>Not related from ' . htmlspecialchars($refer) . '</p>';
		$keys = array_keys($nodes);
		sort($keys, SORT_STRING);
		$body[] = '<ul>';
		foreach ($keys as $page) {
			if (! $nodes[$page]->done) {
				$nodes[$page]->chain($nodes);
				$body[] = $nodes[$page]->toString($nodes, 1, $nodes[$page]->parent_id);
			}
		}
		$body[] = '</ul>';
	}
	
	$body[] = '<hr />';
	$body[] = '<p>Total: ' . count($pages) . ' page(s) on this site.</p>';

	
	$retval['body'] = implode("\n", $body) . "\n";
	return $retval;
}

class MapNode
{
	var $page;
	var $is_page;
	var $link;
	var $id;
	var $rels;
	var $parent_id = 0;
	var $done;
	var $hide_pattern;

	function MapNode($page, $reverse = FALSE)
	{
		global $non_list;
		static $script, $_hide_pattern, $id = 0;

		if (! isset($script)) {
			$script = get_script_uri();
			$_hide_pattern = '/' . $non_list . '/';
		}

		$this->page    = $page;
		$this->is_page = is_page($page);
		$this->cache   = CACHE_DIR . encode($page);
		$this->done    = ! $this->is_page;
		$this->link    = make_pagelink($page);
		$this->id      = ++$id;
		$this->hide_pattern = & $_hide_pattern;

		$this->rels = $reverse ? $this->ref() : $this->rel();
		$mark       = $reverse ? '' : '<sup>+</sup>';
		$this->mark = '<a id="rel_' . $this->id . '" href="' . $script .
			'?plugin=map&amp;refer=' . rawurlencode($this->page) . '">' .
			$mark . '</a>';
	}

	function hide(& $pages)
	{
		if (! PLUGIN_MAP_SHOW_HIDDEN) {
			$pages = array_diff($pages, preg_grep($this->hide_pattern, $pages));
		}
		return $pages;
	}

	function ref()
	{
		$refs = array();
		$file = $this->cache . '.ref';
		if (file_exists($file)) {
			foreach (file($file) as $line) {
				$ref = explode("\t", $line);
				$refs[] = $ref[0];
			}
			$this->hide($refs);
			sort($refs, SORT_STRING);
		}
		return $refs;
	}

	function rel()
	{
		$rels = array();
		$file = $this->cache . '.rel';
		if (file_exists($file)) {
			$data = file($file);
			$rels = explode("\t", trim($data[0]));
			$this->hide($rels);
			sort($rels, SORT_STRING);
		}
		return $rels;
	}

	function chain(& $nodes)
	{
		if ($this->done) return;

		$this->done = TRUE;
		if ($this->parent_id == 0) $this->parent_id = -1;

		foreach ($this->rels as $page) {
			if (! isset($nodes[$page])) $nodes[$page] = & new MapNode($page);
			if ($nodes[$page]->parent_id == 0)
				$nodes[$page]->parent_id = $this->id;
		}
		foreach ($this->rels as $page) {
			$nodes[$page]->chain($nodes);
		}
	}

	function toString(& $nodes, $level = 1, $parent_id = -1)
	{
		$indent = str_repeat(' ', $level);

		if (! $this->is_page) {
			return $indent . '<li>' . $this->link . '</li>';
		} else if ($this->parent_id != $parent_id) {
			return $indent . '<li>' . $this->link .
				'<a href="#rel_' . $this->id . '">...</a></li>';
		} else if (empty($this->rels)) {
			return $indent . '<li>' . $this->mark . $this->link . '</li>';
		}

		$retval = array();
		$retval[] = $indent . '<li>' . $this->mark . $this->link;
		$childs = array();
		$level += 2;
		foreach ($this->rels as $page) {
			if (isset($nodes[$page]) && $this->parent_id != $nodes[$page]->id) {
				$childs[] = $nodes[$page]->toString($nodes, $level, $this->id);
			}
		}
		if (! empty($childs)) {
			$retval[] = $indent . ' <ul>';
			foreach(array_keys($childs) as $key){
				$retval[] = & $childs[$key];
			}
			$retval[] = $indent . ' </ul>';
		}
		$retval[] = $indent . '</li>';

		return implode("\n", $retval);
	}
}
?>
