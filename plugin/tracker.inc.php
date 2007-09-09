<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: tracker.inc.php,v 1.47 2007/09/09 00:57:57 henoheno Exp $
// Copyright (C) 2003-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Issue tracker plugin (See Also bugtrack plugin)

define('PLUGIN_TRACKER_USAGE',      '#tracker([config[/form][,basepage]])');
define('PLUGIN_TRACKER_LIST_USAGE', '#tracker_list([config][[,base][,field:sort[;field:sort ...][,limit]]])');

define('PLUGIN_TRACKER_DEFAULT_CONFIG', 'default');
define('PLUGIN_TRACKER_DEFAULT_FORM',   'form');

// #tracker_list: Excluding pattern
define('PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN','#^SubMenu$|/#');	// 'SubMenu' and using '/'
//define('PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN','#(?!)#');		// Nothing excluded

// #tracker_list: Show error rows (can't capture columns properly)
define('PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE', 1);


// Show a form
function plugin_tracker_convert()
{
	global $vars;

	if (PKWK_READONLY) return ''; // Show nothing

	$base = $refer = isset($vars['page']) ? $vars['page'] : '';
	$config_name = PLUGIN_TRACKER_DEFAULT_CONFIG;
	$form        = PLUGIN_TRACKER_DEFAULT_FORM;

	$args = func_get_args();
	$argc = count($args);
	if ($argc > 2) {
		return PLUGIN_TRACKER_USAGE . '<br />';
	} else {
		if ($argc > 1) {
			// Base page name
			$arg = get_fullname($args[1], $base);
			if (is_pagename($arg)) $base = $arg;
		}
		if ($argc > 0 && $args[0] != '') {
			// Configuration name AND form name
			$arg = explode('/', $args[0], 2);
			if ($arg[0] != '' ) $config_name = $arg[0];
			if (isset($arg[1])) $form        = $arg[1];
		}
	}

	$config = new Config('plugin/tracker/' . $config_name);
	if (! $config->read()) {
		return '#tracker: Config \'' . htmlspecialchars($config_name) . '\' not found<br />';
	}
	$config->config_name = $config_name;
	$form = $config->page . '/' . $form;
	if (! is_page($form)) {
		return '#tracker: Form \'' . make_pagelink($form) . '\' not found<br />';
	}

	$from = $to = $hidden = array();
	$fields = plugin_tracker_get_fields($base, $refer, $config);
	foreach (array_keys($fields) as $field) {
		$from[] = '[' . $field . ']';
		$_to    = $fields[$field]->get_tag();
		if (is_a($fields[$field], 'Tracker_field_hidden')) {
			$to[]     = '';
			$hidden[] = $_to;
		} else {
			$to[]     = $_to;
		}
		unset($fields[$field]);
	}

	$script = get_script_uri();
	$retval = str_replace($from, $to, convert_html(plugin_tracker_get_source($form)));
	$hidden = implode('<br />' . "\n", $hidden);
	return <<<EOD
<form enctype="multipart/form-data" action="$script" method="post">
<div>
$retval
$hidden
</div>
</form>
EOD;
}

