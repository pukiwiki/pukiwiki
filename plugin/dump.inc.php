<?php
/////////////////////////////////////////////////
// $Id: dump.inc.php,v 1.20 2004/09/26 13:42:20 henoheno Exp $
// Originated as tarfile.inc.php by teanan / Interfair Laboratory 2004.

// [更新履歴]
// 2004-09-21 version 0.0 [暫定版]
// ・とりあえず wiki ディレクトリがtar.gzで取り出せるようになりました。
// 2004-09-22 version 0.1 [暫定版]
// ・〜tar.gz/〜.tarの選択に対応
// ・attach,backupディレクトリのバックアップに対応
// ・ファイル名をページ名に変換する機能を追加(wiki/attach/backup)
// ・ファイル一覧の取得方法を変更(glob→opendir)
// 2004-09-22 version 0.2
// ・ファイルのアップロード(リストア)に対応(tar/tar.gz)
//   (対象は wiki,attachディレクトリのみ)
// 2004-09-22 version 1.0
// ・LongLink(100バイトを超えたファイル名)に対応
// ・リストア時ファイルの更新時刻を元に戻すように修正

/////////////////////////////////////////////////
// User define

// ページ名をディレクトリ構造に変換する際の文字コード (for mbstring)
define('PLUGIN_DUMP_FILENAME_ENCORDING', 'SJIS');

// 最大アップロードサイズ
define('PLUGIN_DUMP_MAX_FILESIZE', 1024); // Kbyte

/////////////////////////////////////////////////
// Internal

// Action
define('PLUGIN_DUMP_DUMP',    'dump');    // Dump & download
define('PLUGIN_DUMP_RESTORE', 'restore'); // Upload & restore

/////////////////////////////////////////////////
// プラグイン本体
function plugin_dump_action()
{
	global $vars;

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	$act  = isset($vars['act'])  ? $vars['act']  : NULL;

	$body = '';

	if ($pass !== NULL) {
		if (pkwk_login($pass) && ($act !== NULL) ) {
			switch($act){
			case PLUGIN_DUMP_DUMP:
				$body = plugin_dump_download();
				break;
			case PLUGIN_DUMP_RESTORE:
				$retcode = plugin_dump_upload();
				if ($retcode['code'] == TRUE) {
					// 正常終了
					$msg = 'アップロードが完了しました';
					$body .= $retcode['msg'];
					return array('msg' => $msg, 'body' => $body);
				}
				break;
			}
		} else {
			$body = ($pass === NULL) ? '' : "<p><strong>パスワードが違います。</strong></p>\n";
		}
	}

	// 入力フォームを表示
	$body .= plugin_dump_disp_form();
	
	return array('msg' => 'dump & restore', 'body' => $body);
}

/////////////////////////////////////////////////
// ファイルのダウンロード
function plugin_dump_download()
{
	global $vars;

	// アーカイブの種類
	$arc_kind = ($vars['pcmd'] == 'tar') ? 'tar' : 'tgz';

	// ページ名に変換する
	$namedecode = isset($vars['namedecode']) ? TRUE : FALSE;

	// バックアップディレクトリ
	$bk_wiki   = isset($vars['bk_wiki'])   ? TRUE : FALSE;
	$bk_attach = isset($vars['bk_attach']) ? TRUE : FALSE;
	$bk_backup = isset($vars['bk_backup']) ? TRUE : FALSE;

	$tar = new tarlib();

	// ファイルを生成する
	if ($tar->create(CACHE_DIR, $arc_kind))
	{
		$filecount = 0;		// ファイル数
		if ($bk_wiki)   $filecount .= $tar->add(DATA_DIR,   '^[0-9A-F]+\.txt', $namedecode);
		if ($bk_attach) $filecount .= $tar->add(UPLOAD_DIR, '^[0-9A-F_]+',     $namedecode);
		if ($bk_backup) $filecount .= $tar->add(BACKUP_DIR, '^[0-9A-F]+\.gz',  $namedecode);
		$tar->close();

		if ($filecount > 0) {
			// ダウンロード
			download_tarfile($tar->filename, $arc_kind);
			@unlink($tar->filename);
			exit;	// 正常終了
		} else {
			@unlink($tar->filename);
			return '<p><strong>ファイルがみつかりませんでした。</strong></p>';
		}
	}
	else
	{
		die_message('テンポラリファイルの生成に失敗しました。');
	}
}

