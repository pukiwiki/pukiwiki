<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: links.inc.php,v 1.15 2003/03/14 04:12:33 panda Exp $
//

// メッセージ設定
function plugin_links_init()
{
	$messages = array(
		'_links_messages'=>array(
			'title_update'  => 'キャッシュ更新',
			'msg_adminpass' => '管理者パスワード',
			'btn_submit'    => '実行',
			'msg_done'      => 'キャッシュの更新が完了しました。',
			'msg_usage'     => "
* 処理内容

:キャッシュを更新|
全てのページをスキャンし、あるページがどのページからリンクされているかを調査して、キャッシュに記録します。

* 注意
実行には数分かかる場合もあります。実行ボタンを押したあと、しばらくお待ちください。

* 実行
管理者パスワードを入力して、[実行]ボタンをクリックしてください。
"
		)
	);
	set_plugin_messages($messages);
}

function plugin_links_action()
{
	global $script,$post,$vars,$adminpass;
	global $_links_messages;
	global $whatsnew;
	
	if (empty($vars['action']) or empty($post['adminpass']) or md5($post['adminpass']) != $adminpass)
	{
		$body = convert_html($_links_messages['msg_usage']);
		$body .= <<<EOD
<form method="POST" action="$script">
 <div>
  <input type="hidden" name="plugin" value="links" />
  <input type="hidden" name="action" value="update" />
  {$_links_messages['msg_adminpass']}
  <input type="password" name="adminpass" size="20" value="" />
  <input type="submit" value="{$_links_messages['btn_submit']}" />
 </div>
</form>
EOD;
		return array(
			'msg'=>$_links_messages['title_update'],
			'body'=>$body
		);
	}
	else if ($vars['action'] == 'update')
	{
		error_reporting(E_ALL);
		links_init();
	
		return array(
			'msg'=>$_links_messages['title_update'],
			'body'=>$_links_messages['msg_done']
		);
	}
	
	return array(
		'msg'=>$_links_messages['title_update'],
		'body'=>$_links_messages['err_invalid']
	);
}
?>
