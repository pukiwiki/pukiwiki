<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: file.php,v 1.31 2003/09/12 06:43:04 arino Exp $
//

// ソースを取得
function get_source($page=NULL)
{
	if (!is_page($page))
	{
		return array();
	}
	return str_replace("\r",'',file(get_filename($page)));
}

// ページの更新時刻を得る
function get_filetime($page)
{
	if (!is_page($page))
	{
		return 0;
	}
	return filemtime(get_filename($page)) - LOCALZONE;
}

// ページのファイル名を得る
function get_filename($page)
{
	return DATA_DIR.encode($page).'.txt';
}

// ページの出力
function page_write($page,$postdata,$notimestamp=FALSE)
{
	$postdata = make_str_rules($postdata);
	
	// 差分ファイルの作成
	$oldpostdata = is_page($page) ? join('',get_source($page)) : '';
	$diffdata = do_diff($oldpostdata,$postdata);
	file_write(DIFF_DIR,$page,$diffdata);
	
	// バックアップの作成
	make_backup($page,$postdata == '');
	
	// ファイルの書き込み
	file_write(DATA_DIR,$page,$postdata,$notimestamp);
	
	// TrackBack Ping の送信
	// 「追加」行を抽出
	$lines = join("\n",preg_replace('/^\+/','',preg_grep('/^\+/',explode("\n",$diffdata))));
	tb_send($page,$lines);
	
	// linkデータベースを更新
	links_update($page);
}

// ユーザ定義ルール(ソースを置換する)
function make_str_rules($str)
{
	global $str_rules,$fixed_heading_anchor;
	
	$arr = explode("\n",$str);
	
	$retvars = array();
	foreach ($arr as $str)
	{
		if ($str != '' and $str{0} != ' ' and $str{0} != "\t")
		{
			foreach ($str_rules as $rule => $replace)
			{
				$str = preg_replace("/$rule/",$replace,$str);
			}
		}
		// 見出しに固有IDを付与する
		if ($fixed_heading_anchor and
			preg_match('/^(\*{1,3}(.(?!\[#[A-Za-z][\w-]+\]))+)$/',$str,$matches))
		{
			// 固有IDを生成する
			// ランダムな英字(1文字)+md5ハッシュのランダムな部分文字列(7文字)
			$anchor = chr(mt_rand(ord('a'),ord('z'))).
				substr(md5(uniqid(substr($matches[1],0,100),1)),mt_rand(0,24),7);
			$str = rtrim($matches[1])." [#$anchor]";
		}
		$retvars[] = $str;
	}
	
	return join("\n",$retvars);
}

// ファイルへの出力
function file_write($dir,$page,$str,$notimestamp=FALSE)
{
	global $post,$update_exec;
	global $_msg_invalidiwn;
	global $notify,$notify_to,$notify_from,$notify_subject,$notify_header;
	
	if (!is_pagename($page))
	{
		die_message(
			str_replace('$1',htmlspecialchars($page),
				str_replace('$2','WikiName',$_msg_invalidiwn)
			)
		);
	}
	$page = strip_bracket($page);
	$timestamp = FALSE;
	$file = $dir.encode($page).'.txt';
	
	if ($dir == DATA_DIR and $str == '' and file_exists($file))
	{
		unlink($file);
	}
	if ($str != '')
	{
		$str = preg_replace("/\r/",'',$str);
		$str = rtrim($str)."\n";
		
		if ($notimestamp and file_exists($file))
		{
			$timestamp = filemtime($file) - LOCALZONE;
		}
		
		$fp = fopen($file,'w')
			or die_message('cannot write page file or diff file or other'.htmlspecialchars($page).'<br />maybe permission is not writable or filename is too long');
		flock($fp,LOCK_EX);
		fputs($fp,$str);
		flock($fp,LOCK_UN);
		fclose($fp);
		if ($timestamp)
		{
			touch($file,$timestamp + LOCALZONE);
		}
	}
	
	// is_pageのキャッシュをクリアする。
	is_page($page,TRUE);
	
	if (!$timestamp and $dir == DATA_DIR)
	{
		put_lastmodified();
	}
	
	if ($update_exec and $dir == DATA_DIR)
	{
		system($update_exec.' > /dev/null &');
	}
	
	if ($notify and $dir == DIFF_DIR)
	{
 		$subject = str_replace('$page',$page,$notify_subject);
 		mb_language(LANG);
 		mb_send_mail($notify_to,$subject,$str,$notify_header);
	}
}

