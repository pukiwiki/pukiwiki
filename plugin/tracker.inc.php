<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: tracker.inc.php,v 1.62 2007/09/22 06:42:05 henoheno Exp $
// Copyright (C) 2003-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Issue tracker plugin (See Also bugtrack plugin)

define('PLUGIN_TRACKER_USAGE',      '#tracker([config[/form][,basepage]])');
define('PLUGIN_TRACKER_LIST_USAGE', '#tracker_list([config[/list]][[,base][,field:sort[;field:sort ...][,limit]]])');

define('PLUGIN_TRACKER_DEFAULT_CONFIG', 'default');
define('PLUGIN_TRACKER_DEFAULT_FORM',   'form');
define('PLUGIN_TRACKER_DEFAULT_LIST',   'list');
define('PLUGIN_TRACKER_DEFAULT_LIMIT',  0 );	// 0 = Unlimited
define('PLUGIN_TRACKER_DEFAULT_ORDER',  '');	// Example: '_real'

// Sort N columns at a time
define('PLUGIN_TRACKER_LIST_SORT_LIMIT', 3);

// Excluding pattern
define('PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN','#^SubMenu$|/#');	// 'SubMenu' and using '/'
//define('PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN','#(?!)#');		// Nothing excluded

// Show error rows (can't capture columns properly)
define('PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE', 1);

// ----

// Sort options
define('PLUGIN_TRACKER_LIST_SORT_DESC',    3);
define('PLUGIN_TRACKER_LIST_SORT_ASC',     4);
define('PLUGIN_TRACKER_LIST_SORT_DEFAULT', PLUGIN_TRACKER_LIST_SORT_ASC);

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
	}
	switch ($argc) {
	case 2:
		$arg = get_fullname($args[1], $base);
		if (is_pagename($arg)) $base = $arg;
		/*FALLTHROUGH*/
	case 1:
		// Config/form
		if ($args[0] != '') {
			$arg = explode('/', $args[0], 2);
			if ($arg[0] != '' ) $config_name = $arg[0];
			if (isset($arg[1])) $form        = $arg[1];
		}
	}
	unset($args, $argc, $arg);

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
class Tracker_field
{
	var $name;
	var $title;
	var $values;
	var $default_value;
	var $base;
	var $refer;
	var $config;
	var $data;
	var $sort_type = SORT_REGULAR;
	var $id        = 0;

