<?php
/////////////////////////////////////////////////
// tarfile.inc.php
//       by teanan / Interfair Laboratory 2004.

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
// ファイル名をページ名にする際の日本語の文字コード
define('PLUGIN_DUMP_FILENAME_ENCORDING','SJIS');

// 最大アップロードサイズ
define('PLUGIN_DUMP_MAX_FILESIZE',1024);	// Kbyte

/////////////////////////////////////////////////
// 拡張子
define('PLUGIN_DUMP_SFX_TAR' , '.tar');
define('PLUGIN_DUMP_SFX_GZIP', '.tar.gz');

// TARファイル用定義
define('TAR_HDR_LEN',           512);	// ヘッダの大きさ
define('TAR_BLK_LEN',           512);	// 単位ブロック長さ
define('TAR_HDR_NAME_OFFSET',     0);	// ファイル名のオフセット
define('TAR_HDR_NAME_LEN',      100);	// ファイル名の最大長さ
define('TAR_HDR_MODE_OFFSET',   100);	// modeへのオフセット
define('TAR_HDR_UID_OFFSET',    108);	// uidへのオフセット
define('TAR_HDR_GID_OFFSET',    116);	// gidへのオフセット
define('TAR_HDR_SIZE_OFFSET',   124);	// サイズへのオフセット
define('TAR_HDR_SIZE_LEN',       12);	// サイズの長さ
define('TAR_HDR_MTIME_OFFSET',  136);	// 最終更新時刻のオフセット
define('TAR_HDR_MTIME_LEN',      12);	// 最終更新時刻の長さ
define('TAR_HDR_CHKSUM_OFFSET', 148);	// チェックサムのオフセット
define('TAR_HDR_CHKSUM_LEN',      8);	// チェックサムの長さ
define('TAR_HDR_TYPE_OFFSET',   156);	// ファイルタイプへのオフセット

// 状態定義
define('TAR_STATS_INIT',    0);		// 初期状態
define('TAR_STATS_OPEN',   10);		// 読み取り
define('TAR_STATS_CREATE', 20);		// 書き込み

define('TAR_DATA_MODE',      '100666 ');	// ファイルパーミッション
define('TAR_DATA_UGID',      '000000 ');	// uid / gid
define('TAR_DATA_CHKBLANKS', '        ');

// GNU拡張仕様(ロングファイル名対応)
define('TAR_DATA_LONGLINK', '././@LongLink');
define('TAR_HDR_FILE', '0');
define('TAR_HDR_LINK', 'L');

// アーカイブの種類
define('ARCFILE_GZIP', 0);
define('ARCFILE_TAR',  1);

// action定義
define('PLUGIN_DUMP_CREATE', 'act_download');	// Create & download
define('PLUGIN_DUMP_RESTORE',   'act_upload');	// Upload & restore


/////////////////////////////////////////////////
// プラグイン本体
function plugin_tarfile_action()
{
	global $adminpass;
	global $vars, $post;

	$pass = isset($post['pass']) ? $post['pass'] : NULL;
	$act  = isset($post['act'])  ? $post['act']  : NULL;

	$body = '';

//	if (pkwk_login($pass))	// for pukiwiki-1.4.4
	if ($pass !== NULL) {
		if((md5($pass) == $adminpass) && ($act !== NULL) ) {
			switch($act){
			case PLUGIN_DUMP_CREATE:
				$body = plugin_tarfile_download();
				break;
			case PLUGIN_DUMP_RESTORE:
				$retcode = plugin_tarfile_upload();
				$body .= $retcode['msg'];
				if($retcode['code'] == true) {
					// 正常終了
					$msg = 'アップロードが完了しました';
					return array('msg' => $msg, 'body' => $body);
				}
				break;
			}
		} else {
			$body = ($pass === NULL) ? '' : "<p><strong>パスワードが違います。</strong></p>\n";
		}
	}

	// 入力フォームを表示
	$body .= plugin_tarfile_disp_form();
	
	return array('msg' => 'tarfile', 'body' => $body);
}

