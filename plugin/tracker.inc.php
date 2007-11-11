<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: tracker.inc.php,v 1.123 2007/11/11 12:28:19 henoheno Exp $
// Copyright (C) 2003-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Issue tracker plugin (See Also bugtrack plugin)


// Tracker_list: Excluding pattern
define('PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN','#^SubMenu$|/#');	// 'SubMenu' and using '/'
//define('PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN','#(?!)#');		// Nothing excluded

// Tracker_list: Show error rows (can't capture columns properly)
define('PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE', 1);

// Tracker_list: Allow N columns sorted at a time
define('PLUGIN_TRACKER_LIST_SORT_LIMIT', 3);


// ----
// Basic interface and strategy

define('PLUGIN_TRACKER_USAGE',      '#tracker([config[/form][,basepage]])');
define('PLUGIN_TRACKER_LIST_USAGE', '#tracker_list([config[/list]][[,base][,field:sort[;field:sort ...][,limit]]])');

// $refer  : Where the plugin had been set / Where to return back to
//           If ($refer == '') $refer = $base;
// $base   : "$base/nnn" will be added by plugin_tracker_action(), or will be shown by Tracker_list
//           Compat: If ($base  == '') $base  = $refer;
// $config : ":config/plugin/tracker/$config" will be load to the Config
// $form   : ":config/plugin/tracker/$config/$form" will be load as template for XHTML form by Tracker_form
// $page   : ":config/plugin/tracker/$config/$page" will be load as template for a new page written by Tracker_form
// $list   : ":config/plugin/tracker/$config/$list" will be load as template of Tracker_list
// $order  : "field:sort" ... i.e. "Severity:desc" means sorting the field "Severity" descendant order.
// $limit  : Show top N rows at a time

define('PLUGIN_TRACKER_DEFAULT_CONFIG', 'default');
define('PLUGIN_TRACKER_DEFAULT_FORM',   'form');
define('PLUGIN_TRACKER_DEFAULT_PAGE',   'page');
define('PLUGIN_TRACKER_DEFAULT_LIST',   'list');
define('PLUGIN_TRACKER_DEFAULT_ORDER',  '');
define('PLUGIN_TRACKER_DEFAULT_LIMIT',  0 );	// 0 = Unlimited

// Sort type
define('PLUGIN_TRACKER_SORT_TYPE_REGULAR',       0);
define('PLUGIN_TRACKER_SORT_TYPE_NUMERIC',       1);
define('PLUGIN_TRACKER_SORT_TYPE_STRING',        2);
define('PLUGIN_TRACKER_SORT_TYPE_NATURAL',       6);
if (! defined('SORT_NATURAL')) define('SORT_NATURAL', PLUGIN_TRACKER_SORT_TYPE_NATURAL);

// Sort order
define('PLUGIN_TRACKER_SORT_ORDER_DESC',    3);
define('PLUGIN_TRACKER_SORT_ORDER_ASC',     4);
define('PLUGIN_TRACKER_SORT_ORDER_DEFAULT', PLUGIN_TRACKER_SORT_ORDER_ASC);

// ----

