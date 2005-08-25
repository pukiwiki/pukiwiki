名前
    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築スクリプト

    Version 1.4.6
    Copyright (C)
      2001-2005 PukiWiki Developers Team
      2001-2002 yu-ji (Based on PukiWiki 1.3 by yu-ji)
    License: GPL version 2 or (at your option) any later version

    URL:
      http://pukiwiki.org/
      http://pukiwiki.sourceforge.jp/dev/
      http://sourceforge.jp/projects/pukiwiki/

    $Id: README.txt,v 1.20 2005/08/25 15:27:37 henoheno Exp $

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
