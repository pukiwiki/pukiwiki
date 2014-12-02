<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: ja.lng.php,v 1.15 2007/02/11 05:53:29 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki message file (japanese)

// ※このファイルの文字コードは、エンコーディングの設定と一致
//   している必要があります

// Encoding hint
$_LANG['encode_hint']['ja'] = 'ぷ';

///////////////////////////////////////
// Page titles
$_title_cannotedit = '$1 は編集できません';
$_title_edit       = '$1 の編集';
$_title_preview    = '$1 のプレビュー';
$_title_collided   = '$1 で【更新の衝突】が起きました';
$_title_updated    = '$1 を更新しました';
$_title_deleted    = '$1 を削除しました';
$_title_help       = 'ヘルプ';
$_title_invalidwn  = '有効なWikiNameではありません';
$_title_backuplist = 'バックアップ一覧';

///////////////////////////////////////
// Messages
$_msg_unfreeze       = '凍結解除';
$_msg_preview        = '以下のプレビューを確認して、よければページ下部のボタンで更新してください。';
$_msg_preview_delete = '（ページの内容は空です。更新するとこのページは削除されます。）';
$_msg_collided       = 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
今回追加した行は +で始まっています。<br />
!で始まる行が変更された可能性があります。<br />
!や+で始まる行を修正して再度ページの更新を行ってください。<br />';

$_msg_collided_auto  = 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
自動で衝突を解消しましたが、問題がある可能性があります。<br />
確認後、[ページの更新]を押してください。<br />';

$_msg_invalidiwn     = '$1 は有効な $2 ではありません。';
$_msg_invalidpass    = 'パスワードが間違っています。';
$_msg_notfound       = '指定されたページは見つかりませんでした。';
$_msg_addline        = '追加された行は<span class="diff_added">この色</span>です。';
$_msg_delline        = '削除された行は<span class="diff_removed">この色</span>です。';
$_msg_goto           = '$1 へ行く。';
$_msg_andresult      = '$1 のすべてを含むページは <strong>$3</strong> ページ中、 <strong>$2</strong> ページ見つかりました。';
$_msg_orresult       = '$1 のいずれかを含むページは <strong>$3</strong> ページ中、 <strong>$2</strong> ページ見つかりました。';
$_msg_notfoundresult = '$1 を含むページは見つかりませんでした。';
$_msg_symbol         = '記号';
$_msg_other          = '日本語';
$_msg_help           = 'テキスト整形のルールを表示する';
$_msg_week           = array('日','月','火','水','木','金','土');
$_msg_content_back_to_top = '<div class="jumpmenu"><a href="#navigator">&uarr;</a></div>';
$_msg_word           = 'これらのキーワードがハイライトされています：';

///////////////////////////////////////
// Symbols
$_symbol_anchor   = '&dagger;';
$_symbol_noexists = '?';

///////////////////////////////////////
// Form buttons
$_btn_preview   = 'プレビュー';
$_btn_repreview = '再度プレビュー';
$_btn_update    = 'ページの更新';
$_btn_cancel    = 'キャンセル';
$_btn_notchangetimestamp = 'タイムスタンプを変更しない';
$_btn_addtop    = 'ページの上に追加';
$_btn_template  = '雛形とするページ';
$_btn_load      = '読込';
$_btn_edit      = '編集';
$_btn_delete    = '削除';

///////////////////////////////////////
// Authentication
$_title_cannotread = '$1 は閲覧できません';
$_msg_auth         = 'PukiWikiAuth';

///////////////////////////////////////
// Page name
$rule_page = 'FormattingRules';	// Formatting rules
$help_page = 'Help';		// Help

/////////////////////////////////////////////////
// 題名が未記入の場合の表記 (article)
$_no_subject = '無題';

/////////////////////////////////////////////////
// 名前が未記入の場合の表記 (article, comment, pcomment)
$_no_name = '';

/////////////////////////////////////////////////
// Skin
/////////////////////////////////////////////////

