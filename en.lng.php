<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: en.lng.php,v 1.16 2007/02/11 05:53:29 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki message file (English)

// NOTE: Encoding of this file, must equal to encoding setting

// Encoding hint
$_LANG['encode_hint']['en'] = '';

///////////////////////////////////////
// Page titles
$_title_cannotedit = ' $1 is not editable';
$_title_edit       = 'Edit of  $1';
$_title_preview    = 'Preview of  $1';
$_title_collided   = 'On updating  $1, a collision has occurred.';
$_title_updated    = ' $1 was updated';
$_title_deleted    = ' $1 was deleted';
$_title_help       = 'Help';
$_title_invalidwn  = 'It is not a valid WikiName';
$_title_backuplist = 'Backup list';

///////////////////////////////////////
// Messages
$_msg_unfreeze = 'Unfreeze';
$_msg_preview  = 'To confirm the changes, click the button at the bottom of the page';
$_msg_preview_delete = '(The contents of the page are empty. Updating deletes this page.)';
$_msg_collided = 'It seems that someone has already updated this page while you were editing it.<br />
 + is placed at the beginning of a line that was newly added.<br />
 ! is placed at the beginning of a line that has possibly been updated.<br />
 Edit those lines, and submit again.';

$_msg_collided_auto = 'It seems that someone has already updated this page while you were editing it.<br /> The collision has been corrected automatically, but there may still be some problems with the page.<br />
 To confirm the changes to the page, press [Update].<br />';

$_msg_invalidiwn  = ' $1 is not a valid $2.';
$_msg_invalidpass = 'Invalid password.';
$_msg_notfound    = 'The page was not found.';
$_msg_addline     = 'The added line is <span class="diff_added">THIS COLOR</span>.';
$_msg_delline     = 'The deleted line is <span class="diff_removed">THIS COLOR</span>.';
$_msg_goto        = 'Go to $1.';
$_msg_andresult   = 'In the page <strong> $2</strong>, <strong> $3</strong> pages that contain all the terms $1 were found.';
$_msg_orresult    = 'In the page <strong> $2</strong>, <strong> $3</strong> pages that contain at least one of the terms $1 were found.';
$_msg_notfoundresult = 'No page which contains $1 has been found.';
$_msg_symbol      = 'Symbols';
$_msg_other       = 'Others';
$_msg_help        = 'View Text Formatting Rules';
$_msg_week        = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
$_msg_content_back_to_top = '<div class="jumpmenu"><a href="#navigator">&uarr;</a></div>';
$_msg_word        = 'These search terms have been highlighted:';

///////////////////////////////////////
// Symbols
$_symbol_anchor   = '&dagger;';
$_symbol_noexists = '?';

///////////////////////////////////////
// Form buttons
$_btn_preview   = 'Preview';
$_btn_repreview = 'Preview again';
$_btn_update    = 'Update';
$_btn_cancel    = 'Cancel';
$_btn_notchangetimestamp = 'Do not change timestamp';
$_btn_addtop    = 'Add to top of page';
$_btn_template  = 'Use page as template';
$_btn_load      = 'Load';
$_btn_edit      = 'Edit';
$_btn_delete    = 'Delete';

///////////////////////////////////////
// Authentication
$_title_cannotread = ' $1 is not readable';
$_msg_auth         = 'PukiWikiAuth';

///////////////////////////////////////
// Page name
$rule_page = 'FormattingRules';	// Formatting rules
$help_page = 'Help';		// Help

/////////////////////////////////////////////////
// No subject (article)
$_no_subject = 'no subject';

/////////////////////////////////////////////////
// No name (article,comment,pcomment)
$_no_name = '';

/////////////////////////////////////////////////
// Skin
/////////////////////////////////////////////////

