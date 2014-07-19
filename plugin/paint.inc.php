<?php
// PukiWiki - Yet another WikiWikiWeb clone
//
// $Id: paint.inc.php,v 1.20 2011/01/25 15:01:01 henoheno Exp $
//
// Paint plugin

/*
 * Usage
 *  #paint(width,height)
 * パラメータ
 *  キャンバスの幅と高さ
 */

// 挿入する位置 1:欄の前 0:欄の後
define('PAINT_INSERT_INS',0);

// デフォルトの描画領域の幅と高さ
define('PAINT_DEFAULT_WIDTH',80);
define('PAINT_DEFAULT_HEIGHT',60);

// 描画領域の幅と高さの制限値
define('PAINT_MAX_WIDTH',320);
define('PAINT_MAX_HEIGHT',240);

// アプレット領域の幅と高さ 50x50未満で別ウインドウが開く
define('PAINT_APPLET_WIDTH',800);
define('PAINT_APPLET_HEIGHT',300);

//コメントの挿入フォーマット
define('PAINT_NAME_FORMAT','[[$name]]');
define('PAINT_MSG_FORMAT','$msg');
define('PAINT_NOW_FORMAT','&new{$now};');
//メッセージがある場合
define('PAINT_FORMAT',"\x08MSG\x08 -- \x08NAME\x08 \x08NOW\x08");
//メッセージがない場合
define('PAINT_FORMAT_NOMSG',"\x08NAME\x08 \x08NOW\x08");

function plugin_paint_action()
{
	global $script, $vars, $pkwk_dtd, $_paint_messages;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	
	//戻り値を初期化
	$retval['msg'] = $_paint_messages['msg_title'];
	$retval['body'] = '';

	if (array_key_exists('attach_file',$_FILES)
		and array_key_exists('refer',$vars))
	{
		$file = $_FILES['attach_file'];
		//BBSPaiter.jarは、shift-jisで内容を送ってくる。面倒なのでページ名はエンコードしてから送信させるようにした。
		$vars['page'] = $vars['refer'] = decode($vars['refer']);

		$filename = $vars['filename'];
		$filename = mb_convert_encoding($filename,SOURCE_ENCODING,'auto');

		//ファイル名置換
		$attachname = preg_replace('/^[^\.]+/',$filename,$file['name']);
		//すでに存在した場合、 ファイル名に'_0','_1',...を付けて回避(姑息)
		$count = '_0';
		while (file_exists(UPLOAD_DIR.encode($vars['refer']).'_'.encode($attachname)))
		{
			$attachname = preg_replace('/^[^\.]+/',$filename.$count++,$file['name']);
		}

		$file['name'] = $attachname;

		if (!exist_plugin('attach') or !function_exists('attach_upload'))
		{
			return array('msg'=>'attach.inc.php not found or not correct version.');
		}

		$retval = attach_upload($file,$vars['refer'],TRUE);
		if ($retval['result'] == TRUE)
		{
			$retval = paint_insert_ref($file['name']);
		}
	}
	else
	{
		$message = '';
		$r_refer = $s_refer = '';
		if (array_key_exists('refer',$vars))
		{
			$r_refer = rawurlencode($vars['refer']);
			$s_refer = htmlsc($vars['refer']);
		}
		$link = "<p><a href=\"$script?$r_refer\">$s_refer</a></p>";;

		$w = PAINT_APPLET_WIDTH;
		$h = PAINT_APPLET_HEIGHT;

		//ウインドウモード :)
		if ($w < 50 and $h < 50)
		{
			$w = $h = 0;
			$retval['msg'] = '';
			$vars['page'] = $vars['refer'];
			$vars['cmd'] = 'read';
			$retval['body'] = convert_html(get_source($vars['refer']));
			$link = '';
		}

		//XSS脆弱性問題 - 外部から来た変数をエスケープ
		$width = empty($vars['width']) ? PAINT_DEFAULT_WIDTH : $vars['width'];
		$height = empty($vars['height']) ? PAINT_DEFAULT_HEIGHT : $vars['height'];
		$f_w = (is_numeric($width) and $width > 0) ? $width : PAINT_DEFAULT_WIDTH;
		$f_h = (is_numeric($height) and $height > 0) ? $height : PAINT_DEFAULT_HEIGHT;
		$f_refer = array_key_exists('refer',$vars) ? encode($vars['refer']) : ''; // BBSPainter.jarがshift-jisに変換するのを回避
		$f_digest = array_key_exists('digest',$vars) ? htmlsc($vars['digest']) : '';
		$f_no = (array_key_exists('paint_no',$vars) and is_numeric($vars['paint_no'])) ?
			$vars['paint_no'] + 0 : 0;

		if ($f_w > PAINT_MAX_WIDTH)
		{
			$f_w = PAINT_MAX_WIDTH;
		}
		if ($f_h > PAINT_MAX_HEIGHT)
		{
			$f_h = PAINT_MAX_HEIGHT;
		}

		$retval['body'] .= <<<EOD
 <div>
 $link
 $message
 <applet codebase="." archive="BBSPainter.jar" code="Main.class" width="$w" height="$h">
 <param name="size" value="$f_w,$f_h" />
 <param name="action" value="$script" />
 <param name="image" value="attach_file" />
 <param name="form1" value="filename={$_paint_messages['field_filename']}=!" />
 <param name="form2" value="yourname={$_paint_messages['field_name']}" />
 <param name="comment" value="msg={$_paint_messages['field_comment']}" />
 <param name="param1" value="plugin=paint" />
 <param name="param2" value="refer=$f_refer" />
 <param name="param3" value="digest=$f_digest" />
 <param name="param4" value="max_file_size=1000000" />
 <param name="param5" value="paint_no=$f_no" />
 <param name="enctype" value="multipart/form-data" />
 <param name="return.URL" value="$script?$r_refer" />
 </applet>
 </div>
EOD;
		// XHTML 1.0 Transitional
		if (! isset($pkwk_dtd) || $pkwk_dtd == PKWK_DTD_XHTML_1_1)
			$pkwk_dtd = PKWK_DTD_XHTML_1_0_TRANSITIONAL;
	}
	return $retval;
}

