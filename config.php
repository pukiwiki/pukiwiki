<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: config.php,v 1.2 2003/03/10 11:32:23 panda Exp $
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
		$obj = &new ConfigTable();
		foreach (get_source($this->page) as $line)
		{
			if ($line == '')
			{
				continue;
			}
			
			$head = $line{0};
			$level = strspn($line,$head);
			
			if ($level > 3)
			{
				$obj->add_line($line);
				continue;
			}
			
			if ($head == '*')
			{
				if ($level == 1)
				{
					$this->objs[$obj->title] = &$obj;
					$obj = &new ConfigTable();
					$obj->add_line($line);
				}
				else
				{
					if (!is_a($obj,'ConfigTable_Direct'))
					{
						$obj = &new ConfigTable_Direct($obj->after);
					}
					$obj->set_key($line);
				}
			}
			else if ($head == '-' and $level > 1)
			{
				if (!is_a($obj,'ConfigTable_Direct'))
				{
					$obj = &new ConfigTable_Direct($obj->after);
				}
				$obj->add_value($line);
			}
			else if ($head == '|' and preg_match('/^\|(.+)\|\s*$/',$line,$matches))
			{
				if (!is_a($obj,'ConfigTable_Sequential'))
				{
					$obj = &new ConfigTable_Sequential($obj->after);
				}
				$obj->add_value(explode('|',$matches[1]));
			}
			else
			{
				$obj->add_line($line);
			}
		}
		$this->objs[$obj->title] = &$obj;
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
	function &get_object($title)
	{
		if (!array_key_exists($title,$this->objs))
		{
			$this->objs[$title] = &new ConfigTable(array('*'.trim($title)."\n"));
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
	// テーブルの名前
	var $title = '';
	// ページの内容(テーブル以外の部分)
	var $before = array();
	// 取得した値の配列
	var $values = array();
	// ページの内容(テーブル以外の部分)
	var $after = array();
	
	function ConfigTable($lines=NULL)
	{
		if ($lines !== NULL)
		{
			$this->title = trim(substr($lines[0],strspn($lines[0],'*')));
			$this->before = $lines;
		}
	}
	// 説明の追加
	function add_line($line)
	{
		$this->after[] = $line;
	}
	function toString()
	{
		return join('',$this->before).join('',$this->after);
	}
}
class ConfigTable_Sequential extends ConfigTable
{
	// 行の追加
	function add_value($value)
	{
		$this->values[] = (count($value) == 1) ? $value[0] : $value;
	}
	// 書式化
	function toString()
	{
		$retval = join('',$this->before);
		if (is_array($this->values))
		{
			foreach ($this->values as $value)
			{
				$value = is_array($value) ? join('|',$value) : $value;
				$retval .= "|$value|\n";
			}
		}
		return $retval.join('',$this->after);
	}
}
class ConfigTable_Direct extends ConfigTable
{
	// 取得したキーの配列。初期化時に使用する。
	var $_keys = array();
	
	// キーの設定
	function set_key($line)
	{
		$level = strspn($line,'*');
		$this->_keys[$level] = trim(substr($line,$level));
	}
	// 行の追加
	function add_value($line)
	{
		$level = strspn($line,'-');
		$arr = &$this->values;
		for ($n = 2; $n <= $level; $n++)
		{
			$arr = &$arr[$this->_keys[$n]];
		}
		$arr[] = trim(substr($line,$level));
	}
	// 書式化
	function toString($values = NULL,$level = 2)
	{
		$retval = join('',$this->before);
		if ($values == NULL)
		{
			$retval = parent::toString();
			$values = &$this->values;
		}
		foreach ($values as $key=>$value)
		{
			if (is_array($value))
			{
				$retval .= str_repeat('*',$level).$key."\n";
				$retval .= $this->toString($value,$level + 1);
			}
			else
			{
				$retval .= str_repeat('-',$level).$value."\n";
			}
		}
		return $retval.join('',$this->after);
	}
}
?>