// 最終更新ページの更新
function put_lastmodified()
{
	global $maxshow,$whatsnew,$non_list,$autolink;

	$pages = get_existpages();
	$recent_pages = array();
	foreach($pages as $page)
	{
		if ($page != $whatsnew and !preg_match("/$non_list/",$page))
		{
			$recent_pages[$page] = get_filetime($page);
		}
	}
	
	//時刻降順でソート
	arsort($recent_pages,SORT_NUMERIC);
	
	// create recent.dat (for recent.inc.php)
	$fp = fopen(CACHE_DIR.'recent.dat','w')
		or die_message('cannot write cache file '.CACHE_DIR.'recent.dat<br />maybe permission is not writable or filename is too long');
	flock($fp,LOCK_EX);
	foreach ($recent_pages as $page=>$time)
	{
		fputs($fp,"$time\t$page\n");
	}
	flock($fp,LOCK_UN);
	fclose($fp);

	// create RecentChanges
	$fp = fopen(get_filename($whatsnew),'w')
		or die_message('cannot write page file '.htmlspecialchars($whatsnew).'<br />maybe permission is not writable or filename is too long');
	flock($fp,LOCK_EX);
	foreach (array_splice($recent_pages,0,$maxshow) as $page=>$time)
	{
		$s_lastmod = htmlspecialchars(format_date($time));
		$s_page = htmlspecialchars($page);
		fputs($fp, "-$s_lastmod - [[$s_page]]\n");
	}
	fputs($fp,"#norelated\n"); // :)
	flock($fp,LOCK_UN);
	fclose($fp);
	
	// for autolink
	if ($autolink)
	{
		list($pattern,$forceignorelist) = get_autolink_pattern($pages);
		
		$fp = fopen(CACHE_DIR.'autolink.dat','w')
			or die_message('cannot write autolink file '.CACHE_DIR.'/autolink.dat<br />maybe permission is not writable');
		flock($fp,LOCK_EX);
		fputs($fp,$pattern."\n");
		fputs($fp,join("\t",$forceignorelist));
		flock($fp,LOCK_UN);
		fclose($fp);
	}
}

// 指定されたページの経過時刻
function get_pg_passage($page,$sw=TRUE)
{
	global $show_passage;
	static $pg_passage = array();
	
	if (!$show_passage)
	{
		return '';
	}
	
	if (!array_key_exists($page,$pg_passage))
	{
		$pg_passage[$page] = (is_page($page) and $time = get_filetime($page)) ?
			get_passage($time) : '';
	}
	
	return $sw ? "<small>{$pg_passage[$page]}</small>" : " {$pg_passage[$page]}";
}

// Last-Modified ヘッダ
function header_lastmod($page=NULL)
{
	global $lastmod;
	
	if ($lastmod and is_page($page))
	{
		header('Last-Modified: '.date('D, d M Y H:i:s',get_filetime($page)).' GMT');
	}
}