// Show a form
function plugin_tracker_convert()
{
	global $vars;

	if (PKWK_READONLY) return ''; // Show nothing

	$args = func_get_args();
	$argc = count($args);
	if ($argc > 2) return PLUGIN_TRACKER_USAGE . '<br />';

	$base   = isset($vars['page']) ? $vars['page'] : '';
	$refer  = '';
	$config = '';
	$form   = '';
	$rel    = '';
	switch ($argc) {
	case 2:
		$rel = $args[1];
		/*FALLTHROUGH*/
	case 1:
		// Set "$config/$form"
		if ($args[0] != '') {
			$arg = explode('/', trim($args[0]), 2);
			if ($arg[0] != '' ) $config = trim($arg[0]);
			if (isset($arg[1])) $form   = trim($arg[1]);
		}
	}
	unset($args, $argc, $arg);

	$tracker_form = & new Tracker_form();
	if (! $tracker_form->init($base, $refer, $config, $rel)) {
		return '#tracker: ' . htmlspecialchars($tracker_form->error) . '<br />';
	}

	// Load $template
	$form = ($form != '') ? $form : PLUGIN_TRACKER_DEFAULT_FORM;
	$form = $tracker_form->config->page . '/' . $form;
	$template = plugin_tracker_get_source($form, TRUE);
	if ($template === FALSE || empty($template)) {
		return '#tracker: Form not found: ' . $form . '<br />';
	}

	if (! $tracker_form->initFields(plugin_tracker_field_pickup($template)) ||
		! $tracker_form->initHiddenFields()) {
		return '#tracker: ' . htmlspecialchars($tracker_form->error);
	}
	$fields = $tracker_form->fields;
	unset($tracker_form);

	$from = $to = $hidden = array();
	foreach (array_keys($fields) as $fieldname) {
		$from[] = '[' . $fieldname . ']';
		$_to    = $fields[$fieldname]->get_tag();
		if (is_a($fields[$fieldname], 'Tracker_field_hidden')) {
			$to[]     = '';
			$hidden[] = $_to;
		} else {
			$to[]     = $_to;
		}
		unset($fields[$fieldname]);
	}

	$script   = get_script_uri();
	$template = str_replace($from, $to, convert_html($template));
	$hidden   = implode('<br />' . "\n", $hidden);
	return <<<EOD
<form enctype="multipart/form-data" action="$script" method="post">
<div>
$template
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
	$refer = isset($post['_refer']) ? $post['_refer'] : '';

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

	$config = isset($post['_config']) ? $post['_config'] : '';

	// TODO: Why here
	// Default
	$_post = array_merge($post, $_FILES);
	$_post['_date'] = $now;
	$_post['_page'] = $page;
	$_post['_name'] = $name;
	$_post['_real'] = $real;
	// $_post['_refer'] = $_post['refer'];

	// TODO: Why here => See BugTrack/662
	// Creating an empty page, before attaching files
	pkwk_touch_file(get_filename($page));

	$from = $to = array();

	$tracker_form = & new Tracker_form();
	if (! $tracker_form->init($base, $refer, $config)) {
		return array(
			'msg'  => 'Cannot write',
			'body' => htmlspecialchars($tracker_form->error)
		);
	}

	// Load $template
	$template_page = $tracker_form->config->page . '/' . PLUGIN_TRACKER_DEFAULT_PAGE;
	$template = plugin_tracker_get_source($template_page);
	if ($template === FALSE || empty($template)) {
		return array(
			'msg'  => 'Cannot write',
			'body' => 'Page template (' . htmlspecialchars($template_page) . ') not found'
		);
	}

	if (! $tracker_form->initFields(plugin_tracker_field_pickup(implode('', $template)))) {
		return array(
			'msg'  => 'Cannot write',
			'body' => htmlspecialchars($tracker_form->error)
		);
	}
	$fields = $tracker_form->fields;
	unset($tracker_form);

	foreach (array_keys($fields) as $field) {
		$from[] = '[' . $field . ']';
		$to[]   = isset($_post[$field]) ? $fields[$field]->format_value($_post[$field]) : '';
		unset($fields[$field]);
	}

	// Repalace every [$field]s (found inside $template) to real values
	$subject = $escape = array();
	foreach (array_keys($template) as $linenum) {
		if (trim($template[$linenum]) == '') continue;

		// Escape some TextFormattingRules
		$letter = $template[$linenum][0];
		if ($letter == '|' || $letter == ':') {
			$escape['|'][$linenum] = $template[$linenum];
		} else if ($letter == ',') {
			$escape[','][$linenum] = $template[$linenum];
		} else {
			// TODO: Escape "\n" except multiline-allowed fields
			$subject[$linenum]     = $template[$linenum];
		}
	}
	foreach (str_replace($from, $to, $subject) as $linenum => $line) {
		$template[$linenum] = $line;
	}
	if ($escape) {
		// Escape for some TextFormattingRules
		foreach(array_keys($escape) as $hint) {
			$to_e = plugin_tracker_escape($to, $hint);
			foreach (str_replace($from, $to_e, $escape[$hint]) as $linenum => $line) {
				$template[$linenum] = $line;
			}
		}
		unset($to_e);
	}
	unset($from, $to);

	// Write $template, without touch
	page_write($page, join('', $template));

	pkwk_headers_sent();
	header('Location: ' . get_script_uri() . '?' . rawurlencode($page));
	exit;
}

// Data set of XHTML form or something
class Tracker_form
{
	var $base;
	var $refer;
	var $config_name;

	var $config;	// class Config

	var $raw_fields;
	var $fields = array();

	var $error  = '';	// Error message

	function init($base, $refer = '', $config = NULL, $relative = '')
	{
		$base     = trim($base);
		$refer    = trim($refer);
		$relative = trim($relative);

		if ($refer  == '') $refer  = $base;
		if ($base   == '') $base   = $refer;	// Compat

		if ($base  == '') {
			$this->error = 'Base not specified';
			return FALSE;
		} else if (! is_pagename($refer)) {
			$this->error = 'Invalid page name: ' . $refer;
			return FALSE;
		}

		$absolute = get_fullname($relative, $base);
		if (is_pagename($absolute)) $base = $absolute;

		$this->base  = $base;
		$this->refer = $refer;

		if ($config !== NULL && ! $this->loadConfig($config)) {
			return FALSE;
		}

		return TRUE;
	}

	function loadConfig($config = '')
	{
		if (isset($this->config)) return TRUE;

		$config = trim($config);
		if ($config == '') $config = PLUGIN_TRACKER_DEFAULT_CONFIG;

		$obj_config  = new Config('plugin/tracker/' . $config);

		if ($obj_config->read()) {
			$this->config      = $obj_config;
			$this->config_name = $config;
			return TRUE;
		} else {
			$this->error = "Config not found: " . $obj_config->page;
			return FALSE;
		}
	}

	// Init $this->raw_fields and $this->fields
	function initFields($requests = NULL)
	{
		// No argument
		if (func_num_args() == 0 && $requests === NULL) {
			return $this->initFields(NULL);
		}

		if (! isset($this->raw_fields)) {
			$raw_fields = array();
			// From config
			foreach ($this->config->get('fields') as $field) {
				$fieldname = isset($field[0]) ? $field[0] : '';
				$raw_fields[$fieldname] = array(
					'display' => isset($field[1]) ? $field[1] : '',
					'type'    => isset($field[2]) ? $field[2] : '',
					'options' => isset($field[3]) ? $field[3] : '',
					'default' => isset($field[4]) ? $field[4] : '',
				);
			}
			// From reserved
			$default = array('options' => '20', 'default' => '');
			foreach (array(
				'_date'   => 'text',	// Post date
				'_update' => 'date',	// Last modified date
				'_past'   => 'past',	// Elapsed time (passage)
				'_page'   => 'page',	// Page name
				'_name'   => 'text',	// Page name specified by poster
				'_real'   => 'real',	// Page name (Real)
				'_refer'  => 'page',	// Page name refer from this (Page who has forms)
				'_base'   => 'page',
				'_submit' => 'submit'
			) as $fieldname => $type) {
				if (isset($raw_fields[$fieldname])) continue;
				$raw_fields[$fieldname] = array(
					'display' => plugin_tracker_message('btn' . $fieldname),
					'type'    => $type,
				) + $default;
			}
			$this->raw_fields = & $raw_fields;
		} else {
			$raw_fields = & $this->raw_fields;
		}

		foreach(func_get_args() as $requests) {
			if (empty($raw_fields)) return TRUE;

			if (! is_array($requests)) {
				if ($requests === NULL) {
					$requests = array_keys($raw_fields);	// (The rest of) All, defined order
				} else {
					$requests = array($requests);	// Just one
				}
			}
			foreach ($requests as $fieldname) {
				if (! isset($raw_fields[$fieldname])) continue;
				$field = $raw_fields[$fieldname];
				$err = $this->addField(
					$fieldname,
					$field['display'],
					$field['type'],
					$field['options'],
					$field['default']
				);
				unset($raw_fields[$fieldname]);
				if (! $err) return FALSE;
			}
		}

		return TRUE;
	}

