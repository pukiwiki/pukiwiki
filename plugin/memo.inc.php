<?php
// $Id: memo.inc.php,v 1.10 2004/07/24 14:16:32 henoheno Exp $

/////////////////////////////////////////////////
// テキストエリアのカラム数
define('MEMO_COLS', 80);

// テキストエリアの行数
define('MEMO_ROWS', 5);

/////////////////////////////////////////////////
function plugin_memo_action()
{
	global $script, $vars, $cols, $rows;
	global $_title_collided, $_msg_collided, $_title_updated;

	if (! isset($vars['msg']) || $vars['msg'] == '') return;

	$memo_body = preg_replace("/\r/", '', $vars['msg']);
	$memo_body = str_replace("\n", "\\n", $memo_body);

	$postdata_old  = get_source($vars['refer']);
	$postdata = '';
	$memo_no = 0;
	foreach($postdata_old as $line)
	{
		if (preg_match("/^#memo\(?.*\)?$/", $line))
		{
			if ($memo_no == $vars['memo_no'])
			{
				$postdata .= "#memo($memo_body)\n";
				$line = '';
			}
			++$memo_no;
		}
		$postdata .= $line;
	}

	$postdata_input = "$memo_body\n";

	if (md5(@join('', get_source($vars['refer']))) != $vars['digest'])
	{
		$title = $_title_collided;

		$body = "$_msg_collided\n";

		$s_refer  = htmlspecialchars($vars['refer']);
		$s_digest = htmlspecialchars($vars['digest']);
		$s_postdata_input = htmlspecialchars($postdata_input);

		$body .= <<<EOD
<form action="$script?cmd=preview" method="post">
 <div>
  <input type="hidden" name="refer"  value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols" id="textarea">$s_postdata_input</textarea><br />
 </div>
</form>
EOD;
	}
	else
	{
		page_write($vars['refer'], $postdata);

		$title = $_title_updated;
	}
	$retvars['msg'] = $title;
	$retvars['body'] = $body;

	$vars['page'] = $vars['refer'];

	return $retvars;
}

function plugin_memo_convert()
{
	global $script, $vars, $digest;
	global $_btn_memo_update;
	static $numbers = array();

	if (! isset($numbers[$vars['page']]))
	{
		$numbers[$vars['page']] = 0;
	}
	$memo_no = $numbers[$vars['page']]++;

	$data = '';
	if (func_num_args()) {
		list($data) = func_get_args();
	}

	$data = htmlspecialchars(str_replace("\\n", "\n", $data));

	$s_page   = htmlspecialchars($vars['page']);
	$s_digest = htmlspecialchars($digest);
	$s_cols = MEMO_COLS;
	$s_rows = MEMO_ROWS;
	$string = <<<EOD
<form action="$script" method="post" class="memo">
 <div>
  <input type="hidden" name="memo_no" value="$memo_no" />
  <input type="hidden" name="refer"   value="$s_page" />
  <input type="hidden" name="plugin"  value="memo" />
  <input type="hidden" name="digest"  value="$s_digest" />
  <textarea name="msg" rows="$s_rows" cols="$s_cols">$data</textarea><br />
  <input type="submit" name="memo"    value="$_btn_memo_update" />
 </div>
</form>
EOD;

	return $string;
}
?>