$_LANG['skin']['add']       = 'Add';
$_LANG['skin']['backup']    = 'Backup';
$_LANG['skin']['copy']      = 'Copy';
$_LANG['skin']['diff']      = 'Diff';
$_LANG['skin']['edit']      = 'Edit';
$_LANG['skin']['filelist']  = 'List of page files';	// List of filenames
$_LANG['skin']['freeze']    = 'Freeze';
$_LANG['skin']['help']      = 'Help';
$_LANG['skin']['list']      = 'List of pages';
$_LANG['skin']['new']       = 'New';
$_LANG['skin']['rdf']       = 'RDF of recent changes';
$_LANG['skin']['recent']    = 'Recent changes';	// RecentChanges
$_LANG['skin']['reload']    = 'Reload';
$_LANG['skin']['rename']    = 'Rename';	// Rename a page (and related)
$_LANG['skin']['rss']       = 'RSS of recent changes';
$_LANG['skin']['rss10']     = & $_LANG['skin']['rss'];
$_LANG['skin']['rss20']     = & $_LANG['skin']['rss'];
$_LANG['skin']['search']    = 'Search';
$_LANG['skin']['top']       = 'Front page';	// Top page
$_LANG['skin']['unfreeze']  = 'Unfreeze';
$_LANG['skin']['upload']    = 'Upload';	// Attach a file

///////////////////////////////////////
// Plug-in message
///////////////////////////////////////
// add.inc.php
$_title_add = 'Add to $1';
$_msg_add   = 'Two and the contents of an input are added for a new-line to the contents of a page of present addition.';
	// This message is such bad english that I don't understand it, sorry. --Bjorn De Meyer

///////////////////////////////////////
// article.inc.php
$_btn_name    = 'Name: ';
$_btn_article = 'Submit';
$_btn_subject = 'Subject: ';
$_msg_article_mail_sender = 'Author: ';
$_msg_article_mail_page   = 'Page: ';

///////////////////////////////////////
// attach.inc.php
$_attach_messages = array(
	'msg_uploaded' => 'Uploaded the file to  $1',
	'msg_deleted'  => 'Deleted the file in  $1',
	'msg_freezed'  => 'The file has been frozen.',
	'msg_unfreezed'=> 'The file has been unfrozen',
	'msg_renamed'  => 'The file has been renamed',
	'msg_upload'   => 'Upload to $1',
	'msg_info'     => 'File information',
	'msg_confirm'  => '<p>Delete %s.</p>',
	'msg_list'     => 'List of attached file(s)',
	'msg_listpage' => 'File already exists in  $1',
	'msg_listall'  => 'Attached file list of all pages',
	'msg_file'     => 'Attach file',
	'msg_maxsize'  => 'Maximum file size is %s.',
	'msg_count'    => ' <span class="small">%s download</span>',
	'msg_password' => 'password',
	'msg_adminpass'=> 'Administrator password',
	'msg_delete'   => 'Delete file.',
	'msg_freeze'   => 'Freeze file.',
	'msg_unfreeze' => 'Unfreeze file.',
	'msg_isfreeze' => 'File is frozen.',
	'msg_rename'   => 'Rename',
	'msg_newname'  => 'New file name',
	'msg_require'  => '(require administrator password)',
	'msg_filesize' => 'size',
	'msg_date'     => 'date',
	'msg_dlcount'  => 'access count',
	'msg_md5hash'  => 'MD5 hash',
	'msg_page'     => 'Page',
	'msg_filename' => 'Stored filename',
	'err_noparm'   => 'Cannot upload/delete file in  $1',
	'err_exceed'   => 'File size too large to  $1',
	'err_exists'   => 'File already exists in  $1',
	'err_notfound' => 'Could not fid the file in  $1',
	'err_noexist'  => 'File does not exist.',
	'err_delete'   => 'Cannot delete file in  $1',
	'err_rename'   => 'Cannot rename this file',
	'err_password' => 'Wrong password.',
	'err_adminpass'=> 'Wrong administrator password',
	'btn_upload'   => 'Upload',
	'btn_info'     => 'Information',
	'btn_submit'   => 'Submit'
);

///////////////////////////////////////
// back.inc.php
$_msg_back_word = 'Back';

///////////////////////////////////////
// backup.inc.php
$_title_backup_delete  = 'Deleting backup of  $1';
$_title_backupdiff     = 'Backup diff of  $1(No. $2)';
$_title_backupnowdiff  = 'Backup diff of  $1 vs current(No. $2)';
$_title_backupsource   = 'Backup source of  $1(No. $2)';
$_title_backup         = 'Backup of  $1(No. $2)';
$_title_pagebackuplist = 'Backup list of  $1';
$_title_backuplist     = 'Backup list';
$_msg_backup_deleted   = 'Backup of  $1 has been deleted.';
$_msg_backup_adminpass = 'Please input the password for deleting.';
$_msg_backuplist       = 'List of Backups';
$_msg_nobackup         = 'There are no backup(s) of  $1.';
$_msg_diff             = 'diff';
$_msg_nowdiff          = 'diff current';
$_msg_source           = 'source';
$_msg_backup           = 'backup';
$_msg_view             = 'View the  $1.';
$_msg_deleted          = ' $1 has been deleted.';

