NAME

    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築PHPスクリプト

        PukiWiki 1.4.6
        Copyright (C)
          2001-2005 PukiWiki Developers Team
          2001-2002 yu-ji (Based on PukiWiki 1.3 by yu-ji)
        License: GPL version 2 or (at your option) any later version

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
        1.3まではyu-jiさんが作成し、1.3.1b以降はPukiWiki Developers Team
        によって開発が続けられています。

        PukiWikiはPHPで書かれたPHPスクリプトとして実現されていますので、
        PHPが動作するWebサーバならば比較的容易に設置できます。

        PukiWikiはフリーソフトです。 ご自由にお使いください。
        最新版は、 http://pukiwiki.org/ から入手できます。

設置方法

        以下は一例です。Webサーバーへのシェルアクセスが可能であれば、
        アーカイブをそのままサーバーに転送し、サーバー上で解凍
        (tar pzxf pukiwiki*.tar.gz) するだけでも動作するはずです。

    1.  アーカイブを解きます。

    2.  必要に応じて設定ファイル(*.ini.php)の内容を確認します。
        1.11  から設定ファイルが別ファイルのpukiwiki.ini.phpになりました。
        1.4   から設定ファイルが分割されました。
        1.4.4 から携帯電話およびPDA向けの設定ファイルが一つに集約されました。
          (i_mode.ini.php, jphone.ini.php の設定+αを keitai.ini.php に集約)

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
        lib            755	ライブラリ
        plugin         755	プラグイン
        skin           755	スキン、CSS、JavaScirptファイル
        trackback      777	TrackBackファイル格納ディレクトリ
        wiki           777	データの格納ディレクトリ

        ファイル       パーミッション 転送モード
        *.php          644            ASCII
	attach/*       666            BINARY
	backup/*.gz    666            BINARY
	backup/*.txt   666            ASCII
        cache/*        666            ASCII
        counter/*      666            ASCII
        diff/*.txt     666            ASCII
        wiki/*.txt     666            ASCII
        image/*        644            BINARY
        image/face/*   644            BINARY
        lib/*          644            ASCII
        plugin/*       644            ASCII
        skin/*         644            ASCII
        wiki/*         666            ASCII

    5.  index.php あるいは pukiwiki.php にブラウザからアクセスします。
        必要に応じて、さらに設定やデザインを調整して下さい。

データのバックアップ方法

        データファイルディレクトリ以下をバックアップします。
        (デフォルトディレクトリ名は wiki)

        必要に応じて他のディレクトリの内容をバックアップします。
        (デフォルトディレクトリ名は attach, backup, counter, cache,
         diff, trackback)


新しいページの作り方

        「新規」リンクから新しくページを作成する以外に、ページの中に
        書いた語句からそのページ名のページを作成することができます。

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

        [[整形ルール]] のページを参照してください。

InterWiki

        1.11 からInterWikiが実装されました。

        InterWiki とは、Wikiサーバーをつなげる機能です。
        最初はそうだったんで InterWiki という名前なのだそうですが、
        今は、Wikiサーバーだけではなくて、いろんなサーバーを引けます。
        なかなか便利です。そうなると InterWiki という名前はあまり機能を
        表していないことになります。
        この機能は Tiki からほぼ完全に移植しています。

        詳細は [[InterWikiテクニカル]] のページを参照してください。

RDF/RSSの出力

        1.2.1から、RecentChangesのRDF/RSSを出力できるようになりました。
        1.4.5から、RSS 2.0 を出力できるようになりました。

        * 出力方法の例
          RSS 0.91 http://path/to/pukiwiki/index.php?plugin=rss
          RSS 1.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=1.0
          RSS 2.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=2.0

FAQ

        PukiWiki.orgのそれぞれのページをチェックして下さい。

        FAQ        http://pukiwiki.org/?FAQ
        質問箱     http://pukiwiki.org/?%E8%B3%AA%E5%95%8F%E7%AE%B1
        続・質問箱 http://pukiwiki.org/?%E7%B6%9A%E3%83%BB%E8%B3%AA%E5%95%8F%E7%AE%B1

BUG

        バグ報告は devサイトまでお願いします。
        (我々はPukiWikiでPukiWikiのバグトラッキングを行っています)

        dev:BugTrack
        http://pukiwiki.sourceforge.jp/dev/?BugTrack

謝辞

    PukiWiki Develpers Teamの皆さん、PukiWikiユーザの皆さんに感謝します。
    PukiWiki を開発した、yu-ji(旧sng)さんに感謝します。
    YukiWiki のクローン化を許可していただいた結城浩さんに感謝します。
    本家のWikiWikiを作ったCunningham & Cunningham, Inc.に 感謝します。

参照リンク

    * PukiWikiホームページ      http://pukiwiki.org/
    * yu-jiさんのホームページ   http://factage.com/yu-ji/
    * 結城浩さんのホームページ  http://www.hyuki.com/
    * YukiWikiホームページ      http://www.hyuki.com/yukiwiki/
    * Tiki                      http://todo.org/cgi-bin/tiki/tiki.cgi
    * 本家WikiWikiWeb           http://c2.com/cgi/wiki?WikiWikiWeb
    * WikiWikiWebの作者(Cunningham & Cunningham, Inc.) http://c2.com/
    
