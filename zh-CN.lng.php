<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: zh-CN.lng.php,v 1.4 2011/06/05 13:47:26 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki message file (Simplified Chinese)

// ※このファイルの文字コードは、エンコーディングの設定と一致
//   している必要があります

// Encoding hint
$_LANG['encode_hint']['zh-CN'] = '';

///////////////////////////////////////
// Page titles
$_title_cannotedit = '无法编辑 $1';
$_title_edit       = '编辑 $1';
$_title_preview    = '预览 $1';
$_title_collided   = '在 $1 中发生了【编辑冲突】';
$_title_updated    = '$1 已更新';
$_title_deleted    = '$1 已删除';
$_title_help       = '帮助';
$_title_invalidwn  = '无效的WikiName';
$_title_backuplist = '编辑历史';

///////////////////////////////////////
// Messages
$_msg_unfreeze       = '取消保护';
$_msg_preview        = '在预览后，按页面下方的按钮提交编辑。';
$_msg_preview_delete = '（页面无任何内容。如果提交编辑，此页面将被删除。）';
$_msg_collided       = '当您编辑此页面的同时，有其他人提交了对同一页面的编辑。<br />
以 + 为起始的行是新增的行。<br />
以 ! 为起始的行是内容发生变化的行。<br />
请处理以!或+起始的行，并再次提交编辑。<br />';

$_msg_collided_auto  = '当您编辑此页面的同时，有其他人提交了对同一页面的编辑。<br />
编辑冲突已解决，但仍然可能存在一些问题。<br />
请您在检查完成后，按[提交编辑]。<br />';

$_msg_invalidiwn     = '$1 不是有效的 $2。';
$_msg_invalidpass    = '密码错误。';
$_msg_notfound       = '未找到指定页面。';
$_msg_addline        = '新增的行显示为<span class="diff_added">此颜色</span>。';
$_msg_delline        = '删除的行显示为<span class="diff_removed">此颜色</span>。';
$_msg_goto           = '转到 $1。';
$_msg_andresult      = '在所有可能包含 $1 的 <strong>$3</strong> 个页面中，找到了符合条件的 <strong>$2</strong> 个页面。';
$_msg_orresult       = '在所有可能包含 $1 其中一个的 <strong>$3</strong> 个页面中，找到了符合条件的 <strong>$2</strong> 个页面。';
$_msg_notfoundresult = '找不到包含 $1 的页面。';
$_msg_symbol         = '符号';
$_msg_other          = '中文';
$_msg_help           = '查看文本格式语法';
$_msg_week           = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
$_msg_content_back_to_top = '<div class="jumpmenu"><a href="#navigator">&uarr;</a></div>';
$_msg_word           = '高亮显示这些关键字：';

///////////////////////////////////////
// Symbols
$_symbol_anchor   = '&dagger;';
$_symbol_noexists = '?';

///////////////////////////////////////
// Form buttons
$_btn_preview   = '预览';
$_btn_repreview = '重新预览';
$_btn_update    = '提交编辑';
$_btn_cancel    = '撤销';
$_btn_notchangetimestamp = '不更新时间戳';
$_btn_addtop    = '添加到页面顶部';
$_btn_template  = '模板页';
$_btn_load      = '打开';
$_btn_edit      = '编辑';
$_btn_delete    = '删除';

///////////////////////////////////////
// Authentication
$_title_cannotread = '无法浏览 $1';
$_msg_auth         = 'PukiWikiAuth';

///////////////////////////////////////
// Page name
$rule_page = 'FormattingRules';	// Formatting rules
$help_page = '帮助';		// Help

/////////////////////////////////////////////////
// 題名が未記入の場合の表記 (article)
$_no_subject = '无标题';

/////////////////////////////////////////////////
// 名前が未記入の場合の表記 (article, comment, pcomment)
$_no_name = '';

/////////////////////////////////////////////////
// Skin
/////////////////////////////////////////////////