// 全ページ名を配列に
function get_existpages($dir=DATA_DIR,$ext='.txt')
{
	$aryret = array();
	
	$pattern = '^((?:[0-9A-F]{2})+)';
	if ($ext != '')
	{
		$pattern .= preg_quote($ext,'/').'$';
	}
	$dp = @opendir($dir)
		or die_message($dir. ' is not found or not readable.');
	while ($file = readdir($dp))
	{
		if (preg_match("/$pattern/",$file,$matches))
		{
			$aryret[$file] = decode($matches[1]);
		}
	}
	closedir($dp);
	return $aryret;
}
// ページ名の読みを配列に
function get_readings()
{
	global $pagereading_enable, $pagereading_kanji2kana_converter;
	global $pagereading_kanji2kana_encoding, $pagereading_chasen_path;
	global $pagereading_kakasi_path, $pagereading_config_page;
	
	$pages = get_existpages();
	
	$readings = array();
	foreach ($pages as $page) {
		$readings[$page] = '';
	}
	foreach (get_source($pagereading_config_page) as $line) {
		$line = preg_replace('/[\s\r\n]+$/', '', $line);
		if(preg_match('/^-\[\[([^]]+)\]\]\s(.+)$/', $line, $matches)
		   and isset($readings[$matches[1]])) {
			$readings[$matches[1]] = $matches[2];
		}
	}
	if($pagereading_enable) {
		// ChaSen/KAKASI 呼び出しが有効に設定されている場合
		
		// 読みが不明のページがあるかチェック
		foreach ($readings as $page => $reading) {
			if($reading=='') {
				$unknownPage = TRUE;
				break;
			}
		}
		if($unknownPage) {
			// 読みが不明のページがある場合
			//			$tmpfname = tempnam(CACHE_DIR, 'PageReading');
			$tmpfname = tempnam(CACHE_DIR, 'PageReading');
			$fp = fopen($tmpfname, "w")
				or die_message("cannot write temporary file '$tmpfname'.\n");
			foreach ($readings as $page => $reading) {
				if($reading=='') {
					fputs($fp, mb_convert_encoding("$page\n", $pagereading_kanji2kana_encoding, SOURCE_ENCODING));
				}
			}
			fclose($fp);
			// ChaSen/KAKASI を実行
			switch(strtolower($pagereading_kanji2kana_converter)) {
			case 'chasen':
				if(!file_exists($pagereading_chasen_path)) {
					unlink($tmpfname);
					die_message("CHASEN not found: $pagereading_chasen_path");
				}
				$fp = popen("$pagereading_chasen_path -F %y $tmpfname", "r");
				if(!$fp) {
					unlink($tmpfname);
					die_message("ChaSen execution failed: $pagereading_chasen_path -F %y $tmpfname");
				}
				break;
			case 'kakasi':
			case 'kakashi':
				if(!file_exists($pagereading_kakasi_path)) {
					unlink($tmpfname);
					die_message("KAKASI not found: $pagereading_kakasi_path");
				}					
				$fp = popen("$pagereading_kakasi_path -kK -HK -JK <$tmpfname", "r");
				if(!$fp) {
					unlink($tmpfname);
					die_message("KAKASI execution failed: $pagereading_kakasi_path -kK -HK -JK <$tmpfname");
				}
				break;
			default:
				die_message("unknown kanji-kana converter: $pagereading_kanji2kana_converter.");
				break;
			}
			foreach ($readings as $page => $reading) {
				if($reading=='') {
					$line = fgets($fp);
					$line = mb_convert_encoding($line, SOURCE_ENCODING, $pagereading_kanji2kana_encoding);
					$line = preg_replace('/[\s\r\n]+$/', '', $line);
					$readings[$page] = $line;
				}
			}
			pclose($fp);
			unlink($tmpfname) or die_message("temporary file can not be removed: $tmpfname");
			// 読みでソート
			asort($readings);
			
			// ページを書き込み
			$body = '';
			foreach ($readings as $page => $reading) {
				$body .= "-[[$page]] $reading\n";
			}
			page_write($pagereading_config_page, $body);
		}
	}
	
	// 読み不明のページは、そのままページ名を返す (ChaSen/KAKASI 呼
	// び出しが無効に設定されている場合や、ChaSen/KAKASI 呼び出しに
	// 失敗した時の為)
	foreach ($pages as $page) {
		if($readings[$page]=='') {
			$readings[$page] = $page;
		}
	}
	
	return $readings;
}
//ファイル名の一覧を配列に(エンコード済み、拡張子を指定)
function get_existfiles($dir,$ext)
{
	$aryret = array();
	
	$pattern = '^(?:[0-9A-F]{2})+'.preg_quote($ext,'/').'$';
	$dp = @opendir($dir)
		or die_message($dir. ' is not found or not readable.');
	while ($file = readdir($dp)) {
		if (preg_match("/$pattern/",$file)) {
			$aryret[] = $dir.$file;
		}
	}
	closedir($dp);
	return $aryret;
}
//あるページの関連ページを得る
function links_get_related($page)
{
	global $vars,$related;
	static $links = array();
	
	if (array_key_exists($page,$links))
	{
		return $links[$page];
	}
	
	// 可能ならmake_link()で生成した関連ページを取り込む
	$links[$page] = ($page == $vars['page']) ? $related : array();
	
	// データベースから関連ページを得る
	$links[$page] += links_get_related_db($vars['page']);
	
	return $links[$page];
}
?>