// Add new page
function plugin_tracker_action()
{
	global $post, $vars, $now;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');

	$base  = isset($post['_base'])  ? $post['_base']  : '';
	$refer = isset($post['_refer']) ? $post['_refer'] : $base;
	if (! is_pagename($refer)) {
		return array(
			'msg'  => 'Cannot write',
			'body' => 'Page name (' . htmlspecialchars($refer) . ') invalid'
		);
	}

	// $page name to add will be decided here
	$num  = 0;
	$name = isset($post['_name']) ? $post['_name'] : '';
	if (isset($post['_page'])) {
		$real = $page = $post['_page'];
	} else {
		$real = is_pagename($name) ? $name : ++$num;
		$page = get_fullname('./' . $real, $base);
	}
	if (! is_pagename($page)) $page = $base;
	while (is_page($page)) {
		$real = ++$num;
		$page = $base . '/' . $real;
	}

	// Loading configuration
	$config_name = isset($post['_config']) ? $post['_config'] : '';
	$config = new Config('plugin/tracker/' . $config_name);
	if (! $config->read()) {
		return '<p>config file \'' . htmlspecialchars($config_name) . '\' not found.</p>';
	}
	$config->config_name = $config_name;
	$template_page = $config->page . '/page';
	if (! is_page($template_page)) {
		return array(
			'msg'  => 'Cannot write',
			'body' => 'Page template (' . htmlspecialchars($template_page) . ') not exists'
		);
	}

	// Default
	$_post = array_merge($post, $_FILES);
	$_post['_date'] = $now;
	$_post['_page'] = $page;
	$_post['_name'] = $name;
	$_post['_real'] = $real;
	// $_post['_refer'] = $_post['refer'];

	// Creating an empty page, before attaching files
	pkwk_touch_file(get_filename($page));

	$from = $to = array();
	$fields = plugin_tracker_get_fields($page, $refer, $config);
	foreach (array_keys($fields) as $field) {
		$from[] = '[' . $field . ']';
		$to[]   = isset($_post[$field]) ? $fields[$field]->format_value($_post[$field]) : '';
		unset($fields[$field]);
	}

	// Load $template
	$template = plugin_tracker_get_source($template_page);

	// Repalace every [$field]s to real values in the $template
	$subject = $subject_e = array();
	foreach (array_keys($template) as $num) {
		if (trim($template[$num]) == '') continue;
		$letter = $template[$num]{0};
		if ($letter == '|' || $letter == ':') {
			// Escape for some TextFormattingRules: <table> and <dr>
			$subject_e[$num] = $template[$num];
		} else {
			$subject[$num]   = $template[$num];
		}
	}
	foreach (str_replace($from,   $to,   $subject  ) as $num => $line) {
		$template[$num] = $line;
	}
	// Escape for some TextFormattingRules: <table> and <dr>
	if ($subject_e) {
		$to_e = array();
		foreach($to as $value) {
			if (strpos($value, '|') !== FALSE) {
				// Escape for some TextFormattingRules: <table> and <dr>
				$to_e[] = str_replace('|', '&#x7c;', $value);
			} else{
				$to_e[] = $value;	
			}
		}
		foreach (str_replace($from, $to_e, $subject_e) as $num => $line) {
			$template[$num] = $line;
		}
	}

	// Write $template, without touch
	page_write($page, join('', $template));

	pkwk_headers_sent();
	header('Location: ' . get_script_uri() . '?' . rawurlencode($page));
	exit;
}

// Construct $fields (an array of Tracker_field objects)
function plugin_tracker_get_fields($base, $refer, & $config)
{
	global $now;

	$fields = array();

	foreach ($config->get('fields') as $field) {
		// $field[0]: Field name
		// $field[1]: Field name (for display)
		// $field[2]: Field type
		// $field[3]: Option ("size", "cols", "rows", etc)
		// $field[4]: Default value
		$class = 'Tracker_field_' . $field[2];
		if (! class_exists($class)) {
			// Default
			$field[2] = 'text';
			$class    = 'Tracker_field_' . $field[2];
			$field[3] = '20';
		}
		$fieldname = $field[0];
		$fields[$fieldname] = & new $class($field, $base, $refer, $config);
	}

	foreach (
		array(
			// Reserved ones
			'_date'   => 'text',	// Post date
			'_update' => 'date',	// Last modified date
			'_past'   => 'past',	// Elapsed time (passage)
			'_page'   => 'page',	// Page name
			'_name'   => 'text',	// Page name specified by poster
			'_real'   => 'real',	// Page name (Real)
			'_refer'  => 'page',	// Page name refer from this (Page who has forms)
			'_base'   => 'page',
			'_submit' => 'submit'
		) as $fieldname => $type)
	{
		if (isset($fields[$fieldname])) continue;
		$field = array($fieldname, plugin_tracker_message('btn' . $fieldname), '', '20', '');
		$class = 'Tracker_field_' . $type;
		$fields[$fieldname] = & new $class($field, $base, $refer, $config);
	}

	return $fields;
}

// Field classes
// TODO: Not to use static variables (except $id)
class Tracker_field
{
	var $name;
	var $title;
	var $values;
	var $default_value;
	var $page;
	var $refer;
	var $config;
	var $data;
	var $sort_type = SORT_REGULAR;
	var $id        = 0;