$_LANG['skin']['add']       = '添加';
$_LANG['skin']['backup']    = '历史';
$_LANG['skin']['copy']      = '复制';
$_LANG['skin']['diff']      = '差异';
$_LANG['skin']['edit']      = '编辑';
$_LANG['skin']['filelist']  = '文件名列表';	// List of filenames
$_LANG['skin']['freeze']    = '保护';
$_LANG['skin']['help']      = '帮助';
$_LANG['skin']['list']      = '列表';	// List of pages
$_LANG['skin']['new']       = '新建';
$_LANG['skin']['rdf']       = '最近更新的RDF';	// RDF of RecentChanges
$_LANG['skin']['recent']    = '最近更新';	// RecentChanges
$_LANG['skin']['reload']    = '重新加载';
$_LANG['skin']['rename']    = '更改名称';	// Rename a page (and related)
$_LANG['skin']['rss']       = '最近更新的RSS';	// RSS of RecentChanges
$_LANG['skin']['rss10']     = & $_LANG['skin']['rss'];
$_LANG['skin']['rss20']     = & $_LANG['skin']['rss'];
$_LANG['skin']['search']    = '搜索';
$_LANG['skin']['top']       = '首页';	// Top page
$_LANG['skin']['unfreeze']  = '取消保护';
$_LANG['skin']['upload']    = '附件';	// Attach a file

///////////////////////////////////////
// Plug-in message
///////////////////////////////////////
// add.inc.php
$_title_add = '添加到 $1';
$_msg_add   = '如果要向页面中添加内容，请输入2个换行，再输入文本。';

///////////////////////////////////////
// article.inc.php
$_btn_name    = '名字';
$_btn_article = '提交编辑';
$_btn_subject = '标题: ';
$_msg_article_mail_sender = '编辑者: ';
$_msg_article_mail_page   = '编辑页: ';


///////////////////////////////////////
// attach.inc.php
$_attach_messages = array(
	'msg_uploaded' => '已上传到 $1',
	'msg_deleted'  => '已从 $1 中删除',
	'msg_freezed'  => '附件已保护。',
	'msg_unfreezed'=> '已取消对附件的保护。',
	'msg_renamed'  => '附件的名称已更改。',
	'msg_upload'   => '添加附件到 $1',
	'msg_info'     => '附件信息',
	'msg_confirm'  => '<p>删除 %s。</p>',
	'msg_list'     => '附件列表',
	'msg_listpage' => '$1 的附件列表',
	'msg_listall'  => '所有页面的附件列表',
	'msg_file'     => '附件',
	'msg_maxsize'  => '添加小于 %s 的文件作为附件。',
	'msg_count'    => ' <span class="small">%s个</span>',
	'msg_password' => '密码',
	'msg_adminpass'=> '管理员密码',
	'msg_delete'   => '删除此文件。',
	'msg_freeze'   => '保护此文件。',
	'msg_unfreeze' => '取消对此文件的保护。',
	'msg_isfreeze' => '已保护该文件。',
	'msg_rename'   => '重命名。',
	'msg_newname'  => '新名称',
	'msg_require'  => '(管理员密码为必填)',
	'msg_filesize' => '大小',
	'msg_date'     => '添加时间',
	'msg_dlcount'  => '下载次数',
	'msg_md5hash'  => 'MD5哈希值',
	'msg_page'     => '页',
	'msg_filename' => '文件名',
	'err_noparm'   => '无法上传或删除 $1',
	'err_exceed'   => '上传到 $1 的文件过大',
	'err_exists'   => '存在与 $1 相同的文件名',
	'err_notfound' => '在 $1 中找不到文件',
	'err_noexist'  => '没有附件。',
	'err_delete'   => '无法在 $1 中删除文件',
	'err_rename'   => '无法更改文件名',
	'err_password' => '密码不匹配。',
	'err_adminpass'=> '管理员密码不匹配。',
	'btn_upload'   => '上传',
	'btn_info'     => '详细信息',
	'btn_submit'   => '应用'
);

///////////////////////////////////////
// back.inc.php
$_msg_back_word = '返回';

///////////////////////////////////////
// backup.inc.php
$_title_backup_delete  = '删除 $1 的历史';
$_title_backupdiff     = '$1 的历史差异(No.$2)';
$_title_backupnowdiff  = '$1 的历史与当前的差异(No.$2)';
$_title_backupsource   = '$1 历史源(No.$2)';
$_title_backup         = '$1 的历史(No.$2)';
$_title_pagebackuplist = '$1 的历史列表';
$_title_backuplist     = '编辑历史';
$_msg_backup_deleted   = '已删除 $1 的历史。';
$_msg_backup_adminpass = '请输入用于删除的密码。';
$_msg_backuplist       = '编辑历史';
$_msg_nobackup         = '$1 无任何历史。';
$_msg_diff             = '差异';
$_msg_nowdiff          = '与当前的差异';
$_msg_source           = '源';
$_msg_backup           = '历史';
$_msg_view             = '显示 $1';
$_msg_deleted          = '$1 已删除。';