function plugin_paint_convert()
{
	global $script,$vars,$digest;
	global $_paint_messages;
	static $numbers = array();

	if (PKWK_READONLY) return ''; // Show nothing

	if (!array_key_exists($vars['page'],$numbers))
	{
		$numbers[$vars['page']] = 0;
	}
	$paint_no = $numbers[$vars['page']]++;

	//戻り値
	$ret = '';

	//文字列を取得
	$width = $height = 0;
	$args = func_get_args();
	if (count($args) >= 2)
	{
		$width = array_shift($args);
		$height = array_shift($args);
	}
	if (!is_numeric($width) or $width <= 0)
	{
		$width = PAINT_DEFAULT_WIDTH;
	}
	if (!is_numeric($height) or $height <= 0)
	{
		$height = PAINT_DEFAULT_HEIGHT;
	}

	//XSS脆弱性問題 - 外部から来た変数をエスケープ
	$f_page = htmlsc($vars['page']);

	$max = sprintf($_paint_messages['msg_max'],PAINT_MAX_WIDTH,PAINT_MAX_HEIGHT);

	$ret = <<<EOD
  <form action="$script" method="post">
  <div>
  <input type="hidden" name="paint_no" value="$paint_no" />
  <input type="hidden" name="digest" value="$digest" />
  <input type="hidden" name="plugin" value="paint" />
  <input type="hidden" name="refer" value="$f_page" />
  <input type="text" name="width" size="3" value="$width" />
  x
  <input type="text" name="height" size="3" value="$height" />
  $max
  <input type="submit" value="{$_paint_messages['btn_submit']}" />
  </div>
  </form>
EOD;
	return $ret;
}
function paint_insert_ref($filename)
{
	global $script,$vars,$now,$do_backup;
	global $_paint_messages,$_no_name;

	$ret['msg'] = $_paint_messages['msg_title'];

	$msg = mb_convert_encoding(rtrim($vars['msg']),SOURCE_ENCODING,'auto');
	$name = mb_convert_encoding($vars['yourname'],SOURCE_ENCODING,'auto');

	$msg  = str_replace('$msg',$msg,PAINT_MSG_FORMAT);
	$name = ($name == '') ? $_no_name : $vars['yourname'];
	$name = ($name == '') ? '' : str_replace('$name',$name,PAINT_NAME_FORMAT);
	$now  = str_replace('$now',$now,PAINT_NOW_FORMAT);

	$msg = trim($msg);
	$msg = ($msg == '') ?
		PAINT_FORMAT_NOMSG :
		str_replace("\x08MSG\x08", $msg, PAINT_FORMAT);
	$msg = str_replace("\x08NAME\x08",$name, $msg);
	$msg = str_replace("\x08NOW\x08",$now, $msg);

	//ブロックに食われないように、#clearの直前に\nを2個書いておく
	$msg = "#ref($filename,wrap,around)\n" . trim($msg) . "\n\n" .
		"#clear\n";

	$postdata_old = get_source($vars['refer']);
	$postdata = '';
	$paint_no = 0; //'#paint'の出現回数
	foreach ($postdata_old as $line)
	{
		if (!PAINT_INSERT_INS)
		{
			$postdata .= $line;
		}
		if (preg_match('/^#paint/i',$line))
		{
			if ($paint_no == $vars['paint_no'])
			{
				$postdata .= $msg;
			}
			$paint_no++;
		}
		if (PAINT_INSERT_INS)
		{
			$postdata .= $line;
		}
	}

	// 更新の衝突を検出
	if (md5(join('',$postdata_old)) != $vars['digest'])
	{
		$ret['msg'] = $_paint_messages['msg_title_collided'];
		$ret['body'] = $_paint_messages['msg_collided'];
	}

	page_write($vars['refer'],$postdata);

	return $ret;
}
?>