	function Tracker_field($field, $page, $refer, & $config)
	{
		global $post;
		static $id = 0;	// Unique id per instance

		$this->id     = ++$id;
		$this->name   = $field[0];
		$this->title  = $field[1];
		$this->values = explode(',', $field[3]);
		$this->default_value = $field[4];
		$this->page   = $page;
		$this->refer  = $refer;
		$this->config = & $config;
		$this->data   = isset($post[$this->name]) ? $post[$this->name] : '';
	}

	// XHTML part inside a form
	function get_tag()
	{
		return '';
	}

	function get_style()
	{
		return '%s';
	}

	function format_value($value)
	{
		return $value;
	}

	function format_cell($str)
	{
		return $str;
	}

	// Compare key for Tracker_list->sort()
	function get_value($value)
	{
		return $value;	// Default: $value itself
	}
}

class Tracker_field_text extends Tracker_field
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		return '<input type="text"' .
				' name="'  . htmlspecialchars($this->name)          . '"' .
				' size="'  . htmlspecialchars($this->values[0])     . '"' .
				' value="' . htmlspecialchars($this->default_value) . '" />';
	}
}

class Tracker_field_page extends Tracker_field_text
{
	var $sort_type = SORT_STRING;

	function format_value($value)
	{
		$value = strip_bracket($value);
		if (is_pagename($value)) $value = '[[' . $value . ']]';
		return parent::format_value($value);
	}
}

class Tracker_field_real extends Tracker_field_text
{
	var $sort_type = SORT_REGULAR;
}

class Tracker_field_title extends Tracker_field_text
{
	var $sort_type = SORT_STRING;

	function format_cell($str)
	{
		make_heading($str);
		return $str;
	}
}

class Tracker_field_textarea extends Tracker_field
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		return '<textarea' .
			' name="' . htmlspecialchars($this->name)      . '"' .
			' cols="' . htmlspecialchars($this->values[0]) . '"' .
			' rows="' . htmlspecialchars($this->values[1]) . '">' .
						htmlspecialchars($this->default_value) .
			'</textarea>';
	}

	function format_cell($str)
	{
		$str = preg_replace('/[\r\n]+/', '', $str);
		if (! empty($this->values[2]) && strlen($str) > ($this->values[2] + 3)) {
			$str = mb_substr($str, 0, $this->values[2]) . '...';
		}
		return $str;
	}
}

class Tracker_field_format extends Tracker_field
{
	var $sort_type = SORT_STRING;
	var $styles    = array();
	var $formats   = array();

	function Tracker_field_format($field, $page, $refer, & $config)
	{
		parent::Tracker_field($field, $page, $refer, $config);

		foreach ($this->config->get($this->name) as $option) {
			list($key, $style, $format) =
				array_pad(array_map(create_function('$a', 'return trim($a);'), $option), 3, '');
			if ($style  != '') $this->styles[$key]  = $style;
			if ($format != '') $this->formats[$key] = $format;
		}
	}

	function get_tag()
	{
		return '<input type="text"' .
			' name="' . htmlspecialchars($this->name)      . '"' .
			' size="' . htmlspecialchars($this->values[0]) . '" />';
	}

	function get_key($str)
	{
		return ($str == '') ? 'IS NULL' : 'IS NOT NULL';
	}

	function format_value($str)
	{
		if (is_array($str)) {
			return join(', ', array_map(array($this, 'format_value'), $str));
		}

		$key = $this->get_key($str);
		return isset($this->formats[$key]) ? str_replace('%s', $str, $this->formats[$key]) : $str;
	}

	function get_style($str)
	{
		$key = $this->get_key($str);
		return isset($this->styles[$key]) ? $this->styles[$key] : '%s';
	}
}

class Tracker_field_file extends Tracker_field_format
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		return '<input type="file"' .
			' name="' . htmlspecialchars($this->name)      . '"' .
			' size="' . htmlspecialchars($this->values[0]) . '" />';
	}

	function format_value()
	{
		if (isset($_FILES[$this->name])) {

			require_once(PLUGIN_DIR . 'attach.inc.php');

			$result = attach_upload($_FILES[$this->name], $this->page);
			if ($result['result']) {
				// Upload success
				return parent::format_value($this->page . '/' . $_FILES[$this->name]['name']);
			}
		}

		// Filename not specified, or Fail to upload
		return parent::format_value('');
	}
}