	function initHiddenFields()
	{
		// Make sure to init $this->raw_fields
		if (! $this->initFields(array())) return FALSE;

		$fields = array();
		foreach ($this->raw_fields as $fieldname => $field) {
			if (isset($field['type']) && $field['type'] == 'hidden') {
				$fields[] = $fieldname;
			}
		}

		return $this->initFields($fields);
	}

	// Add $this->fields
	function addField($fieldname, $displayname, $type = 'text', $options = '20', $default = '')
	{
		if (isset($this->fields[$fieldname])) return TRUE;	// Already

		$class = 'Tracker_field_' . $type;
		if (! class_exists($class)) {
			$this->error = "No such type: " . $type;
			return FALSE;
		}

		$this->fields[$fieldname] = & new $class(
			$this,			// Reference
			array(
				$fieldname,
				$displayname,
				NULL,		// $type
				$options,
				$default
			)
		);

		return TRUE;
	}
}

// TODO: Why a filter sometimes created so many?
// Field classes within a form
class Tracker_field
{
	var $id;	// Unique id per instance, and per class(extended-class)
	var $form;	// Parent (class Tracker_form)

	var $name;
	var $title;
	var $options;
	var $default_value;

	var $data;

	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_REGULAR;

	function Tracker_field(& $tracker_form, $field)
	{
		global $post;
		static $id = 0;

		$this->id = ++$id;

		$this->form          = & $tracker_form;
		$this->name          = isset($field[0]) ? $field[0] : '';
		$this->title         = isset($field[1]) ? $field[1] : '';
		$this->options       = isset($field[3]) ? explode(',', $field[3]) : array();
		$this->default_value = isset($field[4]) ? $field[4] : '';

		$this->data = isset($post[$this->name]) ? $post[$this->name] : '';
	}

	// Output a part of XHTML form for the field
	function get_tag()
	{
		return '';
	}

	// Format user input before write
	function format_value($value)
	{
		return $value;
	}

	// Compare key for Tracker_list->sort()
	function get_value($value)
	{
		return $value;
	}

	// Get $this->formats[$key] for format_value()), or
	// Get $this->styles[$key]  for get_style()
	// from cell contents
 	function get_key($value)
	{
		return $value;
	}

	// Format table cell data before output the wiki text
	function format_cell($value)
	{
		return $value;
	}

	// Format-string for sprintf() before output the wiki text
	function get_style($value)
	{
		return '%s';
	}
}

class Tracker_field_text extends Tracker_field
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_STRING;

	function get_tag()
	{
		$s_name  = htmlspecialchars($this->name);
		$s_size  = isset($this->options[0]) ? htmlspecialchars($this->options[0]) : '';
		$s_value = htmlspecialchars($this->default_value);

		return '<input type="text"' .
				' name="'  . $s_name  . '"' .
				' size="'  . $s_size  . '"' .
				' value="' . $s_value . '" />';
	}
}

// Special type: Page name with link syntax
class Tracker_field_page extends Tracker_field_text
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_STRING;

	function _format($page)
	{
		$page = strip_bracket($page);
		if (is_pagename($page)) $page = '[[' . $page . ']]';
		return $page;
	}

	function format_value($value)
	{
		return $this->_format($value);
	}

	function format_cell($value)
	{
		return $this->_format($value);
	}
}

// Special type: Page name minus 'base'
// e.g.
//  page name: Tracker/sales/100
//  base     : Tracker/sales
//  _real    : 100
//
// NOTE:
//   Don't consider using within ":config/plugin/tracker/*/page".
//   This value comes from _the_page_name_ itself.
class Tracker_field_real extends Tracker_field_text
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_NATURAL;

	function format_cell($value)
	{
		// basename(): Rough but work with this
		// (PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN prohibits '/') situation
		return basename($value);
	}
}

// Special type: For headings cleaning
class Tracker_field_title extends Tracker_field_text
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_STRING;

	function format_cell($value)
	{
		make_heading($value);
		return $value;
	}
}

class Tracker_field_textarea extends Tracker_field
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_STRING;

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_cols = isset($this->options[0]) ? htmlspecialchars($this->options[0]) : '';
		$s_rows = isset($this->options[1]) ? htmlspecialchars($this->options[1]) : '';
		$s_default = htmlspecialchars($this->default_value);

		return '<textarea' .
				' name="' . $s_name . '"' .
				' cols="' . $s_cols . '"' .
				' rows="' . $s_rows . '">' .
				$s_default .
			'</textarea>';
	}

	function format_cell($value)
	{
		// Cut too long ones
		// TODO: Why store all of them to the memory?
		if (isset($this->options[2])) {
			$limit = max(0, $this->options[2]);
			$len = mb_strlen($value);
			if ($len > ($limit + 3)) {	// 3 = mb_strlen('...')
				$value = mb_substr($value, 0, $limit) . '...';
			}
		}
		return $value;
	}
}

