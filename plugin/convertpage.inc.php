<?php
/*

Last-Update:2002-12-03 rev.3

新書式に移行するプラグイン

usage:
http://..../pukiwiki.php?plugin=convertpage

なにをするか：
・./wiki/ディレクトリにバックアップファイル(ページ名.bak)を作ります。
・連続する行頭>を、最初のひとつを残して取り除きます。
・ネスト可能なブロック要素の次の行に空行以外の他要素が出現したときに、
  空行を間にはさみこんで分断します。
・行頭-/+に続いてチルダが出現したときに、スペースを挿入します。
・dlを::から:|に修正します。
・空行の前の行末チルダを取り除きます。

*/

define('CONVERTPAGE_LOGPAGE',':ConvertLog');

function plugin_convertpage_init()
{
	$messages = array('_convert_messages'=>array(
		'title_convertpage' => 'PukiWiki 書式コンバータ',
		'msg_invalidparam' => 'パラメータが不正です',
		'msg_convert' => 'ページ内容を変換(wiki/*.txt->wiki/*.bak)',
		'msg_revert' => '変更を元に戻す(wiki/*.bak -> wiki/*.txt)',
		'msg_clean' => 'バックアップを削除(wiki/*.bak)',
		'msg_adminpass' => '管理者パスワード',
		'err_alreadypage' => 'エラー:すでに '.make_link(CONVERTPAGE_LOGPAGE) . ' が存在します。',
		'err_alreadybak' => 'エラー:wiki/$1.bak ファイルがすでに存在します。',
		'err_makebak' => 'エラー:wiki/$1.bakファイルが作れません。',
		'btn_submit' => '実行',
	));
	set_plugin_messages($messages);
}

function plugin_convertpage_action()
{
	global $script,$post,$vars,$adminpass;
	global $_convert_messages;
	
	if (empty($vars['action']) or empty($post['adminpass']) or md5($post['adminpass']) != $adminpass)
	{
		$body = <<<EOD
<form method="POST" action="$script">
 <div>
  <input type="hidden" name="plugin" value="convertpage" />
  <input type="radio" name="action" value="convert" />
  {$_convert_messages['msg_convert']}<br />
  <input type="radio" name="action" value="revert" />
  {$_convert_messages['msg_revert']}<br />
  <input type="radio" name="action" value="clean" />
  {$_convert_messages['msg_clean']}<br />
  {$_convert_messages['msg_adminpass']}
  <input type="password" name="adminpass" size="20" value="" /><br />
  <input type="submit" value="{$_convert_messages['btn_submit']}" />
 </div>
</form>
EOD;
		return array(
			'msg'=>$_convert_messages['title_convertpage'],
			'body'=>$body
		);
	}
	else if ($vars['action'] == 'convert')
	{
		return convertpage_convert();
	}
	else if ($vars['action'] == 'revert')
	{
		return convertpage_revert();
	}
	else if ($vars['action'] == 'clean')
	{
		return convertpage_clean();
	}
	
	return array(
		'msg'=>$_convert_messages['title_convertpage'],
		'body'=>$_convert_messages['msg_invalidparam']
	);
}

//変換
function convertpage_convert()
{
	global $post,$vars,$whatsnew;
	global $_convert_messages;
	
	set_time_limit(0); // 時間切れ防止
	
	$pages = get_existpages();
	
	if (is_page(CONVERTPAGE_LOGPAGE))
		return array(
			'msg' =>$_convert_messages['title_convertpage'],
			'body'=>$_convert_messages['err_alreadypage']
		);
	
	// *.bakファイルが存在したら変換を中止する
	foreach ($pages as $page) {
		$file = get_filename($page);
		$bak = str_replace('.txt','.bak',$file);
		if (file_exists($bak)) {
			$body = str_replace('$1',$page,$_convert_messages['err_alreadybak']); // $1を置換
			return array(
				'msg' =>$_convert_messages['title_convertpage'],
				'body'=>$body
			);
		}
	}
	
	// *.bakファイルを作成する
	foreach ($pages as $page) {
		$file = get_filename($page);
		$bak = str_replace('.txt','.bak',$file);
		$stat = stat($file);
		if ($stat['size'] == 0)
			continue;
		
		if (!copy($file,$bak)) {
			$body = str_replace('$1',$page,$_convert_messages['err_makebak']); // $1を置換
			return array(
				'msg' =>$_convert_messages['title_convertpage'],
				'body'=>$body
			);
		}
	}
	$convert = array(); //更新したファイル
	
	// 変換
	foreach ($pages as $page)
	{
		$data = get_source($page);
		
		page_convert($page,$data,$convert);
		
		//ページを更新
		$time = get_filetime($page);
		unlink(get_filename($page));
		$page = strip_bracket($page);
		page_write($page,$data);
		touch(get_filename($page),$time);
	}
	
	// 結果
	$count = count($convert);
	$postdata = join('',get_source(CONVERTPAGE_LOGPAGE));
	$postdata .= $_convert_messages['title_convertpage']."\n\n";
	$postdata .= "修正したページ数:$count\n";
	if (count($convert) > 0) {
		$postdata .= "\n----\n修正したページは以下のとおりです。\n";
		$postdata .= join("\n",$convert);
	}
	
	file_write(DATA_DIR,CONVERTPAGE_LOGPAGE,$postdata);
	
	$vars['page'] = CONVERTPAGE_LOGPAGE;
	return array('msg' =>'','body'=>'');
}
function page_convert($page,&$data,&$convert)
{
	$bq = $last_bq = 0;
	$block = $last_block = '';
	$result = array();
	$modify = array();
	
	foreach ($data as $line)
	{
		//行頭書式をチェック
		$head = substr($line,0,1);
		$block = '';
		if (strpos('-+:>',$head) !== FALSE) { //次の行を食うブロック
			$block = $head;
		}
		
		//ネスト可能なブロック要素の直後の行かどうか
		if (
			$last_block != '' and               //前の行が"次の行を食うブロック要素で
			$block != $last_block and           //前の行と現在行の種類が違って
			($line != "\n" and $line != "\r\n") //現在行が空行でない場合
		)
		{
			$result[] = "\n"; //空行をはさむ
			$modify['nest'] = '';
		}
		
		//行頭+/-の直後のチルダをスペースでエスケープ
		if (preg_match("/^([\-\+]{1,3})(~.*)$/", $line, $matches)) { //マッチしなかったら無視
			$line = "{$matches[1]} {$matches[2]}\n";
			$modify['tilde'] = '--modify (+/-)...~. ';
		}
		
		//ブロッククオートの修正
		if ($head == '>' and preg_match("/^(>{1,3})(.*)$/",$line,$matches)) { //マッチしなかったら無視
			$bq = strlen($matches[1]);
			if ($bq == $last_bq) {
				$line = "{$matches[2]}\n";
				$modify['bq'] = '--modify blockquote.';
			}
		}
		else {
			$bq = 0;
		}
		
		//定義リストの修正
		if ($head == ':' and preg_match("/^:([^:]+):(.*)/",$line,$matches)) { //マッチしなかったら無視
			$line = ":{$matches[1]}|{$matches[2]}\n";
			$modify['dl'] = '--modify dl.';
		}
		
		$result[] = $line;

		$last_bq = $bq;
		$last_block = $block;
	}
	if (count($modify)) {
		$convert[] = "-[[$page]]\n".join("\n",$modify);
	}
	
	$data = join('',$result);
	//空行前のチルダを削除
	$data = preg_replace("/~(\n\n)/",'$1',$data);
}
?>