class Tracker_field_radio extends Tracker_field_format
{
	var $sort_type = SORT_NUMERIC;
	var $_options  = array();

	function get_tag()
	{
		$retval = '';

		$id = 0;
		$s_name = htmlspecialchars($this->name);
		foreach ($this->config->get($this->name) as $option) {
			++$id;
			$s_id = '_p_tracker_' . $s_name . '_' . $this->id . '_' . $id;
			$s_option = htmlspecialchars($option[0]);
			$checked  = trim($option[0]) == trim($this->default_value) ? ' checked="checked"' : '';

			$retval .= '<input type="radio"' .
				' name="'  . $s_name   . '"' .
				' id="'    . $s_id     . '"' .
				' value="' . $s_option . '"' .
				$checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}

	function get_key($str)
	{
		return $str;
	}

	function get_value($value)
	{
		$options = & $this->_options;
		$name    = $this->name;

		if (! isset($options[$name])) {
			$values = array_map(
				create_function('$array', 'return $array[0];'),
				$this->config->get($name)
			);
			$options[$name] = array_flip($values);	// array('value0' => 0, 'value1' => 1, ...)
		}

		return isset($options[$name][$value]) ? $options[$name][$value] : $value;
	}
}

class Tracker_field_select extends Tracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag($empty = FALSE)
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = (isset($this->values[0]) && is_numeric($this->values[0])) ?
			' size="' . htmlspecialchars($this->values[0]) . '"' :
			'';
		$s_multiple = (isset($this->values[1]) && strtolower($this->values[1]) == 'multiple') ?
			' multiple="multiple"' :
			'';

		$retval = '<select name="' . $s_name . '[]"' . $s_size . $s_multiple . '>' . "\n";
		if ($empty) $retval .= ' <option value=""></option>' . "\n";
		$defaults = array_flip(preg_split('/\s*,\s*/', $this->default_value, -1, PREG_SPLIT_NO_EMPTY));
		foreach ($this->config->get($this->name) as $option) {
			$s_option = htmlspecialchars($option[0]);
			$selected = isset($defaults[trim($option[0])]) ? ' selected="selected"' : '';
			$retval  .= ' <option value="' . $s_option . '"' . $selected . '>' . $s_option . '</option>' . "\n";
		}
		$retval .= '</select>';

		return $retval;
	}
}

class Tracker_field_checkbox extends Tracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag()
	{
		$retval = '';

		$id = 0;
		$s_name   = htmlspecialchars($this->name);
		$defaults = array_flip(preg_split('/\s*,\s*/', $this->default_value, -1, PREG_SPLIT_NO_EMPTY));
		foreach ($this->config->get($this->name) as $option)
		{
			++$id;
			$s_id     = '_p_tracker_' . $s_name . '_' . $this->id . '_' . $id;
			$s_option = htmlspecialchars($option[0]);
			$checked  = isset($defaults[trim($option[0])]) ? ' checked="checked"' : '';

			$retval .= '<input type="checkbox"' .
				' name="' . $s_name . '[]"' .
				' id="' . $s_id . '"' .
				' value="' . $s_option . '"' .
				$checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}
}

class Tracker_field_hidden extends Tracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag()
	{
		return '<input type="hidden"' .
			' name="'  . htmlspecialchars($this->name)          . '"' .
			' value="' . htmlspecialchars($this->default_value) . '" />' . "\n";
	}
}

class Tracker_field_submit extends Tracker_field
{
	function get_tag()
	{
		$s_title  = htmlspecialchars($this->title);
		$s_page   = htmlspecialchars($this->page);
		$s_refer  = htmlspecialchars($this->refer);
		$s_config = htmlspecialchars($this->config->config_name);

		return <<<EOD
<input type="submit" value="$s_title" />
<input type="hidden" name="plugin" value="tracker" />
<input type="hidden" name="_refer" value="$s_refer" />
<input type="hidden" name="_base" value="$s_page" />
<input type="hidden" name="_config" value="$s_config" />
EOD;
	}
}

