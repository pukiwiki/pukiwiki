<?php
 /*
 
 PukiWiki BBS風プラグイン

 CopyRight 2002 OKAWARA,Satoshi
 http://www.dml.co.jp/~kawara/pukiwiki/pukiwiki.php
 kawara@dml.co.jp
 
 メッセージを変更したい場合はLANGUAGEファイルに下記の値を追加してからご使用ください
	$_btn_name = 'お名前';
	$_btn_article = '記事の投稿';
	$_btn_subject = '題名: ';

 ※$_btn_nameはcommentプラグインで既に設定されている場合があります
 
 投稿内容の自動メール転送機能をご使用になりたい場合は
 -投稿内容のメール自動配信
 -投稿内容のメール自動配信先
 を設定の上、ご使用ください。

 $Id: article.inc.php,v 1.12 2003/04/13 06:28:52 arino Exp $
 
 */

global $_mailto;

/////////////////////////////////////////////////
// テキストエリアのカラム数
define('article_COLS',70);
/////////////////////////////////////////////////
// テキストエリアの行数
define('article_ROWS',5);
/////////////////////////////////////////////////
// 名前テキストエリアのカラム数
define('NAME_COLS',24);
/////////////////////////////////////////////////
// 題名テキストエリアのカラム数
define('SUBJECT_COLS',60);
/////////////////////////////////////////////////
// 名前の挿入フォーマット
define('NAME_FORMAT','[[$name]]');
/////////////////////////////////////////////////
// 題名の挿入フォーマット
define('SUBJECT_FORMAT','**$subject');
/////////////////////////////////////////////////
// 題名が未記入の場合の表記 
define('NO_SUBJECT','無題');
/////////////////////////////////////////////////
// 挿入する位置 1:欄の前 0:欄の後
define('ARTICLE_INS',0);
/////////////////////////////////////////////////
// 書き込みの下に一行コメントを入れる 1:入れる 0:入れない
define('ARTICLE_COMMENT',1);
/////////////////////////////////////////////////
// 改行を自動的変換 1:する 0:しない
define('ARTICLE_AUTO_BR',1);

/////////////////////////////////////////////////
// 投稿内容のメール自動配信 1:する 0:しない
define('MAIL_AUTO_SEND',0);
/////////////////////////////////////////////////
// 投稿内容のメール送信時の送信者メールアドレス
define('MAIL_FROM','');
/////////////////////////////////////////////////
// 投稿内容のメール送信時の題名
define('MAIL_SUBJECT_PREFIX','[someone\'sPukiWiki]');
/////////////////////////////////////////////////
// 投稿内容のメール自動配信先
$_mailto = array (
	''
);

function plugin_article_init()
{
	if (LANG == 'ja') {
		$messages = array(
			'_btn_name'    => 'お名前',
			'_btn_article' => '記事の投稿',
			'_btn_subject' => '題名: '
		);
	}
	else {
		$messages = array(
			'_btn_name'    => 'Name: ',
			'_btn_article' => 'Submit',
			'_btn_subject' => 'Subject: '
		);
	}
  set_plugin_messages($messages);
}

