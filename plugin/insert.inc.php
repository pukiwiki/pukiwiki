<?php
// $Id: insert.inc.php,v 1.15 2007/08/19 14:20:58 henoheno Exp $
//
// Text inserting box plugin

define('INSERT_COLS', 70); // Columns of textarea
define('INSERT_ROWS',  5); // Rows of textarea
define('INSERT_INS',   1); // Order of insertion (1:before the textarea, 0:after)

function plugin_insert_action()
{
	global $script, $vars, $cols, $rows;
	global $_title_collided, $_msg_collided, $_title_updated;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (! isset($vars['msg']) || $vars['msg'] == '') return;

	$vars['msg'] = preg_replace('/' . "\r" . '/', '', $vars['msg']);
	$insert = ($vars['msg'] != '') ? "\n" . $vars['msg'] . "\n" : '';

	$postdata = '';
	$postdata_old  = get_source($vars['refer']);
	$insert_no = 0;


	foreach($postdata_old as $line) {
		if (! INSERT_INS) $postdata .= $line;
		if (preg_match('/^#insert$/i', $line)) {
			if ($insert_no == $vars['insert_no'])
				$postdata .= $insert;
			$insert_no++;
		}
		if (INSERT_INS) $postdata .= $line;
	}

	$postdata_input = $insert . "\n";

	$body = '';
	if (md5(get_source($vars['refer'], TRUE, TRUE)) !== $vars['digest']) {
		$title = $_title_collided;
		$body = $_msg_collided . "\n";

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
	} else {
		page_write($vars['refer'], $postdata);

		$title = $_title_updated;
	}
	$retvars['msg']  = $title;
	$retvars['body'] = $body;

	$vars['page'] = $vars['refer'];

	return $retvars;
}

function plugin_insert_convert()
{
	global $script, $vars, $digest;
	global $_btn_insert;
	static $numbers = array();

	if (PKWK_READONLY) return ''; // Show nothing

	if (! isset($numbers[$vars['page']])) $numbers[$vars['page']] = 0;

	$insert_no = $numbers[$vars['page']]++;

	$s_page   = htmlspecialchars($vars['page']);
	$s_digest = htmlspecialchars($digest);
	$s_cols = INSERT_COLS;
	$s_rows = INSERT_ROWS;
	$string = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="insert_no" value="$insert_no" />
  <input type="hidden" name="refer"  value="$s_page" />
  <input type="hidden" name="plugin" value="insert" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$s_rows" cols="$s_cols"></textarea><br />
  <input type="submit" name="insert" value="$_btn_insert" />
 </div>
</form>
EOD;

	return $string;
}
?>