class Tracker_field_date extends Tracker_field
{
	var $sort_type = SORT_NUMERIC;

	function format_cell($timestamp)
	{
		return format_date($timestamp);
	}
}

class Tracker_field_past extends Tracker_field
{
	var $sort_type = SORT_NUMERIC;

	function format_cell($timestamp)
	{
		return get_passage($timestamp, FALSE);
	}

	function get_value($value)
	{
		return UTIME - $value;
	}
}

///////////////////////////////////////////////////////////////////////////
// tracker_list plugin

function plugin_tracker_list_convert()
{
	global $vars;

	$config = PLUGIN_TRACKER_DEFAULT_CONFIG;
	$page   = $refer = isset($vars['page']) ? $vars['page'] : '';
	$order  = '';
	$list   = 'list';
	$limit  = 0;

	// TODO: SHOW USAGE OR ERROR CLEARLY
	if (func_num_args()) {
		$args = func_get_args();
		switch (count($args)) {
		case 4:
			if (! is_numeric($args[3])) return PLUGIN_TRACKER_LIST_USAGE . '<br />';
			$limit = intval($args[3]);
		case 3:
			$order = $args[2];
		case 2:
			$arg = get_fullname($args[1], $page);
			if (is_pagename($arg)) $page = $arg;
		case 1:
			if ($args[0] != '') $config = $args[0];
			list($config, $list) = array_pad(explode('/', $config, 2), 2, $list);
		}
	}
	return plugin_tracker_list_render($page, $refer, $config, $list, $order, $limit);
}

function plugin_tracker_list_action()
{
	global $vars;

	$page   = $refer = isset($vars['refer']) ? $vars['refer'] : '';
	$config = isset($vars['config']) ? $vars['config'] : '';
	$list   = isset($vars['list'])   ? $vars['list']   : 'list';
	$order  = isset($vars['order'])  ? $vars['order']  : '_real:SORT_DESC';
	$limit  = isset($vars['limit'])  ? intval($vars['limit']) : 0;

	$s_page = make_pagelink($page);
	return array(
		'msg' => plugin_tracker_message('msg_list'),
		'body'=> str_replace('$1', $s_page, plugin_tracker_message('msg_back')) .
			plugin_tracker_list_render($page, $refer, $config, $list, $order, $limit)
	);
}

function plugin_tracker_list_render($page, $refer, $config_name, $list, $order_commands = '', $limit = 0)
{
	$config = new Config('plugin/tracker/' . $config_name);
	if (! $config->read()) {
		return '#tracker_list: Config \'' . htmlspecialchars($config_name) . '\' not found<br />';
	}
	$config->config_name = $config_name;
	if (! is_page($config->page . '/' . $list)) {
		return '#tracker_list: List \'' . make_pagelink($config->page . '/' . $list) . '\' not found<br />';
	}

	$list = & new Tracker_list($page, $refer, $config, $list);
	$list->sort($order_commands);
	$result = $list->toString($limit);
	if ($result == FALSE) {
		$result = '#tracker_list: Pages under \'' . htmlspecialchars($page) . '/\' not found' . '<br />';
	}

	return $result;
}

// Listing class
// TODO: Not to use static variable
class Tracker_list
{
	var $page;
	var $config;
	var $list;
	var $fields;
	var $pattern;
	var $pattern_fields;
	var $rows;
	var $order;
	var $_added;

