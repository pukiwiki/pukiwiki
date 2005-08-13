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
    PukiWiki(ぷきうぃき)は自由にページを追加・削除・編集できる
    ページ群を作ることができるWebアプリケーション(WikiWikiWeb)
    です。テキストデータからXHTML 1.1を生成することができ、
    そのテキストをWebブラウザから自由に修正することができます。

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

インストール
    PukiWikiはPHPスクリプトなので、(例えばPerlのように)スクリプト
    に実行権を付ける必要はありません。CGI起動でないのであれば、
    スクリプトの一行目を修正する必要もありません。

    Webサーバーへのシェルアクセスが可能であれば、PukiWikiのアー
    カイブをそのままサーバーに転送し、サーバー上で解凍
    (tar pzxf pukiwiki*.tar.gz) するだけでパーミッションの設定も
    行われ、すぐに使い始める事ができるでしょう。

    以下に、事前にクライアントPCで作業を行う場合の例を記します。

    1. PukiWikiのアーカイブを展開します。

    2. 必要に応じて設定ファイル(*.ini.php)の内容を確認します。
      スクリプトの中の日本語は(あれば、基本的に)EUC-JPで、また改行
      コードはLFで記述されていますので、日本語文字コードと改行コード
      の自動判別ができ、それを元のまま保存できるテキストエディタを
      使用して下さい。

    * 共通設定ファイル
      全体               : pukiwiki.ini.php
      ユーザ定義         : rules.ini.php

    * ユーザーエージェント別設定ファイル
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

    5.  サーバーに設置した PukiWiki の index.php あるいは
      pukiwiki.php に、Webブラウザからアクセスします。

    6.  必要に応じて、さらに設定やデザインを調整して下さい。

    ※CSS(外見)は skin/スキン名.css.php にあります。これは目的に
        応じたCSSを出力することのできる、単独のPHPスクリプトです。
      これを静的なファイルにしたい場合は、出力結果をWebブラウザ
      で取り出して下さい。どのようなCSSが求められているかはスキン
      に記述されています。
    ※スキン(外見の骨組み)に関する設定項目は skin/スキン名.skin.php
      の先頭にあります。また tDiaryスキン の使用法は BugTrack/769
      を参照して下さい
    ※プラグイン独自の設定項目は plugin/プラグイン名.inc.php の
      先頭にあります

バックアップとリストア
    ページの最新データを収めているディレクトリ
    (デフォルトの名前は wiki)以下を、また必要に応じて他の
    データを収めているディレクトリ以下をバックアップして
    下さい。(同 attach, backup, cache, counter, diff,
    trackback)

    ※cacheディレクトリもバックアップすることをお薦めします。
      1. cache/*.rel ファイルと cache/*.ref ファイルは
        linksプラグインで再生成可能ですが、この処理は非常
        に重く、環境によっては処理が必ず失敗する(中断する)場
        合があります。
      2. cache/*.rel ファイルがPukiWikiに全くない時に既存の
        ページを編集すると、linksプラグインを実行した状態と
        ほぼ同等の負荷がかかります。(詳細:BugTrack2/56)
      3. amazonプラグインはここに画像(のキャッシュ)を保存し
	     ます。

    データを配置した時は、ファイルのパーミッションが期待さ
    れている通りかどうか、また実際に動作するかどうかを確認
    して下さい。 (例: 配置したページの更新を試みる)

    PukiWiki 1.4.5 以降では、添付されているdumpプラグイン
    で wiki/attach/backup ディレクトリのリモートバック
    アップ(*.tar.gzないし*.tar形式)が可能です。

    * 起動の例
      http://path/to/pukiwiki/index.php?plugin=dump

    dumpプラグインにはdumpプラグインで取得したファイルの
    中身をPukiWikiに展開する機能(リモートリストア)も用意
    されています。
    ただしファイルに含まれていないデータをPukiWikiから削除
    する機能はありませんし、WebサーバーやPHPのアップロード
    ファイルサイズ制限を越えるファイルを利用することはでき
    ません。またこの機能はデフォルトで無効になっています。

    その他、PukiWikiの更新内容をメールで通知する機能は、
    既存のデータを失わないための機能としてとらえる事が
    できるでしょう。

ページの作成
    そのページが置かれるはずのURLに直接アクセスしたり、
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

テキストのルール
    テキストデータをXHTMLに変換するためのルールについては
    [[ヘルプ]] [[整形ルール]] のページを参照してください。

    テキストデータでの改行を、XHTMLの出力でもそのまま改行(<br />)
    として反映させたい場合:
      1. 設定 $line_break の内容を切り替えることで全体の動作が
         変わります
      2. #setlinebreak プラグインで行単位に操作する事が可能です

    WikiName (大文字始まりの英単語が二つ以上続いた単語) に対する
    自動リンク機能を無効にするには、設定 $nowikiname の内容を切り
    替えて下さい。

    AutoLink (既存のページに対するリンクを自動的に作成する機能)
    を無効にしたり、有効とみなすページ名のバイト数を修正する場合
    設定 $autolink の値を修正して下さい。

InterWikiについて
    InterWiki とは、WikiとWikiをつなげる機能です。例えば
    [[Wikiサイト名:ページ名]]
    このように記述することで、そのWikiの特定のページに対する
    リンクを簡単に出力させる事ができます。
    ※Wiki以外のサイト、例えば検索エンジンへのURIを生成する
      ことも可能です

    この機能は Tiki からほぼ完全に移植されています。
    詳細は [[InterWikiテクニカル]] のページを参照してください。

RDF/RSSの出力
    一部のWebブラウザなどに搭載されているRSSリーダーを使って、
    PukiWikiの更新状況を確認することができます。
    1.2.1から、RecentChangesのRDF/RSSを出力できるようになりました。
    1.4.5から、RSS 2.0 を出力できるようになりました。

    * 出力方法の例
      RSS 0.91 http://path/to/pukiwiki/index.php?plugin=rss
      RSS 1.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=1.0
      RSS 2.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=2.0

関連項目
    標準添付されているプラグインの簡単な説明は、
    [[PukiWiki/1.4/Manual/Plugin]] のページを参照して下さい。

    その他、リリース版に関する情報はPukiWiki.orgのそれぞれの
    ページをご覧下さい。

    FAQ    http://pukiwiki.org/?FAQ
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