$_LANG['skin']['add']       = '追加';
$_LANG['skin']['backup']    = 'バックアップ';
$_LANG['skin']['copy']      = '複製';
$_LANG['skin']['diff']      = '差分';
$_LANG['skin']['edit']      = '編集';
$_LANG['skin']['filelist']  = 'ファイル名一覧';	// List of filenames
$_LANG['skin']['freeze']    = '凍結';
$_LANG['skin']['help']      = 'ヘルプ';
$_LANG['skin']['list']      = '一覧';	// List of pages
$_LANG['skin']['new']       = '新規';
$_LANG['skin']['rdf']       = '最終更新のRDF';	// RDF of RecentChanges
$_LANG['skin']['recent']    = '最終更新';	// RecentChanges
$_LANG['skin']['reload']    = 'リロード';
$_LANG['skin']['rename']    = '名前変更';	// Rename a page (and related)
$_LANG['skin']['rss']       = '最終更新のRSS';	// RSS of RecentChanges
$_LANG['skin']['rss10']     = & $_LANG['skin']['rss'];
$_LANG['skin']['rss20']     = & $_LANG['skin']['rss'];
$_LANG['skin']['search']    = '単語検索';
$_LANG['skin']['top']       = 'トップ';	// Top page
$_LANG['skin']['unfreeze']  = '凍結解除';
$_LANG['skin']['upload']    = '添付';	// Attach a file

///////////////////////////////////////
// Plug-in message
///////////////////////////////////////
// add.inc.php
$_title_add = '$1 への追加';
$_msg_add   = 'ページへの追加は、現在のページ内容に改行が二つと入力内容が追加されます。';

///////////////////////////////////////
// article.inc.php
$_btn_name    = 'お名前';
$_btn_article = '記事の投稿';
$_btn_subject = '題名: ';
$_msg_article_mail_sender = '投稿者: ';
$_msg_article_mail_page   = '投稿先: ';


///////////////////////////////////////
// attach.inc.php
$_attach_messages = array(
	'msg_uploaded' => '$1 にアップロードしました',
	'msg_deleted'  => '$1 からファイルを削除しました',
	'msg_freezed'  => '添付ファイルを凍結しました。',
	'msg_unfreezed'=> '添付ファイルを凍結解除しました。',
	'msg_renamed'  => '添付ファイルの名前を変更しました。',
	'msg_upload'   => '$1 への添付',
	'msg_info'     => '添付ファイルの情報',
	'msg_confirm'  => '<p>%s を削除します。</p>',
	'msg_list'     => '添付ファイル一覧',
	'msg_listpage' => '$1 の添付ファイル一覧',
	'msg_listall'  => '全ページの添付ファイル一覧',
	'msg_file'     => '添付ファイル',
	'msg_maxsize'  => 'アップロード可能最大ファイルサイズは %s です。',
	'msg_count'    => ' <span class="small">%s件</span>',
	'msg_password' => 'パスワード',
	'msg_adminpass'=> '管理者パスワード',
	'msg_delete'   => 'このファイルを削除します。',
	'msg_freeze'   => 'このファイルを凍結します。',
	'msg_unfreeze' => 'このファイルを凍結解除します。',
	'msg_isfreeze' => 'このファイルは凍結されています。',
	'msg_rename'   => '名前を変更します。',
	'msg_newname'  => '新しい名前',
	'msg_require'  => '(管理者パスワードが必要です)',
	'msg_filesize' => 'サイズ',
	'msg_date'     => '登録日時',
	'msg_dlcount'  => 'アクセス数',
	'msg_md5hash'  => 'MD5ハッシュ値',
	'msg_page'     => 'ページ',
	'msg_filename' => '格納ファイル名',
	'err_noparm'   => '$1 へはアップロード・削除はできません',
	'err_exceed'   => '$1 へのファイルサイズが大きすぎます',
	'err_exists'   => '$1 に同じファイル名が存在します',
	'err_notfound' => '$1 にそのファイルは見つかりません',
	'err_noexist'  => '添付ファイルがありません。',
	'err_delete'   => '$1 からファイルを削除できませんでした',
	'err_rename'   => 'ファイル名を変更できませんでした',
	'err_password' => 'パスワードが一致しません。',
	'err_adminpass'=> '管理者パスワードが一致しません。',
	'btn_upload'   => 'アップロード',
	'btn_info'     => '詳細',
	'btn_submit'   => '実行'
);

///////////////////////////////////////
// back.inc.php
$_msg_back_word = '戻る';

///////////////////////////////////////
// backup.inc.php
$_title_backup_delete  = '$1 のバックアップを削除';
$_title_backupdiff     = '$1 のバックアップ差分(No.$2)';
$_title_backupnowdiff  = '$1 のバックアップの現在との差分(No.$2)';
$_title_backupsource   = '$1 のバックアップソース(No.$2)';
$_title_backup         = '$1 のバックアップ(No.$2)';
$_title_pagebackuplist = '$1 のバックアップ一覧';
$_title_backuplist     = 'バックアップ一覧';
$_msg_backup_deleted   = '$1 のバックアップを削除しました。';
$_msg_backup_adminpass = '削除用のパスワードを入力してください。';
$_msg_backuplist       = 'バックアップ一覧';
$_msg_nobackup         = '$1 のバックアップはありません。';
$_msg_diff             = '差分';
$_msg_nowdiff          = '現在との差分';
$_msg_source           = 'ソース';
$_msg_backup           = 'バックアップ';
$_msg_view             = '$1 を表示';
$_msg_deleted          = '$1 は削除されています。';

