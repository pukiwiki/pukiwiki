<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: map.inc.php,v 1.10 2003/04/17 02:09:01 arino Exp $
//
/*
プラグイン map

サイトマップ(のようなもの)を表示

Usage : http://.../pukiwiki.php?plugin=map

パラメータ

&refer=ページ名
 起点となるページを指定

&reverse=true
 あるページがどこからリンクされているかを一覧。

*/

function plugin_map_action()
{
	global $vars,$whatsnew,$defaultpage;
	
	$reverse = array_key_exists('reverse',$vars);
	
	$refer = array_key_exists('refer',$vars) ? $vars['refer'] : '';
	
	if ($refer == '' or !is_page($refer))
	{
		$vars['refer'] = $refer = $defaultpage;
	}
	
	$retval['msg'] = $reverse ? 'Relation map (link from)' : 'Relation map, from $1';
	$retval['body'] = '';
	
	$pages = array_values(array_diff(get_existpages(),array($whatsnew)));
	
	$count = count($pages);
	
	if ($count == 0)
	{
		$retval['body'] = 'no pages.';
		return $retval;
	}
	
	// ページ数
	$retval['body'] .= "<p>\ntotal: $count page(s) on this site.\n</p>\n";
	
	// ツリー作成
	$nodes = array();
	foreach ($pages as $page)
	{
		$nodes[$page] = & new MapNode($page,$reverse);
	}
	
	if ($reverse)
	{
		$keys = array_keys($nodes);
		sort($keys);
		$alone = array();
		$retval['body'] .= "<ul>\n";
		foreach ($keys as $page)
		{
			if (count($nodes[$page]->rels))
			{
				$retval['body'] .= $nodes[$page]->toString($nodes,1,$nodes[$page]->parent_id);
			}
			else
			{
				$alone[] = $page;
			}
		}
		$retval['body'] .= "</ul>\n";
		if (count($alone))
		{
			$retval['body'] .= "<hr />\n<p>no link from anywhere in this site.</p>\n";
			$retval['body'] .= "<ul>\n";
			foreach ($alone as $page)
			{
				$retval['body'] .= $nodes[$page]->toString($nodes,1,$nodes[$page]->parent_id);
			}
			$retval['body'] .= "</ul>\n";
		}
	}
	else
	{
		$nodes[$refer]->chain($nodes);
		$retval['body'] .= "<ul>\n".$nodes[$refer]->toString($nodes)."</ul>\n";
		$retval['body'] .= "<hr /><p>not related from ".htmlspecialchars($refer)."</p>\n";
		$keys = array_keys($nodes);
		sort($keys);
		$retval['body'] .= "<ul>\n";
		foreach ($keys as $page)
		{
			if (!$nodes[$page]->done)
			{
				$nodes[$page]->chain($nodes);
				$retval['body'] .= $nodes[$page]->toString($nodes,1,$nodes[$page]->parent_id);
			}
		}
		$retval['body'] .= "</ul>\n";
	}
	// 終了
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
	
	function MapNode($page,$reverse=FALSE)
	{
		global $script;
		
		static $id = 0;
		
		$this->page = $page;
		$this->is_page = is_page($page);
		$this->cache = CACHE_DIR.encode($page);
		$this->done = !$this->is_page;
		$this->link = make_pagelink($page);
		$this->id = ++$id;

		$this->rels = $reverse ? $this->ref() : $this->rel();
		$mark = $reverse ? '' : '<sup>+</sup>';
		$this->mark = "<a id=\"rel_{$this->id}\" href=\"$script?plugin=map&amp;refer=".rawurlencode($this->page)."\">$mark</a>";
	}
	function ref()
	{
		$refs = array();
		if (file_exists($this->cache.'.ref'))
		{
			foreach (file($this->cache.'.ref') as $line)
			{
				$ref = explode("\t",$line);
				$refs[] = $ref[0];
			}
			sort($refs);
		}
		return $refs;
	}
	function rel()
	{
		$rels = array();
		if (file_exists($this->cache.'.rel'))
		{
			$file = file($this->cache.'.rel');
			$rels = explode("\t",trim($file[0]));
			sort($rels);
		}
		return $rels;
	}
	function chain(&$nodes)
	{
		if ($this->done)
		{
			return;
		}
		$this->done = TRUE;
		
		if ($this->parent_id == 0)
		{
			$this->parent_id = -1;
		}
		
		foreach ($this->rels as $page)
		{
			if (!array_key_exists($page,$nodes))
			{
				$nodes[$page] = &new MapNode($page);
			}
			if ($nodes[$page]->parent_id == 0)
			{
				$nodes[$page]->parent_id = $this->id;
			}
		}
		foreach ($this->rels as $page)
		{
			$nodes[$page]->chain($nodes);
		}
	}
	function toString(&$nodes,$level=1,$parent_id=-1)
	{
		$indent = str_repeat(' ',$level);
		
		if (!$this->is_page)
		{
			return $indent."<li>{$this->link}</li>\n";
		}
		else if ($this->parent_id != $parent_id)
		{
			return $indent."<li>{$this->link}<a href=\"#rel_{$this->id}\">...</a></li>\n";
		}
		$retval = $indent."<li>{$this->mark}{$this->link}\n";
		if (count($this->rels))
		{
			$childs = array();
			$level += 2;
			foreach ($this->rels as $page)
			{
				if ($this->parent_id != $nodes[$page]->id)
				{
					$childs[] = $nodes[$page]->toString($nodes,$level,$this->id);
				}
			}
			if (count($childs))
			{
				$retval .= $indent." <ul>\n".join('',$childs).$indent." </ul>\n";
			}
		}
		$retval .= $indent."</li>\n";
		
		return $retval;
	}
}
?>
