NAME

    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築PHPスクリプト

        PukiWiki 1.4
        Copyright (C) 2001,2002,2003 PukiWiki Developers Team.
        License is GNU/GPL.
        Based on "PukiWiki" 1.3 by sng
        http://pukiwiki.org/

SYNOPSIS

        http://pukiwiki.org/

DESCRIPTION

        PukiWikiは参加者が自由にページを追加・削除・編集できる
        Webページ群を作るPHPスクリプトです。
        Webで動作する掲示板とちょっと似ていますが、
        Web掲示板が単にメッセージを追加するだけなのに対して、
        PukiWikiは、Webページ全体を自由に変更することができます。

        PukiWikiは、結城浩さんのYukiWikiの仕様を参考にして独自に作られました。
        1.3まではsngさんが作成し、1.3.1b以降はPukiWiki Developers Teamによって
        開発が続けられています。

        PukiWikiはPHPで書かれたPHPスクリプトとして実現されていますので、
        PHPが動作するWebサーバならば比較的容易に設置できます。

        PukiWikiはフリーソフトです。 ご自由にお使いください。

設置方法

    入手

        PukiWikiの最新版は、 http://pukiwiki.org/ から入手できます。

    インストール

    1.  アーカイブを解きます。

    2.  必要に応じて設定ファイル(*.ini.php)の内容を確認します。
        1.11 から設定ファイルが別ファイルのpukiwiki.ini.phpになりました。
        1.4 から設定ファイルが分割されました。

        *   全体設定          : pukiwiki.ini.php

        *   エージェント別設定
                I-MODE,AirH"  : i_mode.ini.php
                J-PHONE       : jphone.ini.php
                その他        : default.ini.php

        *   ユーザ定義ルール  : rules.ini.php

    3.  アーカイブの内容をサーバに転送します。
        ファイルの転送モードについては次項を参照してください。

    4.  pukiwiki.ini.php内で指定した以下のディレクトリを作成します。

        データの格納ディレクトリ               (デフォルトはwiki)
        差分ファイル格納ディレクトリ           (デフォルトはdiff)
        バックアップファイル格納ディレクトリ   (デフォルトはbackup)
        キャッシュファイル格納ディレクトリ     (デフォルトはcache)
        添付ファイル格納ディレクトリ           (デフォルトはattach)
        カウンタファイル格納ディレクトリ       (デフォルトはcounter)
        TrackBackファイル格納ディレクトリ      (デフォルトはtrackback)

        ディレクトリ内にファイルがある場合には、そのファイルの属性を
        666に変更してください。

    5.  サーバ上のファイルおよびディレクトリのパーミッションを確認します。
        ファイルのパーミッションについては次項を参照してください。

    6.  pukiwiki.phpにブラウザからアクセスします。

    パーミッション

        ディレクトリ   パーミッション
        attach         777
        backup         777
        cache          777
        counter        777
        diff           777
        face           755
        image          755
        plugin         755
        skin           755
        trackback      777
        wiki           777

        ファイル       パーミッション 転送モード
        *.php          644            ASCII
        *.lng          644            ASCII
        pukiwiki.png   644            BINARY

        cache/*        666            ASCII
        face/*         644            BINARY
        image/*        644            BINARY
        plugin/*       644            ASCII
        skin/*         644            ASCII
        wiki/*         666            ASCII

データのバックアップ方法

        データファイルディレクトリ以下をバックアップします。
        (デフォルトディレクトリ名は wiki)

        必要に応じて他のディレクトリの内容をバックアップします。
        (デフォルトディレクトリ名は attach,backup,counter,cache,diff,trackback)

新しいページの作り方

    1.  まず、適当なページ（例えばFrontPage）を選び、
        ページの下にある「編集」リンクをたどります。

    2.  するとテキスト入力ができる状態になるので、 そこにNewPageのような単語
        （大文字小文字混在している英文字列） を書いて「保存」します。

    3.  保存すると、FrontPageのページが書き換わり、
        あなたが書いたNewPageという文字列の後ろに ?
        というリンクが表示されます。 この ?
        はそのページがまだ存在しないことを示す印です。

    4.  その ? をクリックすると新しいページNewPageができますので、
        あなたの好きな文章をその新しいページに書いて保存します。

    5.  NewPageページができるとFrontPageの ? は消えて、リンクとなります。

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

RDF/RSS

        1.2.1から、RecentChangesのRDF/RSSを出力できるようになりました。
        実用できるかはわからないですが、将来何かに使えれば、と思ってます。

    *   RSS 0.91 の出力方法の例

        *   http://pukiwiki/index.php?cmd=rss

    *   RSS 1.0 の出力方法の例

        *   http://pukiwiki.org/index.php?cmd=rss10


PukiWiki/1.3.xとの非互換点

    1.  [[WikiName]]とWikiNameは同じページを指します。
    2.  定義リストの書式が違います。 :term:description -> :term|description
    3.  リストや引用文は、下位レベルのリストや引用文を包含することができます。
        (1.3.xでは、リストは同種のみ、引用内には引用しか包含できませんでした。)

更新履歴

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

    http://pukiwiki.org/?BugTrack

作者

    PukiWiki 1.4
    Copyright (C) 2001,2002,2003 PukiWiki Developers Team. License is GNU/GPL.
    Based on "PukiWiki" 1.3 by sng
    http://pukiwiki.org/

    質問、意見、バグ報告は http://pukiwiki.org/ までお願いします。

配布条件

    PukiWikiは、 GNU General Public Licenseにて公開します。

    PukiWikiはフリーソフトです。 ご自由にお使いください。

謝辞

    PukiWiki Develpers Teamの皆さん、PukiWikiユーザの皆さんに感謝します。

    PukiWiki を開発した、sngさんに感謝します。

    YukiWiki のクローン化を許可していただいた結城浩さんに感謝します。

    本家のWikiWikiを作ったCunningham & Cunningham, Inc.に 感謝します。

参照リンク

    *   PukiWikiホームページ http://pukiwiki.org/

    *   sngのホームページ http://factage.com/sng/

    *   結城浩さんのホームページ http://www.hyuki.com/

    *   YukiWikiホームページ http://www.hyuki.com/yukiwiki/

    *   Tiki http://todo.org/cgi-bin/jp/tiki.cgi

    *   本家のWikiWiki http://c2.com/cgi/wiki?WikiWikiWeb

    *   本家のWikiWikiの作者(Cunningham & Cunningham, Inc.) http://c2.com/
