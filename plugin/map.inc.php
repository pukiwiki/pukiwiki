<?php
// $Id: map.inc.php,v 1.3 2002/12/05 05:02:27 panda Exp $
/*
Last-Update:2002-11-29 rev.12
 http://home.arino.jp/?map.inc.php

プラグイン map

サイトマップ(のようなもの)を表示

Usage : http://.../pukiwiki.php?plugin=map

パラメータ

&refer=ページ名
 起点となるページを指定

このバージョンではcmd=reloadを指定しても動作は変わりません。
%%&cmd=reload%%
%% キャッシュを破棄し、ページを解析しなおす%%

&reverse=true
 あるページがどこからリンクされているかを一覧。

&url=true
 ハイパーリンク(http:/ftp:/mail:)を表示する。
*/

function plugin_map_action() {
	global $script,$vars,$defaultpage,$whatsnew;
	global $Pages,$Anchor,$Level,$Dirty,$retval;

	//reverse=true?
	$reverse = array_key_exists('reverse',$vars);

	//基準となるページ名を決定
	$refer = array_key_exists('refer',$vars) ? $vars['refer'] : '';

	//$retval['msg']の$1を置換させるために$vars['refer']を書き換えている。
	if ($refer == '') { $vars['refer'] = $refer = $defaultpage; }

	//戻り値を初期化
	if ($reverse) {
		$retval['msg'] = 'Relation map (link from)';
	} else {
		$retval['msg'] = 'Relation map, from $1';
	}
	$retval['body'] = '';

	//ページを列挙
	$pages = get_existpages();

	//RecentChangesは除く。
	$n = array_search($whatsnew,$pages);
	if ($n !== FALSE) { unset($pages[$n]); }

	//現存するページの数
	$count = count($pages);

	if ($count == 0) {
		$retval['body'] = 'no pages.';
		return $retval;
	}

	//ページ数
	$retval['body'] .= "<p>\ntotal: $count page(s) on this site.\n</p>\n";

	//get_linkを通すことで、オブジェクトの配列とする
	$obj = new link_wrapper($refer);
	$pages = $obj->get_link(join(' ',$pages));
	
	//ページの属性を取得
	$anchor = 0;
	$_pages = array();
	foreach (array_keys($pages) as $key) {
		$_obj =& $pages[$key];
		$_obj->_exist = TRUE;
		$_obj->_ctime = filemtime(get_filename(encode($_obj->name)));
		$_obj->_anchor = ++$anchor;
		$_obj->_link = $_obj->toString($refer);
		$_obj->_level = 0;

		$_pages[$_obj->name] =& $pages[$key];
	}
	$pages = $_pages; unset($_pages);
	
	//ページ内のリンクを列挙
	foreach (array_keys($pages) as $page) {
		$obj->page = $page; // link_wrapperの使いまわし
		$data = $obj->get_link(join('',preg_grep('/^[^\s#]/',get_source($page))));
		$pages[$page]->_count = count($data);
		foreach ($data as $link) {
			if ($link->type == 'WikiName' or $link->type == 'BracketName') {
				if (array_key_exists($link->name,$pages)) {
					$pages[$page]->_links['WikiName'][$link->name] =& $pages[$link->name];
				} else {
					$link->_exist = FALSE;
					$link->_link = $link->toString();
					$pages[$page]->_links['WikiName'][$link->name] = $link;
				}
			} else {
				$link->is_image = FALSE; //おい
				$link->_link = $link->toString();
				$pages[$page]->_links[$link->type][$link->name] = $link;
			}
		}
	}

	//並べ替え
//	uksort($Pages,'myWikiNameComp');

	if ($reverse) { //逆方向
		//列挙
		foreach (array_keys($pages) as $page)
			foreach ($pages[$page]->_links['WikiName'] as $from)
				if ($page != $from->name) { $pages[$from->name]->_from[] = $page; }

		foreach (array_keys($pages) as $page)
			usort($pages[$page]->_from);

		$retval['body'] .= showReverse($pages,TRUE);
		$retval['body'] .= "<hr />\n<p>no link from anywhere in this site.</p>\n";
		$retval['body'] .= showReverse($pages,FALSE);
	} else { //順方向
		//整形
		$retval['body'] .= "<ul>\n".showNode($pages,$refer)."</ul>\n";

		//not related
		$link_refer = '<a href="$script?'.rawurlencode($refer).'">'.htmlspecialchars(strip_bracket($refer)).'</a>';
		$retval['body'] .= "<hr />\n<p>not related from $link_refer</p>\n";
		foreach (array_keys($pages) as $page) {
			if ($pages[$page]->_exist and $pages[$page]->_level == 0) {
				$retval['body'] .= "<ul>\n" . showNode($pages,$page) . "</ul>\n";
			}
		}
	}

	//終了
	return $retval;
}
function showReverse(&$pages,$not) {
	$body = '';
	
	foreach (array_keys($pages) as $page) {
		if (!$pages[$page]->_exist) { continue; }
		if (count($pages[$page]->_from) xor $not) { continue; }
		$body .= ' <li>'.$pages[$page]->_link;
		if (count($pages[$page]->_from)) {
			if ($not) { $body .= ' is link from'; }
			$body .= "\n  <ul>\n";
			foreach ($pages[$page]->_from as $from)
				$body .= "   <li>{$pages[$from]->_link}</li>\n";
			$body .= '  </ul>';
		}
		$body .= "</li>\n";
	}
	return ($body == '') ? '' : "<ul>\n$body</ul>\n";
}

