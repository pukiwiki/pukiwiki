NAME

    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築PHPスクリプト

        PukiWiki 1.4.4
        Copyright (C) 2001-2004 PukiWiki Developers Team
        License is GNU GPL
        Based on "PukiWiki" 1.3 by yu-ji

SYNOPSIS

        http://pukiwiki.org/
	http://pukiwiki.sourceforge.jp/dev/
	http://sourceforge.jp/projects/pukiwiki/

DESCRIPTION

        PukiWikiは参加者が自由にページを追加・削除・編集できる
        Webページ群を作るPHPスクリプトです。
        Webで動作する掲示板とちょっと似ていますが、
        Web掲示板が単にメッセージを追加するだけなのに対して、
        PukiWikiは、Webページ全体を自由に変更することができます。

        PukiWikiは、結城浩さんのYukiWikiの仕様を参考にして独自に作られました。
        1.3まではyu-jiさんが作成し、1.3.1b以降はPukiWiki Developers Teamによって
        開発が続けられています。

        PukiWikiはPHPで書かれたPHPスクリプトとして実現されていますので、
        PHPが動作するWebサーバならば比較的容易に設置できます。

        PukiWikiはフリーソフトです。 ご自由にお使いください。
        最新版は、 http://pukiwiki.org/ から入手できます。