// Writing text with formatting if trim($cell) != ''
// See also: http://home.arino.jp/?tracker.inc.php%2F41
class Tracker_field_format extends Tracker_field
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_STRING;

	var $styles    = array();
	var $formats   = array();

	function Tracker_field_format(& $tracker_form, $field)
	{
		parent::Tracker_field($tracker_form, $field);

		foreach ($this->form->config->get($this->name) as $option) {
			list($key, $style, $format) = array_pad(array_map('trim', $option), 3, '');
			if ($style  != '') $this->styles[$key]  = $style;
			if ($format != '') $this->formats[$key] = $format;
		}
	}

	function get_key($value)
	{
		return ($value == '') ? 'IS NULL' : 'IS NOT NULL';
	}

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = isset($this->options[0]) ? htmlspecialchars($this->options[0]) : '';

		return '<input type="text" name="' . $s_name . '" size="' . $s_size . '" />';
	}

	function format_value($value)
	{
		if (is_array($value)) {
			return join(', ', array_map(array($this, 'format_value'), $value));
		}

		$key = $this->get_key($value);
		return isset($this->formats[$key]) ? str_replace('%s', $value, $this->formats[$key]) : $value;
	}

	function get_style($value)
	{
		$key = $this->get_key($value);
		return isset($this->styles[$key]) ? $this->styles[$key] : '%s';
	}
}

class Tracker_field_file extends Tracker_field_format
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_STRING;

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = isset($this->options[0]) ? htmlspecialchars($this->options[0]) : '';

		return '<input type="file" name="' . $s_name . '" size="' . $s_size . '" />';
	}

	function format_value()
	{
		if (isset($_FILES[$this->name])) {
			require_once(PLUGIN_DIR . 'attach.inc.php');

			$base = $this->form->base;
			$result = attach_upload($_FILES[$this->name], $base);
			if (isset($result['result']) && $result['result']) {
				// Upload success
				return parent::format_value($base . '/' . $_FILES[$this->name]['name']);
			}
		}

		// Filename not specified, or Fail to upload
		return parent::format_value('');
	}
}

class Tracker_field_radio extends Tracker_field_format
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_NUMERIC;
	var $_options  = array();

	function get_tag()
	{
		$retval = '';

		$id = 0;
		$s_name = htmlspecialchars($this->name);
		foreach ($this->form->config->get($this->name) as $option) {
			++$id;
			$s_id = '_p_tracker_' . $s_name . '_' . $this->id . '_' . $id;
			$s_option = htmlspecialchars($option[0]);
			$checked  = trim($option[0]) === trim($this->default_value) ? ' checked="checked"' : '';

			$retval .= '<input type="radio"' .
				' name="'  . $s_name   . '"' .
				' id="'    . $s_id     . '"' .
				' value="' . $s_option . '"' .
				$checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}

	function get_value($value)
	{
		$options = & $this->_options;
		$name    = $this->name;

		if (! isset($options[$name])) {
			$values = array_map('reset', $this->form->config->get($name));
			$options[$name] = array_flip($values);	// array('value0' => 0, 'value1' => 1, ...)
		}

		return isset($options[$name][$value]) ? $options[$name][$value] : $value;
	}

	// Revert(re-overload) Tracker_field_format's specific code
	function get_key($value)
	{
		return $value;
	}
}

class Tracker_field_select extends Tracker_field_radio
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_NUMERIC;

	var $_defaults;

	function get_tag($empty = FALSE)
	{
		if (! isset($this->_defaults)) {
			$this->_defaults = array_flip(preg_split('/\s*,\s*/', $this->default_value, -1, PREG_SPLIT_NO_EMPTY));
		}
		$defaults = $this->_defaults;

		$retval = array();

		$s_name = htmlspecialchars($this->name);
		$s_size = (isset($this->options[0]) && is_numeric($this->options[0])) ?
			' size="' . htmlspecialchars($this->options[0]) . '"' : '';
		$s_multiple = (isset($this->options[1]) && strtolower($this->options[1]) == 'multiple') ?
			' multiple="multiple"' : '';
		$retval[] = '<select name="' . $s_name . '[]"' . $s_size . $s_multiple . '>';

		if ($empty) $retval[] = ' <option value=""></option>';

		foreach ($this->form->config->get($this->name) as $option) {
			$option   = reset($option);
			$s_option = htmlspecialchars($option);
			$selected = isset($defaults[trim($option)]) ? ' selected="selected"' : '';
			$retval[] = ' <option value="' . $s_option . '"' . $selected . '>' . $s_option . '</option>';
		}

		$retval[] = '</select>';

		return implode("\n", $retval);
	}
}

class Tracker_field_checkbox extends Tracker_field_radio
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_NUMERIC;

	function get_tag()
	{
		$config   = $this->form->config;

		$s_name   = htmlspecialchars($this->name);
		$s_fid    = htmlspecialchars($this->id);
		$defaults = array_flip(preg_split('/\s*,\s*/', $this->default_value, -1, PREG_SPLIT_NO_EMPTY));

		$id     = 0;
		$retval = '';
		foreach ($config->get($this->name) as $option) {
			++$id;
			$s_id     = '_p_tracker_' . $s_name . '_' . $s_fid . '_' . $id;
			$s_option = htmlspecialchars($option[0]);
			$checked  = isset($defaults[trim($option[0])]) ? ' checked="checked"' : '';

			$retval .= '<input type="checkbox"' .
				' name="' . $s_name . '[]" id="' . $s_id . '"' .
				' value="' . $s_option . '"' . $checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}
}

