<?php
// $Id: pcomment.inc.php,v 1.5 2002/12/07 09:43:43 panda Exp $
/*
Last-Update:2002-09-12 rev.15

*プラグイン pcomment
指定したページにコメントを挿入

*Usage
 #pcomment([ページ名][,表示するコメント数][,オプション])

*パラメータ
-ページ名~
 投稿されたコメントを記録するページの名前
-表示するコメント数~
 過去のコメントを何件表示するか(0で全件)

*オプション
-above~
 コメントをフィールドの前に表示(新しい記事が下)
-below~
 コメントをフィールドの後に表示(新しい記事が上)
-reply~
 2レベルまでのコメントにリプライをつけるradioボタンを表示

*/
// ページ名のデフォルト(%sに$vars['page']が入る)
define('PCMT_PAGE','[[コメント/%s]]');
//
// ページのカテゴリ(新規作成時に挿入)
define('PCMT_CATEGORY','[[:Comment]]');
//
// 表示するコメント数のデフォルト
define('PCMT_NUM_COMMENTS',10);
//
// コメントの名前テキストエリアのカラム数
define('PCMT_COLS_NAME',15);
//
// コメントのテキストエリアのカラム数
define('PCMT_COLS_COMMENT',70);
//
// 挿入する位置 1:末尾 0:先頭
define('PCMT_INSERT_INS',1);
//
//コメントの挿入フォーマット
define('PCMT_FORMAT_NAME','[[%s]]');
define('PCMT_FORMAT_MSG','%s');
define('PCMT_FORMAT_DATE','SIZE(10){%s}');
// \x08は、投稿された文字列中に現れない文字であればなんでもいい。
define("PCMT_FORMAT","\x08MSG\x08 -- \x08NAME\x08 \x08DATE\x08");

function plugin_pcomment_init() {
	$_plugin_pcmt_messages = array(
		'_pcmt_btn_name' => 'お名前: ',
		'_pcmt_btn_comment' => 'コメントの挿入',
		'_pcmt_msg_comment' => 'コメント: ',
		'_pcmt_msg_recent' => '最新の%d件を表示しています。',
		'_pcmt_msg_all' => 'コメントページを参照',
		'_pcmt_msg_none' => 'コメントはありません。',
		'_title_pcmt_collided' => '$1 で【更新の衝突】が起きました',
		'_msg_pcmt_collided' => 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
コメントを追加しましたが、違う位置に挿入されているかもしれません。<br />',
	);
  set_plugin_messages($_plugin_pcmt_messages);
}
function plugin_pcomment_action() {
	global $post;

	$retval = '';
	if($post['msg']) { $retval = pcmt_insert(); }
	return $retval;
}

function plugin_pcomment_convert() {
	global $script,$vars,$BracketName;
	global $_pcmt_btn_name, $_pcmt_btn_comment, $_pcmt_msg_comment, $_pcmt_msg_all, $_pcmt_msg_recent;

	//戻り値
	$ret = '';

	//パラメータ変換
	$args = func_get_args();
	array_walk($args, 'pcmt_check_arg', &$params);
	unset($args);

	//文字列を取得
	list($page, $count) = $params['arg'];
	if ($page == '') { $page = sprintf(PCMT_PAGE,strip_bracket($vars['page'])); }

	$_page = get_fullname($page,$vars['page']);
	if (!preg_match("/^$BracketName$/",$_page))
		return 'invalid page name.';

	if ($count == 0 and $count !== '0') { $count = PCMT_NUM_COMMENTS; }

	//向きを決定
	$dir = PCMT_INSERT_INS;
	if ($params['above']) { $dir = 1; }
	if ($params['below']) { $dir = 0; } //両方指定されたら下に (^^;

	//コメントを取得
	list($comments, $digest) = pcmt_get_comments($_page,$count,$dir,$params['reply']);

	//フォームを表示
	if($params['noname']) {
		$title = $_pcmt_msg_comment;
		$name = '';
	} else {
		$title = $_pcmt_btn_name;
		$name = '<input type="text" name="name" size="'.PCMT_COLS_NAME.'" />';
	}

	$radio = $params['reply'] ? '<input type="radio" name="reply" value="0" checked />' : '';
	$comment = '<input type="text" name="msg" size="'.PCMT_COLS_COMMENT.'" />';

	//XSS脆弱性問題 - 外部から来た変数をエスケープ
	$f_page = htmlspecialchars($page);
	$f_refer = htmlspecialchars($vars['page']);
	$f_nodate = htmlspecialchars($params['nodate']);

	$form = <<<EOD
  <div>
  <input type="hidden" name="digest" value="$digest" />
  <input type="hidden" name="plugin" value="pcomment" />
  <input type="hidden" name="refer" value="$f_refer" />
  <input type="hidden" name="page" value="$f_page" />
  <input type="hidden" name="nodate" value="$f_nodate" />
  <input type="hidden" name="dir" value="$dir" />
  $radio $title $name $comment
  <input type="submit" value="$_pcmt_btn_comment" />
  </div>
EOD;
	$link = $_page;
	if (!is_page($_page)) {
		$recent = $_pcmt_msg_none;
	} else {
		if ($_pcmt_msg_all != '')
			$link = preg_replace('/^(\[\[)?/',"$1$_pcmt_msg_all>[[","$_page]]");
		$recent = '';
		if ($count > 0) { $recent = sprintf($_pcmt_msg_recent,$count); }
	}
	$link = make_link($link);
	return $dir ?
		"<div><p>$recent $link</p>\n<form action=\"$script\" method=\"post\">$comments$form</form></div>" :
		"<div><form action=\"$script\" method=\"post\">$form$comments</form>\n<p>$recent $link</p></div>";
}