///////////////////////////////////////
// calendar_viewer.inc.php
$_err_calendar_viewer_param2   = 'Wrong second parameter.';
$_msg_calendar_viewer_right    = 'Next %d&gt;&gt;';
$_msg_calendar_viewer_left     = '&lt;&lt; Prev %d';
$_msg_calendar_viewer_restrict = 'Due to the blocking, the calendar_viewer cannot refer to $1.';

///////////////////////////////////////
// calendar2.inc.php
$_calendar2_plugin_edit  = '[edit]';
$_calendar2_plugin_empty = '%s is empty.';

///////////////////////////////////////
// comment.inc.php
$_btn_name    = 'Name: ';
$_btn_comment = 'Post Comment';
$_msg_comment = 'Comment: ';
$_title_comment_collided = 'On updating  $1, a collision has occurred.';
$_msg_comment_collided   = 'It seems that someone has already updated the page you were editing.<br />
 The comment was added, alhough it may be inserted in the wrong position.<br />';

///////////////////////////////////////
// deleted.inc.php
$_deleted_plugin_title = 'The list of deleted pages';
$_deleted_plugin_title_withfilename = 'The list of deleted pages (with filename)';

///////////////////////////////////////
// diff.inc.php
$_title_diff         = 'Diff of  $1';
$_title_diff_delete  = 'Deleting diff of  $1';
$_msg_diff_deleted   = 'Diff of  $1 has been deleted.';
$_msg_diff_adminpass = 'Please input the password for deleting.';

///////////////////////////////////////
// filelist.inc.php (list.inc.php)
$_title_filelist = 'List of page files';

///////////////////////////////////////
// freeze.inc.php
$_title_isfreezed = ' $1 has already been frozen';
$_title_freezed   = ' $1 has been frozen.';
$_title_freeze    = 'Freeze  $1';
$_msg_freezing    = 'Please input the password for freezing.';
$_btn_freeze      = 'Freeze';

///////////////////////////////////////
// include.inc.php
$_msg_include_restrict = 'Due to the blocking, $1 cannot be include(d).';

///////////////////////////////////////
// insert.inc.php
$_btn_insert = 'add';

///////////////////////////////////////
// interwiki.inc.php
$_title_invalidiwn = 'This is not a valid InterWikiName';

///////////////////////////////////////
// list.inc.php
$_title_list = 'List of pages';

///////////////////////////////////////
// ls2.inc.php
$_ls2_err_nopages = '<p>There is no child page in \' $1\'</p>';
$_ls2_msg_title   = 'List of pages which begin with \' $1\'';

///////////////////////////////////////
// memo.inc.php
$_btn_memo_update = 'update';

///////////////////////////////////////
// navi.inc.php
$_navi_prev = 'Prev';
$_navi_next = 'Next';
$_navi_up   = 'Up';
$_navi_home = 'Home';

///////////////////////////////////////
// newpage.inc.php
$_msg_newpage = 'New page';

///////////////////////////////////////
// paint.inc.php
$_paint_messages = array(
	'field_name'    => 'Name',
	'field_filename'=> 'Filename',
	'field_comment' => 'Comment',
	'btn_submit'    => 'paint',
	'msg_max'       => '(Max %d x %d)',
	'msg_title'     => 'Paint and Attach to  $1',
	'msg_title_collided' => 'On updating  $1, there was a collision.',
	'msg_collided'  => 'It seems that someone has already updated this page while you were editing it.<br />
 The picture and the comment were added to this page, but there may be a problem.<br />'
);

///////////////////////////////////////
// pcomment.inc.php
$_pcmt_messages = array(
	'btn_name'       => 'Name: ',
	'btn_comment'    => 'Post Comment',
	'msg_comment'    => 'Comment: ',
	'msg_recent'     => 'Show recent %d comments.',
	'msg_all'        => 'Go to the comment page.',
	'msg_none'       => 'No comment.',
	'title_collided' => 'On updating  $1, there was a collision.',
	'msg_collided'   => 'It seems that someone has already updated this page while you were editing it.<br />
	The comment was added to the page, but there may be a problem.<br />',
	'err_pagename'   => '[[%s]] : not a valid page name.',
);
$_msg_pcomment_restrict = 'Due to the blocking, no comments could be read from  $1 at all.';

