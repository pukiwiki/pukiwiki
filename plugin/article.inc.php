<?
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

 $Id: article.inc.php,v 1.4 2002/07/01 07:08:57 masui Exp $
 
 */

global $name_format, $subject_format, $no_subject, $_mailto;

/////////////////////////////////////////////////
// テキストエリアのカラム数
define("article_COLS",70);
/////////////////////////////////////////////////
// テキストエリアの行数
define("article_ROWS",5);
/////////////////////////////////////////////////
// 名前テキストエリアのカラム数
define("NAME_COLS",24);
/////////////////////////////////////////////////
// 題名テキストエリアのカラム数
define("SUBJECT_COLS",60);
/////////////////////////////////////////////////
// 名前の挿入フォーマット
$name_format = '[[$name]]';
/////////////////////////////////////////////////
// 題名の挿入フォーマット
$subject_format = '**$subject';
/////////////////////////////////////////////////
// 題名が未記入の場合の表記 
$no_subject = '無題';
/////////////////////////////////////////////////
// 挿入する位置 1:欄の前 0:欄の後
define("ARTICLE_INS",0);
/////////////////////////////////////////////////
// 書き込みの下に一行コメントを入れる 1:入れる 0:入れない
define("ARTICLE_COMMENT",1);
/////////////////////////////////////////////////
// 改行を自動的変換 1:する 0:しない
define("ARTICLE_AUTO_BR",1);

/////////////////////////////////////////////////
// 投稿内容のメール自動配信 1:する 0:しない
define("MAIL_AUTO_SEND",0);
/////////////////////////////////////////////////
// 投稿内容のメール送信時の送信者メールアドレス
define("MAIL_FROM",'');
/////////////////////////////////////////////////
// 投稿内容のメール送信時の題名
define("MAIL_SUBJECT_PREFIX",'[someone\'sPukiWiki]');
/////////////////////////////////////////////////
// 投稿内容のメール自動配信先
$_mailto = array (
	''
);


function plugin_article_init()
{
  $_plugin_article_messages = array(
    '_btn_name' => 'お名前',
    '_btn_article' => '記事の投稿',
    '_btn_subject' => '題名: '
    );
  set_plugin_messages($_plugin_article_messages);
}