///////////////////////////////////////
// calendar_viewer.inc.php
$_err_calendar_viewer_param2 = '第二参数不正确';
$_msg_calendar_viewer_right  = '后%d个&gt;&gt;';
$_msg_calendar_viewer_left   = '&lt;&lt;前%d个';
$_msg_calendar_viewer_restrict = '$1 有浏览限制，无法被calendar_viewer引用';

///////////////////////////////////////
// calendar2.inc.php
$_calendar2_plugin_edit  = '[编辑此日记]';
$_calendar2_plugin_empty = '%s中无任何内容。';

///////////////////////////////////////
// comment.inc.php
$_btn_name    = '名字: ';
$_btn_comment = '插入评论';
$_msg_comment = '评论内容: ';
$_title_comment_collided = '在 $1 中发生了【编辑冲突】';
$_msg_comment_collided   = '当您编辑此页的同时，有其他人更新了同一页面。<br />
批注有可能被插入到了与原先不同的位置。<br />';

///////////////////////////////////////
// deleted.inc.php
$_deleted_plugin_title = '删除页面的列表';
$_deleted_plugin_title_withfilename = '删除页面文件的列表';

///////////////////////////////////////
// diff.inc.php
$_title_diff = '$1 的差异';
$_title_diff_delete  = '删除 $1 的差异';
$_msg_diff_deleted   = '已删除 $1 的差异。';
$_msg_diff_adminpass = '请输入用于删除的密码。';

///////////////////////////////////////
// filelist.inc.php (list.inc.php)
$_title_filelist = '页面文件列表';

///////////////////////////////////////
// freeze.inc.php
$_title_isfreezed = '$1 为保护状态';
$_title_freezed   = '已保护 $1';
$_title_freeze    = '保护 $1';
$_msg_freezing    = '请输入用于保护页面的密码。';
$_btn_freeze      = '保护';

///////////////////////////////////////
// insert.inc.php
$_btn_insert = '添加';

///////////////////////////////////////
// include.inc.php
$_msg_include_restrict = '$1 有浏览限制，无法进行include操作';

///////////////////////////////////////
// interwiki.inc.php
$_title_invalidiwn = '不是有效的 InterWikiName';

///////////////////////////////////////
// list.inc.php
$_title_list = '页面列表';

///////////////////////////////////////
// ls2.inc.php
$_ls2_err_nopages = '<p>在 \'$1\' 中不包含任何子页面。</p>';
$_ls2_msg_title   = '以\'$1\'为起始的页面列表';

///////////////////////////////////////
// memo.inc.php
$_btn_memo_update = '日志更新';

///////////////////////////////////////
// navi.inc.php
$_navi_prev = 'Prev';
$_navi_next = 'Next';
$_navi_up   = 'Up';
$_navi_home = 'Home';

///////////////////////////////////////
// newpage.inc.php
$_msg_newpage = '新建页面';

///////////////////////////////////////
// paint.inc.php
$_paint_messages = array(
	'field_name'    => '您的名字',
	'field_filename'=> '文件名',
	'field_comment' => '评论',
	'btn_submit'    => 'paint',
	'msg_max'       => '(小于 %d x %d)',
	'msg_title'     => 'Paint and Attach to $1',
	'msg_title_collided' => '在 $1 中发生了【编辑冲突】',
	'msg_collided'  => '当您编辑图像的同时，有其他人对同一页面进行了编辑。<br />
图像和评论可能被插入到了与原来不同的位置。<br />'
);

///////////////////////////////////////
// pcomment.inc.php
$_pcmt_messages = array(
	'btn_name'     => '您的名字: ',
	'btn_comment'  => '插入评论',
	'msg_comment'  => '评论内容: ',
	'msg_recent'   => '显示最近的%d个。',
	'msg_all'      => '引用评论页',
	'msg_none'     => '没有评论。',
	'title_collided' => '在 $1 中发生了【编辑冲突】',
	'msg_collided' => '您在编辑此页的同时，有其他人编辑了同一页。<br />
评论有可能被插入到了与原先不同的位置。<br />',
	'err_pagename' => '无法使用 [[%s]] 作为页面名称。 请指定正确的页面名称。',
);
$_msg_pcomment_restrict = '存在浏览限制，无法查看来自$1的评论。';