///////////////////////////////////////
// calendar_viewer.inc.php
$_err_calendar_viewer_param2 = '第2引数が変だよ';
$_msg_calendar_viewer_right  = '次の%d件&gt;&gt;';
$_msg_calendar_viewer_left   = '&lt;&lt;前の%d件';
$_msg_calendar_viewer_restrict = '$1 は閲覧制限がかかっているためcalendar_viewerによる参照はできません';

///////////////////////////////////////
// calendar2.inc.php
$_calendar2_plugin_edit  = '[この日記を編集]';
$_calendar2_plugin_empty = '%sは空です。';

///////////////////////////////////////
// comment.inc.php
$_btn_name    = 'お名前: ';
$_btn_comment = 'コメントの挿入';
$_msg_comment = 'コメント: ';
$_title_comment_collided = '$1 で【更新の衝突】が起きました';
$_msg_comment_collided   = 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
コメントを追加しましたが、違う位置に挿入されているかもしれません。<br />';

///////////////////////////////////////
// deleted.inc.php
$_deleted_plugin_title = '削除ページの一覧';
$_deleted_plugin_title_withfilename = '削除ページファイルの一覧';

///////////////////////////////////////
// diff.inc.php
$_title_diff = '$1 の変更点';
$_title_diff_delete  = '$1 の差分を削除';
$_msg_diff_deleted   = '$1 の差分を削除しました。';
$_msg_diff_adminpass = '削除用のパスワードを入力してください。';

///////////////////////////////////////
// filelist.inc.php (list.inc.php)
$_title_filelist = 'ページファイルの一覧';

///////////////////////////////////////
// freeze.inc.php
$_title_isfreezed = '$1 はすでに凍結されています';
$_title_freezed   = '$1 を凍結しました';
$_title_freeze    = '$1 の凍結';
$_msg_freezing    = '凍結用のパスワードを入力してください。';
$_btn_freeze      = '凍結';

///////////////////////////////////////
// insert.inc.php
$_btn_insert = '追加';

///////////////////////////////////////
// include.inc.php
$_msg_include_restrict = '$1 は閲覧制限がかかっているためincludeできません';

///////////////////////////////////////
// interwiki.inc.php
$_title_invalidiwn = '有効なInterWikiNameではありません';

///////////////////////////////////////
// list.inc.php
$_title_list = 'ページの一覧';

///////////////////////////////////////
// ls2.inc.php
$_ls2_err_nopages = '<p>\'$1\' には、下位層のページがありません。</p>';
$_ls2_msg_title   = '\'$1\'で始まるページの一覧';

///////////////////////////////////////
// memo.inc.php
$_btn_memo_update = 'メモ更新';

///////////////////////////////////////
// navi.inc.php
$_navi_prev = 'Prev';
$_navi_next = 'Next';
$_navi_up   = 'Up';
$_navi_home = 'Home';

///////////////////////////////////////
// newpage.inc.php
$_msg_newpage = 'ページ新規作成';

///////////////////////////////////////
// paint.inc.php
$_paint_messages = array(
	'field_name'    => 'お名前',
	'field_filename'=> 'ファイル名',
	'field_comment' => 'コメント',
	'btn_submit'    => 'paint',
	'msg_max'       => '(最大 %d x %d)',
	'msg_title'     => 'Paint and Attach to $1',
	'msg_title_collided' => '$1 で【更新の衝突】が起きました',
	'msg_collided'  => 'あなたが画像を編集している間に、他の人が同じページを更新してしまったようです。<br />
画像とコメントを追加しましたが、違う位置に挿入されているかもしれません。<br />'
);

///////////////////////////////////////
// pcomment.inc.php
$_pcmt_messages = array(
	'btn_name'     => 'お名前: ',
	'btn_comment'  => 'コメントの挿入',
	'msg_comment'  => 'コメント: ',
	'msg_recent'   => '最新の%d件を表示しています。',
	'msg_all'      => 'コメントページを参照',
	'msg_none'     => 'コメントはありません。',
	'title_collided' => '$1 で【更新の衝突】が起きました',
	'msg_collided' => 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
コメントを追加しましたが、違う位置に挿入されているかもしれません。<br />',
	'err_pagename' => 'ページ名 [[%s]] は使用できません。 正しいページ名を指定してください。',
);
$_msg_pcomment_restrict = '閲覧制限がかかっているため、$1からはコメントを読みこむことができません。';

