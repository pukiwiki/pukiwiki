<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: topicpath.inc.php,v 1.2 2004/08/04 11:21:13 henoheno Exp $
//
// topicpath plugin for PukiWiki
//   available under the GPL


//	defaultpageを一番最初に表示するかどうか。TRUE:表示する FALSE:表示しない.
define('PLUGIN_TOPICPATH_TOP_DISPLAY',TRUE);
//	$defaultpageに対するラベル
define('PLUGIN_TOPICPATH_TOP_LABEL','Top');
//	階層を区切るセパレータ
define('PLUGIN_TOPICPATH_TOP_SEPARATOR',' / ');
//	自分のページに対するリンクを表示するかどうか
define('PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY',TRUE);
//	自分のページに対してリンクするかどうか
define('PLUGIN_TOPICPATH_THIS_PAGE_LINK',TRUE);

function plugin_topicpath_convert()
{
	return '<div>'.plugin_topicpath_inline().'</div>';
}

function plugin_topicpath_inline()
{
	global $script,$vars,$defaultpage;
	
	$args = func_get_args();
	
	$page = $vars['page'];
	
	if ($page == $defaultpage) { return ''; }
	
	$topic_path = array();
	$parts = explode('/', $page);

	if (!PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY) { array_pop($parts); }

	$b_link = PLUGIN_TOPICPATH_THIS_PAGE_LINK;
	while (count($parts)) {
		$landing = join('/', $parts);
		$element = array_pop($parts);
		$topic_path[] = $b_link ? "<a href=\"$script?".rawurlencode($landing)."\">$element</a>" : htmlspecialchars($element);
		$b_link = TRUE;
	}
	if (PLUGIN_TOPICPATH_TOP_DISPLAY)
	{
		$topic_path[] = make_pagelink($defaultpage,PLUGIN_TOPICPATH_TOP_LABEL);
	}
	return join(PLUGIN_TOPICPATH_TOP_SEPARATOR, array_reverse($topic_path));
}
?>