function plugin_article_action()
{
	global $script,$post,$vars,$cols,$rows,$now;
	global $_title_collided,$_msg_collided,$_title_updated;
	global $_mailto;
	
	if ($post['msg'] == '') {
		return;
	}
	
	$postdata = '';
	$postdata_old  = get_source($post['refer']);
	$article_no = 0;
	
	if ($post['name'] != '') {
		$name = str_replace('$name',$post['name'],NAME_FORMAT);
	}
	
	$subject = str_replace('$subject',$post['subject'] == '' ? NO_SUBJECT : $post['subject'],SUBJECT_FORMAT);
	
	$article  = "$subject\n>$name ($now)~\n~\n";
	
	$msg = rtrim($post['msg']);
	if (ARTICLE_AUTO_BR){
		//改行の取り扱いはけっこう厄介。特にURLが絡んだときは…
		//コメント行、整形済み行には~をつけないように arino
		$msg = join("\n",preg_replace('/^(?!\/\/)(?!\s)(.*)$/','$1~',explode("\n",$msg)));
	}
	$article .= $msg;
	
	if (ARTICLE_COMMENT) {
		$article .= "\n\n#comment\n";
	}
	
	foreach($postdata_old as $line) {
		if (!ARTICLE_INS) {
			$postdata .= $line;
		}
		if (preg_match('/^#article/',$line)) {
			if ($article_no == $post['article_no'] && $post['msg'] != '') {
				$postdata .= "$article\n";
			}
			$article_no++;
		}
		if (ARTICLE_INS) {
			$postdata .= $line;
		}
	}
	
	$postdata_input = "$article\n";
	$body = '';
	
	if (md5(@join('',get_source($post['refer']))) != $post['digest']) {
		$title = $_title_collided;
		
		$body = "$_msg_collided\n";
		
		$s_refer = htmlspecialchars($post['refer']);
		$s_digest = htmlspecialchars($post['digest']);
		$s_postdata = htmlspecialchars($postdata_input);
		$body .= <<<EOD
<form action="$script?cmd=preview" method="post">
 <div>
  <input type="hidden" name="refer" value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols" id="textarea">$s_postdata</textarea><br />
 </div>
</form>
EOD;
	}
	else {
		page_write($post['refer'],trim($postdata));
		
		// 投稿内容のメール自動送信
		if (MAIL_AUTO_SEND) {
			$mailaddress = implode(',' ,$_mailto);
			$mailsubject = MAIL_SUBJECT_PREFIX.' '.str_replace('**','',$subject);
			if ($post['name']) {
				$mailsubject .= '/'.$post['name'];
			}
			$mailsubject = mb_encode_mimeheader($mailsubject);
			
			$mailbody = $post['msg'];
			$mailbody .= "\n\n---\n";
			$mailbody .= "投稿者: ".$post['name']." ($now)\n";
			$mailbody .= "投稿先: ".$post['refer']."\n";
			$mailbody .= "　 URL: ".$script.'?'.rawurlencode($post['refer'])."\n";
			$mailbody = mb_convert_encoding( $mailbody, "JIS" );
			
			$mailaddheader = "From: ".MAIL_FROM;
			
			mail($mailaddress, $mailsubject, $mailbody, $mailaddheader);
		}
		
		$title = $_title_updated;
	}
	$retvars['msg'] = $title;
	$retvars['body'] = $body;
	
	$post['page'] = $post['refer'];
	$vars['page'] = $post['refer'];
	
	return $retvars;
}
function plugin_article_convert()
{
	global $script,$vars,$digest;
	global $_btn_article,$_btn_name,$_btn_subject;
	static $numbers = array();
	
	if (!array_key_exists($vars['page'],$numbers))
	{
		$numbers[$vars['page']] = 0;
	}
	$article_no = $numbers[$vars['page']]++;
	
	$s_page = htmlspecialchars($vars['page']);
	$s_digest = htmlspecialchars($digest);
	$name_cols = NAME_COLS;
	$subject_cols = SUBJECT_COLS;
	$article_rows = article_ROWS;
	$article_cols = article_COLS;
	$string = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="article_no" value="$article_no" />
  <input type="hidden" name="plugin" value="article" />
  <input type="hidden" name="digest" value="$s_digest" />
  <input type="hidden" name="refer" value="$s_page" />
  $_btn_name <input type="text" name="name" size="$name_cols" /><br />
  $_btn_subject <input type="text" name="subject" size="$subject_cols" /><br />
  <textarea name="msg" rows="$article_rows" cols="$article_cols">\n</textarea><br />
  <input type="submit" name="article" value="$_btn_article" />
 </div>
</form>
EOD;
	
	return $string;
}
?>
