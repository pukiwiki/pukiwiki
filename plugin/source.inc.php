<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: source.inc.php,v 1.10 2004/02/29 07:05:26 arino Exp $
//
// ページソースを表示

function plugin_source_action()
{
	global $vars;
	global $_source_messages;
	
	$vars['refer'] = $vars['page'];
	
	if (!is_page($vars['page']) || !check_readable($vars['page'],false,false)))
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
