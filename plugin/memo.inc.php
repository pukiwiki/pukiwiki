<?php
// $Id: memo.inc.php,v 1.8 2003/04/13 06:32:45 arino Exp $

/////////////////////////////////////////////////
// テキストエリアのカラム数
define('MEMO_COLS',80);
/////////////////////////////////////////////////
// テキストエリアの行数
define('MEMO_ROWS',5);

function plugin_memo_action()
{
	global $script,$post,$vars,$cols,$rows;
	global $_title_collided,$_msg_collided,$_title_updated;
	
	if ($post['msg'] == '') { return; }
	
	$post["msg"] = preg_replace("/\r/",'',$post["msg"]);
	$post["msg"] = str_replace("\n","\\n",$post["msg"]);

	$postdata = "";
	$postdata_old  = get_source($post["refer"]);
	$memo_no = 0;

	$memo_body = $post["msg"];

	foreach($postdata_old as $line)
	{
		if (preg_match("/^#memo\(?.*\)?$/",$line))
		{
			if ($memo_no == $post["memo_no"] && $post["msg"]!="")
			{
				$postdata .= "#memo($memo_body)\n";
				$line = "";
			}
			$memo_no++;
		}
		$postdata .= $line;
	}
	
	$postdata_input = "$memo_body\n";
	
	if (md5(@join('',get_source($post["refer"]))) != $post['digest'])
	{
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
	else
	{
		page_write($post['refer'],$postdata);
		
		$title = $_title_updated;
	}
	$retvars["msg"] = $title;
	$retvars["body"] = $body;
	
	$post['page'] = $vars['page'] = $post['refer'];
	
	return $retvars;
}
function plugin_memo_convert()
{
	global $script,$vars,$digest;
	global $_btn_memo_update;
	static $numbers = array();
	
	if (!array_key_exists($vars['page'],$numbers))
	{
		$numbers[$vars['page']] = 0;
	}
	$memo_no = $numbers[$vars['page']]++;

	$data = '';
	if (func_num_args()) {
		list($data) = func_get_args();
	}
	
	$data = htmlspecialchars(str_replace("\\n","\n",$data));
	
	$s_page = htmlspecialchars($vars['page']);
	$s_digest = htmlspecialchars($digest);
	$s_cols = MEMO_COLS;
	$s_rows = MEMO_ROWS;
	$string = <<<EOD
<form action="$script" method="post" class="memo">
 <div>
  <input type="hidden" name="memo_no" value="$memo_no" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="plugin" value="memo" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$s_rows" cols="$s_cols">$data</textarea><br />
  <input type="submit" name="memo" value="$_btn_memo_update" />
 </div>
</form>
EOD;
	
	return $string;
}
?>