	function Tracker_list($page, $refer, & $config, $list)
	{
		$this->page    = $page;
		$this->config  = & $config;
		$this->list    = $list;
		$this->fields  = plugin_tracker_get_fields($page, $refer, $config);
		$this->pattern = '';
		$this->pattern_fields = array();
		$this->rows    = array();
		$this->order   = array();
		$this->_added  = array();

		$pattern = plugin_tracker_get_source($config->page . '/page', TRUE);
		// TODO: if (is FALSE) OR file_exists()

		// Convert block-plugins to fields
		// Incleasing and decreasing around #comment etc, will be covererd with [_block_xxx]
		$pattern = preg_replace('/^\#([^\(\s]+)(?:\((.*)\))?\s*$/m', '[_block_$1]', $pattern);

		// Generate regexes
		$pattern = preg_split('/\\\\\[(\w+)\\\\\]/', preg_quote($pattern, '/'), -1, PREG_SPLIT_DELIM_CAPTURE);
		while (! empty($pattern)) {
			$this->pattern .= preg_replace('/\s+/', '\\s*', '(?>\\s*' . trim(array_shift($pattern)) . '\\s*)');
			if (! empty($pattern)) {
				$field = array_shift($pattern);
				$this->pattern_fields[] = $field;
				$this->pattern         .= '(.*)';
			}
		}

		// Listing
		$pattern     = $page . '/';
		$pattern_len = strlen($pattern);
		foreach (get_existpages() as $_page) {
			if (strpos($_page, $pattern) === 0) {
				$name = substr($_page, $pattern_len);
				if (preg_match(PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN, $name)) continue;
				$this->add($_page, $name);
			}
		}
	}

	function add($page, $name)
	{

		if (isset($this->_added[$page])) return;

		$this->_added[$page] = TRUE;

		$source  = plugin_tracker_get_source($page);

		// Compat: 'move to [[page]]' (bugtrack plugin)
		$matches = array();
		if (! empty($source) && preg_match('/move\sto\s(.+)/', $source[0], $matches)) {
			$to_page = strip_bracket(trim($matches[1]));
			if (is_page($to_page)) {
				unset($source);	// Release
				$this->add($to_page, $name);	// Recurse(Rescan)
				return;
			} else {
				return;	// Invalid
			}
		}

		// Default
		$filetime = get_filetime($page);
		$row = array(
			'_page'   => '[[' . $page . ']]',
			'_refer'  => $this->page,
			'_real'   => $name,
			'_update' => $filetime,
			'_past'   => $filetime,
			'_match'  => FALSE,
		);

		// Redefine
		$matches = array();
		$row['_match'] = preg_match('/' . $this->pattern . '/s', implode('', $source), $matches);
		unset($source);
		if ($row['_match']) {
			array_shift($matches);
			foreach ($this->pattern_fields as $key => $field) {
				$row[$field] = trim($matches[$key]);
			}
		}

		$this->rows[$name] = $row;
	}

	// Sort $this->rows with $order_commands
	function sort($order_commands = '')
	{
		if ($order_commands == '') {
			$this->order = array();
			return TRUE;
		}

		$orders = array();
		$params = array();	// Arguments for array_multisort()
		$names  = array_flip(array_keys($this->fields));

		foreach (explode(';', $order_commands) as $command) {
			// TODO: ???
			list($fieldname, $order) = array_pad(explode(':', $command), 1, 'SORT_ASC');
			$fieldname = trim($fieldname);

			if (! isset($names[$fieldname])) {
				// TODO: SHOW INVALID FIELDNAME CLEARLY
				return FALSE;
			}

			// TODO: SHOULD NOT TO USE DEFINES AT THIS string WORLD
			switch (strtoupper(trim($order))) {
			case SORT_ASC:
			case 'SORT_ASC':
			case 'ASC':
				$order = SORT_ASC;
				break;
			case SORT_DESC:
			case 'SORT_DESC':
			case 'DESC':
				$order = SORT_DESC;
				break;
			default:
				continue;
			}

			$orders[$fieldname] = $order;
		}
		// TODO: LIMIT (count($orders) < N < count(fields)) TO LIMIT array_multisort()

		foreach ($orders as $fieldname => $order) {
			// One column set (one-dimensional array(), sort type, and order-by)
			$array = array();
			foreach ($this->rows as $row) {
				$array[] = isset($row[$fieldname]) ?
					$this->fields[$fieldname]->get_value($row[$fieldname]) :
					'';
			}
			$params[] = $array;
			$params[] = $this->fields[$fieldname]->sort_type;
			$params[] = $order;
		}
		$params[] = & $this->rows;

		call_user_func_array('array_multisort', $params);
		$this->order = $orders;

		return TRUE; 
	}