function plugin_article_action()
{
	global $post,$vars,$script,$cols,$rows,$del_backup,$do_backup,$now;
	global $name_format, $subject_format, $no_subject, $name, $subject, $article;
	global $_title_collided,$_msg_collided,$_title_updated;
	global $_mailto;
	
	if($post["msg"])
	{
		$postdata = "";
		$postdata_old  = file(get_filename(encode($post["refer"])));
		$article_no = 0;

		if($post[name])
		{
			$name = str_replace('$name',$post[name],$name_format);
		}
		if($post[subject])
		{
			$subject = str_replace('$subject',$post[subject],$subject_format);
		} else {
			$subject = str_replace('$subject',$no_subject,$subject_format);
		}

		$article  = $subject."\n>";
		$article .= $name." (".$now.")\n>~\n";

		if(ARTICLE_AUTO_BR){
			//改行の取り扱いはけっこう厄介。特にURLが絡んだときは…
			$article_body = $post[msg];
			$article_body = str_replace("\n","\n>~\n",$article_body);
			$article_body = preg_replace("/\n\n/","\n",$article_body);
			$article .= $article_body;
		} else {
			$article .= ">".$post[msg];
		}

		if(ARTICLE_COMMENT){
			$article .= "\n\n#comment\n";
		}

		foreach($postdata_old as $line)
		{
			if(!ARTICLE_INS) $postdata .= $line;
			if(preg_match("/^#article$/",$line))
			{
				if($article_no == $post["article_no"] && $post[msg]!="")
				{
					$postdata .= "$article\n";
				}
				$article_no++;
			}
			if(ARTICLE_INS) $postdata .= $line;
		}

		$postdata_input = "$article\n";
	}
	else
		return;

	if(md5(@join("",@file(get_filename(encode($post["refer"]))))) != $post["digest"])
	{
		$title = $_title_collided;

		$body = "$_msg_collided\n";

		$body .= "<form action=\"$script?cmd=preview\" method=\"post\">\n"
			."<div>\n"
			."<input type=\"hidden\" name=\"refer\" value=\"".$post["refer"]."\" />\n"
			."<input type=\"hidden\" name=\"digest\" value=\"".$post["digest"]."\" />\n"
			."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\" id=\"textarea\">$postdata_input</textarea><br />\n"
			."</div>\n"
			."</form>\n";
	}
	else
	{
		$postdata = user_rules_str($postdata);

		// 差分ファイルの作成
		if(is_page($post["refer"]))
			$oldpostdata = join("",file(get_filename(encode($post["refer"]))));
		else
			$oldpostdata = "\n";
		if($postdata)
			$diffdata = do_diff($oldpostdata,$postdata);
		file_write(DIFF_DIR,$post["refer"],$diffdata);

		// バックアップの作成
		if(is_page($post["refer"]))
			$oldposttime = filemtime(get_filename(encode($post["refer"])));
		else
			$oldposttime = time();

		// 編集内容が何も書かれていないとバックアップも削除する?しないですよね。
		if(!$postdata && $del_backup)
			backup_delete(BACKUP_DIR.encode($post["refer"]).".txt");
		else if($do_backup && is_page($post["refer"]))
			make_backup(encode($post["refer"]).".txt",$oldpostdata,$oldposttime);

		// ファイルの書き込み
		file_write(DATA_DIR,$post["refer"],$postdata);

		// 投稿内容のメール自動送信
		if(MAIL_AUTO_SEND){
			$mailaddress = implode(',' ,$_mailto);
			$mailsubject = MAIL_SUBJECT_PREFIX." ".str_replace("**","",$subject);
			if($post["name"]){
				$mailsubject .= '/'.$post["name"];
			}
			$mailsubject = mb_encode_mimeheader($mailsubject);

			$mailbody = $post["msg"];
			$mailbody .= "\n\n---\n";
			$mailbody .= "投稿者: ".$post["name"]." ($now)\n";
			$mailbody .= "投稿先: ".$post["refer"]."\n";
			$mailbody .= "　 URL: ".$script.'?'.rawurlencode($post["refer"])."\n";
			$mailbody = mb_convert_encoding( $mailbody, "JIS" );

			$mailaddheader = "From: ".MAIL_FROM;

			mail($mailaddress, $mailsubject, $mailbody, $mailaddheader);
		}

		// is_pageのキャッシュをクリアする。
		is_page($post["refer"],true);

		$title = $_title_updated;
	}
	$retvars["msg"] = $title;
	$retvars["body"] = $body;

	$post["page"] = $post["refer"];
	$vars["page"] = $post["refer"];

	return $retvars;
}
function plugin_article_convert()
{
	global $script,$article_no,$vars,$digest;
	global $_btn_article,$_btn_name,$_btn_subject,$vars;

	if((arg_check("read")||$vars["cmd"] == ""||arg_check("unfreeze")||arg_check("freeze")||$vars["write"]||$vars["article"]))
		$button = "<input type=\"submit\" name=\"article\" value=\"$_btn_article\" />\n";

	$string = "<form action=\"$script\" method=\"post\">\n"
		 ."<div>\n"
		 ."<input type=\"hidden\" name=\"article_no\" value=\"$article_no\" />\n"
		 ."<input type=\"hidden\" name=\"refer\" value=\"$vars[page]\" />\n"
		 ."<input type=\"hidden\" name=\"plugin\" value=\"article\" />\n"
		 ."<input type=\"hidden\" name=\"digest\" value=\"$digest\" />\n"
		 ."$_btn_name<input type=\"text\" name=\"name\" size=\"".NAME_COLS."\" /><br />\n"
		 ."$_btn_subject<input type=\"text\" name=\"subject\" size=\"".SUBJECT_COLS."\" /><br />\n"
		 ."<textarea name=\"msg\" rows=\"".article_ROWS."\" cols=\"".article_COLS."\">\n</textarea><br />\n"
		 .$button
		 ."</div>\n"
		 ."</form>";

	$article_no++;

	return $string;
}
?>