/////////////////////////////////////////////////
// ファイルのアップロード
function plugin_dump_upload()
{
	global $vars;

	$code = FALSE;
	$msg  = '';

	$filename = $_FILES['upload_file']['name'];
	$matches = array();
	$arc_kind = FALSE;
	if(! preg_match('/(\.tar|\.tar.gz|\.tgz)$/', $filename, $matches)){
		die_message("Invalid file suffix");
	} else { 
		$matches[1] = strtolower($matches[1]);
		switch ($matches[1]) {
		case '.tar':    $arc_kind = 'tar'; break;
		case '.tgz':    $arc_kind = 'tar'; break;
		case '.tar.gz': $arc_kind = 'tgz'; break;
		default: die_message('Invalid file suffix: ' . $matches[1]);
		}
	}

	if ($_FILES['upload_file']['size'] >  PLUGIN_DUMP_MAX_FILESIZE * 1024)
		die_message('Max file size exceeded: ' . PLUGIN_DUMP_MAX_FILESIZE . 'KB');

	// アップロードファイル
	$uploadfile = tempnam(CACHE_DIR, 'upload' );
	if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile))
	{
		// tarファイルを展開する
		$tar = new tarlib();
		if ($tar->open($uploadfile, $arc_kind))
		{
			// DATA_DIR (wiki/*.txt)
			$quote_wiki  = preg_quote(DATA_DIR, '/');
			$quote_wiki .= '((?:[0-9A-F])+)(\.txt){0,1}';

			// UPLOAD_DIR (attach/*)
			$quote_attach  = preg_quote(UPLOAD_DIR,'/');
			$quote_attach .= '((?:[0-9A-F]{2})+)_((?:[0-9A-F])+)';

			$pattern = "((^$quote_wiki)|(^$quote_attach))";
	
			$files = $tar->extract($pattern);
			if (! empty($files)) {
				$msg  = '<p><strong>展開したファイル一覧</strong><ul>';
				foreach($files as $name) {
					$msg .= "<li>$name</li>\n";
				}
				$msg .= '</ul></p>';
				$code = TRUE;
			} else {
				$msg = '<p>展開できるファイルがありませんでした。</p>';
				$code = FALSE;
			}
			$tar->close();
		}
		else
		{
			$msg = '<p>ファイルがみつかりませんでした。</p>';
			$code = FALSE;
		}
		// 処理が終了したらアップロードしたファイルは削除する
		@unlink($uploadfile);
	}
	else
	{
		die_message('ファイルがみつかりませんでした。');
	}

	return array('code' => $code , 'msg' => $msg);
}

/////////////////////////////////////////////////
// tarファイルのダウンロード
function download_tarfile($tempnam, $arc_kind)
{
	$size = filesize($tempnam);

	$filename = strftime('tar%Y%m%d', time());
	if ($arc_kind == 'tgz') {
		$filename .= '.tar.gz';
	} else {
		$filename .= '.tar';
	}

	header('Content-Disposition: attachment; filename="' . $filename . '"');
	header('Content-Length: ' . $size);
	header('Content-Type: application/octet-stream');
	header('Pragma: no-cache');
	@readfile($name);
}