/////////////////////////////////////////////////
// ファイルのダウンロード
function plugin_tarfile_download()
{
	global $post;

	// アーカイブの種類
	$arc_kind = ($post['pcmd'] == 'tar') ? ARCFILE_TAR : ARCFILE_GZIP;

	// ページ名に変換する
	$namedecode = isset($post['namedecode']) ? true : false;

	// バックアップディレクトリ
	$bk_wiki   = isset($post['bk_wiki'])   ? true : false;
	$bk_attach = isset($post['bk_attach']) ? true : false;
	$bk_backup = isset($post['bk_backup']) ? true : false;

	$tar = new compact_tarlib();

	// ファイルを生成する
	if($tar->create(CACHE_DIR, $arc_kind))
	{
		$filecount = 0;		// ファイル数
		if ($bk_wiki)   $filecount .= $tar->add(DATA_DIR,   '^[0-9A-F]+\.txt', $namedecode);
		if ($bk_attach) $filecount .= $tar->add(UPLOAD_DIR, '^[0-9A-F_]+',     $namedecode);
		if ($bk_backup) $filecount .= $tar->add(BACKUP_DIR, '^[0-9A-F]+\.gz',  $namedecode);
		$tar->close();

		if($filecount > 0) {
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
function plugin_tarfile_upload()
{
	global $post;

	$code = false;
	$msg  = '';

	// アーカイブの種類
	$arc_kind = ($post['pcmd'] == 'tar') ? ARCFILE_TAR : ARCFILE_GZIP;

	$filename = $_FILES['upload_file']['name'];


	// アップロードファイル
	$uploadfile = tempnam(CACHE_DIR, 'upload' );
	if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile))
	{
		// tarファイルを展開する
		$tar = new compact_tarlib();
		if($tar->open($uploadfile, $arc_kind))
		{
			// DATA_DIR (wiki/*.txt)
			$quote_wiki  = preg_quote(DATA_DIR, '/');
			$quote_wiki .= '((?:[0-9A-F])+)(\.txt){0,1}';

			// UPLOAD_DIR (attach/*)
			$quote_attach  = preg_quote(UPLOAD_DIR,'/');
			$quote_attach .= '((?:[0-9A-F]{2})+)_((?:[0-9A-F])+)';

			$pattern = "((^$quote_wiki)|(^$quote_attach))";
	
			$files = $tar->extract($pattern);
			if(! empty($files)) {
				$msg  = '<p><strong>展開したファイル一覧</strong><ul>';
				foreach($files as $name) {
					$msg .= "<li>$name</li>\n";
				}
				$msg .= '</ul></p>';
				$code = true;
			} else {
				$msg = '<p>展開できるファイルがありませんでした。</p>';
				$code = false;
			}
			$tar->close();
		}
		else
		{
			$msg = '<p>ファイルがみつかりませんでした。</p>';
			$code = false;
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
function download_tarfile($name, $arc_kind)
{
	// ファイル名
	$filename = strftime("tar%Y%m%d", time()) . $file_ext;

	if($arc_kind == ARCFILE_GZIP) {
		$filename .= PLUGIN_DUMP_SFX_GZIP;
	} else {
		$filename .= PLUGIN_DUMP_SFX_TAR;
	}

	$size = filesize($name);

	header('Content-Disposition: attachment; filename="' . $filename . '"');
	header('Content-Length: ' . $size);
	header('Content-Type: application/octet-stream');
	header('Pragma: no-cache');
	@readfile($name);
}

/////////////////////////////////////////////////
// 入力フォームを表示
function plugin_tarfile_disp_form()
{
	global $script, $defaultpage;

	$act_down  = PLUGIN_DUMP_CREATE;
	$act_up    = PLUGIN_DUMP_RESTORE;
	$maxsize   = PLUGIN_DUMP_MAX_FILESIZE * 1024;
	$maxsizekb = PLUGIN_DUMP_MAX_FILESIZE;

	$data = <<<EOD
<span class="small">
TARファイルバックアップ / リストアプラグイン
</span>
<br /><br />
<h3>Tarファイルのダウンロード</h3>
<form action="$script" method="post">
 <div>
  <input type="hidden" name="cmd"  value="tarfile" />
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
  <input type="checkbox" name="namedecode" /> ファイル名をページ名に変換 (※リストアに使うことはできなくなります)<br />
</p>
<p><strong>管理者パスワード</strong>
  <input type="password" name="pass" size="12" />
  <input type="submit"   name="ok"   value="OK" />
</p>
 </div>
</form>

<h3>データのリストア</h3>
<form enctype="multipart/form-data" action="$script" method="post">
 <div>
  <input type="hidden" name="cmd"  value="tarfile" />
  <input type="hidden" name="page" value="$defaultpage" />
  <input type="hidden" name="act"  value="$act_up" />
  <input type="hidden" name="max_file_size" value="$maxsize" />
<p><strong>[重要] 同じ名前のデータファイルは上書きされますので、十分ご注意ください。</strong></p>
<p><span class="small">
アップロード可能な最大ファイルサイズは、$maxsizekb KByte までです。<br />
</span>
  ファイル: <input type="file" name="upload_file" size="40" />
</p>
<p><strong>アーカイブの形式</strong>
<br />
  <input type="radio" name="pcmd" value="tgz" checked="checked" /> 〜.tar.gz 形式<br />
  <input type="radio" name="pcmd" value="tar" /> 〜.tar 形式
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
// tarデータの作成/展開ライブラリ

class compact_tarlib
{
	var $filename;
	var $fp;
	var $status;
	var $dummydata;
	var $arc_kind;

	// コンストラクタ
	function compact_tarlib( $name = '' ) {
		$this->fp       = false;
		$this->filename = $name;
		$this->status   = TAR_STATS_INIT;
		$arc_kind       = ARCFILE_GZIP;
	}
	
	////////////////////////////////////////////////////////////
	//
	// 関数  : tarファイルを開く
	// 引数  : tarファイル名
	// 返り値: ture .. 成功 , false .. 失敗
	//
	////////////////////////////////////////////////////////////
	function open( $name = '', $kind = ARCFILE_GZIP )
	{
		if( $name != '' ) $this->filename = $name;

		if($kind == ARCFILE_GZIP) {
			$this->arc_kind = ARCFILE_GZIP;
			$this->fp = gzopen( $this->filename, 'rb');
		} else {
			$this->arc_kind = ARCFILE_TAR;
			$this->fp =  fopen( $this->filename, 'rb');
		}

		if( $this->fp == false ) return false;	// 指定ファイルなし

		$this->status = TAR_STATS_OPEN;
		rewind($this->fp);

		return true;
	}

	////////////////////////////////////////////////////////////
	//
	// 関数  : tarファイルを作成する
	// 引数  : tarファイルを作成するパス
	// 返り値: ture .. 成功 , false .. 失敗
	//
	////////////////////////////////////////////////////////////
	function create( $odir, $kind = ARCFILE_GZIP )
	{
		$tname = tempnam( $odir, 'tar' );

		if($kind == ARCFILE_GZIP) {
			$this->arc_kind = ARCFILE_GZIP;
			$this->fp = gzopen($tname, 'wb');
		} else {
			$this->arc_kind = ARCFILE_TAR;
			$this->fp = @fopen($tname, 'wb');
		}
		if( $this->fp==false ) return false;	// 作成失敗

		// 作成に成功したらファイル名を記憶しておく
		$this->filename = $tname;
		$this->status   = TAR_STATS_CREATE;
		
		// ダミーデータ
		$this->dummydata = join('', array_fill(0, TAR_BLK_LEN, "\0"));
		rewind($this->fp);

		return true;
	}

	////////////////////////////////////////////////////////////
	//
	// 関数  : tarファイルを閉じる
	// 引数  : なし
	// 返り値: なし
	//
	////////////////////////////////////////////////////////////
	function close()
	{
		if($this->status = TAR_STATS_CREATE)
		{
			// バイナリーゼロを1024バイト出力
			flock($this->fp, LOCK_EX);
			fwrite($this->fp, $this->dummydata, TAR_HDR_LEN);
			fwrite($this->fp, $this->dummydata, TAR_HDR_LEN);
			flock($this->fp, LOCK_UN);

			// ファイルを閉じる
			if($this->arc_kind == ARCFILE_GZIP) {
				gzclose($this->fp);
			} else {
				 fclose($this->fp);
			}
		}
		else if($this->status = TAR_STATS_OPEN)
		{
			if($this->arc_kind == ARCFILE_GZIP) {
				gzclose($this->fp);
			} else {
				 fclose($this->fp);
			}
		}

		$this->status = TAR_STATS_INIT;
	}

	////////////////////////////////////////////////////////////
	//
	// 関数  : 指定したディレクトリにtarファイルを展開する
	// 引数  : 展開するファイルパターン(正規表現)
	// 返り値: 展開したファイル名の一覧
	// 補足  : ARAIさんのattachプラグインパッチを参考にしました
	//
	////////////////////////////////////////////////////////////
	function extract( $pattern )
	{
		if($this->status != TAR_STATS_OPEN)
			return ''; // openされていない
		
		$files = array();
		$longname = '';

		while(1) {
			$buff = fread($this->fp,TAR_HDR_LEN);
			if(strlen($buff) != TAR_HDR_LEN ) break;

			// ファイル名
			if($longname != '') {
				$name     = $longname;	// LongLink対応
				$longname = '';
			} else {
				$name = '';
				for ($i = 0; $i < TAR_HDR_NAME_LEN; $i++ ) {
					if($buff{$i + TAR_HDR_NAME_OFFSET} != '\0') {
						$name .= $buff{$i + TAR_HDR_NAME_OFFSET};
					} else {
						break;
					}
				}
			}

			$name = trim($name);
			if($name == '') break;	// 展開終了

			// チェックサムを取得しつつ、ブランクに置換していく
			$checksum = '';
			$chkblanks = TAR_DATA_CHKBLANKS;
			for ($i = 0; $i < TAR_HDR_CHKSUM_LEN; $i++ )
			{
				$checksum .= $buff{$i + TAR_HDR_CHKSUM_OFFSET};
				$buff{$i + TAR_HDR_CHKSUM_OFFSET} = $chkblanks{$i};
			}
			list($checksum) = sscanf('0' . trim($checksum), "%i");

			// チェックサムの計算
			$sum = 0;
			for($i = 0; $i < TAR_BLK_LEN; $i++ ) {
				$sum += 0xff & ord($buff{$i});
			}

			if($sum != $checksum) break;	// チェックサムエラー
				
			// サイズ
			$size = '';
			for ($i = 0; $i < TAR_HDR_SIZE_LEN; $i++ ) {
				$size .= $buff{$i + TAR_HDR_SIZE_OFFSET};
			}

			list($size) = sscanf('0' . trim($size), "%i");

			// ceil
			// データブロックは512byteでパディングされている
			$pdsz = ceil($size / TAR_BLK_LEN) * TAR_BLK_LEN;

			// 最終更新時刻
			$strmtime = '';
			for ($i = 0; $i < TAR_HDR_MTIME_LEN; $i++ ) {
				$strmtime .= $buff{$i + TAR_HDR_MTIME_OFFSET};
			}
			list($mtime) = sscanf('0' . trim($strmtime), "%i");

			// タイプフラグ
			$type = $buff{TAR_HDR_TYPE_OFFSET};

			if($name == TAR_DATA_LONGLINK)
			{
				// LongLink
				$buff = fread( $this->fp, $pdsz );
				$longname = substr($buff, 0, $size);
			}
			else if (preg_match("/$pattern/", $name) )
//			if ($type == 0 && preg_match("/$pattern/", $name) )
			{
				$buff = fread($this->fp, $pdsz);

				// 既に同じファイルがある場合は上書きされる
				if($fpw = @fopen($name, 'wb')) {
					fwrite($fpw, $buff, $size);
					fclose($fpw);
					chmod($name, 0666); // 念のためパーミッションを設定しておく
					touch($name, $mtime); // 最終更新時刻の修正
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
	//
	// 関数  : tarファイルに追加する
	// 引数  : $dir    .. ディレクトリ名
	//         $mask   .. 追加するファイル(正規表現)
	//         $decode .. ページ名の変換をするか
	// 返り値: 作成したファイル数
	//
	////////////////////////////////////////////////////////////
	function add($dir, $mask, $decode = false)
	{
		$retvalue = 0;
		
		if ($this->status != TAR_STATS_CREATE)
			return ''; // ファイルが作成されていない

		unset($files);

		//  指定されたパスのファイルのリストを取得する
		$dp = @opendir($dir) or
			die_message($dir . ' is not found or not readable.');
		while ($filename = readdir($dp)) {
			if(preg_match("/$mask/", $filename)) {
				$files[] = $dir . $filename;
			}
		}
		closedir($dp);
		
		sort($files);

		foreach($files as $name)
		{
			// Tarに格納するファイル名
			if($decode == true )
			{
				// ファイル名をページ名に変換する処理
				$dirname  = dirname(trim($name)) . '/';
				$filename = basename(trim($name));
				if(preg_match("/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)/", $filename, $matches))
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
						$filename = str_replace(':','_',$filename);
						$filename = str_replace('\\','_',$filename);
					}
				}
				$filename = $dirname . $filename;
				if(function_exists('mb_convert_encoding')) {
					// ファイル名の文字コードを変換
					$filename = mb_convert_encoding($filename, PLUGIN_DUMP_FILENAME_ENCORDING);
				}
			}
			else
			{
				$filename = $name;
			}

			// 最終更新時刻
			$mtime = filemtime( $name );

			// ファイル名長のチェック
			if(strlen($filename) > TAR_HDR_NAME_LEN) {
				// LongLink対応
				$size = strlen($filename);
				// LonkLinkヘッダ生成
				$tar_data = $this->make_header(TAR_DATA_LONGLINK, $size, $mtime, TAR_HDR_LINK);
				// ファイル出力
	 			$this->write_data(join('', $tar_data), $filename, $size);
			}

			// ファイルサイズを取得
			$size = filesize( $name );
			if ($size == FALSE) {
				die_message($name . ' is not found or not readable.');
				continue;	// ファイルがない
			}

			// ヘッダ生成
			$tar_data = $this->make_header($filename, $size, $mtime, TAR_HDR_FILE);

			// ファイルデータの取得
			$fpr = @fopen($name , 'rb');
			$data = fread($fpr, $size);
			fclose( $fpr );

			// ファイル出力
			$this->write_data(join('', $tar_data), $data, $size);
			++$retvalue;
		}
		return $retvalue;
	}
	
	////////////////////////////////////////////////////////////
	//
	// 関数  : tarのヘッダ情報を生成する
	// 引数  : $filename .. ファイル名
	//         $size     .. データサイズ
	//         $mtime    .. 最終更新日
	//         $typeflag .. TypeFlag (file/link)
	// 戻り値: tarヘッダ情報
	//
	////////////////////////////////////////////////////////////
	function make_header($filename, $size, $mtime, $typeflag)
	{
		$tar_data = array_fill(0, TAR_HDR_LEN, "\0");
		
		// ファイル名を保存
		for($i = 0; $i < strlen($filename); $i++ )
		{
			if ($i < TAR_HDR_NAME_LEN) {
				$tar_data[$i + TAR_HDR_NAME_OFFSET] = $filename{$i};
			} else {
				break;	// ファイル名が長すぎ
			}
		}

		// mode
		$modeid = TAR_DATA_MODE;
		for($i = 0; $i < strlen($modeid); $i++ ) {
			$tar_data[$i + TAR_HDR_MODE_OFFSET] = $modeid{$i};
		}

		// uid / gid
		$ugid = TAR_DATA_UGID;
		for($i = 0; $i < strlen($ugid); $i++ ) {
			$tar_data[$i + TAR_HDR_UID_OFFSET] = $ugid{$i};
			$tar_data[$i + TAR_HDR_GID_OFFSET] = $ugid{$i};
		}

		// サイズ
		$strsize = sprintf('%11o', $size);
		for($i = 0; $i < strlen($strsize); $i++ ) {
			$tar_data[$i + TAR_HDR_SIZE_OFFSET] = $strsize{$i};
		}

		// 最終更新時刻
		$strmtime = sprintf('%o', $mtime);
		for($i = 0; $i < strlen($strmtime); $i++ ) {
			$tar_data[$i + TAR_HDR_MTIME_OFFSET] = $strmtime{$i};
		}

		// チェックサム計算用のブランクを設定
		$chkblanks = TAR_DATA_CHKBLANKS;
		for($i = 0; $i < strlen($chkblanks); $i++ ) {
			$tar_data[$i + TAR_HDR_CHKSUM_OFFSET] = $chkblanks{$i};
		}

		// タイプフラグ
		$tar_data[TAR_HDR_TYPE_OFFSET] = $typeflag;

		// チェックサムの計算
		$sum = 0;
		for($i = 0; $i < TAR_BLK_LEN; $i++ ) {
			$sum += 0xff & ord($tar_data[$i]);
		}
		$strchksum = sprintf('%7o',$sum);
		for($i = 0; $i < strlen($strchksum); $i++ ) {
			$tar_data[$i + TAR_HDR_CHKSUM_OFFSET] = $strchksum{$i};
		}
		return $tar_data;
	}
	
	////////////////////////////////////////////////////////////
	//
	// 関数  : tarデータのファイル出力
	// 引数  : $header .. tarヘッダ情報
	//         $body   .. tarデータ
	//         $size   .. データサイズ
	// 戻り値: なし
	//
	////////////////////////////////////////////////////////////
	function write_data($header, $body, $size)
	{
		// フィルするサイズを計算しておく
		$fixsize = ceil($size / TAR_BLK_LEN) * TAR_BLK_LEN;
		$fixsize -= $size;

		flock($this->fp, LOCK_EX);
		fwrite($this->fp, $header, TAR_HDR_LEN); // ヘッダ出力
		fwrite($this->fp, $body, $size); // データ出力

		// サイズ調整(あまった部分を0でフィル)
		fwrite($this->fp, $this->dummydata, $fixsize); // Padding

		flock($this->fp, LOCK_UN);
	}
}
?>