class Tracker_field_hidden extends Tracker_field_radio
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_NUMERIC;

	function get_tag()
	{
		$s_name    = htmlspecialchars($this->name);
		$s_default = htmlspecialchars($this->default_value);

		return '<input type="hidden"' .
			' name="'  . $s_name    . '"' .
			' value="' . $s_default . '" />' . "\n";
	}
}

class Tracker_field_submit extends Tracker_field
{
	function get_tag()
	{
		$form = $this->form;

		$s_title  = htmlspecialchars($this->title);
		$s_base   = htmlspecialchars($form->base);
		$s_refer  = htmlspecialchars($form->refer);
		$s_config = htmlspecialchars($form->config_name);

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
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_NUMERIC;

	function format_cell($timestamp)
	{
		return format_date($timestamp);
	}
}

class Tracker_field_past extends Tracker_field
{
	var $sort_type = PLUGIN_TRACKER_SORT_TYPE_NUMERIC;

	function get_value($timestamp)
	{
		return UTIME - $timestamp;
	}

	function format_cell($timestamp)
	{
		return get_passage($timestamp, FALSE);
	}
}

///////////////////////////////////////////////////////////////////////////
// tracker_list plugin

function plugin_tracker_list_convert()
{
	global $vars;

	$args = func_get_args();
	$argc = count($args);
	if ($argc > 4) {
		return PLUGIN_TRACKER_LIST_USAGE . '<br />';
	}

	$base   = isset($vars['page']) ? $vars['page'] : '';
	$refer  = '';
	$rel    = '';
	$config = '';
	$order  = '';
	$list   = '';
	$limit  = NULL;
	switch ($argc) {
	case 4: $limit = $args[3];	/*FALLTHROUGH*/
	case 3: $order = $args[2];	/*FALLTHROUGH*/
	case 2: $rel   = $args[1];	/*FALLTHROUGH*/
	case 1:
		// Set "$config/$list"
		if ($args[0] != '') {
			$arg = explode('/', $args[0], 2);
			if ($arg[0] != '' ) $config = $arg[0];
			if (isset($arg[1])) $list   = $arg[1];
		}
	}

	unset($args, $argc, $arg);

	return plugin_tracker_list_render($base, $refer, $rel, $config, $order, $list, $limit);
}

function plugin_tracker_list_action()
{
	global $get;

	$base   = isset($get['base'])   ? $get['base']   : '';
	$refer  = isset($get['refer'])  ? $get['refer']  : '';
	$rel    = '';
	$config = isset($get['config']) ? $get['config'] : '';
	$order  = isset($get['order'])  ? $get['order']  : '';
	$list   = isset($get['list'])   ? $get['list']   : '';
	$limit  = isset($get['limit'])  ? $get['limit']  : NULL;

	$s_refer = make_pagelink($refer);

	return array(
		'msg' => plugin_tracker_message('msg_list'),
		'body'=>
			str_replace('$1', $s_refer, plugin_tracker_message('msg_back')) .
			plugin_tracker_list_render($base, $refer, $rel, $config, $order, $list, $limit)
	);
}

function plugin_tracker_list_render($base, $refer, $rel = '', $config = '', $order = '', $list = '', $limit = NULL)
{
	$tracker_list = & new Tracker_list();

	if (! $tracker_list->init($base, $refer, $config, $rel)  ||
		! $tracker_list->setSortOrder($order)) {
		return '#tracker_list: ' . htmlspecialchars($tracker_list->error) . '<br />';
	}

	if (! is_page($tracker_list->form->refer)) {
		return '#tracker_list: Refer page not found: ' . htmlspecialchars($refer) . '<br />';
	}

	$result = $tracker_list->toString($list, $limit);
	if ($result === FALSE) {
		return '#tracker_list: ' . htmlspecialchars($tracker_list->error) . '<br />';
	}
	unset($tracker_list);

	return convert_html($result);
}

// Listing class
class Tracker_list
{
	var $form;	// class Tracker_form

	var $rows   = array();
	var $orders;
	var $error  = '';	// Error message

	// _generate_regex()
	var $pattern;
	var $pattern_fields;

	// add()
	var $_added = array();

	// toString()
	var $_list;
	var $_row;
	var $_the_first_character_of_the_line;

	function init($base, $refer, $config = NULL, $relative = '')
	{
		$this->form = & new Tracker_form();
		return $this->form->init($base, $refer, $config, $relative);
	}

	// Generate/Regenerate regex to load one page
	function _generate_regex()
	{
		if (isset($this->pattern) && isset($this->pattern_fields)) return TRUE;

		$template_page = $this->form->config->page . '/' . 'page';
		$fields        = $this->form->fields;
		
		$pattern        = array();
		$pattern_fields = array();

		$template = plugin_tracker_get_source($template_page, TRUE);
		if ($template === FALSE || empty($template)) {
			$this->error = 'Page not found or seems empty: ' . $template_page;
			return FALSE;
		}

		// Block-plugins to pseudo fields (#convert => [_block_convert])
		$template = preg_replace('/^\#([^\(\s]+)(?:\((.*)\))?\s*$/m', '[_block_$1]', $template);

		// Now, $template = array('*someting*', 'fieldname', '*someting*', 'fieldname', ...)
		$template = preg_split('/\\\\\[(\w+)\\\\\]/', preg_quote($template, '/'), -1, PREG_SPLIT_DELIM_CAPTURE);

		// NOTE: if the page has garbages between [field]s, it will fail to be load
		while (! empty($template)) {
			// Just ignore these _fixed_ data
			$pattern[] = preg_replace('/\s+/', '\\s*', '(?>\\s*' . trim(array_shift($template)) . '\\s*)');
			if (empty($template)) continue;

			$fieldname = array_shift($template);
			if (isset($fields[$fieldname])) {
				$pattern[] = '(.*?)';	// Just capture it
				$pattern_fields[] = $fieldname;	// Capture it as this $filedname
			} else {
				$pattern[] = '.*?';	// Just ignore pseudo fields etc
			}
		}

		$this->pattern        = '/' . implode('', $pattern) . '/sS';
		$this->pattern_fields = $pattern_fields;

		return TRUE;
	}

