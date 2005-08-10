NAME

    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築スクリプト

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

        PukiWiki(ぷきうぃき)は自由にページを追加・削除・編集できる
        ページ群を作ることができるWebアプリケーション(WikiWikiWeb)
        です。Web掲示板が単にメッセージを追加するだけなのに対して、
        コンテンツ全体を自由に変更することができます。

        特にPukiWikiはPHP言語で書かれたPHPスクリプトですので、PHPが
        動作するWebサーバならば容易に設置できます。

        PukiWikiは、結城浩さんが作られたYukiWikiの仕様を参考に独自に
        開発されました。PukiWiki バージョン1.3まではyu-jiさんが個人
        で製作し、1.3.1b 以降は PukiWiki Developers Team によって開発
        が続けられています。

        PukiWikiは、yu-jiさんを含む PukiWiki Develpers Team やその貢献
        者が、各自の著作物にGPLバージョン2(または _あなたの選択で_ 
        それ以降のGPL)を適用している「フリーソフトウェア(自由なソフト
        ウェア)」です。最新版はPukiWiki公式サイトから入手できます。

設置方法

        PukiWikiはPHPスクリプトなので、(例えばPerlのように)スクリプト
        に実行権を付ける必要はありません。CGI起動でないのであれば、
        スクリプトの一行目を修正する必要もありません。

        Webサーバーへのシェルアクセスが可能であれば、PukiWikiのアー
        カイブをそのままサーバーに転送し、サーバー上で解凍
        (tar pzxf pukiwiki*.tar.gz) するだけでパーミッションの設定も
        行われます。

        以下に、事前にクライアントPCで作業を行う場合の例を記します。

    1.  PukiWikiのアーカイブを展開します。

    2.  必要に応じて設定ファイル(*.ini.php)の内容を確認します。
        スクリプトの中の日本語は(あれば、基本的に)EUC-JPで、また改行
        コードはLFで記述されていますので、日本語文字コードと改行コード
        の自動判別ができ、それを元のまま保存できるテキストエディタを
        使用して下さい。

        * 共通設定
          全体               : pukiwiki.ini.php
          ユーザ定義         : rules.ini.php

        * ユーザーエージェント別設定
          携帯電話およびPDA  : keitai.ini.php
                               (旧 i_mode.ini.php/jphone.ini.php)
          デスクトップPC     : default.ini.php

    3.  ファイルをFTPなどでサーバに転送します。
        ※ここまでの間に文字コードや改行コードを壊していないので
          あれば、転送モードは全て「バイナリ」で行うことができる
          はずです

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

        ファイル       パーミッション データの種類(参考)
        .htaccess      644            ASCII
        .htpasswd      644            ASCII
        */.htaccess    644            ASCII

        ファイル       パーミッション データの種類(参考)
        *.php          644            ASCII
        */*.php        644            ASCII
        attach/*       666            BINARY (インストール時は存在せず)
        backup/*.gz    666            BINARY (インストール時は存在せず)
        backup/*.txt   666            ASCII  (多くの環境では存在せず)
        cache/*        666            ASCII  (一部のプラグインはバイナリファイルを保存する)
        counter/*      666            ASCII  (インストール時は存在せず)
        diff/*.txt     666            ASCII  (インストール時は存在せず)
        wiki/*.txt     666            ASCII
        image/*        644            BINARY
        image/face/*   644            BINARY
        lib/*          644            ASCII
        plugin/*       644            ASCII
        skin/*         644            ASCII

    5.  サーバーに設置した index.php あるいは pukiwiki.php に、Web
        ブラウザからアクセスします。

    6.  必要に応じて、さらに設定やデザインを調整して下さい。


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

        [[ヘルプ]][[整形ルール]] のページを参照してください。

InterWikiについて

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

        dev:BugTrack2
        http://pukiwiki.sourceforge.jp/dev/?BugTrack2

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
    
