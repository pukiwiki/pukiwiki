<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: update_entities.inc.php,v 1.3 2004/07/18 14:11:37 henoheno Exp $
//

// DTDの場所
define('W3C_XHTML_DTD_LOCATION','http://www.w3.org/TR/xhtml1/DTD/');

// メッセージ設定
function plugin_update_entities_init()
{
	$messages = array(
		'_entities_messages'=>array(
			'title_update'  => 'キャッシュ更新',
			'msg_adminpass' => '管理者パスワード',
			'btn_submit'    => '実行',
			'msg_done'      => 'キャッシュの更新が完了しました。',
			'msg_usage'     => '
* 処理内容

:文字実体参照にマッチする正規表現パターンのキャッシュを更新|
PHPの持つテーブルおよびW3CのDTDをスキャンして、キャッシュに記録します。

* 処理対象
「COLOR(red){not found.}」が表示されたファイルは処理されません。
-%s

* 実行
管理者パスワードを入力して、[実行]ボタンをクリックしてください。
'
		)
	);
	set_plugin_messages($messages);
}

function plugin_update_entities_action()
{
	global $script, $vars;
	global $_entities_messages;
	
	if (empty($vars['action']) or empty($vars['adminpass']) or ! pkwk_login($vars['adminpass']))
	{
		$items = plugin_update_entities_create();
		$body = convert_html(sprintf($_entities_messages['msg_usage'],join("\n-",$items)));
		$body .= <<<EOD
<form method="POST" action="$script">
 <div>
  <input type="hidden" name="plugin" value="update_entities" />
  <input type="hidden" name="action" value="update" />
  {$_entities_messages['msg_adminpass']}
  <input type="password" name="adminpass" size="20" value="" />
  <input type="submit" value="{$_entities_messages['btn_submit']}" />
 </div>
</form>
EOD;
		return array(
			'msg'=>$_entities_messages['title_update'],
			'body'=>$body
		);
	}
	else if ($vars['action'] == 'update')
	{
		plugin_update_entities_create(TRUE);
		return array(
			'msg'=>$_entities_messages['title_update'],
			'body'=>$_entities_messages['msg_done']
		);
	}
	
	return array(
		'msg'=>$_entities_messages['title_update'],
		'body'=>$_entities_messages['err_invalid']
	);
}

function plugin_update_entities_create($do=FALSE)
{
	$files = array('xhtml-lat1.ent','xhtml-special.ent','xhtml-symbol.ent');
	$entities = strtr(
		array_values(get_html_translation_table(HTML_ENTITIES)),
		array('&'=>'',';'=>'')
	);
	$items = array('php:html_translation_table');
	foreach ($files as $file)
	{
		$source = file(W3C_XHTML_DTD_LOCATION.$file);
//			or die_message('cannot receive '.W3C_XHTML_DTD_LOCATION.$file.'.');
		if (!is_array($source))
		{
			$items[] = "w3c:$file COLOR(red):not found.";
			continue;
		}
		$items[] = "w3c:$file";
		if (preg_match_all('/<!ENTITY\s+([A-Za-z0-9]+)/',
			join('',$source),$matches,PREG_PATTERN_ORDER))
		{
			$entities = array_merge($entities,$matches[1]);
		}
	}
	if (!$do)
	{
		return $items;
	}
	$entities = array_unique($entities);
	sort($entities,SORT_STRING);
	$min = 999;
	$max = 0;
	foreach ($entities as $entity)
	{
		$len = strlen($entity);
		$max = max($max,$len);
		$min = min($min,$len);
	}
	
	$pattern = "(?=[a-zA-Z0-9]\{$min,$max})".get_autolink_pattern_sub($entities,0,count($entities),0);
	$fp = fopen(CACHE_DIR.'entities.dat','w')
		or die_message('cannot write file '.CAHCE_DIR.'entities.dat<br />maybe permission is not writable or filename is too long');
	fwrite($fp,$pattern);
	fclose($fp);
	
	return $items;
}
?>