	// Adding $this->rows
	// Add multiple pages at a time
	function loadRows()
	{
		$base  = $this->form->base . '/';
		$len   = strlen($base);
		$regex = '#^' . preg_quote($base, '#') . '#';

		foreach (preg_grep($regex, array_values(get_existpages())) as $pagename) {
			if (preg_match(PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN, substr($pagename, $len))) {
				continue;
			}
			if ($this->addRow($pagename) === FALSE) return FALSE;
		}
		if (empty($this->rows)) {
			$this->error = 'Pages not found under: ' . $base;
			return FALSE;
		}

		return TRUE;
	}

	// Add one pages
	function addRow($pagename, $rescan = FALSE)
	{
		// Generate/Regenerate regex if needed
		if ($this->_generate_regex() === FALSE) return FALSE;

		if (isset($this->_added[$pagename])) return TRUE;
		$this->_added[$pagename] = TRUE;

		$source = plugin_tracker_get_source($pagename, TRUE);
		if ($source === FALSE) $source = '';

		// Compat: 'move to [[page]]' (like bugtrack plugin)
		$matches = array();
		if (! $rescan && ! empty($source) && preg_match('/move\sto\s(.+)/', $source, $matches)) {
			$to_page = strip_bracket(trim($matches[1]));
			if (is_page($to_page)) {
				unset($source, $matches);	// Release
				return $this->addRow($to_page, TRUE);	// Recurse(Rescan) once
			}
		}

		// Default column
		$filetime = get_filetime($pagename);
		$row = array(
			// column => default data of the cell
			'_page'   => $pagename,	// TODO: Redudant column pair [1]
			'_real'   => $pagename,	// TODO: Redudant column pair [1]
			'_update' => $filetime,	// TODO: Redudant column pair [2]
			'_past'   => $filetime,	// TODO: Redudant column pair [2]
		);

		// Load / Redefine cell
		$matches = array();
		if (preg_match($this->pattern, $source, $matches)) {
			array_shift($matches);	// $matches[0] = all of the captured string
			foreach ($this->pattern_fields as $key => $fieldname) {
				$row[$fieldname] = trim($matches[$key]);
				unset($matches[$key]);
			}
			$this->rows[] = $row;
		} else if (PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE) {
			$this->rows[] = $row;	// Error
		}

		return TRUE;
	}

	// setSortOrder()
	function _order_commands2orders($order_commands = '')
	{
		$order_commands = trim($order_commands);
		if ($order_commands == '') $order_commands = PLUGIN_TRACKER_DEFAULT_ORDER;
		if ($order_commands == '') return array();

		$orders = array();

		$i = 0;
		foreach (explode(';', $order_commands) as $command) {
			$command = trim($command);
			if ($command == '') continue;

			$arg = explode(':', $command, 2);
			$fieldname = isset($arg[0]) ? trim($arg[0]) : '';
			$order     = isset($arg[1]) ? trim($arg[1]) : '';

			$_order = $this->_sortkey_string2define($order);
			if ($_order === FALSE) {
				$this->error =  'Invalid sort key: ' . $order;
				return FALSE;
			} else if (isset($orders[$fieldname])) {
				$this->error =  'Sort key already set for: ' . $fieldname;
				return FALSE;
			}

			if (PLUGIN_TRACKER_LIST_SORT_LIMIT <= $i) continue;
			++$i;

			$orders[$fieldname] = $_order;
		}

		return $orders;
	}

	// Set commands for sort()
	function setSortOrder($order_commands = '')
	{
		$orders = $this->_order_commands2orders($order_commands);
		if ($orders === FALSE) {
			unset($this->orders);
			return FALSE;
		} else {
			$this->orders = $orders;
			return TRUE;
		}
	}

	// sortRows(): Internal sort type => PHP sort define
	function _sort_type_dropout($order)
	{
		switch ($order) {
		case PLUGIN_TRACKER_SORT_TYPE_REGULAR: return SORT_REGULAR;
		case PLUGIN_TRACKER_SORT_TYPE_NUMERIC: return SORT_NUMERIC;
		case PLUGIN_TRACKER_SORT_TYPE_STRING:  return SORT_STRING;
		case PLUGIN_TRACKER_SORT_TYPE_NATURAL: return SORT_NATURAL;
		default:
			$this->error = 'Invalid sort type';
			return FALSE;
		}
	}

	// sortRows(): Internal sort order => PHP sort define
	function _sort_order_dropout($order)
	{
		switch ($order) {
		case PLUGIN_TRACKER_SORT_ORDER_ASC:  return SORT_ASC;
		case PLUGIN_TRACKER_SORT_ORDER_DESC: return SORT_DESC;
		default:
			$this->error = 'Invalid sort order';
			return FALSE;
		}
	}