	// Used with preg_replace_callback() at toString()
	function replace_item($arr)
	{
		$params = explode(',', $arr[1]);
		$name   = array_shift($params);
		if ($name == '') {
			$str = '';
		} else if (isset($this->items[$name])) {
			$str = $this->items[$name];
			if (isset($this->fields[$name])) {
				$str = $this->fields[$name]->format_cell($str);
			}
		} else {
			return $this->pipe ? str_replace('|', '&#x7c;', $arr[0]) : $arr[0];
		}

		$style = empty($params) ? $name : $params[0];
		if (isset($this->items[$style]) && isset($this->fields[$style])) {
			$str = sprintf($this->fields[$style]->get_style($this->items[$style]), $str);
		}

		return $this->pipe ? str_replace('|', '&#x7c;', $str) : $str;
	}

	// Used with preg_replace_callback() at toString()
	function replace_title($arr)
	{
		$field = $sort = $arr[1];
		if (! isset($this->fields[$field])) return $arr[0];

		if ($sort == '_name' || $sort == '_page') $sort = '_real';

		$dir   = SORT_ASC;
		$arrow = '';
		$order = $this->order;
		if (is_array($order) && isset($order[$sort])) {
			// BugTrack2/106: Only variables can be passed by reference from PHP 5.0.5
			$order_keys = array_keys($order); // with array_shift();

			$index   = array_flip($order_keys);
			$pos     = 1 + $index[$sort];
			$b_end   = ($sort == array_shift($order_keys));
			$b_order = ($order[$sort] == SORT_ASC);
			$dir     = ($b_end xor $b_order) ? SORT_ASC : SORT_DESC;
			$arrow   = '&br;' . ($b_order ? '&uarr;' : '&darr;') . '(' . $pos . ')';

			unset($order[$sort], $order_keys);
		}
		$title  = $this->fields[$field]->title;
		$r_page = rawurlencode($this->page);
		$r_config = rawurlencode($this->config->config_name);
		$r_list = rawurlencode($this->list);
		$_order = array($sort . ':' . $dir);
		if (is_array($order)) {
			foreach ($order as $key => $value) {
				$_order[] = $key . ':' . $value;
			}
		}
		$r_order = rawurlencode(join(';', $_order));

		$script = get_script_uri();
		return '[[' . $title . $arrow . '>' .
				$script . '?plugin=tracker_list&refer=' . $r_page .
				'&config=' . $r_config .
				'&list=' . $r_list . '&order=' . $r_order . ']]';
	}

	function toString($limit = 0)
	{
		if (empty($this->rows)) return FALSE;

		$count = $_count = count($this->rows);

		if ($limit != 0) {
			$limit = max(1, intval($limit));
			if ($count > $limit) {
				$rows   = array_slice($this->rows, 0, $limit);
				$_count = count($rows);
			} else {
				$rows = $this->rows;
			}
		} else {
			$rows = $this->rows;
		}

		$source = array();

		if ($count > $_count) {
			// Message
			$source[] = str_replace(
				array('$1',   '$2'  ),
				array($count, $_count),
				plugin_tracker_message('msg_limit')
			) . "\n";
		}

		$body   = array();
		foreach (plugin_tracker_get_source($this->config->page . '/' . $this->list) as $line) {
			if (preg_match('/^\|(.+)\|[hfc]$/i', $line)) {
				// Table decolations
				$source[] = preg_replace_callback('/\[([^\[\]]+)\]/', array(& $this, 'replace_title'), $line);
			} else {
				$body[] = $line;
			}
		}
		foreach ($rows as $row) {
			if (! PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE && ! $row['_match']) continue;

			$this->items = $row;
			foreach ($body as $line) {
				if (ltrim($line) == '') {
					$source[] = $line;
				} else {
					$this->pipe = ($line{0} == '|' || $line{0} == ':');
					$source[] = preg_replace_callback('/\[([^\[\]]+)\]/', array(& $this, 'replace_item'), $line);
				}
			}
		}

		return convert_html(implode('', $source));
	}
}

function plugin_tracker_get_source($page, $join = FALSE)
{
	$source = get_source($page, TRUE, $join);

	// Remove fixed-heading anchors
	$source = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $source);

	// Remove #freeze-es
	return preg_replace('/^#freeze\s*$/im', '', $source);
}

function plugin_tracker_message($key)
{
	global $_tracker_messages;
	return isset($_tracker_messages[$key]) ? $_tracker_messages[$key] : 'NOMESSAGE';
}

?>