///////////////////////////////////////
// popular.inc.php
$_popular_plugin_frame       = '<h5>popular(%d)</h5><div>%s</div>';
$_popular_plugin_today_frame = '<h5>today\'s(%d)</h5><div>%s</div>';

///////////////////////////////////////
// recent.inc.php
$_recent_plugin_frame = '<h5>recent(%d)</h5>
 <div>%s</div>';

///////////////////////////////////////
// rename.inc.php
$_rename_messages  = array(
	'err'            => '<p>error:%s</p>',
	'err_nomatch'    => 'no matching page(s)',
	'err_notvalid'   => 'the new name is invalid.',
	'err_adminpass'  => 'Incorrect administrator password.',
	'err_notpage'    => '%s is not a valid pagename.',
	'err_norename'   => 'cannot rename %s.',
	'err_already'    => 'already exists :%s.',
	'err_already_below' => 'The following files already exist.',
	'msg_title'      => 'Rename page',
	'msg_page'       => 'specify source page name',
	'msg_regex'      => 'rename with regular expressions.',
	'msg_related'    => 'related pages',
	'msg_do_related' => 'A related page is also renamed.',
	'msg_rename'     => 'rename %s',
	'msg_oldname'    => 'current page name',
	'msg_newname'    => 'new page name',
	'msg_adminpass'  => 'Administrator password',
	'msg_arrow'      => '->',
	'msg_exist_none' => 'page is not processed when it already exists.',
	'msg_exist_overwrite' => 'page is overwritten when it already exists.',
	'msg_confirm'    => 'The following files will be renamed.',
	'msg_result'     => 'The following files have been overwritten.',
	'btn_submit'     => 'Submit',
	'btn_next'       => 'Next'
);

///////////////////////////////////////
// search.inc.php
$_title_search  = 'Search';
$_title_result  = 'Search result of  $1';
$_msg_searching = 'Key words are case-insenstive, and are searched for in all pages.';
$_btn_search    = 'Search';
$_btn_and       = 'AND';
$_btn_or        = 'OR';
$_search_pages  = 'Search for page starts from $1';
$_search_all    = 'Search for all pages';

///////////////////////////////////////
// source.inc.php
$_source_messages = array(
	'msg_title'    => 'Source of  $1',
	'msg_notfound' => ' $1 was not found.',
	'err_notfound' => 'cannot display the page source.'
);

///////////////////////////////////////
// template.inc.php
$_msg_template_start   = 'Start:<br />';
$_msg_template_end     = 'End:<br />';
$_msg_template_page    = '$1/copy';
$_msg_template_refer   = 'Page:';
$_msg_template_force   = 'Edit with a page name which already exists';
$_err_template_already = ' $1 already exists.';
$_err_template_invalid = ' $1 is not a valid page name.';
$_btn_template_create  = 'Create';
$_title_templatei      = 'create a new page, using  $1 as a template.';

///////////////////////////////////////
// tracker.inc.php
$_tracker_messages = array(
	'msg_list'   => 'List items of  $1',
	'msg_back'   => '<p> $1</p>',
	'msg_limit'  => 'top  $2 results out of  $1.',
	'btn_page'   => 'Page',
	'btn_name'   => 'Name',
	'btn_real'   => 'Realname',
	'btn_submit' => 'Add',
	'btn_date'   => 'Date',
	'btn_refer'  => 'Refer page',
	'btn_base'   => 'Base page',
	'btn_update' => 'Update',
	'btn_past'   => 'Past',
);

///////////////////////////////////////
// unfreeze.inc.php
$_title_isunfreezed = ' $1 is not frozen';
$_title_unfreezed   = ' $1 has been unfrozen.';
$_title_unfreeze    = 'Unfreeze  $1';
$_msg_unfreezing    = 'Please input the password for unfreezing.';
$_btn_unfreeze      = 'Unfreeze';

///////////////////////////////////////
// versionlist.inc.php
$_title_versionlist = 'version list';

///////////////////////////////////////
// vote.inc.php
$_vote_plugin_choice = 'Selection';
$_vote_plugin_votes  = 'Vote';

///////////////////////////////////////
// yetlist.inc.php
$_title_yetlist = 'List of pages which have not yet been created.';
$_err_notexist  = 'All pages have been created.';
?>