	// Sort $this->rows by $this->orders
	function sortRows()
	{
		if (! isset($this->orders)) {
			$this->error = "Sort order seems not set";
			return FALSE;
		}

		$fields = $this->form->fields;
		$orders = $this->orders;
		$types  = array();

		$fieldnames = array_keys($orders);	// Field names to sort

		foreach ($fieldnames as $fieldname) {
			if (! isset($fields[$fieldname])) {
				$this->error =  'No such field: ' . $fieldname;
				return FALSE;
			}
			$types[$fieldname]  = $this->_sort_type_dropout($fields[$fieldname]->sort_type);
			$orders[$fieldname] = $this->_sort_order_dropout($orders[$fieldname]);
			if ($types[$fieldname] === FALSE || $orders[$fieldname] === FALSE) return FALSE;
		}

		$columns = array();
		foreach ($this->rows as $row) {
			foreach ($fieldnames as $fieldname) {
				if (isset($row[$fieldname])) {
					$columns[$fieldname][] = $fields[$fieldname]->get_value($row[$fieldname]);
				} else {
					$columns[$fieldname][] = '';
				}
			}
		}

		$params = array();
		foreach ($fieldnames as $fieldname) {

			if ($types[$fieldname] == SORT_NATURAL) {
				$column = & $columns[$fieldname];
				natcasesort($column);
				$i = 0;
				$last = NULL;
				foreach (array_keys($column) as $key) {
					// Consider the same values there, for array_multisort()
					if ($last !== $column[$key]) ++$i;
					$last = strtolower($column[$key]);	// natCASEsort()
					$column[$key] = $i;
				}
				ksort($column, SORT_NUMERIC);	// Revert the order
				$types[$fieldname] = SORT_NUMERIC;
			}

			// One column set (one-dimensional array, sort type, and sort order)
			// for array_multisort()
			$params[] = $columns[$fieldname];
			$params[] = $types[$fieldname];
			$params[] = $orders[$fieldname];
		}
		if (! empty($orders) && ! empty($this->rows)) {
			$params[] = & $this->rows;	// The target
			call_user_func_array('array_multisort', $params);
		}

		return TRUE; 
	}

	// toString(): Sort key: Define to string (internal var => string)
	function _sortkey_define2string($sortkey)
	{
		switch ($sortkey) {
		case PLUGIN_TRACKER_SORT_ORDER_ASC:     return 'asc';
		case PLUGIN_TRACKER_SORT_ORDER_DESC:    return 'desc';
		default:
			$this->error =  'No such define: ' . $sortkey;
			return FALSE;
		}
	}

	// toString(): Sort key: String to define (string => internal var)
	function _sortkey_string2define($sortkey)
	{
		switch (strtoupper(trim($sortkey))) {
		case '':          return PLUGIN_TRACKER_SORT_ORDER_DEFAULT; break;

		case SORT_ASC:    /*FALLTHROUGH*/ // Compat, will be removed at 1.4.9 or later
		case 'SORT_ASC':  /*FALLTHROUGH*/
		case 'ASC':       return PLUGIN_TRACKER_SORT_ORDER_ASC;

		case SORT_DESC:   /*FALLTHROUGH*/ // Compat, will be removed at 1.4.9 or later
 		case 'SORT_DESC': /*FALLTHROUGH*/
		case 'DESC':      return PLUGIN_TRACKER_SORT_ORDER_DESC;

		default:
			$this->error =  'Invalid sort key: ' . $sortkey;
			return FALSE;
		}
	}

	// toString(): Called within preg_replace_callback()
	function _replace_title($matches = array())
	{
		$form   = $this->form;
		$base   = $form->base;
		$refer  = $form->refer;
		$fields = $form->fields;
		$config = $form->config_name;

		$orders = $this->orders;
		$list   = $this->_list;

		$fieldname = isset($matches[1]) ? $matches[1] : '';
		if (! isset($fields[$fieldname])) {
			// Invalid $fieldname or user's own string or something. Nothing to do
			return isset($matches[0]) ? $matches[0] : '';
		}

		// This column seems sorted or not
		if (isset($orders[$fieldname])) {
			$is_asc = ($orders[$fieldname] == PLUGIN_TRACKER_SORT_ORDER_ASC);

			$indexes = array_flip(array_keys($orders));
			$index   = $indexes[$fieldname] + 1;
			unset($indexes);

			$arrow = '&br;' . ($is_asc ? '&uarr;' : '&darr;') . '(' . $index . ')';
			// Allow flip, if this is the first column
			if (($index == 1) xor $is_asc) {
				$order = PLUGIN_TRACKER_SORT_ORDER_ASC;
			} else {
				$order = PLUGIN_TRACKER_SORT_ORDER_DESC;
			}
		} else {
			$arrow = '';
			$order = PLUGIN_TRACKER_SORT_ORDER_DEFAULT;
		}

		// This column will be the first position , if you click
		$orders = array($fieldname => $order) + $orders;

		$_orders = array();
		foreach ($orders as $_fieldname => $_order) {
			if ($_order == PLUGIN_TRACKER_SORT_ORDER_DEFAULT) {
				$_orders[] = $_fieldname;
			} else {
				$_orders[] = $_fieldname . ':' . $this->_sortkey_define2string($_order);
			}
		}

		$script = get_script_uri();
		$r_base   = ($refer  != $base) ? '&base='  . rawurlencode($base) : '';
		$r_config = ($config != PLUGIN_TRACKER_DEFAULT_CONFIG) ? '&config=' . rawurlencode($config) : '';
		$r_list   = ($list   != PLUGIN_TRACKER_DEFAULT_LIST  ) ? '&list='   . rawurlencode($list)   : '';
		$r_order  = ! empty($_orders) ? '&order=' . rawurlencode(join(';', $_orders)) : '';

		return
			 '[[' .
				$fields[$fieldname]->title . $arrow .
			'>' .
				$script . '?plugin=tracker_list' .
				'&refer=' . rawurlencode($refer) .	// Try to show 'page title' properly
				$r_base . $r_config . $r_list . $r_order  .
			']]';
	}

