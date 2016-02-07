<?php
/**
 *
 * PukiWiki - Yet another WikiWikiWeb clone.
 *
 * backup.php
 *
 * バックアップを管理する
 *
 * @package org.pukiwiki
 * @access  public
 * @author
 * @create
 * Copyright (C)
 *   2002-2016 PukiWiki Development Team
 *   2001-2002 Originally written by yu-ji
 * License: GPL v2 or (at your option) any later version
 **/

/**
 * make_backup
 * バックアップを作成する
 *
 * @access    public
 * @param     String    $page        ページ名
 * @param     Boolean   $delete      TRUE:バックアップを削除する
 *
 * @return    Void
 */

function make_backup($page, $is_delete, $wikitext)
{
	global $cycle, $maxage;
	global $do_backup, $del_backup;
	global $auth_user;

	if (PKWK_READONLY || ! $do_backup) return;

	if ($del_backup && $is_delete) {
		_backup_delete($page);
		return;
	}

	if (! is_page($page)) return;

	$lastmod = _backup_get_filetime($page);
	$backups = get_backup($page);
	$is_author_differ = false;
	$need_backup_by_time = $lastmod == 0 || UTIME - $lastmod > 60 * 60 * $cycle;
	if (!$need_backup_by_time) {
		// Backup file is saved recently, but the author may differ.
		$last_content = get_source($page, TRUE, TRUE);
		$m = array();
		$prev_author = null;
		if (preg_match('/^#author\("([^"]+)","([^"]*)","([^"]*)"\)/m', $last_content, $m)) {
			$prev_author = preg_replace('/^[^:]+:/', '', $m[2]);
		}
		if ($prev_author !== $auth_user) {
			$is_author_differ = true;
		}
	}
	if ($need_backup_by_time || $is_author_differ || $is_delete)
	{
		$backups = get_backup($page);
		$count   = count($backups) + 1;
		// 直後に1件追加するので、(最大件数 - 1)を超える要素を捨てる
		if ($count > $maxage)
			array_splice($backups, 0, $count - $maxage);

		$strout = '';
		foreach($backups as $age=>$data) {
			$strout .= PKWK_SPLITTER . ' ' . $data['time'] . "\n"; // Splitter format
			$strout .= join('', $data['data']);
			unset($backups[$age]);
		}
		$strout = preg_replace("/([^\n])\n*$/", "$1\n", $strout);

		// Escape 'lines equal to PKWK_SPLITTER', by inserting a space
		$body = preg_replace('/^(' . preg_quote(PKWK_SPLITTER) . "\s\d+)$/", '$1 ', get_source($page));
		$body = PKWK_SPLITTER . ' ' . get_filetime($page) . "\n" . join('', $body);
		$body = preg_replace("/\n*$/", "\n", $body);
		$body_on_delete = '';
		if ($is_delete) {
			$body_on_delete = PKWK_SPLITTER . ' ' . UTIME . "\n" . $wikitext;
			$body_on_delete = preg_replace("/\n*$/", "\n", $body_on_delete);
		}
		$fp = _backup_fopen($page, 'wb')
			or die_message('Cannot open ' . htmlsc(_backup_get_filename($page)) .
			'<br />Maybe permission is not writable or filename is too long');
		_backup_fputs($fp, $strout);
		_backup_fputs($fp, $body);
		_backup_fputs($fp, $body_on_delete);
		_backup_fclose($fp);
	}
}

/**
 * get_backup
 * バックアップを取得する
 * $age = 0または省略 : 全てのバックアップデータを配列で取得する
 * $age > 0           : 指定した世代のバックアップデータを取得する
 *
 * @access    public
 * @param     String    $page        ページ名
 * @param     Integer   $age         バックアップの世代番号 省略時は全て
 *
 * @return    String    バックアップ       ($age != 0)
 *            Array     バックアップの配列 ($age == 0)
 */