	function Tracker_field($field, $base, $refer, & $config)
	{
		global $post;
		static $id = 0;	// Unique id per instance

		$this->id     = ++$id;
		$this->name   = $field[0];
		$this->title  = $field[1];
		$this->values = explode(',', $field[3]);
		$this->default_value = $field[4];
		$this->base   = $base;
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

	function Tracker_field_format($field, $base, $refer, & $config)
	{
		parent::Tracker_field($field, $base, $refer, $config);

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

			$result = attach_upload($_FILES[$this->name], $this->base);
			if (isset($result['result']) && $result['result']) {
				// Upload success
				return parent::format_value($this->base . '/' . $_FILES[$this->name]['name']);
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
		$s_base   = htmlspecialchars($this->base);
		$s_refer  = htmlspecialchars($this->refer);
		$s_config = htmlspecialchars($this->config->config_name);

		return <<<EOD
<input type="submit" value="$s_title" />
<input type="hidden" name="plugin"  value="tracker" />
<input type="hidden" name="_refer"  value="$s_refer" />
<input type="hidden" name="_base"   value="$s_base" />
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

	$base = $refer = isset($vars['page']) ? $vars['page'] : '';
	$config_name = PLUGIN_TRACKER_DEFAULT_CONFIG;
	$list        = PLUGIN_TRACKER_DEFAULT_LIST;
	$limit       = PLUGIN_TRACKER_DEFAULT_LIMIT;
	$order       = PLUGIN_TRACKER_DEFAULT_ORDER;

	$args = func_get_args();
	$argc = count($args);
	if ($argc > 4) {
		return PLUGIN_TRACKER_LIST_USAGE . '<br />';
	}
	switch ($argc) {
	case 4: $limit = $args[3];	/*FALLTHROUGH*/
	case 3: $order = $args[2];	/*FALLTHROUGH*/
	case 2:
		$arg = get_fullname($args[1], $base);
		if (is_pagename($arg)) $base = $arg;
		/*FALLTHROUGH*/
	case 1:
		// Config/list
		if ($args[0] != '') {
			$arg = explode('/', $args[0], 2);
			if ($arg[0] != '' ) $config_name = $arg[0];
			if (isset($arg[1])) $list        = $arg[1];
		}
	}
	unset($args, $argc, $arg);

	return plugin_tracker_list_render($base, $refer, $config_name, $list, $order, $limit);
}

function plugin_tracker_list_action()
{
	global $get, $vars;

	$base   = isset($get['base'])   ? $get['base']   : '';
	$config = isset($get['config']) ? $get['config'] : '';
	$list   = isset($get['list'])   ? $get['list']   : 'list';

	$order  = isset($vars['order']) ? $vars['order'] : PLUGIN_TRACKER_DEFAULT_ORDER;
	$limit  = isset($vars['limit']) ? $vars['limit'] : 0;

	// Compat before 1.4.8
	if ($base == '') $base = isset($get['refer']) ? $get['refer'] : '';

	$s_base = make_pagelink(trim($base));
	return array(
		'msg' => plugin_tracker_message('msg_list'),
		'body'=> str_replace('$1', $s_base, plugin_tracker_message('msg_back')) .
			plugin_tracker_list_render($base, $base, $config, $list, $order, $limit)
	);
}

function plugin_tracker_list_render($base, $refer, $config_name, $list, $order_commands = '', $limit = 0)
{
	$base  = trim($base);
	if ($base == '') return '#tracker_list: Base not specified' . '<br />';

	$refer = trim($refer);

	$config_name = trim($config_name);
	if ($config_name == '') $config_name = PLUGIN_TRACKER_DEFAULT_CONFIG;

	$list  = trim($list);
	if (! is_numeric($limit)) return PLUGIN_TRACKER_LIST_USAGE . '<br />';
	$limit = intval($limit);


	$config = new Config('plugin/tracker/' . $config_name);
	if (! $config->read()) {
		return '#tracker_list: Config not found: ' . htmlspecialchars($config_name) . '<br />';
	}
	$config->config_name = $config_name;
	if (! is_page($config->page . '/' . $list)) {
		return '#tracker_list: List not found: ' . make_pagelink($config->page . '/' . $list) . '<br />';
	}

	$list = & new Tracker_list($base, $refer, $config, $list);
	if ($list->setOrder($order_commands) === FALSE) {
		return '#tracker_list: ' . htmlspecialchars($list->error) . '<br />';
	}
	$result = $list->toString($limit);
	if ($result === FALSE) {
		return '#tracker_list: ' . htmlspecialchars($list->error) . '<br />';
	}
	unset($list);

	return convert_html($result);
}

// Listing class
class Tracker_list
{
	var $base;
	var $config;
	var $list;
	var $fields;
	var $pattern;
	var $pattern_fields;

	var $rows   = array();
	var $orders = array();
	var $_added = array();

	var $error  = '';	// Error message

	// Used by toString() only
	var $_itmes;
	var $_the_first_character_of_the_line;

	// TODO: Why list here, why load all of columns
	function Tracker_list($base, $refer, & $config, $list)
	{
		$this->base     = $base;
		$this->config   = & $config;
		$this->list     = $list;

		$pattern        = array();
		$pattern_fields = array();

		// Generate regexes:

		// TODO: if (is FALSE) OR file_exists()
		$source = plugin_tracker_get_source($config->page . '/page', TRUE);
		// Block-plugins to pseudo fields (#convert => [_block_convert])
		$source = preg_replace('/^\#([^\(\s]+)(?:\((.*)\))?\s*$/m', '[_block_$1]', $source);

		// Now, $source = array('*someting*', 'fieldname', '*someting*', 'fieldname', ...)
		$source = preg_split('/\\\\\[(\w+)\\\\\]/', preg_quote($source, '/'), -1, PREG_SPLIT_DELIM_CAPTURE);

		// NOTE: if the page has garbages between fields, it will fail to be load
		$fields = plugin_tracker_get_fields($base, $refer, $config);
		while (! empty($source)) {
			// Just ignore these _fixed_ data
			$pattern[] = preg_replace('/\s+/', '\\s*', '(?>\\s*' . trim(array_shift($source)) . '\\s*)');
			if (empty($source)) continue;

			$fieldname = array_shift($source);
			if (isset($fields[$fieldname])) {
				$pattern[]        = '(.*)';		// Just capture it
				$pattern_fields[] = $fieldname;	// Capture it as this $filedname
			} else {
				$pattern[]        = '.*';		// Just ignore pseudo fields
			}
		}
		$this->fields         = $fields;
		$this->pattern        = '/' . implode('', $pattern) . '/s';
		$this->pattern_fields = $pattern_fields;

		// Listing
		$pattern     = $base . '/';
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

		$source = plugin_tracker_get_source($page);

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

		// Default column
		$filetime = get_filetime($page);
		$row = array(
			// column => default data of the cell
			'_page'   => '[[' . $page . ']]',
			'_real'   => $name,
			'_update' => $filetime,
			'_past'   => $filetime,
			'_match'  => FALSE,
		);

		// Load / Redefine cell
		$matches = array();
		$row['_match'] = preg_match($this->pattern, implode('', $source), $matches);
		unset($source);
		if ($row['_match']) {
			array_shift($matches);	// $matches[0] = all of the captured string
			foreach ($this->pattern_fields as $key => $fieldname) {
				$row[$fieldname] = trim($matches[$key]);
				unset($matches[$key]);
			}
		}

		$this->rows[$name] = $row;
	}

	// sort()
	function _order_commands2orders($order_commands = '')
	{
		$order_commands = trim($order_commands);
		if ($order_commands == '') return array();

		$orders = array();
		$fields = $this->fields;

		$i = 0;
		foreach (explode(';', $order_commands) as $command) {
			$command = trim($command);
			if ($command == '') continue;
			$arg = explode(':', $command, 2);
			$fieldname = isset($arg[0]) ? trim($arg[0]) : '';
			$order     = isset($arg[1]) ? trim($arg[1]) : '';

			if (! isset($fields[$fieldname])) {
				$this->error =  'No such field: ' . $fieldname;
				return FALSE;
			}
			$_order = $this->_sortkey_string2define($order);
			if ($_order === FALSE) {
				$this->error =  'Invalid sortkey: ' . $order;
				return FALSE;
			} else if (isset($orders[$fieldname])) {
				$this->error =  'Sortkey already set: ' . $fieldname;
				return FALSE;
			}

			if (PLUGIN_TRACKER_LIST_SORT_LIMIT <= $i) continue;	// Ignore
			++$i;
			$orders[$fieldname] = $_order;
		}

		return $orders;
	}

	// sort()
	function setOrder($order_commands = '')
	{
		$orders = $this->_order_commands2orders($order_commands);
		if ($orders === FALSE) {
			$this->orders = array();
			return FALSE;
		}

		$this->orders = $orders;
		return $orders;
	}

	// Sort $this->rows by $this->orders
	function _sort()
	{
		$orders = $this->orders;
		$fields = $this->fields;

		$params = array();	// Arguments for array_multisort()
		foreach ($orders as $fieldname => $order) {
			// One column set (one-dimensional array(), sort type, and order-by)

			if ($order = PLUGIN_TRACKER_LIST_SORT_ASC) {
				$order = SORT_ASC;
			} else if ($order = PLUGIN_TRACKER_LIST_SORT_DESC) {
				$order = SORT_DESC;
			} else {
				$this->error = 'Invalid sort order for array_multisort()';
				return FALSE;
			}

			$array = array();
			foreach ($this->rows as $row) {
				$array[] = isset($row[$fieldname]) ?
					$fields[$fieldname]->get_value($row[$fieldname]) :
					'';
			}
			$params[] = $array;
			$params[] = $fields[$fieldname]->sort_type;
			$params[] = $order;
		}
		$params[] = & $this->rows;

		call_user_func_array('array_multisort', $params);

		return TRUE; 
	}

	// toString(): Sort key: Define to string (internal var => string)
	function _sortkey_define2string($sortkey)
	{
		switch ($sortkey) {
		case PLUGIN_TRACKER_LIST_SORT_ASC:  $sortkey = 'SORT_ASC';  break;
		case PLUGIN_TRACKER_LIST_SORT_DESC: $sortkey = 'SORT_DESC'; break;
		default:
			$this->error =  'No such define: ' . $sortkey;
			$sortkey = FALSE;
		}
		return $sortkey;
	}

	// toString(): Sort key: String to define (string => internal var)
	function _sortkey_string2define($sortkey)
	{
		switch (strtoupper(trim($sortkey))) {
		case '':          $sortkey = PLUGIN_TRACKER_LIST_SORT_DEFAULT; break;

		case SORT_ASC:    /*FALLTHROUGH*/ // Compat, will be removed at 1.4.9 or later
		case 'SORT_ASC':  /*FALLTHROUGH*/
		case 'ASC':       $sortkey = PLUGIN_TRACKER_LIST_SORT_ASC; break;

		case SORT_DESC:   /*FALLTHROUGH*/ // Compat, will be removed at 1.4.9 or later
 		case 'SORT_DESC': /*FALLTHROUGH*/
		case 'DESC':      $sortkey = PLUGIN_TRACKER_LIST_SORT_DESC; break;

		default:
			$this->error =  'Invalid sort key: ' . $sortkey;
			$sortkey = FALSE;
		}
		return $sortkey;
	}

	// toString(): Escape special characters not to break Wiki syntax
	function _escape($syntax_hint = '|', $string)
	{
		$from = array("\n",   "\r"  );
		$to   = array('&br;', '&br;');
		if ($syntax_hint == '|' || $syntax_hint == ':') {
			// <table> or <dl> Wiki syntax: Excape '|'
			$from[] = '|';
			$to[]   = '&#x7c;';
		} else if ($syntax_hint == ',') {
			// <table> by comma
			$from[] = ',';
			$to[]   = '&#x2c;';
		}
		return str_replace($from, $to, $string);
	}

	// toString(): Called within preg_replace_callback()
	function _replace_title($matches = array())
	{
		$fields = $this->fields;
		$orders = $this->orders;
		$list   = $this->list;
		$config_name = $this->config->config_name;

		$fieldname = isset($matches[1]) ? $matches[1] : '';
		if (! isset($fields[$fieldname])) {
			// Invalid $fieldname or user's own string or something. Nothing to do
			return isset($matches[0]) ? $matches[0] : '';
		}
		if ($fieldname == '_name' || $fieldname == '_page') $fieldname = '_real';

		$arrow  = '';
		if (isset($orders[$fieldname])) {
			// Sorted
			$order_keys = array_keys($orders);

			// Toggle
			$b_end   = ($fieldname == (isset($order_keys[0]) ? $order_keys[0] : ''));
			$b_order = ($orders[$fieldname] === PLUGIN_TRACKER_LIST_SORT_ASC);
			$order   = ($b_end xor $b_order)
				? PLUGIN_TRACKER_LIST_SORT_ASC
				: PLUGIN_TRACKER_LIST_SORT_DESC;

			// Arrow decoration
			$index   = array_flip($order_keys);
			$pos     = 1 + $index[$fieldname];
			$arrow   = '&br;' . ($b_order ? '&uarr;' : '&darr;') . '(' . $pos . ')';

			unset($order_keys, $index);
			unset($orders[$fieldname]);	// $fieldname will be added to the first
		} else {
			// Not sorted yet, but
			$order = PLUGIN_TRACKER_LIST_SORT_ASC;	// Default
		}

		// $fieldname become the first, if you click this link
		$_order = array($fieldname . ':' . $this->_sortkey_define2string($order));
		foreach ($orders as $key => $value) {
			$_order[] = $key . ':' . $this->_sortkey_define2string($value);
		}

		$r_config = ($config_name != PLUGIN_TRACKER_DEFAULT_CONFIG) ?
			'&config=' . rawurlencode($config_name) : '';
		$r_list   = ($list != PLUGIN_TRACKER_DEFAULT_LIST) ?
			'&list=' . rawurlencode($list) : '';

		return
			 '[[' .
				$fields[$fieldname]->title . $arrow .
			'>' .
				get_script_uri() .
				'?plugin=tracker_list' .
				'&base=' . rawurlencode($this->base) .
				$r_config .
				$r_list .
				'&order=' . rawurlencode(join(';', $_order)) .
			']]';
	}

	// toString(): Called within preg_replace_callback()
	function _replace_item($matches = array())
	{	
		$fields = $this->fields;
		$items  = $this->_items;
		$tfc    = $this->_the_first_character_of_the_line ;

		$params    = isset($matches[1]) ? explode(',', $matches[1]) : array();
		$fieldname = isset($params[0])  ? $params[0] : '';
		$stylename = isset($params[1])  ? $params[1] : $fieldname;

		if ($fieldname == '') return '';	// Invalid

		if (! isset($items[$fieldname])) {
			// Maybe load miss of the page
			if (isset($fields[$fieldname])) {
				$str = '[page_err]';	// Exactlly
			} else {
				$str = isset($matches[0]) ? $matches[0] : '';	// Nothing to do
			}
		} else {
			$str = $items[$fieldname];
			if (isset($fields[$fieldname])) {
				$str    = $fields[$fieldname]->format_cell($str);
			}
			if (isset($fields[$stylename]) && isset($items[$stylename])) {
				$_style = $fields[$stylename]->get_style($items[$stylename]);
				$str    = sprintf($_style, $str);
			}
		}

		return $this->_escape($tfc, $str);
	}

	// Output a part of Wiki text
	function toString($limit = 0)
	{
		if (empty($this->rows)) {
			$this->error = 'Pages not found under: ' . $this->base . '/';
			return FALSE;
		}

		// Sort $this->rows
		$this->_sort();
		$rows   = $this->rows;

		$source = array();

		$count = count($this->rows);
		$limit = intval($limit);
		if ($limit != 0) $limit = max(1, $limit);
		if ($limit != 0 && $count > $limit) {
			$source[] = str_replace(
				array('$1',   '$2'  ),
				array($count, $limit),
				plugin_tracker_message('msg_limit')
			) . "\n";
			$rows  = array_slice($this->rows, 0, $limit);
		}

		// Loading template
		$header = $body = array();
		foreach (plugin_tracker_get_source($this->config->page . '/' . $this->list) as $line) {
			if (preg_match('/^\|(.+)\|[hfc]$/i', $line)) {
				// TODO: Why c and f  here
				$header[] = $line;	// Table header, footer, and decoration
			} else {
				$body[]   = $line;	// The others
			}
		}

		foreach($header as $line) {
			$source[] = preg_replace_callback('/\[([^\[\]]+)\]/', array(& $this, '_replace_title'), $line);
		}
		foreach ($rows as $row) {
			if (! PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE && ! $row['_match']) continue;
			$this->_items = $row;
			foreach ($body as $line) {
				if (ltrim($line) != '') {
					$this->_the_first_character_of_the_line = $line[0];
					$line = preg_replace_callback('/\[([^\[\]]+)\]/', array(& $this, '_replace_item'), $line);
				}
				$source[] = $line;
			}
		}

		return implode('', $source);
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