	// toString(): Called within preg_replace_callback()
	function _replace_item($matches = array())
	{
		$fields = $this->form->fields;
		$row    = $this->_row;
		$tfc    = $this->_the_first_character_of_the_line ;

		$params    = isset($matches[1]) ? explode(',', $matches[1]) : array();
		$fieldname = isset($params[0])  ? $params[0] : '';
		$stylename = isset($params[1])  ? $params[1] : $fieldname;

		$str = '';

		if ($fieldname != '') {
			if (! isset($row[$fieldname])) {
				// Maybe load miss of the page
				if (isset($fields[$fieldname])) {
					$str = '[match_err]';	// Exactlly
				} else {
					$str = isset($matches[0]) ? $matches[0] : '';	// Nothing to do
				}
			} else {
				$str = $row[$fieldname];
				if (isset($fields[$fieldname])) {
					$str = $fields[$fieldname]->format_cell($str);
				}
			}
			$str = plugin_tracker_escape($str, $tfc);
		}

		if (isset($fields[$stylename]) && isset($row[$stylename])) {
			$_style = $fields[$stylename]->get_style($row[$stylename]);
			$str    = sprintf($_style, $str);
		}

		return $str;
	}

	// Output a part of Wiki text
	function toString($list = PLUGIN_TRACKER_DEFAULT_LIST, $limit = NULL)
	{
		$list = trim($list);
		if ($list == '') $list = PLUGIN_TRACKER_DEFAULT_LIST;

		if ($limit == NULL) $limit = PLUGIN_TRACKER_DEFAULT_LIMIT;
		if (! is_numeric($limit)) {
			$this->error = "Limit seems not numeric: " . $limit;
			return FALSE;
		}
	
		$form   = & $this->form;

		$this->_list = $list;	// For _replace_title() only
		$list = $form->config->page . '/' . $list;

		$source = array();
		$regex  = '/\[([^\[\]]+)\]/';

		// Loading template
		$template = plugin_tracker_get_source($list, TRUE);
		if ($template === FALSE || empty($template)) {
			$this->error = 'List not found: ' . $list;
			return FALSE;
		}

		// Try to create $form->fields just you need
		if ($form->initFields('_real', plugin_tracker_field_pickup($template),
		    array_keys($this->orders)) === FALSE) {
			$this->error = $form->error;
			return FALSE;
		}

		// Load and sort $this->rows
 		if ($this->loadRows() === FALSE || $this->sortRows() === FALSE) return FALSE;
		$rows = $this->rows;

		// toString()
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
		// TODO: How do you feel single/multiple table rows with 'c'(decolation)?
		$matches = $t_header = $t_body = $t_footer = array();
		$template = plugin_tracker_get_source($list);
		if ($template === FALSE) {
			$this->error = 'Page not found or seems empty: ' . $list;
			return FALSE;
		}
		foreach ($template as $line) {
			if (preg_match('/^\|.+\|([hfc])$/i', $line, $matches)) {
				if (strtolower($matches[1]) == 'f') {
					$t_footer[] = $line;	// Table footer
				} else {
					$t_header[] = $line;	// Table header, or decoration
				}
			} else {
				$t_body[]   = $line;
			}
		}
		unset($template);

		// Header and decolation
		foreach($t_header as $line) {
			$source[] = preg_replace_callback($regex, array(& $this, '_replace_title'), $line);
		}
		unset($t_header);
		// Repeat
		foreach ($rows as $row) {
			$this->_row = $row;
			// Body
			foreach ($t_body as $line) {
				if (ltrim($line) != '') {
					$this->_the_first_character_of_the_line = $line[0];
					$line = preg_replace_callback($regex, array(& $this, '_replace_item'), $line);
				}
				$source[] = $line;
			}
		}
		unset($t_body);
		// Footer
		foreach($t_footer as $line) {
			$source[] = preg_replace_callback($regex, array(& $this, '_replace_title'), $line);
		}
		unset($t_footer);

		return implode('', $source);
	}
}

// Roughly checking listed fields from template
// " [field1] [field2,style1] " => array('fielld', 'field2')
function plugin_tracker_field_pickup($string = '')
{
	if (! is_string($string) || empty($string)) return array();

	$fieldnames = array();

	$matches = array();
	preg_match_all('/\[([^\[\]]+)\]/', $string, $matches);
	unset($matches[0]);

	foreach ($matches[1] as $match) {
		$params = explode(',', $match, 2);
		if (isset($params[0])) {
			$fieldnames[$params[0]] = TRUE;
		}
	}

	return array_keys($fieldnames);
}

function plugin_tracker_get_source($page, $join = FALSE)
{
	$source = get_source($page, TRUE, $join);
	if ($source === FALSE) return FALSE;

	return preg_replace(
		 array(
			'/^#freeze\s*$/im',
			'/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m',	// Remove fixed-heading anchors
		),
		array(
			'',
			'$1$2',
		),
		$source
	);
}

// Escape special characters not to break Wiki syntax
function plugin_tracker_escape($string, $syntax_hint = '')
{
	// Default: line-oriented
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

function plugin_tracker_message($key)
{
	global $_tracker_messages;
	return isset($_tracker_messages[$key]) ? $_tracker_messages[$key] : 'NOMESSAGE';
}

?>
