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
 * @version $Id: backup.php,v 1.3 2004/10/07 13:07:40 henoheno Exp $
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
function make_backup($page, $delete = FALSE)
{
	global $splitter, $cycle, $maxage;
	global $do_backup, $del_backup;

	if (! $do_backup) return;

	if ($del_backup && $delete) {
		backup_delete($page);
		return;
	}

	if (! is_page($page)) return;

	$lastmod = backup_get_filetime($page);
	if ($lastmod == 0 || UTIME - $lastmod > 60 * 60 * $cycle)
	{
		$backups = get_backup($page);
		$count   = count($backups) + 1;
		if ($count > $maxage) {
			//直後に1件追加するので、(最大件数-1)を超える要素を捨てる
			array_splice($backups, 0, $count - $maxage);
		}

		$strout = '';
		foreach($backups as $age=>$data) {
			$strout .= "$splitter {$data['time']}\n";
			$strout .= join('', $data['data']);
		}
		$strout = preg_replace("/([^\n])\n*$/", "$1\n", $strout);

		// 本文に含まれる$splitterをエスケープする(半角スペースを一個付加)
		$body = preg_replace('/^(' . preg_quote($splitter) . "\s\d+)$/", '$1 ', get_source($page));
		$body = "$splitter " . get_filetime($page) . "\n" . join('', $body);
		$body = preg_replace("/\n*$/", "\n", $body);

		$fp = backup_fopen($page, 'wb')
			or die_message('cannot write file ' . htmlspecialchars($realfilename) .
			'<br />maybe permission is not writable or filename is too long');
		backup_fputs($fp, $strout);
		backup_fputs($fp, $body);
		backup_fclose($fp);
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
 * @return    String    バックアップ($age!=0)
 *            Array     バックアップの配列($age==0)
 */
function get_backup($page, $age = 0)
{
	global $splitter;

	$lines = backup_file($page);
	if (! is_array($lines)) return array();

	$_age = 0;
	$retvars = $match = array();
	foreach($lines as $line) {
		if (preg_match("/^$splitter\s(\d+)$/", $line, $match)) {
			++$_age;
			if ($age > 0 && $_age > $age) {
				return $retvars[$age];
			}
			$retvars[$_age] = array('time'=>$match[1], 'data'=>array());
		} else {
			$retvars[$_age]['data'][] = $line;
		}
	}

	return $retvars;
}

/**
 * backup_get_filename
 * バックアップファイル名を取得する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    String    バックアップのファイル名
 */
function backup_get_filename($page)
{
	return BACKUP_DIR . encode($page) . BACKUP_EXT;
}

/**
 * backup_file_exists
 * バックアップファイルが存在するか
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Boolean   TRUE:ある FALSE:ない
 */
function backup_file_exists($page)
{
	return file_exists(backup_get_filename($page));
}

/**
 * backup_get_filetime
 * バックアップファイルの更新時刻を得る
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Integer   ファイルの更新時刻(GMT)
 */

function backup_get_filetime($page)
{
	return backup_file_exists($page) ?
		filemtime(backup_get_filename($page)) - LOCALZONE : 0;
}

/**
 * backup_delete
 * バックアップファイルを削除する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Boolean   FALSE:失敗
 */
function backup_delete($page)
{
	return unlink(backup_get_filename($page));
}

/////////////////////////////////////////////////

if (extension_loaded('zlib')) {
	// ファイルシステム関数
	// zlib関数を使用
	define('BACKUP_EXT', '.gz');

/**
 * backup_fopen
 * バックアップファイルを開く
 *
 * @access    private
 * @param     String    $page        ページ名
 * @param     String    $mode        モード
 *
 * @return    Boolean   FALSE:失敗
 */
	function backup_fopen($page, $mode)
	{
		return gzopen(backup_get_filename($page), $mode);
	}

/**
 * backup_fputs
 * バックアップファイルに書き込む
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 * @param     String    $str         文字列
 *
 * @return    Boolean   FALSE:失敗 その他:書き込んだバイト数
 */
	function backup_fputs($zp, $str)
	{
		return gzputs($zp, $str);
	}

/**
 * backup_fclose
 * バックアップファイルを閉じる
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 *
 * @return    Boolean   FALSE:失敗
 */
	function backup_fclose($zp)
	{
		return gzclose($zp);
	}

/**
 * backup_file
 * バックアップファイルの内容を取得する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Array     ファイルの内容
 */
	function backup_file($page)
	{
		return backup_file_exists($page) ?
			gzfile(backup_get_filename($page)) :
			array();
	}
}
/////////////////////////////////////////////////
else
{
	// ファイルシステム関数
	define('BACKUP_EXT', '.txt');

/**
 * backup_fopen
 * バックアップファイルを開く
 *
 * @access    private
 * @param     String    $page        ページ名
 * @param     String    $mode        モード
 *
 * @return    Boolean   FALSE:失敗
 */
	function backup_fopen($page, $mode)
	{
		return fopen(backup_get_filename($page), $mode);
	}

/**
 * backup_fputs
 * バックアップファイルに書き込む
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 * @param     String    $str         文字列
 *
 * @return    Boolean   FALSE:失敗 その他:書き込んだバイト数
 */
	function backup_fputs($zp, $str)
	{
		return fputs($zp, $str);
	}

/**
 * backup_fclose
 * バックアップファイルを閉じる
 *
 * @access    private
 * @param     Integer   $zp          ファイルポインタ
 *
 * @return    Boolean   FALSE:失敗
 */
	function backup_fclose($zp)
	{
		return fclose($zp);
	}

/**
 * backup_file
 * バックアップファイルの内容を取得する
 *
 * @access    private
 * @param     String    $page        ページ名
 *
 * @return    Array     ファイルの内容
 */
	function backup_file($page)
	{
		return backup_file_exists($page) ?
			file(backup_get_filename($page)) :
			array();
	}
}
?>