/////////////////////////////////////////////////
// 入力フォームを表示
function plugin_dump_disp_form()
{
	global $script, $defaultpage;

	$act_down = PLUGIN_DUMP_DUMP;
	$act_up   = PLUGIN_DUMP_RESTORE;
	$maxsize  = PLUGIN_DUMP_MAX_FILESIZE;

	$data = <<<EOD
<span class="small">
</span>
<h3>データのダウンロード</h3>
<form action="$script" method="post">
 <div>
  <input type="hidden" name="cmd"  value="dump" />
  <input type="hidden" name="page" value="$defaultpage" />
  <input type="hidden" name="act"  value="$act_down" />

<p><strong>アーカイブの形式</strong>
<br />
  <input type="radio" name="pcmd" value="tgz" checked="checked" /> 〜.tar.gz 形式<br />
  <input type="radio" name="pcmd" value="tar" /> 〜.tar 形式
</p>
<p><strong>バックアップディレクトリ</strong>
<br />
  <input type="checkbox" name="bk_wiki" checked="checked" /> wiki<br />
  <input type="checkbox" name="bk_attach" /> attach<br />
  <input type="checkbox" name="bk_backup" /> backup
</p>
<p><strong>オプション</strong>
<br />
  <input type="checkbox" name="namedecode" /> エンコードされているページ名をディレクトリ階層つきのファイルにデコード (※リストアに使うことはできなくなります。また、一部の文字は '_' に置換されます)<br />
</p>
<p><strong>管理者パスワード</strong>
  <input type="password" name="pass" size="12" />
  <input type="submit"   name="ok"   value="OK" />
</p>
 </div>
</form>

<h3>データのリストア (*.tar, *.tar.gz)</h3>
<form enctype="multipart/form-data" action="$script" method="post">
 <div>
  <input type="hidden" name="cmd"  value="dump" />
  <input type="hidden" name="page" value="$defaultpage" />
  <input type="hidden" name="act"  value="$act_up" />
<p><strong>[重要] 同じ名前のデータファイルは上書きされますので、十分ご注意ください。</strong></p>
<p><span class="small">
アップロード可能な最大ファイルサイズは、$maxsize KByte までです。<br />
</span>
  ファイル: <input type="file" name="upload_file" size="40" />
</p>
<p><strong>管理者パスワード</strong>
  <input type="password" name="pass" size="12" />
  <input type="submit"   name="ok"   value="OK" />
</p>
 </div>
</form>
EOD;

	return $data;
}

/////////////////////////////////////////////////
// tarlib: a class library for tar file creation and expansion

// Tar related definition
define('TARLIB_HDR_LEN',           512);	// ヘッダの大きさ
define('TARLIB_BLK_LEN',           512);	// 単位ブロック長さ
define('TARLIB_HDR_NAME_OFFSET',     0);	// ファイル名のオフセット
define('TARLIB_HDR_NAME_LEN',      100);	// ファイル名の最大長さ
define('TARLIB_HDR_MODE_OFFSET',   100);	// modeへのオフセット
define('TARLIB_HDR_UID_OFFSET',    108);	// uidへのオフセット
define('TARLIB_HDR_GID_OFFSET',    116);	// gidへのオフセット
define('TARLIB_HDR_SIZE_OFFSET',   124);	// サイズへのオフセット
define('TARLIB_HDR_SIZE_LEN',       12);	// サイズの長さ
define('TARLIB_HDR_MTIME_OFFSET',  136);	// 最終更新時刻のオフセット
define('TARLIB_HDR_MTIME_LEN',      12);	// 最終更新時刻の長さ
define('TARLIB_HDR_CHKSUM_OFFSET', 148);	// チェックサムのオフセット
define('TARLIB_HDR_CHKSUM_LEN',      8);	// チェックサムの長さ
define('TARLIB_HDR_TYPE_OFFSET',   156);	// ファイルタイプへのオフセット

// Status
define('TARLIB_STATUS_INIT',    0);		// 初期状態
define('TARLIB_STATUS_OPEN',   10);		// 読み取り
define('TARLIB_STATUS_CREATE', 20);		// 書き込み

define('TARLIB_DATA_MODE',      '100666 ');	// ファイルパーミッション
define('TARLIB_DATA_UGID',      '000000 ');	// uid / gid
define('TARLIB_DATA_CHKBLANKS', '        ');

// GNU拡張仕様(ロングファイル名対応)
define('TARLIB_DATA_LONGLINK', '././@LongLink');
define('TARLIB_HDR_FILE', '0');
define('TARLIB_HDR_LINK', 'L');

define('TARLIB_KIND_TGZ', 0);
define('TARLIB_KIND_TAR',  1);

class tarlib
{
	var $filename;
	var $fp;
	var $status;
	var $arc_kind;
	var $dummydata;

	// コンストラクタ
	function tarlib() {
		$this->filename = '';
		$this->fp       = FALSE;
		$this->status   = TARLIB_STATUS_INIT;
		$this->arc_kind = TARLIB_KIND_TGZ;
	}
	
