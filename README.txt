名前
    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築スクリプト

    PukiWiki 1.4.6
    Copyright (C)
      2001-2005 PukiWiki Developers Team
      2001-2002 yu-ji (Based on PukiWiki 1.3 by yu-ji)
    License: GPL version 2 or (at your option) any later version
    URL:
      http://pukiwiki.org/
      http://pukiwiki.sourceforge.jp/dev/
      http://sourceforge.jp/projects/pukiwiki/

書式
    index.php?PAGE_NAME_ENCODED
    index.php?plugin=PLUGIN_NAME

概要
    PukiWiki(ぷきうぃき)は自由にページを追加・削除・編集できるページ群を作るこ
    とができるWebアプリケーション(WikiWikiWeb)です。テキストデータからXHTML1.1
    を生成することができ、そのテキストをWebブラウザから自由に修正することがで
    きます。

    特にPukiWikiはPHP言語で書かれたスクリプトですので、PHPが動作するWebサーバ
    ならば容易に設置できます。

    PukiWikiは、結城浩さんが作られたYukiWikiの仕様を参考に独自に開発されまし
    た。PukiWiki バージョン1.3まではyu-jiさんが個人で製作し、1.3.1b 以降は
    PukiWiki Developers Team によって開発が続けられています。

    PukiWikiは、yu-jiさんを含む PukiWiki Develpers Team やその貢献者が、各自の
    著作物にGPLバージョン2(または _あなたの選択で_ それ以降のGPL)を適用してい
    る「フリーソフトウェア(自由なソフトウェア)」です。最新版はPukiWiki公式サイ
    トから入手できます。

