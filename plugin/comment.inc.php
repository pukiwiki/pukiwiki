<?php
// $Id: comment.inc.php,v 1.11 2003/01/31 01:49:35 panda Exp $

global $name_cols, $comment_cols, $msg_format, $name_format;
global $msg_format, $now_format, $comment_format;
global $comment_ins, $comment_mail, $comment_no;

/////////////////////////////////////////////////
// コメントの名前テキストエリアのカラム数
$name_cols = 15;
/////////////////////////////////////////////////
// コメントのテキストエリアのカラム数
$comment_cols = 70;
/////////////////////////////////////////////////
// コメントの挿入フォーマット
$name_format = '[[$name]]';
$msg_format = '$msg';
$now_format = 'SIZE(10){$now}';
/////////////////////////////////////////////////
// コメントの挿入フォーマット(コメント内容)
$comment_format = '$msg -- $name $now';
/////////////////////////////////////////////////
// コメントを挿入する位置 1:欄の前 0:欄の後
$comment_ins = 1;
/////////////////////////////////////////////////
// コメントが投稿された場合、内容をメールで送る先
$comment_mail = FALSE;

// initialize
$comment_no = 0;

function plugin_comment_action()
{
	global $script,$vars,$post,$now;
	global $name_format,$msg_format,$now_format,$comment_format,$comment_ins;
	global $_title_updated;
	global $_msg_comment_collided,$_title_comment_collided;
		
	$_comment_format = $comment_format;
	if ($post['nodate']=='1') {
		$_comment_format = str_replace('$now','',$_comment_format);
	}
	if ($post['msg']=='') {
		$retvars['msg'] = $name;
		$post['page'] = $post['refer'];
		$vars['page'] = $post['refer'];
		$retvars['body'] = convert_html(get_source($post['refer']));
		return $retvars;
	}
	if ($post['msg']) {
		$post['msg'] = preg_replace("/\n/",'',$post['msg']);
		
		$postdata = '';
		$postdata_old  = get_source($post['refer']);
		$comment_no = 0;
		
		if ($post['name'])
			$name = str_replace('$name',$post['name'],$name_format);
		
		if ($post['msg']) {
			$head = '';
			if (preg_match('/^(-{1,2})(.*)/',$post['msg'],$match)) {
				$head = $match[1];
				$post['msg'] = $match[2];
			}
			
			$comment = str_replace('$msg',str_replace('$msg',$post["msg"],$msg_format),$_comment_format);
			$comment = str_replace('$name',$name,$comment);
			$comment = str_replace('$now',str_replace('$now',$now,$now_format),$comment);
			$comment = $head.$comment;
		}
		
		foreach($postdata_old as $line) {
			if (!$comment_ins) {
				$postdata .= $line;
			}
			if (preg_match('/^#comment/',$line)) {
				if ($comment_no == $post['comment_no'] and $post['msg'] != '') {
					$postdata = rtrim($postdata)."\n-$comment\n";
					if ($comment_ins) {
						$postdata .= "\n";
					}
				}
				$comment_no++;
			}
			if ($comment_ins) {
				$postdata .= $line;
			}
		}
		
		$postdata_input = "-$comment\n";
	}
	
	$title = $_title_updated;
	$body = '';
	if (md5(@join('',get_source($post['refer']))) != $post['digest']) {
		$title = $_title_comment_collided;
		$body = $_msg_comment_collided . make_link($post['refer']);
	}
	
	page_write($post['refer'],$postdata);
	
	$retvars['msg'] = $title;
	$retvars['body'] = $body;
	
	$post['page'] = $vars['page'] = $post['refer'];
	
	return $retvars;
}
function plugin_comment_convert()
{
	global $script,$vars,$name_cols,$comment_cols,$digest;
	global $_btn_comment,$_btn_name,$_msg_comment,$vars;
	static $comment_no = 0;
	
	$options = func_num_args() ? func_get_args() : array();
	
	$nametags = "$_btn_name<input type=\"text\" name=\"name\" size=\"$name_cols\" />\n";
	if (in_array('noname',$options)) {
		$nametags = $_msg_comment;
	}
	
	$nodate = (in_array('nodate',$options)) ? '1' : '0';
	
	$s_comment_no = htmlspecialchars($comment_no);
	$s_page = htmlspecialchars($vars["page"]);
	$s_digest = htmlspecialchars($digest);
	$s_comment_cols = htmlspecialchars($comment_cols);
	$string = <<<EOD
<br />
<form action="$script" method="post">
 <div>
  <input type="hidden" name="comment_no" value="$s_comment_no" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="plugin" value="comment" />
  <input type="hidden" name="nodate" value="$nodate" />
  <input type="hidden" name="digest" value="$s_digest" />
  $nametags
  <input type="text" name="msg" size="$s_comment_cols" />
  <input type="submit" name="comment" value="$_btn_comment" />
 </div>
</form>
EOD;
	
	$comment_no++;
	
	return $string;
}
?>
