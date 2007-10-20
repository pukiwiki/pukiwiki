<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: config.php,v 1.7 2007/10/20 04:46:55 henoheno Exp $
// Copyright (C) 2003-2005 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Parse a PukiWiki page as a configuration page

/*
 * $obj = new Config('plugin/plugin_name/')
 * $obj->read();
 * $array = & $obj->get($title);
 * $array[] = array(4, 5, 6);		// Add - directly
 * $obj->add($title, array(4, 5, 6));	// Add - method of Config object
 * $array = array(1=>array(1, 2, 3));		// Replace - directly
 * $obj->put($title, array(1=>array(1, 2, 3));	// Replace - method of Config object
 * $obj->put_values($title, NULL);	// Delete
 * $obj->write();
 */

// Fixed prefix of configuration-page's name
define('PKWK_CONFIG_PREFIX', ':config/');

// Configuration-page manager
class Config
{
	var $name, $page; // Page name
	var $objs = array();

	function Config($name)
	{
		$this->name = $name;
		$this->page = PKWK_CONFIG_PREFIX . $name;
	}

	// Load the configuration-page
	function read()
	{
		if (! is_page($this->page)) return FALSE;

		$this->objs = array();
		$obj        = & new ConfigTable('');
		$matches = array();

		foreach (get_source($this->page) as $line) {
			if ($line == '') continue;

			$head  = $line{0};	// The first letter
			$level = strspn($line, $head);

			if ($level > 3) {
				$obj->add_line($line);

			} else if ($head == '*') {
				// Cut fixed-heading anchors
				$line = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/', '$1$2', $line);

				if ($level == 1) {
					$this->objs[$obj->title] = $obj;
					$obj = & new ConfigTable($line);
				} else {
					if (! is_a($obj, 'ConfigTable_Direct'))
						$obj = & new ConfigTable_Direct('', $obj);
					$obj->set_key($line);
				}
				
			} else if ($head == '-' && $level > 1) {
				if (! is_a($obj, 'ConfigTable_Direct'))
					$obj = & new ConfigTable_Direct('', $obj);
				$obj->add_value($line);

			} else if ($head == '|' && preg_match('/^\|(.+)\|\s*$/', $line, $matches)) {
				// Table row
				if (! is_a($obj, 'ConfigTable_Sequential'))
					$obj = & new ConfigTable_Sequential('', $obj);
				// Trim() each table cell
				$obj->add_value(array_map('trim', explode('|', $matches[1])));
			} else {
				$obj->add_line($line);
			}
		}
		$this->objs[$obj->title] = $obj;

		return TRUE;
	}

	// Get an array
	function & get($title)
	{
		$obj = & $this->get_object($title);
		return $obj->values;
	}

	// Set an array (Override)
	function put($title, $values)
	{
		$obj         = & $this->get_object($title);
		$obj->values = $values;
	}

	// Add a line
	function add($title, $value)
	{
		$obj = & $this->get_object($title);
		$obj->values[] = $value;
	}

	// Get an object (or create it)
	function & get_object($title)
	{
		if (! isset($this->objs[$title]))
			$this->objs[$title] = & new ConfigTable('*' . trim($title) . "\n");
		return $this->objs[$title];
	}

	function write()
	{
		page_write($this->page, $this->toString());
	}

	function toString()
	{
		$retval = '';
		foreach ($this->objs as $title=>$obj)
			$retval .= $obj->toString();
		return $retval;
	}
}

// Class holds array values
class ConfigTable
{
	var $title  = '';	// Table title
	var $before = array();	// Page contents (except table ones)
	var $after  = array();	// Page contents (except table ones)
	var $values = array();	// Table contents

	function ConfigTable($title, $obj = NULL)
	{
		if ($obj !== NULL) {
			$this->title  = $obj->title;
			$this->before = array_merge($obj->before, $obj->after);
		} else {
			$this->title  = trim(substr($title, strspn($title, '*')));
			$this->before[] = $title;
		}
	}

	// Add an  explanation
	function add_line($line)
	{
		$this->after[] = $line;
	}

	function toString()
	{
		return join('', $this->before) . join('', $this->after);
	}
}

class ConfigTable_Sequential extends ConfigTable
{
	// Add a line
	function add_value($value)
	{
		$this->values[] = (count($value) == 1) ? $value[0] : $value;
	}

	function toString()
	{
		$retval = join('', $this->before);
		if (is_array($this->values)) {
			foreach ($this->values as $value) {
				$value   = is_array($value) ? join('|', $value) : $value;
				$retval .= '|' . $value . '|' . "\n";
			}
		}
		$retval .= join('', $this->after);
		return $retval;
	}
}

class ConfigTable_Direct extends ConfigTable
{
	var $_keys = array();	// Used at initialization phase

	function set_key($line)
	{
		$level = strspn($line, '*');
		$this->_keys[$level] = trim(substr($line, $level));
	}

	// Add a line
	function add_value($line)
	{
		$level = strspn($line, '-');
		$arr   = & $this->values;
		for ($n = 2; $n <= $level; $n++)
			$arr = & $arr[$this->_keys[$n]];
		$arr[] = trim(substr($line, $level));
	}

	function toString($values = NULL, $level = 2)
	{
		$retval = '';
		$root   = ($values === NULL);
		if ($root) {
			$retval = join('', $this->before);
			$values = & $this->values;
		}
		foreach ($values as $key=>$value) {
			if (is_array($value)) {
				$retval .= str_repeat('*', $level) . $key . "\n";
				$retval .= $this->toString($value, $level + 1);
			} else {
				$retval .= str_repeat('-', $level - 1) . $value . "\n";
			}
		}
		if ($root) $retval .= join('', $this->after);

		return $retval;
	}
}
?>
