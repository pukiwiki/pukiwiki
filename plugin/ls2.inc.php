<?php
/*
$Id: ls2.inc.php,v 1.4 2003/02/26 08:54:23 panda Exp $

*プラグイン ls2
配下のページの見出し(*,**,***)の一覧を表示する

*Usage
 #ls2(パターン[,パラメータ])

*パラメータ
-パターン(最初に指定)~
省略するときもカンマが必要
-title~
 見出しの一覧を表示する
-include~
 インクルードしているページの見出しを再帰的に列挙する
-link~
 actionプラグインを呼び出すリンクを表示
-reverse~
 ページの並び順を反転し、降順にする

*/

//見出しアンカーの書式
define('LS2_CONTENT_HEAD','#content_1_');

//見出しアンカーの開始番号
define('LS2_ANCHOR_ORIGIN',1);

function plugin_ls2_init() {
	$messages = array('_ls2_messages'=>array(
		'err_nopages' => '<p>\'$1\' には、下位層のページがありません。</p>',
		'msg_title' => '\'$1\'で始まるページの一覧',
		'msg_go' => '<span class="small">...</span>',
	));
  set_plugin_messages($messages);
}
function plugin_ls2_action() {
	global $vars;
	global $_ls2_messages;
	
	$params = array();
	foreach (array('title','include','reverse') as $key) {
		$params[$key] = array_key_exists($key,$vars);
	}
	$prefix = array_key_exists('prefix',$vars) ? $vars['prefix'] : '';
	$body = ls2_show_lists($prefix,$params);
	
	return array(
		'body'=>$body,
		'msg'=>str_replace('$1',htmlspecialchars($prefix),$_ls2_messages['msg_title'])
	);
}

function plugin_ls2_convert()
{
	global $script,$vars;
	global $_ls2_messages;

	$prefix = '';
	if (func_num_args()) {
		$args = func_get_args();
		$prefix = array_shift($args);
	}
	else {
		$args = array();
	}
	if ($prefix == '') {
		$prefix = strip_bracket($vars['page']).'/';
	}

	$params = array('link'=>FALSE,'title'=>FALSE,'include'=>FALSE,'reverse'=>FALSE,'_args'=>array(),'_done'=>FALSE);
	array_walk($args, 'ls2_check_arg', &$params);
	$title = (count($params['_args']) > 0) ?
		join(',', $params['_args']) :
		str_replace('$1',htmlspecialchars($prefix),$_ls2_messages['msg_title']);

	if ($params['link']) {
		$tmp = array();
		$tmp[] = 'plugin=ls2&amp;prefix='.$prefix;
		if (isset($params['title'])) {
			$tmp[] = 'title=1';
		}
		if (isset($params['include'])) {
			$tmp[] = 'include=1';
		}
		return '<p><a href="'.$script.'?'.join('&amp;',$tmp).'">'.$title.'</a></p>'."\n";
	}
	return ls2_show_lists($prefix,$params);
}
function ls2_show_lists($prefix,&$params)
{
	global $_ls2_messages;
	
	$pages = array();
	foreach (get_existpages() as $_page) {
		if (strpos($_page,$prefix) === 0) {
			$pages[] = $_page;
		}
	}
	natcasesort($pages);
	
	if ($params['reverse']) {
		$pages = array_reverse($pages);
	}
	foreach ($pages as $page) {
		$params["page_$page"] = 0;
	}
	if (count($pages) == 0) {
		return str_replace('$1',htmlspecialchars($prefix),$_ls2_messages['err_nopages']);
	}
	
	$params['result'] = array();
	$params['saved'] = array();
	foreach ($pages as $page) {
		ls2_get_headings($page,$params,1);
	}
	return join("\n",$params['result']).join("\n",$params['saved']);
}

function ls2_get_headings($page,&$params,$level,$include = FALSE)
{
	global $script, $user_rules;
	global $_ls2_messages;
	static $_ls2_anchor = 0;
	
	$note_rules = '/\(\(((?:(?!\)\)).)*)\)\)/';
	$is_done = (isset($params["page_$page"]) and $params["page_$page"] > 0); //ページが表示済みのときTrue
	
	if (!$is_done) {
		$params["page_$page"] = ++$_ls2_anchor;
	}
	
	$name = strip_bracket($page);
	$title = $name.' '.get_pg_passage($page,FALSE);
	$href = $script.'?cmd=read&amp;page='.rawurlencode($page);
	
	ls2_list_push($params,$level);
	$ret = $include ? '<li>include ' : '<li>';
	if ($params['title'] and $is_done) {
		$ret .= "<a href=\"$href\" title=\"$title\">$name</a> ";
		$ret .= "<a href=\"#list_{$params["page_$page"]}\"><sup>&uarr;</sup></a>";
		array_push($params['result'],$ret);
		return;
	}
	else {
		$ret .= "<a id=\"list_{$params["page_$page"]}\" href=\"$href\" title=\"$title\">$name</a>";
		array_push($params['result'],$ret);
	}
	
	$anchor = LS2_ANCHOR_ORIGIN;
	foreach (get_source($page) as $line) {
		if ($params['title'] and preg_match('/^(\*+)\s*(.*)$/',$line,$matches)) {
			$special = inline2(preg_replace($note_rules,'',htmlspecialchars($matches[2])));
			ls2_list_push($params,$level + strlen($matches[1]));
			array_push($params['result'], '<li>'.$special
				.'<a href="'.$href.LS2_CONTENT_HEAD.$anchor.'">'.$_ls2_messages['msg_go'].'</a>'
			);
			$anchor++;
		}
		else if ($params['include'] and preg_match('/^#include\((.+)\)/',$line,$matches) and is_page($matches[1])) {
			ls2_get_headings($matches[1],$params,$level + 1,TRUE);
		}
	}
}
//リスト構造を構築する
function ls2_list_push(&$params,$level)
{
	global $_ul_left_margin, $_ul_margin, $_list_pad_str;
	
	$result =& $params['result'];
	$saved  =& $params['saved'];
	$cont   = TRUE;
	$open   = "<ul%s>";
	$close  = '</li></ul>';
	
	while (count($saved) > $level or
		(count($saved) > 0 and $saved[0] != $close)) {
		array_push($result, array_shift($saved));
	}
	
	$margin = $level - count($saved);
	
	while (count($saved) < ($level - 1)) {
		array_unshift($saved, ''); //count($saved)を増やすためのdummy
	}
	
	if (count($saved) < $level) {
		$cont = FALSE;
		array_unshift($saved, $close);
		
		$left = $margin * $_ul_margin;
		if ($level == $margin) {
			$left += $_ul_left_margin;
		}
		$str = sprintf($_list_pad_str, $level, $left, $left);
		array_push($result, sprintf($open, $str));
	}
	if ($cont) {
		array_push($result, '</li>');
	}
}
//オプションを解析する
function ls2_check_arg($val, $key, &$params)
{
	if ($val == '') {
		$params['_done'] = TRUE;
		return;
	}
	if (!$params['_done']) {
		foreach (array_keys($params) as $key) {
			if (strpos($key, strtolower($val)) === 0) {
				$params[$key] = TRUE;
				return;
			}
		}
		$params['_done'] = TRUE;
	}
	$params['_args'][] = $val;
}
?>