function pcmt_insert($page) {
	global $post,$vars,$script,$now,$do_backup,$BracketName;
	global $_title_updated;

	$page = $post['page'];
	if (!preg_match("/^$BracketName$/",$page))
		return array('msg'=>'invalid page name.','body'=>'cannot add comment.','collided'=>TRUE);

	$ret['msg'] = $_title_updated;

	//文字列の整形
	$msg = user_rules_str($post['msg']);

	//コメントフォーマットを適用
	$msg = sprintf(PCMT_FORMAT_MSG, rtrim($post['msg']));
	$name = ($post['name'] == '') ? '' :  sprintf(PCMT_FORMAT_NAME, $post['name']);
	$date = ($post['nodate'] == '1') ? '' : sprintf(PCMT_FORMAT_DATE, $now);
	if ($date != '' or $name != '') { 
		$msg = str_replace("\x08MSG\x08", $msg,  PCMT_FORMAT);
		$msg = str_replace("\x08NAME\x08",$name, $msg);
		$msg = str_replace("\x08DATE\x08",$date, $msg);
	}
	if ($post['reply'] or !is_page($page)) {
		$msg = preg_replace('/^\-+/','',$msg);
	}
	$msg = rtrim($msg);

	if (!is_page($page)) {
		$new = PCMT_CATEGORY.' '.htmlspecialchars($post['refer'])."\n\n-$msg\n";
	} else {
		//ページを読み出す
		$data = file(get_filename(encode($page)));
		$old = join('',$data);

		$reply = $post['reply'];
		// 更新の衝突を検出
		if (md5($old) != $post['digest']) {
			$ret['msg'] = $_title_paint_collided;
			$ret['body'] = $_msg_paint_collided;
			$reply = 0; //リプライでなくする
		}

		// ページ末尾の調整
		if (substr($data[count($data) - 1],-1,1) != "\n") { $data[] = "\n"; }

		//基準点を決定
		$level = 1;
		if ($post['dir'] == '1') {
			$pos = count($data) - 1;
			$step = -1;
		} else {
			$pos = -1;
			foreach ($data as $line) {
				if (preg_match('/^\-/',$line)) break;
				$pos++;
			}
			$step = 1;
		}
		//リプライ先のコメントを検索
		if ($reply > 0) {
			while ($pos >= 0 and $pos < count($data)) {
				if (preg_match('/^(\-{1,2})(?!\-)/',$data[$pos], $matches) and --$reply == 0) {
					$level = strlen($matches[1]) + 1; //挿入するレベル
					break;
				}
				$pos += $step;
			}
			while (++$pos < count($data)) {
				if (preg_match('/^(\-{1,2})(?!\-)/',$data[$pos], $matches)) {
					if (strlen($matches[1]) < $level) { break; }
				}
			}
		} else {
			$pos++;
		}
		//行頭文字
		$head = str_repeat('-',$level);
		//コメントを挿入
		array_splice($data,$pos,0,"$head$msg\n");
		$new = join('',$data);

		// 差分ファイルの作成
		file_write(DIFF_DIR,$page,do_diff($old,$new));

		// バックアップの作成
		if ($do_backup) {
			$oldtime = filemtime(get_filename(encode($page)));
			make_backup(encode($page).'.txt', $old, $oldtime);
		}
	}

	// ファイルの書き込み
	file_write(DATA_DIR, $page, $new);

	// is_pageのキャッシュをクリアする。
	is_page($page, true);

	$vars['page'] = $post['page'] = $post['refer'];

	return $ret;
}
//オプションを解析する
function pcmt_check_arg($val, $key, &$params) {
	$valid_args = array('noname','nodate','below','above','reply');
	foreach ($valid_args as $valid) {
		if (strpos($valid, strtolower($val)) === 0) {
			$params[$valid] = 1;
			return;
		}
	}
	$params['arg'][] = $val;
}
function pcmt_get_comments($page,$count,$dir,$reply) {
	$data = @file(get_filename(encode($page)));

	if (!is_array($data)) { return array('',0); }

	$digest = md5(join('',$data));

	//コメントを指定された件数だけ切り取る
	if ($dir) { $data = array_reverse($data); }
	$num = $cnt = 0;
	$cmts = array();
	foreach ($data as $line) {
		if ($count > 0 and $dir and $cnt == $count) { break; }
		if (preg_match('/^(\-{1,2})(?!\-)(.*)$/', $line, $matches)) {
			if ($count > 0 and strlen($matches[1]) == 1 and ++$cnt > $count) { break; }
			if ($reply) {
				++$num;
				$cmts[] = "$matches[1]\x01$num\x02$matches[2]\n";
			} else {
				$cmts[] = $line;
			}
		} else {
			$cmts[] = $line;
		}
	}
	$data = $cmts;
	if ($dir) { $data = array_reverse($data); }
	unset($cmts);

	//コメントより前のデータを取り除く。
	while (count($data) > 0 and substr($data[0],0,1) != '-') { array_shift($data); }

	//html変換
	$comments = convert_html(join('', $data));
	unset($data);

	//コメントにラジオボタンの印をつける
	if ($reply) {
		$comments = preg_replace("/\x01(\d+)\x02/",'<input type="radio" name="reply" value="$1" />', $comments);
	}
	return array($comments,$digest);
}
?>