インストール
    PukiWikiはPHPスクリプトなので、(例えばPerlのように)スクリプトに実行権を付
    ける必要はありません。CGI起動でないのであれば、スクリプトの一行目を修正す
    る必要もありません。

    Webサーバーへのシェルアクセスが可能であれば、PukiWikiのアーカイブをそのま
    まサーバーに転送し、サーバー上で解凍(tar pzxf pukiwiki*.tar.gz) するだけで
    パーミッションの設定も行われ、すぐに使い始める事ができるでしょう。

    以下に、事前にクライアントPCで作業を行う場合の例を記します。

    1. PukiWikiのアーカイブを展開します。

    2. 必要に応じて設定ファイル(*.ini.php)の内容を確認します。
      スクリプトの中の日本語は(あれば、基本的に)EUC-JPで、また改行コードはLFで
      記述されていますので、日本語文字コードと改行コードの自動判別ができ、それ
      を元のまま保存できるテキストエディタを使用して下さい。

      ※インターネットに公開するPukiWikiであるならば、PKWK_SAFE_MODEを有効にす
        ることをお薦めします。(詳細:BugTrack/787)

        全体設定           : pukiwiki.ini.php
        ユーザ定義         : rules.ini.php

        デスクトップPC     : default.ini.php
        携帯電話およびPDA  : keitai.ini.php
           (旧 i_mode.ini.php/jphone.ini.php)

    3.  ファイルをFTPなどでサーバに転送します。
      ※FTPの転送モードは「バイナリ(bin)」を使用して下さい

    4.  サーバ上のファイルおよびディレクトリのパーミッションを確認します。

    ディレクトリ パーミッション
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

    ファイル    パーミッション データの種類(参考)
      .htaccess      644       ASCII
      .htpasswd      644       ASCII
      */.htaccess    644       ASCII

    ファイル    パーミッション データの種類(参考)
      *.php          644       ASCII
      */*.php        644       ASCII
      attach/*       666       BINARY (はじめは存在せず)
      backup/*.gz    666       BINARY (インストール時は存在せず)
      backup/*.txt   666       ASCII  (多くの環境では存在せず)
      cache/*        666       ASCII
        (一部のプラグインはバイナリファイルを保存します)
      counter/*      666       ASCII  (はじめは存在せず)
      diff/*.txt     666       ASCII  (はじめは存在せず)
      wiki/*.txt     666       ASCII
      image/*        644       BINARY
      image/face/*   644       BINARY
      lib/*          644       ASCII
      plugin/*       644       ASCII
      skin/*         644       ASCII

    5.  サーバーに設置した PukiWiki の index.php あるいは pukiwiki.php に、Web
      ブラウザからアクセスします。

    6.  必要に応じて、さらに設定やデザインを調整して下さい。

      ※CSS(外見)は skin/スキン名.css.php にあります。これは目的に応じたCSSを
        出力することのできる、単独のPHPスクリプトです。これを静的なファイルに
        したい場合は、出力結果をWebブラウザで取り出して下さい。どのようなCSS
        が求められているかはスキンに記述されています。
      ※スキン(外見の骨組み)に関する設定項目は skin/スキン名.skin.php の先頭に
        あります。また tDiaryスキン の使用法は BugTrack/769 を参照して下さい。
      ※プラグイン独自の設定項目は plugin/プラグイン名.inc.php の先頭にありま
        す

バックアップとリストア
    ページの最新データを収めているディレクトリ(デフォルトの名前は wiki)以下
    を、また必要に応じて他のデータを収めているディレクトリ以下をバックアップし
    て下さい。(同 attach, backup, cache, counter, diff, trackback)

    cacheディレクトリもバックアップすることをお薦めします。
    1. cache/*.rel ファイルと cache/*.ref ファイルは linksプラグイン で再生
       成可能ですが、この処理は非常に重く、環境によっては処理が必ず失敗する
       (中断する)場合があります。
    2. cache/*.rel ファイルがPukiWikiに全くない時に既存のページを編集すると、
      linksプラグインを実行した状態とほぼ同等の負荷がかかります。
      (詳細:BugTrack2/56)
    3. amazonプラグインはここに画像(のキャッシュ)を保存します。

    データを配置した時は、ファイルのパーミッションが期待されている通りかどう
    か、また実際に動作するかどうかを確認して下さい。(例: 配置したページの更新
    を試みる)

    PukiWiki 1.4.5 以降では、添付されている dumpプラグイン で、wiki/attach/
    backup ディレクトリのリモートバックアップ(*.tar.gzないし*.tar形式)が可能で
    す。
      起動の例: http://path/to/pukiwiki/index.php?plugin=dump

    dumpプラグインにはdumpプラグインで取得したファイルの中身をPukiWikiに展開す
    る機能(リモートリストア)も用意されています。ただしファイルに含まれていない
    データをPukiWikiから削除する機能はありません(常に上書きになります)し、Web
    サーバーやPHPのアップロードファイルサイズ制限を越えるファイルを利用するこ
    とはできません。またこの機能はデフォルトで無効になっています。

    その他、PukiWikiの更新内容をメールで通知する機能は、既存のデータを失わない
    ための機能としてとらえる事ができるでしょう。

ページの作成
    そのページが置かれるはずのURLに直接アクセスしたり、「新規」リンクから新し
    くページを作成する以外に、ページの中に書いた語句からそのページ名のページを
    作成することができます。

    1.  まず、適当なページ（例えばFrontPage）を選び、ページの上下にある
     「編集」リンクをたどります。

    2.  するとテキスト入力ができる状態になるので、 そこにNewPageのような単語
    （大文字小文字混在している英文字列）や、 [[新しいページ名]] の様に二重のブ
     ラケットで囲んだ語句を書いて「保存」します。

    3.  保存すると、FrontPageのページが書き換わり、あなたが書いたNewPageという
      単語や「新しいページ名」という語句の後ろに '?' という小さなリンクが表示
      されます。 このリンクはそのページがまだ存在しないこと、そしてそのリンク
      からすぐに作成できることを示す印です。

    4.  その '?' をクリックすると新しいページができますので、あなたの好きな文
      章をその新しいページに書いて保存します。

    5.  新しいページができると、元のページにあったそれらの語句からは '?' が消
      え、そのページを指し示す普通のハイパーリンクとなります。

    6. 今後は、それらの語句(のリンク)をクリックすることで、気軽にそのページを
      表示できるようになります。

テキストのルール
    テキストデータをXHTMLに変換するためのルールについては [[ヘルプ]] [[整形ル
    ール]] のページを参照してください。

    テキストデータでの改行を、XHTMLの出力でもそのまま改行(<br />)として反映さ
    せたい場合:
      1. 設定 $line_break の内容を切り替えることで全体の動作が変わります
      2. #setlinebreak プラグインで行単位に操作する事が可能です

    WikiName (大文字始まりの英単語が二つ以上続いた単語) に対する自動リンク機能
    を無効にするには、設定 $nowikiname の内容を切り替えて下さい。

    AutoLink (既存のページに対するリンクを自動的に作成する機能)を無効にした
    り、有効とみなすページ名のバイト数を修正する場合、設定 $autolink の値を修
    正して下さい。

InterWikiについて
    InterWiki とは、WikiとWikiをつなげる機能です。例えば
    [[Wikiサイト名:ページ名]]
    このように記述することで、そのWikiの特定のページに対するリンクを簡単に出力
    させる事ができます。
    ※Wiki以外のサイト、例えば検索エンジンへのURIを生成することも可能です

    この機能は Tiki からほぼ完全に移植されています。
    詳細は [[InterWikiテクニカル]] のページを参照してください。

RDF/RSSの出力
    一部のWebブラウザなどに搭載されているRSSリーダーを使って、PukiWikiの更新状
    況を確認することができます。
      1.2.1から、RecentChangesのRDF/RSSを出力できるようになりました。
    　 1.4.5から、RSS 2.0 を出力できるようになりました。

    出力方法の例:
      RSS 0.91 http://path/to/pukiwiki/index.php?plugin=rss
      RSS 1.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=1.0
      RSS 2.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=2.0

関連項目
    標準添付されているプラグインの簡単な説明は、[[PukiWiki/1.4/Manual/Plugin]]
    のページを参照して下さい。

    その他、リリース版の基本的な使い方に関する情報はPukiWiki.orgをご覧下さい。
    以下のようなコンテンツが特に有用です。

    FAQ        http://pukiwiki.org/?FAQ
    質問箱     http://pukiwiki.org/?%E8%B3%AA%E5%95%8F%E7%AE%B1
    続・質問箱 http://pukiwiki.org/?%E7%B6%9A%E3%83%BB%E8%B3%AA%E5%95%8F%E7%AE%B1

バグ
    PukiWikiのセキュリティに関する情報は以下でまとめられています。
    http://pukiwiki.org/?PukiWiki/Errata

    バグ報告は devサイトまでお願いします。
    (我々はPukiWikiでPukiWikiのバグトラッキングを行っています)
    http://pukiwiki.sourceforge.jp/dev/?BugTrack2

謝辞
    PukiWiki Develpers Teamの皆さん、PukiWikiユーザの皆さんに感謝します。
    PukiWiki を開発した、yu-ji(旧sng)さんに感謝します。
    YukiWiki のクローン化を許可していただいた結城浩さんに感謝します。
    本家のWikiWikiを作ったCunningham & Cunningham, Inc.に 感謝します。

    * yu-jiさんのホームページ   http://factage.com/yu-ji/
    * 結城浩さんのホームページ  http://www.hyuki.com/
    * YukiWikiホームページ      http://www.hyuki.com/yukiwiki/
    * Tiki          http://todo.org/cgi-bin/tiki/tiki.cgi
    * 本家WikiWikiWeb       http://c2.com/cgi/wiki?WikiWikiWeb
    * WikiWikiWebの作者(Cunningham & Cunningham, Inc.) http://c2.com/
