<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: source.inc.php,v 1.4.2.2 2004/07/31 03:15:07 henoheno Exp $
//
// ページソースを表示

function plugin_source_init()
{
	$messages = array(
		'_source_messages'=>array(
			'msg_title' => '$1のソース',
			'msg_notfound' => '$1が見つかりません',
			'err_notfound' => 'ページのソースを表示できません。'
		)
	);
	set_plugin_messages($messages);
}

function plugin_source_action()
{
	global $vars;
	global $_source_messages;

	$vars['refer'] = $vars['page'];

	if (!is_page($vars['page']))
	{
		return array(
			'msg'=>$_source_messages['msg_notfound'],
			'body'=>$_source_messages['err_notfound']
		);
	}
	return array(
		'msg'=>$_source_messages['msg_title'],
		'body' =>
			'<pre id="source">'.
			htmlspecialchars(join('',get_source($vars['page']))).
			'</pre>'
	);
}
?>
