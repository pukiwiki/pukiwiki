<?php
// $Id: comment.inc.php,v 1.16 2003/04/13 06:28:52 arino Exp $

/////////////////////////////////////////////////
// コメントの名前テキストエリアのカラム数
define('COMMENT_NAME_COLS',15);
/////////////////////////////////////////////////
// コメントのテキストエリアのカラム数
define('COMMENT_COLS',70);
/////////////////////////////////////////////////
// コメントの挿入フォーマット
define('COMMENT_NAME_FORMAT','[[$name]]');
define('COMMENT_MSG_FORMAT','$msg');
define('COMMENT_NOW_FORMAT','SIZE(10){$now}');
/////////////////////////////////////////////////
// コメントの挿入フォーマット(コメント内容)
define('COMMENT_FORMAT',"\x08MSG\x08 -- \x08NAME\x08 \x08NOW\x08");
/////////////////////////////////////////////////
// コメントを挿入する位置 1:欄の前 0:欄の後
define('COMMENT_INS',1);
/////////////////////////////////////////////////
// コメントが投稿された場合、内容をメールで送る先
//define('COMMENT_MAIL',FALSE);

function plugin_comment_action()
{
	global $script,$vars,$post,$now;
	global $_title_updated;
	global $_msg_comment_collided,$_title_comment_collided;
	
	$post['msg'] = preg_replace("/\n/",'',$post['msg']);
	
	if ($post['msg'] == '') {
		return array('msg'=>'','body'=>'');
	}
	
	$comment_format = COMMENT_FORMAT;
	if ($post['nodate']=='1') {
		$comment_format = str_replace('$now','',$comment_format);
	}
	
	$postdata = '';
	$postdata_old  = get_source($post['refer']);
	$comment_no = 0;
	
	$name = '';
	if ($post['name'] != '') {
		$name = str_replace('$name',$post['name'],COMMENT_NAME_FORMAT);
	}
	
	$head = '';
	if (preg_match('/^(-{1,2})(.*)/',$post['msg'],$match)) {
		$head = $match[1];
		$post['msg'] = $match[2];
	}
	
	$comment = str_replace("\x08MSG\x08",str_replace('$msg',$post['msg'],COMMENT_MSG_FORMAT),$comment_format);
	$comment = str_replace("\x08NAME\x08",$name,$comment);
	$comment = str_replace("\x08NOW\x08",str_replace('$now',$now,COMMENT_NOW_FORMAT),$comment);
	$comment = $head.$comment;
	
	foreach($postdata_old as $line) {
		if (!COMMENT_INS) {
			$postdata .= $line;
		}
		if (preg_match('/^#comment/',$line)) {
			if ($comment_no == $post['comment_no'] and $post['msg'] != '') {
				$postdata = rtrim($postdata)."\n-$comment\n";
				if (COMMENT_INS) {
					$postdata .= "\n";
				}
			}
			$comment_no++;
		}
		if (COMMENT_INS) {
			$postdata .= $line;
		}
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
	global $script,$vars,$digest;
	global $_btn_comment,$_btn_name,$_msg_comment;
	static $numbers = array();
	
	if (!array_key_exists($vars['page'],$numbers))
	{
		$numbers[$vars['page']] = 0;
	}
	$comment_no = $numbers[$vars['page']]++;
	
	$options = func_num_args() ? func_get_args() : array();
	
	if (in_array('noname',$options)) {
		$nametags = $_msg_comment;
	}
	else {
		$nametags = $_btn_name.'<input type="text" name="name" size="'.COMMENT_NAME_COLS."\" />\n";
	}
	
	$nodate = in_array('nodate',$options) ? '1' : '0';
	
	$s_page = htmlspecialchars($vars['page']);
	$comment_cols = COMMENT_COLS;
	$string = <<<EOD
<br />
<form action="$script" method="post">
 <div>
  <input type="hidden" name="comment_no" value="$comment_no" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="plugin" value="comment" />
  <input type="hidden" name="nodate" value="$nodate" />
  <input type="hidden" name="digest" value="$digest" />
  $nametags
  <input type="text" name="msg" size="$comment_cols" />
  <input type="submit" name="comment" value="$_btn_comment" />
 </div>
</form>
EOD;
	
	return $string;
}
?>