///////////////////////////////////////
// popular.inc.php
$_popular_plugin_frame       = '<h5>人気の%d件</h5><div>%s</div>';
$_popular_plugin_today_frame = '<h5>今日の%d件</h5><div>%s</div>';

///////////////////////////////////////
// recent.inc.php
$_recent_plugin_frame = '<h5>最新の%d件</h5>
<div>%s</div>';

///////////////////////////////////////
// rename.inc.php
$_rename_messages  = array(
	'err' => '<p>エラー:%s</p>',
	'err_nomatch'    => 'マッチするページがありません。',
	'err_notvalid'   => 'リネーム後のページ名が正しくありません。',
	'err_adminpass'  => '管理者パスワードが正しくありません。',
	'err_notpage'    => '%sはページ名ではありません。',
	'err_norename'   => '%sをリネームすることはできません。',
	'err_already'    => 'ページがすでに存在します。:%s',
	'err_already_below' => '以下のファイルがすでに存在します。',
	'msg_title'      => 'ページ名の変更',
	'msg_page'       => '変更元ページを指定',
	'msg_regex'      => '正規表現で置換',
	'msg_related'    => '関連ページ',
	'msg_do_related' => '関連ページもリネームする',
	'msg_rename'     => '%sの名前を変更します。',
	'msg_oldname'    => '現在の名前',
	'msg_newname'    => '新しい名前',
	'msg_adminpass'  => '管理者パスワード',
	'msg_arrow'      => '→',
	'msg_exist_none' => 'そのページを処理しない',
	'msg_exist_overwrite' => 'そのファイルを上書きする',
	'msg_confirm'    => '以下のファイルをリネームします。',
	'msg_result'     => '以下のファイルを上書きしました。',
	'btn_submit'     => '実行',
	'btn_next'       => '次へ'
);

///////////////////////////////////////
// search.inc.php
$_title_search  = '単語検索';
$_title_result  = '$1 の検索結果';
$_msg_searching = '全てのページから単語を検索します。大文字小文字の区別はありません。';
$_btn_search    = '検索';
$_btn_and       = 'AND検索';
$_btn_or        = 'OR検索';
$_search_pages  = '$1 から始まるページを検索';
$_search_all    = '全てのページを検索';

///////////////////////////////////////
// source.inc.php
$_source_messages = array(
	'msg_title'    => '$1のソース',
	'msg_notfound' => '$1が見つかりません',
	'err_notfound' => 'ページのソースを表示できません。'
);

///////////////////////////////////////
// template.inc.php
$_msg_template_start   = '開始行:<br />';
$_msg_template_end     = '終了行:<br />';
$_msg_template_page    = '$1/複製';
$_msg_template_refer   = 'ページ名:';
$_msg_template_force   = '既存のページ名で編集する';
$_err_template_already = '$1 はすでに存在します。';
$_err_template_invalid = '$1 は有効なページ名ではありません。';
$_btn_template_create  = '作成';
$_title_template       = '$1 をテンプレートにして作成';

///////////////////////////////////////
// tracker.inc.php
$_tracker_messages = array(
	'msg_list'   => '$1 の項目一覧',
	'msg_back'   => '<p>$1</p>',
	'msg_limit'  => '全$1件中、上位$2件を表示しています。',
	'btn_page'   => 'ページ名',
	'btn_name'   => 'ページ名',
	'btn_real'   => 'ページ名',
	'btn_submit' => '追加',
	'btn_date'   => '日付',
	'btn_refer'  => '参照',
	'btn_base'   => '基底',
	'btn_update' => '更新日時',
	'btn_past'   => '経過',
);

///////////////////////////////////////
// unfreeze.inc.php
$_title_isunfreezed = '$1 は凍結されていません';
$_title_unfreezed   = '$1 の凍結を解除しました';
$_title_unfreeze    = '$1 の凍結解除';
$_msg_unfreezing    = '凍結解除用のパスワードを入力してください。';
$_btn_unfreeze      = '凍結解除';

///////////////////////////////////////
// versionlist.inc.php
$_title_versionlist = '構成ファイルのバージョン一覧';

///////////////////////////////////////
// vote.inc.php
$_vote_plugin_choice = '選択肢';
$_vote_plugin_votes  = '投票';

///////////////////////////////////////
// yetlist.inc.php
$_title_yetlist = '未作成のページ一覧';
$_err_notexist  = '未作成のページはありません。';
?>