function get_backup($page, $age = 0)
{
	$lines = _backup_file($page);
	if (! is_array($lines)) return array();

	$_age = 0;
	$retvars = $match = array();
	$regex_splitter = '/^' . preg_quote(PKWK_SPLITTER) . '\s(\d+)$/';
	foreach($lines as $index => $line) {
		if (preg_match($regex_splitter, $line, $match)) {
			// A splitter, tells new data of backup will come
			++$_age;
			if ($age > 0 && $_age > $age)
				return $retvars[$age];

			// Allocate
			$retvars[$_age] = array('time'=>$match[1], 'data'=>array());
		} else if (preg_match('/^\s*#author\("([^"]+)","([^"]+)","([^"]*)"\)/', $line, $match)) {
			$retvars[$_age]['author_datetime'] = $match[1];
			$retvars[$_age]['author'] = $match[2];
			$retvars[$_age]['author_fullname'] = $match[3];
			$retvars[$_age]['data'][] = $line;
		} else {
			// The first ... the last line of the data
			$retvars[$_age]['data'][] = $line;
		}
		unset($lines[$index]);
	}

	return $retvars;
}

/**
 * _backup_get_filename
 * バックアップファイル名を取得する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    String    バックアップのファイル名
 */
function _backup_get_filename($page)
{
	return BACKUP_DIR . encode($page) . BACKUP_EXT;
}

/**
 * _backup_file_exists
 * バックアップファイルが存在するか
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Boolean   TRUE:ある FALSE:ない
 */
function _backup_file_exists($page)
{
	return file_exists(_backup_get_filename($page));
}

/**
 * _backup_get_filetime
 * バックアップファイルの更新時刻を得る
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Integer   ファイルの更新時刻(GMT)
 */

function _backup_get_filetime($page)
{
	return _backup_file_exists($page) ?
		filemtime(_backup_get_filename($page)) - LOCALZONE : 0;
}

/**
 * _backup_delete
 * バックアップファイルを削除する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Boolean   FALSE:失敗
 */
function _backup_delete($page)
{
	return unlink(_backup_get_filename($page));
}

/////////////////////////////////////////////////

if (extension_loaded('zlib')) {
	// ファイルシステム関数
	// zlib関数を使用
	define('BACKUP_EXT', '.gz');

/**
 * _backup_fopen
 * バックアップファイルを開く
 *
 * @access    private
 * @param     String    $page        ページ名
 * @param     String    $mode        モード
 *
 * @return    Boolean   FALSE:失敗
 */
	function _backup_fopen($page, $mode)
	{
		return gzopen(_backup_get_filename($page), $mode);
	}

/**
 * _backup_fputs
 * バックアップファイルに書き込む
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 * @param     String    $str         文字列
 *
 * @return    Boolean   FALSE:失敗 その他:書き込んだバイト数
 */
	function _backup_fputs($zp, $str)
	{
		return gzputs($zp, $str);
	}

/**
 * _backup_fclose
 * バックアップファイルを閉じる
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 *
 * @return    Boolean   FALSE:失敗
 */
	function _backup_fclose($zp)
	{
		return gzclose($zp);
	}

/**
 * _backup_file
 * バックアップファイルの内容を取得する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Array     ファイルの内容
 */
	function _backup_file($page)
	{
		return _backup_file_exists($page) ?
			gzfile(_backup_get_filename($page)) :
			array();
	}
}
/////////////////////////////////////////////////
else
{
	// ファイルシステム関数
	define('BACKUP_EXT', '.txt');

/**
 * _backup_fopen
 * バックアップファイルを開く
 *
 * @access    private
 * @param     String    $page        ページ名
 * @param     String    $mode        モード
 *
 * @return    Boolean   FALSE:失敗
 */
	function _backup_fopen($page, $mode)
	{
		return fopen(_backup_get_filename($page), $mode);
	}

/**
 * _backup_fputs
 * バックアップファイルに書き込む
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 * @param     String    $str         文字列
 *
 * @return    Boolean   FALSE:失敗 その他:書き込んだバイト数
 */
	function _backup_fputs($zp, $str)
	{
		return fputs($zp, $str);
	}

/**
 * _backup_fclose
 * バックアップファイルを閉じる
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 *
 * @return    Boolean   FALSE:失敗
 */
	function _backup_fclose($zp)
	{
		return fclose($zp);
	}

/**
 * _backup_file
 * バックアップファイルの内容を取得する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Array     ファイルの内容
 */
	function _backup_file($page)
	{
		return _backup_file_exists($page) ?
			file(_backup_get_filename($page)) :
			array();
	}
}