///////////////////////////////////////
// popular.inc.php
$_popular_plugin_frame       = '<h5>最受欢迎的%d个</h5><div>%s</div>';
$_popular_plugin_today_frame = '<h5>今天的%d个</h5><div>%s</div>';

///////////////////////////////////////
// recent.inc.php
$_recent_plugin_frame = '<h5>最近的%d个</h5>
<div>%s</div>';

///////////////////////////////////////
// rename.inc.php
$_rename_messages  = array(
	'err' => '<p>错误:%s</p>',
	'err_nomatch'    => '没有匹配的页面。',
	'err_notvalid'   => '重命名后的名称不是有效的页面名称。',
	'err_adminpass'  => '管理员密码不正确。',
	'err_notpage'    => '%s不是页面名称。',
	'err_norename'   => '无法重命名%s。',
	'err_already'    => '页面已存在。:%s',
	'err_already_below' => '以下文件已存在。',
	'msg_title'      => '重命名页面',
	'msg_page'       => '指定源页面',
	'msg_regex'      => '使用正则表达式替换',
	'msg_related'    => '相关页面',
	'msg_do_related' => '重命名相关页面',
	'msg_rename'     => '更改%s的名称。',
	'msg_oldname'    => '当前的名称',
	'msg_newname'    => '新名称',
	'msg_adminpass'  => '管理员密码',
	'msg_arrow'      => '→',
	'msg_exist_none' => '不处理该页面',
	'msg_exist_overwrite' => '覆盖到该文件',
	'msg_confirm'    => '重命名下列文件。',
	'msg_result'     => '已覆盖到以下文件。',
	'btn_submit'     => '应用',
	'btn_next'       => '下一步'
);

///////////////////////////////////////
// search.inc.php
$_title_search  = '搜索';
$_title_result  = '$1 的搜索结果';
$_msg_searching = '在所有页面中进行搜索。不对大小写进行区分。';
$_btn_search    = '搜索';
$_btn_and       = 'AND条件搜索';
$_btn_or        = 'OR条件搜索';
$_search_pages  = '搜索从 $1 开始的页面';
$_search_all    = '搜索所有页面';

///////////////////////////////////////
// source.inc.php
$_source_messages = array(
	'msg_title'    => '$1的源码',
	'msg_notfound' => '$1未找到',
	'err_notfound' => '无法显示页面源码。'
);

///////////////////////////////////////
// template.inc.php
$_msg_template_start   = '起始行:<br />';
$_msg_template_end     = '终止行:<br />';
$_msg_template_page    = '$1/复制';
$_msg_template_refer   = '页面名称:';
$_msg_template_force   = '编辑现有的页面名称';
$_err_template_already = '$1 已存在。';
$_err_template_invalid = '$1 不是有效的页面名称。';
$_btn_template_create  = '创建';
$_title_template       = '从 $1 创建模板';

///////////////////////////////////////
// tracker.inc.php
$_tracker_messages = array(
	'msg_list'   => '$1 的项目列表',
	'msg_back'   => '<p>$1</p>',
	'msg_limit'  => '找到$1个结果，显示前$2个结果。',
	'btn_page'   => '页面名称',
	'btn_name'   => '页面名称',
	'btn_real'   => '页面名称',
	'btn_submit' => '添加',
	'btn_date'   => '日期',
	'btn_refer'  => '浏览',
	'btn_base'   => '基础',
	'btn_update' => '修改时间',
	'btn_past'   => '耗时',
);

///////////////////////////////////////
// unfreeze.inc.php
$_title_isunfreezed = '$1 未保护';
$_title_unfreezed   = '已取消对 $1 的保护';
$_title_unfreeze    = '取消对 $1 的保护';
$_msg_unfreezing    = '请输入用于取消保护的密码。';
$_btn_unfreeze      = '取消保护';

///////////////////////////////////////
// versionlist.inc.php
$_title_versionlist = '配置文件的版本列表';

///////////////////////////////////////
// vote.inc.php
$_vote_plugin_choice = '选项';
$_vote_plugin_votes  = '投票';

///////////////////////////////////////
// yetlist.inc.php
$_title_yetlist = '未创建的页面列表';
$_err_notexist  = '未创建的页面不存在。';
?>