	////////////////////////////////////////////////////////////
	// 関数  : tarファイルを作成する
	// 引数  : tarファイルを作成するパス
	// 返り値: TRUE .. 成功 , FALSE .. 失敗
	////////////////////////////////////////////////////////////
	function create($tempdir, $kind = 'tgz')
	{
		$tempnam = tempnam($tempdir, 'tarlib_');
		if ($tempnam === FALSE) return FALSE;

		if ($kind == 'tgz') {
			$this->arc_kind = TARLIB_KIND_TGZ;
			$this->fp = gzopen($tempnam, 'wb');
		} else {
			$this->arc_kind = TARLIB_KIND_TAR;
			$this->fp = @fopen($tempnam, 'wb');
		}
		if ($this->fp === FALSE) return FALSE;

		$this->filename  = $tempnam;
		$this->dummydata = join('', array_fill(0, TARLIB_BLK_LEN, "\0"));
		$this->status    = TARLIB_STATUS_CREATE;

		rewind($this->fp);

		return TRUE;
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarファイルに追加する
	// 引数  : $dir    .. ディレクトリ名
	//         $mask   .. 追加するファイル(正規表現)
	//         $decode .. ページ名の変換をするか
	// 返り値: 作成したファイル数
	////////////////////////////////////////////////////////////
	function add($dir, $mask, $decode = FALSE)
	{
		$retvalue = 0;
		
		if ($this->status != TARLIB_STATUS_CREATE)
			return ''; // ファイルが作成されていない

		unset($files);

		//  指定されたパスのファイルのリストを取得する
		$dp = @opendir($dir) or
			die_message($dir . ' is not found or not readable.');
		while ($filename = readdir($dp)) {
			if (preg_match("/$mask/", $filename))
				$files[] = $dir . $filename;
		}
		closedir($dp);
		
		sort($files);

		$matches = array();
		foreach($files as $name)
		{
			// Tarに格納するファイル名
			if ($decode == TRUE)
			{
				// ファイル名をページ名に変換する処理
				$dirname  = dirname(trim($name)) . '/';
				$filename = basename(trim($name));
				if (preg_match("/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)/", $filename, $matches))
				{
					// attachファイル名
					$filename = decode($matches[1]).'/'.decode($matches[2]);
				}
				else
				{
					$pattern = '^((?:[0-9A-F]{2})+)((\.txt|\.gz)*)$';
					if (preg_match("/$pattern/", $filename, $matches)) {
						$filename = decode($matches[1]).$matches[2];

						// 危ないコードは置換しておく
						$filename = str_replace(':',  '_', $filename);
						$filename = str_replace('\\', '_', $filename);
					}
				}
				$filename = $dirname . $filename;
				if (function_exists('mb_convert_encoding')) {
					// ファイル名の文字コードを変換
					$filename = mb_convert_encoding($filename, PLUGIN_DUMP_FILENAME_ENCORDING);
				}
			}
			else
			{
				$filename = $name;
			}

			// 最終更新時刻
			$mtime = filemtime($name);

			// ファイル名長のチェック
			if (strlen($filename) > TARLIB_HDR_NAME_LEN) {
				// LongLink対応
				$size = strlen($filename);
				// LonkLinkヘッダ生成
				$tar_data = $this->_make_header(TARLIB_DATA_LONGLINK, $size, $mtime, TARLIB_HDR_LINK);
				// ファイル出力
	 			$this->_write_data(join('', $tar_data), $filename, $size);
			}

			// ファイルサイズを取得
			$size = filesize($name);
			if ($size == FALSE) {
				die_message($name . ' is not found or not readable.');
				continue;	// ファイルがない
			}

			// ヘッダ生成
			$tar_data = $this->_make_header($filename, $size, $mtime, TARLIB_HDR_FILE);

			// ファイルデータの取得
			$fpr = @fopen($name , 'rb');
			$data = fread($fpr, $size);
			fclose( $fpr );

			// ファイル出力
			$this->_write_data(join('', $tar_data), $data, $size);
			++$retvalue;
		}
		return $retvalue;
	}
	
	////////////////////////////////////////////////////////////
	// 関数  : tarのヘッダ情報を生成する (add)
	// 引数  : $filename .. ファイル名
	//         $size     .. データサイズ
	//         $mtime    .. 最終更新日
	//         $typeflag .. TypeFlag (file/link)
	// 戻り値: tarヘッダ情報
	////////////////////////////////////////////////////////////
	function _make_header($filename, $size, $mtime, $typeflag)
	{
		$tar_data = array_fill(0, TARLIB_HDR_LEN, "\0");
		
		// ファイル名を保存
		for($i = 0; $i < strlen($filename); $i++ )
		{
			if ($i < TARLIB_HDR_NAME_LEN) {
				$tar_data[$i + TARLIB_HDR_NAME_OFFSET] = $filename{$i};
			} else {
				break;	// ファイル名が長すぎ
			}
		}

		// mode
		$modeid = TARLIB_DATA_MODE;
		for($i = 0; $i < strlen($modeid); $i++ ) {
			$tar_data[$i + TARLIB_HDR_MODE_OFFSET] = $modeid{$i};
		}

		// uid / gid
		$ugid = TARLIB_DATA_UGID;
		for($i = 0; $i < strlen($ugid); $i++ ) {
			$tar_data[$i + TARLIB_HDR_UID_OFFSET] = $ugid{$i};
			$tar_data[$i + TARLIB_HDR_GID_OFFSET] = $ugid{$i};
		}

		// サイズ
		$strsize = sprintf('%11o', $size);
		for($i = 0; $i < strlen($strsize); $i++ ) {
			$tar_data[$i + TARLIB_HDR_SIZE_OFFSET] = $strsize{$i};
		}

		// 最終更新時刻
		$strmtime = sprintf('%o', $mtime);
		for($i = 0; $i < strlen($strmtime); $i++ ) {
			$tar_data[$i + TARLIB_HDR_MTIME_OFFSET] = $strmtime{$i};
		}

		// チェックサム計算用のブランクを設定
		$chkblanks = TARLIB_DATA_CHKBLANKS;
		for($i = 0; $i < strlen($chkblanks); $i++ ) {
			$tar_data[$i + TARLIB_HDR_CHKSUM_OFFSET] = $chkblanks{$i};
		}

		// タイプフラグ
		$tar_data[TARLIB_HDR_TYPE_OFFSET] = $typeflag;

		// チェックサムの計算
		$sum = 0;
		for($i = 0; $i < TARLIB_BLK_LEN; $i++ ) {
			$sum += 0xff & ord($tar_data[$i]);
		}
		$strchksum = sprintf('%7o',$sum);
		for($i = 0; $i < strlen($strchksum); $i++ ) {
			$tar_data[$i + TARLIB_HDR_CHKSUM_OFFSET] = $strchksum{$i};
		}
		return $tar_data;
	}
	
	////////////////////////////////////////////////////////////
	// 関数  : tarデータのファイル出力 (add)
	// 引数  : $header .. tarヘッダ情報
	//         $body   .. tarデータ
	//         $size   .. データサイズ
	// 戻り値: なし
	////////////////////////////////////////////////////////////
	function _write_data($header, $body, $size)
	{
		$fixsize  = ceil($size / TARLIB_BLK_LEN) * TARLIB_BLK_LEN - $size;

		fwrite($this->fp, $header, TARLIB_HDR_LEN);    // Header
		fwrite($this->fp, $body, $size);               // Body
		fwrite($this->fp, $this->dummydata, $fixsize); // Padding
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarファイルを開く
	// 引数  : tarファイル名
	// 返り値: TRUE .. 成功 , FALSE .. 失敗
	////////////////////////////////////////////////////////////
	function open($name = '', $kind = 'tgz')
	{
		if ($name != '') $this->filename = $name;

		if ($kind == 'tgz') {
			$this->arc_kind = TARLIB_KIND_TGZ;
			$this->fp = gzopen($this->filename, 'rb');
		} else {
			$this->arc_kind = TARLIB_KIND_TAR;
			$this->fp =  fopen($this->filename, 'rb');
		}

		if ($this->fp === FALSE) {
			return FALSE;	// No such file
		} else {
			$this->status = TARLIB_STATUS_OPEN;
			rewind($this->fp);
			return TRUE;
		}
	}

	////////////////////////////////////////////////////////////
	// 関数  : 指定したディレクトリにtarファイルを展開する
	// 引数  : 展開するファイルパターン(正規表現)
	// 返り値: 展開したファイル名の一覧
	// 補足  : ARAIさんのattachプラグインパッチを参考にしました
	////////////////////////////////////////////////////////////
	function extract($pattern )
	{
		if ($this->status != TARLIB_STATUS_OPEN) return ''; // Not opened
		
		$files = array();
		$longname = '';

		while(1) {
			$buff = fread($this->fp, TARLIB_HDR_LEN);
			if (strlen($buff) != TARLIB_HDR_LEN) break;

			// ファイル名
			if ($longname != '') {
				$name     = $longname;	// LongLink対応
				$longname = '';
			} else {
				$name = '';
				for ($i = 0; $i < TARLIB_HDR_NAME_LEN; $i++ ) {
					if ($buff{$i + TARLIB_HDR_NAME_OFFSET} != "\0") {
						$name .= $buff{$i + TARLIB_HDR_NAME_OFFSET};
					} else {
						break;
					}
				}
			}

			$name = trim($name);
			if ($name == '') break;	// 展開終了

			// チェックサムを取得しつつ、ブランクに置換していく
			$checksum = '';
			$chkblanks = TARLIB_DATA_CHKBLANKS;
			for ($i = 0; $i < TARLIB_HDR_CHKSUM_LEN; $i++ ) {
				$checksum .= $buff{$i + TARLIB_HDR_CHKSUM_OFFSET};
				$buff{$i + TARLIB_HDR_CHKSUM_OFFSET} = $chkblanks{$i};
			}
			list($checksum) = sscanf('0' . trim($checksum), '%i');

			// Compute checksum
			$sum = 0;
			for($i = 0; $i < TARLIB_BLK_LEN; $i++ ) {
				$sum += 0xff & ord($buff{$i});
			}
			if ($sum != $checksum) break; // Error
				
			// Size
			$size = '';
			for ($i = 0; $i < TARLIB_HDR_SIZE_LEN; $i++ ) {
				$size .= $buff{$i + TARLIB_HDR_SIZE_OFFSET};
			}
			list($size) = sscanf('0' . trim($size), '%i');

			// ceil
			// データブロックは512byteでパディングされている
			$pdsz = ceil($size / TARLIB_BLK_LEN) * TARLIB_BLK_LEN;

			// 最終更新時刻
			$strmtime = '';
			for ($i = 0; $i < TARLIB_HDR_MTIME_LEN; $i++ ) {
				$strmtime .= $buff{$i + TARLIB_HDR_MTIME_OFFSET};
			}
			list($mtime) = sscanf('0' . trim($strmtime), '%i');

			// タイプフラグ
//			 $type = $buff{TARLIB_HDR_TYPE_OFFSET};

			if ($name == TARLIB_DATA_LONGLINK)
			{
				// LongLink
				$buff = fread( $this->fp, $pdsz );
				$longname = substr($buff, 0, $size);
			}
			else
			if (preg_match("/$pattern/", $name) )
//			if ($type == 0 && preg_match("/$pattern/", $name) )
			{
				$buff = fread($this->fp, $pdsz);

				// 既に同じファイルがある場合は上書きされる
				$fpw = @fopen($name, 'wb');
				if ($fpw !== FALSE) {
					fwrite($fpw, $buff, $size);
					fclose($fpw);
					@chmod($name, 0666); // 念のためパーミッションを設定しておく
					@touch($name, $mtime); // 最終更新時刻の修正
					$files[] = $name;
				}
			}
			else
			{
				// ファイルポインタを進める
				@fseek($this->fp, $pdsz, SEEK_CUR);
			}
		}
		return $files;
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarファイルを閉じる
	// 引数  : なし
	// 返り値: なし
	////////////////////////////////////////////////////////////
	function close()
	{
		if ($this->status == TARLIB_STATUS_CREATE)
		{
			// バイナリーゼロを1024バイト出力
			fwrite($this->fp, $this->dummydata, TARLIB_HDR_LEN);
			fwrite($this->fp, $this->dummydata, TARLIB_HDR_LEN);

			// ファイルを閉じる
			if ($this->arc_kind == TARLIB_KIND_TGZ) {
				gzclose($this->fp);
			} else {
				 fclose($this->fp);
			}
		}
		else if ($this->status == TARLIB_STATUS_OPEN)
		{
			if ($this->arc_kind == TARLIB_KIND_TGZ) {
				gzclose($this->fp);
			} else {
				 fclose($this->fp);
			}
		}

		$this->status = TARLIB_STATUS_INIT;
	}

}
?>
