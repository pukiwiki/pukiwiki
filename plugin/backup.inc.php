<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: backup.inc.php,v 1.29 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Backup plugin

// Prohibit rendering old wiki texts (suppresses load, transfer rate, and security risk)
define('PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING', PKWK_SAFE_MODE || PKWK_OPTIMISE);

function plugin_backup_action()
{
	global $vars, $do_backup, $hr;
	global $_msg_backuplist, $_msg_diff, $_msg_nowdiff, $_msg_source, $_msg_backup;
	global $_msg_view, $_msg_goto, $_msg_deleted;
	global $_title_backupdiff, $_title_backupnowdiff, $_title_backupsource;
	global $_title_backup, $_title_pagebackuplist, $_title_backuplist;

	if (! $do_backup) return;

	$page = isset($vars['page']) ? $vars['page']  : '';
	if ($page == '') return array('msg'=>$_title_backuplist, 'body'=>plugin_backup_get_list_all());

	check_readable($page, true, true);
	$s_page = htmlsc($page);
	$r_page = rawurlencode($page);

	$action = isset($vars['action']) ? $vars['action'] : '';
	if ($action == 'delete') return plugin_backup_delete($page);

	$s_action = $r_action = '';
	if ($action != '') {
		$s_action = htmlsc($action);
		$r_action = rawurlencode($action);
	}

	$s_age  = (isset($vars['age']) && is_numeric($vars['age'])) ? $vars['age'] : 0;
	if ($s_age <= 0) return array( 'msg'=>$_title_pagebackuplist, 'body'=>plugin_backup_get_list($page));

	$script = get_script_uri();

	$body  = '<ul>' . "\n";
	$body .= ' <li><a href="' . $script . '?cmd=backup">' . $_msg_backuplist . '</a></li>' ."\n";

	$href    = $script . '?cmd=backup&amp;page=' . $r_page . '&amp;age=' . $s_age;
	$is_page = is_page($page);

	if ($is_page && $action != 'diff')
		$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
			'&amp;action=diff">' . $_msg_diff . '</a>',
			$_msg_view) . '</li>' . "\n";

	if ($is_page && $action != 'nowdiff')
		$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
			'&amp;action=nowdiff">' . $_msg_nowdiff . '</a>',
			$_msg_view) . '</li>' . "\n";

	if ($action != 'source')
		$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
			'&amp;action=source">' . $_msg_source . '</a>',
			$_msg_view) . '</li>' . "\n";

	if (! PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING && $action)
		$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
			'">' . $_msg_backup . '</a>',
			$_msg_view) . '</li>' . "\n";

	if ($is_page) {
		$body .= ' <li>' . str_replace('$1',
			'<a href="' . $script . '?' . $r_page . '">' . $s_page . '</a>',
			$_msg_goto) . "\n";
	} else {
		$body .= ' <li>' . str_replace('$1', $s_page, $_msg_deleted) . "\n";
	}

	$backups = get_backup($page);
	$backups_count = count($backups);
	if ($s_age > $backups_count) $s_age = $backups_count;

	if ($backups_count > 0) {
		$body .= '  <ul>' . "\n";
		foreach($backups as $age => $val) {
			$date = format_date($val['time'], TRUE);
			$body .= ($age == $s_age) ?
				'   <li><em>' . $age . ' ' . $date . '</em></li>' . "\n" :
				'   <li><a href="' . $script . '?cmd=backup&amp;action=' .
				$r_action . '&amp;page=' . $r_page . '&amp;age=' . $age .
				'">' . $age . ' ' . $date . '</a></li>' . "\n";
		}
		$body .= '  </ul>' . "\n";
	}
	$body .= ' </li>' . "\n";
	$body .= '</ul>'  . "\n";

	if ($action == 'diff') {
		$title = & $_title_backupdiff;
		$old = ($s_age > 1) ? join('', $backups[$s_age - 1]['data']) : '';
		$cur = join('', $backups[$s_age]['data']);
		$body .= plugin_backup_diff(do_diff($old, $cur));
	} else if ($s_action == 'nowdiff') {
		$title = & $_title_backupnowdiff;
		$old = join('', $backups[$s_age]['data']);
		$cur = join('', get_source($page));
		$body .= plugin_backup_diff(do_diff($old, $cur));
	} else if ($s_action == 'source') {
		$title = & $_title_backupsource;
		$body .= '<pre>' . htmlsc(join('', $backups[$s_age]['data'])) .
			'</pre>' . "\n";
	} else {
		if (PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING) {
			die_message('This feature is prohibited');
		} else {
			$title = & $_title_backup;
			$body .= $hr . "\n" .
				drop_submit(convert_html($backups[$s_age]['data']));
		}
	}

	return array('msg'=>str_replace('$2', $s_age, $title), 'body'=>$body);
}

