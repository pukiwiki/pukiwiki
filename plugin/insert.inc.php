<?php
// $Id: insert.inc.php,v 1.7 2003/01/27 05:38:46 panda Exp $

/////////////////////////////////////////////////
// テキストエリアのカラム数
define('INSERT_COLS',70);
/////////////////////////////////////////////////
// テキストエリアの行数
define('INSERT_ROWS',5);
/////////////////////////////////////////////////
// 挿入する位置 1:欄の前 0:欄の後
define('INSERT_INS',1);

function plugin_insert_action()
{
	global $script,$post,$vars,$cols,$rows;
	global $_title_collided,$_msg_collided,$_title_updated;
	
	if ($post['msg'] == '') {
		return;
	}
	
	$post["msg"] = preg_replace("/\r/",'',$post['msg']);
	
	$postdata = '';
	$postdata_old  = get_source($post['refer']);
	$insert_no = 0;

	$insert = ($post['msg'] == '') ? '' : "\n{$post['msg']}\n";

	foreach($postdata_old as $line)
	{
		if (!INSERT_INS) {
			$postdata .= $line;
		}
		if (preg_match('/^#insert$/',$line)) {
			if ($insert_no == $post['insert_no']) {
				$postdata .= $insert;
			}
			$insert_no++;
		}
		if (INSERT_INS) {
			$postdata .= $line;
		}
	}
	
	$postdata_input = "$insert\n";
	
	if (md5(@join('',get_source($post['refer']))) != $post['digest']) {
		$title = $_title_collided;
		$body = "$_msg_collided\n";

		$s_refer = htmlspecialchars($post['refer']);
		$s_digest = htmlspecialchars($post['digest']);
		$s_postdata_input = htmlspecialchars($postdata_input);
		
		$body .= <<<EOD
<form action="$script?cmd=preview" method="post">
 <div>
  <input type="hidden" name="refer" value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols" id="textarea">$s_postdata_input</textarea><br />
 </div>
</form>
EOD;
	}
	else {
		page_write($post['refer'],$postdata);
		
		$title = $_title_updated;
	}
	$retvars['msg'] = $title;
	$retvars['body'] = $body;

	$post['page'] = $vars['page'] = $post['refer'];

	return $retvars;
}
function plugin_insert_convert()
{
	global $script,$vars,$digest;
	global $_btn_insert;
	static $insert_no = 0;
	
	$s_page = htmlspecialchars($vars['page']);
	$s_digest = htmlspecialchars($digest);
	$s_cols = INSERT_COLS;
	$s_rows = INSERT_ROWS;
	$string = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="insert_no" value="$insert_no" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="plugin" value="insert" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$s_rows" cols="$s_cols"></textarea><br />
  <input type="submit" name="insert" value="$_btn_insert" />
 </div>
</form>
EOD;
	
	$insert_no++;
	
	return $string;
}
?>