設置方法

    以下は一例です。Webサーバーへのシェルアクセスが可能であれば、
    アーカイブをそのままサーバーに転送し、サーバー上で解凍する
    だけでも動作の確認ができるはずです。

    1.  アーカイブを解きます。

    2.  必要に応じて設定ファイル(*.ini.php)の内容を確認します。
        1.11  から設定ファイルが別ファイルのpukiwiki.ini.phpになりました。
        1.4   から設定ファイルが分割されました。
        1.4.4 から携帯電話およびPDA向けの設定ファイルが一つに集約されました。
	      (旧 i_mode.ini.php, jphone.ini.php の設定を keitai.ini.php に集約)

        * 共通設定
	  全体               : pukiwiki.ini.php
          ユーザ定義         : rules.ini.php

        * エージェント別設定
          携帯電話およびPDA  : keitai.ini.php
          その他             : default.ini.php

    3.  アーカイブの内容をサーバに転送します。
        ファイルの転送モードについては次項を参照してください。

    4.  サーバ上のファイルおよびディレクトリのパーミッションを確認します。

        ディレクトリ   パーミッション
        attach         777	添付ファイル格納ディレクトリ
        backup         777	バックアップファイル格納ディレクトリ
        cache          777	キャッシュファイル格納ディレクトリ
        counter        777	カウンタファイル格納ディレクトリ
        diff           777	差分ファイル格納ディレクトリ
        image          755	画像ファイル
        image/face     755 	(画像ファイル)フェイスマーク  
        plugin         755	プラグイン
        skin           755	スキン、CSS、JavaScirptファイル
        trackback      777	TrackBackファイル格納ディレクトリ
        wiki           777	データの格納ディレクトリ

        ファイル       パーミッション 転送モード
        *.php          644            ASCII
        *.lng          644            ASCII
        cache/*        666            ASCII
        face/*         644            BINARY
        image/*        644            BINARY
        plugin/*       644            ASCII
        skin/*         644            ASCII
        wiki/*         666            ASCII

    5.  index.php あるいは pukiwiki.php にブラウザからアクセスします。
        必要に応じて、さらに設定やデザインを調整して下さい。

データのバックアップ方法

        データファイルディレクトリ以下をバックアップします。
        (デフォルトディレクトリ名は wiki)

        必要に応じて他のディレクトリの内容をバックアップします。
        (デフォルトディレクトリ名は attach,backup,counter,cache,diff,trackback)


新しいページの作り方

    「新規」リンクから新しくページを作成する以外に、ページの中に書いた語句から
    そのページ名のページを作成することができます。

    1.  まず、適当なページ（例えばFrontPage）を選び、
        ページの上下にある「編集」リンクをたどります。

    2.  するとテキスト入力ができる状態になるので、 そこにNewPageのような単語
        （大文字小文字混在している英文字列）や、 [[新しいページ名]] の様に
	二重のブラケットで囲んだ語句を書いて「保存」します。

    3.  保存すると、FrontPageのページが書き換わり、
        あなたが書いたNewPageという単語や「新しいページ名」という語句の
	の後ろに '?' という小さなリンクが表示されます。 このリンク
        はそのページがまだ存在しないことを示す印です。

    4.  その '?' をクリックすると新しいページができますので、
        あなたの好きな文章をその新しいページに書いて保存します。

    5.  新しいページができるとFrontPageのそれらの語句から '?' は消えて、
        普通のハイパーリンクとなります。

テキスト整形のルール

        [[整形ルール]] ページを参照してください。

InterWiki

        1.11 からInterWikiが実装されました。

        InterWiki とは、Wikiサーバーをつなげる機能です。
        最初はそうだったんで InterWiki という名前なのだそうですが、
        今は、Wikiサーバーだけではなくて、いろんなサーバーを引けます。
        なかなか便利です。そうなると InterWiki という名前はあまり機能を
        表していないことになります。
        この機能は Tiki からほぼ完全に移植しています。

        詳細は [[InterWikiテクニカル]] ページを参照してください。

RDF/RSSの出力

        1.2.1から、RecentChangesのRDF/RSSを出力できるようになりました。

        * RSS 0.91 の出力方法の例
            http://pukiwiki/index.php?cmd=rss

        * RSS 1.0 の出力方法の例
            http://pukiwiki.org/index.php?cmd=rss10


PukiWiki/1.3.xとの非互換点

    1.  [[WikiName]]とWikiNameは同じページを指します。
    2.  定義リストの書式が違います。 :term:description -> :term|description
    3.  リストや引用文は、下位レベルのリストや引用文を包含することができます。
        (1.3.xでは、リストは同種のみ、引用内には引用しか包含できませんでした。)

PukiWiki/1.4.2との非互換点

    1.  trackback/ディレクトリ配下に作成されるファイルの名前の生成規則が変わりました。
        trackback/referer機能をお使いで、1.4.2から1.4.3へ移行される場合は、PukiWiki.devサイト
        (http://pukiwiki.sourceforge.jp/dev/)の、開発日記/2004-03-18ページを参照して作業を行ってください。

PukiWiki/1.4.3との非互換点

    1. 主要なライブラリなどが全て lib/ ディレクトリ以下に移動しました。pukiwiki.php も
       lib/pukiwiki.php をインクルードする小さなファイルになっています。

更新履歴
    *   2004-04-04 1.4.3 by PukiWiki Developers Team
        BugTrack/493 リンク元の文字サイズを指定すると文字サイズを変えると＃relatedに反応しない 
            [[cvs:make_link.php]](v1.4:r1.64)
        BugTrack/494 LANG=enの時のtemplateプラグイン
            [[cvs:en.lng]](v1.4:r1.25)
        $scriptのチェックにis_urlを使用
            [[cvs:func.php]](v1.4:r1.55)
            [[cvs:init.php]](v1.4:r1.69)
        BugTrack/496 ChaSenを使った場合ペイジの読みが正しく得られない
            [[cvs:func.php]](v1.4:r1.56)
        質問箱/344 入力時の改行の扱いについて再び
        質問箱/280 改行がそのまま反映されないのか?
        その他 処理の見直し
             [cvs:convert_html.php]](v1.4:r1.58)
        $line_break追加 改行を反映する(改行を<br />に置換する)とき1
             [[cvs:pukiwiki.ini.php]](v1.4:r1.52)
        BugTrack/491 RecentDeleteがほしい
             [[cvs:file.php]](v1.4:r1.38)
             [[cvs:pukiwiki.ini.php]](v1.4:r1.53)
        BugTrack/498 定義リストで説明文を空にするとブロックが終了する
             [[cvs:convert_html.php]](v1.4:r1.58)
        BugTrack/462 IIS 環境下における Basic認証が動かない
             [[cvs:auth.php]](v1.4:r1.3)
        質問箱/343 readme.txtに機種依存文字?
             [[cvs:readme.txt]](v1.4:r1.15)
        質問箱/346 counterプラグインでカウンタ表示対象のyesterdayについて
             [[cvs:plugin/counter.inc.php]](v1.4:r1.12)
        $_SERVER配列のチェックを修正(Warning対策)
             [[cvs:init.php]](v1.4:r1.70)
        使用されていない部分をコメントアウト(Warning対策)
             [[cvs:plugin/bugtrack.inc.php]](v1.4:r1.15)
        引数をチェック(Warinig対策)
             [[cvs:plugin/md5.inc.php]](v1.4:r1.3)
        rss取得に失敗したときのエラー処理を追加(Warning対策)
             [[cvs:plugin/showrss.inc.php]](v1.4:r1.11)
        壊れたレコードをスキップ(Warning対策)
             [[cvs:plugin/tb.inc.php]](v1.4:r1.6)
        エラーチェック強化
             [[cvs:plugin/tracker.inc.php]](v1.4:r1.20)
        BugTrack/499 #contents の直後にリスト要素を追加すると表示されない
             [[cvs:convert_html.php]](v1.4:r1.59)
        TableCellが空のとき<td>~</td>タグが省略されてしまう
             [[cvs:convert_html.php]](v1.4:r1.60)
        Content-Typeヘッダでcharsetを出力しないように(r1.37)
        ファイルダウンロード処理にmb_http_output('pass')を追加(r1.38)
             [[cvs:attach.inc.php]](v1.4:r1.38)
        join('',...)は不要
             [[cvs:plugin/tb.inc.php]](v1.4:r1.7)
        エラーチェック強化
             [[cvs:plugin/tracker.inc.php]](v1.4:r1.20)
        rss取得に失敗したときのエラー処理を追加
             [[cvs:plugin/showrss.inc.php]](v1.4:r1.11)
        引数をチェック
             [[cvs:plugin/md5.inc.php]](v1.4:r1.3)
        mode=submitが指定されていないときは何もしないように
             [[cvs:plugin/bugtrack.inc.php]](v1.4:r1.15)
        BugTrack/514 オートリンクされないページがある (BugTrack/502の修正ミス)
             [[cvs:func.php]](v1.4:r1.58)
        BugTrack/518 refererで同一URLが別のものとして記録される
             [[cvs:trackback.php]](v1.4.r1.15)
             [[cvs:plugin/tb.inc.php]](v1.4:r1.8)
        BugTrack/519 数字名のページがRecentChangesで正しく表示されない。
             [[cvs:file.php]](v1.4:r1.39)
        BugTrack/523 「PukiWiki/1.4/ちょっと便利に/単語検索の結果表示を拡張」を導入すると、「&」を検索した場合に表示が乱れる
             [[cvs:html.php]](v1.4:r1.97)
        BugTrack/525 キャッシュ更新を実行したときにスキンでnoticeが表示されるのを防ぐ
             [[cvs:plugin/links.inc.php]](v1.4:r1.18)
        BugTrack/526 rss10.inc.phpでtypo
             [[cvs:plugin/rss10.inc.php]](v1.4:r1.10)
        BugTrack/538 clearプラグインの同梱
             [[cvs:plugin/clear.inc.php]](v1.4:r1.1)
             [[cvs:skin/default.en.css]](v1.4:r1.28)
             [[cvs:skin/default.ja.css]](v1.4:r1.29)
        BugTrack/539 ヘルプの修正(リンク・エイリアス)
             [[cvs:wiki/C0B0B7C1A5EBA1BCA5EB.txt]](v1.4:r1.7)
        BugTrack/541 source.inc.phpで認証チェック漏れ
             [[cvs:plugin/source.inc.php]](v1.4:r1.11)
        BugTrack/543 backup.inc.phpでタグの不整合
             [[cvs:plugin/backup.inc.php]](v1.4.r1.10)
        BugTrack/549 refプラグインでnoicon、noimgオプションのときに文字列の後ろに空白が入る
             [[cvs:plugin/ref.inc.php]](v1.4:r1.21)
        BugTrack/521 引用文の多重化で、段落の~を省略した場合にXHTML 1.1 not validになる
        BugTrack/524 定義リストで一つの定義に対し複数の説明がつかない
        BugTrack/545 ある条件下でblockquote内にpで囲わずテキスト直書き
             [[cvs:convert_html.php]](v1.4:r1.61)
        BugTrack/555 encode_hintの判定ミス
        BugTrack/536 i-mode対応が正常に働いていない?
             [[cvs:init.php]](v1.4:r1.71) 
        BugTrack/552 backup.inc.phpでタグの不整合によるパースエラー
             [[cvs:plugin/backup.inc.php]](v1.4:r1.11)
        BugTrack/530 TouchGraph Plugin
             [[cvs:plugin/touchgraph.inc.php]](v1.4:r1.3)
        BugTrack/534 refプラグインで参照ページのBracketNameにカンマが含まれているとファイルを参照できない。
             [[cvs:convert_html.php]](v1.4:r1.62)
             [[cvs:func.php]](v1.4:r1.59)
             [[cvs:plugin.php]](v1.4:r1.10)
        BugTrack/539 ヘルプの修正(リンク・エイリアス)
             [[cvs:wiki/C0B0B7C1A5EBA1BCA5EB.txt]](v1.4:r1.7)
        BugTrack/540 trackbackのURLでtrackback listが表示されずFrontPageが表示される
             [[cvs:trackback.php]](v1.4:r1.16)
        BugTrack/558 trackback pingをGETで送ってくるサイトに対応を
             [[cvs:plugin/rss10.inc.php]](v1.4:r1.11)
             [[cvs:plugin/tb.inc.php]](v1.4:r1.9)
             [[cvs:init.php]](v1.4:r1.72)
             [[cvs:trackback.php]](v1.4:r1.16)
        BugTrack/559 $whatsnewに日本語ページ名を指定できない
             [[cvs:plugin/rss.inc.php]](v1.4:r1.6)
             [[cvs:plugin/rss10.inc.php]](v1.4:r1.11)
        ファイルロック処理を調整
             [[cvs:file.php]](v1.4:r1.40)
             [[cvs:plugin/attach.inc.php]](v1.4:r1.39)
             [[cvs:plugin/counter.inc.php]](v1.4:r1.13)
             [[cvs:plugin/online.inc.php]](v1.4:r1.8)
        Notice: Undefined offset: 1 対策
             [[cvs:html.php]](v1.4:r1.98)
        バックアップの際、ページに含まれるバックアップの区切り文字を無害化するように
             [[cvs:backup.php]](v1.4:r1.15)
        不要なset_time_limit()を削除
             [[cvs:plugin/rename.inc.php]](v1.4:r1.10)
        新規プラグイン  $line_breakをページ途中で切り替える
             [[cvs:plugin/setlinebreak.inc.php]](v1.4:r1.1)
        BugTrack/561 フォームでキーボードからタブコードを入力できないので…
             [[cvs:rules.ini.php]](v1.4:r1.2)
             [[cvs:wiki/C0B0B7C1A5EBA1BCA5EB.txt]](v1.4:r1.8)
        BugTrack/562 ChaSen/KAKASI無しでもある程度日本語ページ一覧分類を可能に
             [[cvs:file.php]](v1.4:r1.41)
             [[cvs:pukiwiki.ini.php]](v1.4:r1.54)
             [[cvs:wiki/3A636F6E6669672F5061676552656164696E67.txt]](v1.4:r1.1)
             [[cvs:wiki/3A636F6E6669672F5061676552656164696E672F64696374.txt]](v1.4:r1.1)
        BugTrack/563 renameプラグインで「./hogehoge」というようなページが作成できてしまう
             [[cvs:plugin/rename.inc.php]](v1.4:r1.11)
        BugTrack/564 versionlistプラグインでlngファイルが表示されない
             [[cvs:plugin/versionlist.inc.php]](v1.4:r1.7)
             [[cvs:plugin/versionlist.inc.php]](v1.3:r1.2.2.1)
        csv_explode, csv_implode関数を調整
             [[cvs:func.php]](v1.4:r1.61)
        BugTrack/566 [cvs] $Id が無い/二つある (マイグレーション作業向け) 
             [[cvs:default.ini.php]](v1.4:r1.4)
             [[cvs:i_mode.ini.php]](v1.4:r1.5)
             [[cvs:jphone.ini.php]](v1.4:r1.6)
             [[cvs:pukiwiki.ini.php]](v1.4:r1.55)
             [[cvs:plugin/clear.inc.php]](v1.4:r1.1) (cvs admin -kkv)
             [[cvs:plugin/versionlist.inc.php]](v1.4:r1.8)
             [[cvs:skin/default.en.css]](v1.4:r1.29)
             [[cvs:skin/default.ja.css]](v1.4:r1.30)
             [[cvs:skin/default.js]](v1.4:r1.2)
             [[cvs:skin/keitai.skin.ja.php]](v1.4:r1.5)
             [[cvs:skin/print.en.css]](v1.4:r1.3)
             [[cvs:skin/print.ja.css]](v1.4:r1.3)
             [[cvs:skin/pukiwiki.skin.en.php]](v1.4:r1.31)
             [[cvs:skin/pukiwiki.skin.ja.php]](v1.4:r1.32)
             [[cvs:skin/trackback.css]](v1.4:r1.2)
             [[cvs:skin/trackback.js]](v1.4:r1.2)
        BugTrack/553 ページのバックアップを削除しても、差分は残ってしまう
             [[cvs:plugin/diff.inc.php]](v1.4:r1.4)
             [[cvs:en.lng]](v1.4:r1.26)
             [[cvs:ja.lng]](v1.4:r1.25)
        BugTrack/570 追加で整形ルールを表示すると編集になる
             [[cvs:html.php]](v1.4:r1.99)

    *   2003-11-17 1.4.2 by PukiWiki Developers Team
        BugTrack/487 autolinkで文字化け
            [[cvs:func.php]](v1.4:r1.54)
        BugTrack/488 mbstring無しの状態でAutoLinkを設定するとページが化ける
            [[cvs:mbstring.php]](v1.4:r1.9)
        関数名がコンストラクタと衝突
            [[cvs:convert_html.php]](v1.4:r1.57)
        tracker_list()の第2引数でページ名の相対指定が使えるように
        tracker()の第1引数が省略されたときに'default'を使う
            [[cvs:plugin/tracker.inc.php]](v1.4:r1.18)
        エラー処理を調整
            [[cvs:plugin/template.inc.php]](v1.4:r1.16)
        変数名間違い
            [[cvs:plugin/rename.inc.php]](v1.4:r1.9)

    *   2003-11-10 1.4.1 by PukiWiki Developers Team

        BugTrack/478    リストの子要素の段落が正しく出力されない
        BugTrack/479    CGI版PHPの場合、HTTPSで利用できない
        BugTrack/480    online.inc.php 内のディレクトリ指定を定数に
        BugTrack/482    AutoLinkの動作を調整
        BugTrack/483    注釈内にHTMLエンティティを書くと注釈が作られない
        BugTrack/485    lookupでInterWikiNameの「検索」等を実行すると
                        &でなく&amp;が入る
        BugTrack/486    headerでキャッシュ無効を
        tracker.inc.php radio/select/checkboxで、選択肢がひとつも選択
                        されなかったときは、値を空白とする
        backup.php      dataが空の場合のwarning抑止

    *   2003-11-03 1.4 by PukiWiki Developers Team

        1.4系最初のリリース

TODO


    質問、意見、バグ報告は http://pukiwiki.org/ までお願いします。

    http://pukiwiki.org/?BugTrack

謝辞

    PukiWiki Develpers Teamの皆さん、PukiWikiユーザの皆さんに感謝します。
    PukiWiki を開発した、yu-ji(旧sng)さんに感謝します。
    YukiWiki のクローン化を許可していただいた結城浩さんに感謝します。
    本家のWikiWikiを作ったCunningham & Cunningham, Inc.に 感謝します。

参照リンク

    * PukiWikiホームページ	http://pukiwiki.org/
    * yu-jiのホームページ	http://factage.com/yu-ji/
    * 結城浩さんのホームページ	http://www.hyuki.com/
    * YukiWikiホームページ	http://www.hyuki.com/yukiwiki/
    * Tiki	http://todo.org/cgi-bin/jp/tiki.cgi
    * 本家のWikiWiki	http://c2.com/cgi/wiki?WikiWikiWeb
    * 本家のWikiWikiの作者(Cunningham & Cunningham, Inc.)	http://c2.com/
