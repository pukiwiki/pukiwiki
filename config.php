<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: config.php,v 1.1 2003/03/07 06:45:26 panda Exp $
//
/*
 * プラグインの設定をPukiWikiのページに記述する
 *
 * // オブジェクト生成
 * $obj = new Config('plugin/プラグイン名/')
 * // 読み出し
 * $obj->read();
 * // 配列取得
 * $array = &$obj->get($title);
 * // 追加 - 直接
 * $array[] = array(4,5,6);
 * // 追加 - Configオブジェクトのメソッド
 * $obj->add($title,array(4,5,6));
 * // 置換 - 直接
 * $array = array(1=>array(1,2,3));
 * // 置換 - Configオブジェクトのメソッド
 * $obj->put($title,array(1=>array(1,2,3));
 * // 消去 
 * $obj->put_values($title,NULL);
 * // 書き込み
 * $obj->write();
 * 
 */

// ページ名のプレフィクス
define('CONFIG_BASE',':config/');

// 設定ページ管理
class Config
{
	// ページ名
	var $page;
	// 要素
	var $objs;
	
	function Config($name)
	{
		$this->page = CONFIG_BASE.$name;
	}
	// ページを読み込む
	function read()
	{
		$this->objs = array();
		$title = '';
		$obj = &$this->get_object($title);
		foreach (get_source($this->page) as $line)
		{
			if ($line != '' and $line{0} == '|' and preg_match('/^\|(.+)\|\s*$/',$line,$matches))
			{
				$obj->add_value(explode('|',$matches[1]));
			}
			else if ($line != '' and $line{0} == '*')
			{
				$level = strspn($line,'*');
				$title = trim(substr($line,$level));
				$obj = &$this->get_object($title,$level);
			}
			else
			{
				$obj->add_line($line);
			}
		}
		$this->objs[$title] = &$obj;
	}
	// 配列を取得する
	function &get($title)
	{
		$obj = &$this->get_object($title);
		return $obj->values;
	}
	// 配列を設定する(上書き)
	function put($title,$values)
	{
		$obj = &$this->get_object($title);
		$obj->values = $values;
	}
	// 行を追加する
	function add($title,$value)
	{
		$obj = &$this->get_object($title);
		$obj->values[] = $value;
	}
	// オブジェクトを取得する(ないときは作る)
	function &get_object($title,$level=1)
	{
		if (!array_key_exists($title,$this->objs))
		{
			$this->objs[$title] = &new ConfigTable(str_repeat('*',$level).$title."\n");
		}
		return $this->objs[$title];
	}
	// ページに書き込む
	function write()
	{
		page_write($this->page, $this->toString());
	}
	// 書式化
	function toString()
	{
		$retval = '';
		foreach ($this->objs as $title=>$obj)
		{
			$retval .= $obj->toString();
		}
		return $retval;
	}
}
//配列値を保持するクラス
class ConfigTable
{
	// 取得した値の配列
	var $values = array();
	// ページの内容(テーブル以外の部分)
	var $line;
	
	function ConfigTable($title)
	{
		$this->line = $title;
	}
	// 行の追加
	function add_value($value)
	{
		$this->values[] = (count($value) == 1) ? $value[0] : $value;
	}
	// 説明の追加
	function add_line($line)
	{
		$this->line .= $line;
	}
	// 書式化
	function toString()
	{
		$retval = $this->line;
		if (is_array($this->values))
		{
			foreach ($this->values as $value)
			{
				$value = is_array($value) ? join('|',$value) : $value;
				$retval .= "|$value|\n";
			}
		}
		$retval .= "\n"; // 空行 :)
		
		return $retval;
	}
}
?>