//ツリーを生成し、印字する
function showNode(&$pages,$page,$level = 0) {
	global $script,$vars;
	$body = '';
	
	if ($pages[$page]->_level != $level) { // まだ表示する段階ではない
		$body .= ' <li>'.$pages[$page]->_link;
		if ($pages[$page]->_count > 0)
			$body .= ' <a href="#rel'.$pages[$page]->_anchor.'" title="'.$pages[$page]->special.'">...</a>';
		$body .= "</li>\n";
		return $body;
	}
	$pages[$page]->_level = -1; //表示済み
	$body .= ' <li>';

	if ($pages[$page]->_count > 0) {
		$url = array_key_exists('url',$vars) ? '&amp;url=true' : '';
		$id = ($pages[$page]->_anchor == 0) ? '' : 'id="rel'.$pages[$page]->_anchor.'"';
		$body .= "<a $id href=\"$script?plugin=map$url&amp;refer={$pages[$page]->rawname}\" title=\"change refer\"><sup>+</sup></a>\n";
	}
	
	$body .= $pages[$page]->_link."\n";


	//リレーションの列挙
	if ($pages[$page]->_count > 0) {
		$_rel = showWikiNodes($pages,$page,$level).showHyperLinks($pages[$page]).showInterWikiName($pages[$page]);
		if ($_rel != '')
			$body .= "<ul>\n$_rel</ul>\n";
	}

	return $body."</li>\n";
}
//WikiName,BracketNameの出力
function showWikiNodes(&$pages,$page,$level) {
	if (!array_key_exists('WikiName',$pages[$page]->_links)) { return ''; }

	$body = '';
	$_level = $level + 1;
	
	foreach ($pages[$page]->_links['WikiName'] as $_obj)
		if ($pages[$_obj->name]->_level == 0)
			$pages[$_obj->name]->_level = $_level; //表示を予約
	
	foreach ($pages[$page]->_links['WikiName'] as $_obj) {
		if ($_obj->_exist)
			$body .= showNode($pages,$_obj->name,$_level);
		else
			$body .= " <li>{$_obj->_link}</li>";
	}
	return $body;
}
//HyperLinkを出力
function showHyperLinks(&$obj) {
	global $vars;

	if (!array_key_exists('url',$vars) or !array_key_exists('url',$obj->_links)) { return ''; }
	
	$body = '';
	
	foreach ($obj->_links['url'] as $_obj)
		$body .= " <li>{$_obj->_link}</li>\n";
	
	return $body;
}
//InterWikiNameを出力
function showInterWikiName(&$obj) {
	if (!array_key_exists('InterWikiName',$vars)) { return ''; }
	
	$body = '';
	
	foreach ($obj->_links['InterWikiName'] as $_obj)
		$body .= " <li>inter{$_obj->_link}</li>\n";
	
	return $body;
}

function myWikiNameComp($a,$b) {
	global $Pages;
	return strnatcasecmp($Pages[$a]['Char'],$Pages[$b]['Char']);
}

?>