// Delete backup
function plugin_backup_delete($page)
{
	global $vars, $_title_backup_delete, $_title_pagebackuplist, $_msg_backup_deleted;
	global $_msg_backup_adminpass, $_btn_delete, $_msg_invalidpass;

	if (! _backup_file_exists($page))
		return array('msg'=>$_title_pagebackuplist, 'body'=>plugin_backup_get_list($page)); // Say "is not found"

	$body = '';
	if (isset($vars['pass'])) {
		if (pkwk_login($vars['pass'])) {
			_backup_delete($page);
			return array(
				'msg'  => $_title_backup_delete,
				'body' => str_replace('$1', make_pagelink($page), $_msg_backup_deleted)
			);
		} else {
			$body = '<p><strong>' . $_msg_invalidpass . '</strong></p>' . "\n";
		}
	}

	$script = get_script_uri();
	$s_page = htmlsc($page);
	$body .= <<<EOD
<p>$_msg_backup_adminpass</p>
<form action="$script" method="post">
 <div>
  <input type="hidden"   name="cmd"    value="backup" />
  <input type="hidden"   name="page"   value="$s_page" />
  <input type="hidden"   name="action" value="delete" />
  <input type="password" name="pass"   size="12" />
  <input type="submit"   name="ok"     value="$_btn_delete" />
 </div>
</form>
EOD;
	return	array('msg'=>$_title_backup_delete, 'body'=>$body);
}

function plugin_backup_diff($str)
{
	global $_msg_addline, $_msg_delline, $hr;
	$ul = <<<EOD
$hr
<ul>
 <li>$_msg_addline</li>
 <li>$_msg_delline</li>
</ul>
EOD;

	return $ul . '<pre>' . diff_style_to_css(htmlsc($str)) . '</pre>' . "\n";
}

function plugin_backup_get_list($page)
{
	global $_msg_backuplist, $_msg_diff, $_msg_nowdiff, $_msg_source, $_msg_nobackup;
	global $_title_backup_delete;

	$script = get_script_uri();
	$r_page = rawurlencode($page);
	$s_page = htmlsc($page);
	$retval = array();
	$retval[0] = <<<EOD
<ul>
 <li><a href="$script?cmd=backup">$_msg_backuplist</a>
  <ul>
EOD;
	$retval[1] = "\n";
	$retval[2] = <<<EOD
  </ul>
 </li>
</ul>
EOD;

	$backups = _backup_file_exists($page) ? get_backup($page) : array();
	if (empty($backups)) {
		$msg = str_replace('$1', make_pagelink($page), $_msg_nobackup);
		$retval[1] .= '   <li>' . $msg . '</li>' . "\n";
		return join('', $retval);
	}

	if (! PKWK_READONLY) {
		$retval[1] .= '   <li><a href="' . $script . '?cmd=backup&amp;action=delete&amp;page=' .
			$r_page . '">';
		$retval[1] .= str_replace('$1', $s_page, $_title_backup_delete);
		$retval[1] .= '</a></li>' . "\n";
	}

	$href = $script . '?cmd=backup&amp;page=' . $r_page . '&amp;age=';
	$_anchor_from = $_anchor_to   = '';
	foreach ($backups as $age=>$data) {
		if (! PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING) {
			$_anchor_from = '<a href="' . $href . $age . '">';
			$_anchor_to   = '</a>';
		}
		$date = format_date($data['time'], TRUE);
		$retval[1] .= <<<EOD
   <li>$_anchor_from$age $date$_anchor_to
     [ <a href="$href$age&amp;action=diff">$_msg_diff</a>
     | <a href="$href$age&amp;action=nowdiff">$_msg_nowdiff</a>
     | <a href="$href$age&amp;action=source">$_msg_source</a>
     ]
   </li>
EOD;
	}

	return join('', $retval);
}

// List for all pages
function plugin_backup_get_list_all($withfilename = FALSE)
{
	global $cantedit;

	$pages = array_diff(get_existpages(BACKUP_DIR, BACKUP_EXT), $cantedit);

	if (empty($pages)) {
		return '';
	} else {
		return page_list($pages, 'backup', $withfilename);
	}
}
?>
