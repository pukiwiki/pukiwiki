NAME
    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築PHPスクリプト

        Copyright (C) 2001,2002 by sng.
        sng <sng@factage.com>
        http://factage.com/sng/

SYNOPSIS
        http://factage.com/sng/pukiwiki/pukiwiki.php

DESCRIPTION
    PukiWikiは参加者が自由にページを追加・削除・編集できる
    Webページ群を作るPHPスクリプトです。
    Webで動作する掲示板とちょっと似ていますが、
    Web掲示板が単にメッセージを追加するだけなのに対して、
    PukiWikiは、Webページ全体を自由に変更することができます。

    PukiWikiは、結城浩さんのYukiWikiの仕様を参考にして独自に作られました。

    PukiWikiはPHPで書かれたPHPスクリプトとして実現されていますので、
    PHPが動作するWebサーバならば比較的容易に設置できます。

    PukiWikiはフリーソフトです。 ご自由にお使いください。

設置方法
  入手

    PukiWikiの最新版は、 http://factage.com/sng/php/ から入手できます。

  ファイル一覧

        readme.txt        ドキュメント
        pukiwiki.php      PukiWiki本体
        pukiwiki.ini.php  PukiWikiの設定ファイル
        pukiwiki.skin.php PukiWikiのディフォルトスキンファイル
        pukiwiki.gif      ロゴ

  インストール

    1.  アーカイブを解く。

    2.  必要に応じてpukiwiki.ini.phpの設定を確認します。
        1.11 から設定ファイルが別ファイルのpukiwiki.ini.phpになりました。

    3.  pukiwiki.phpとpukiwiki.gifを同じところに設置します。

    4.  さらにpukiwiki.phpと同じところにpukiwiki.ini.phpとpukiwiki.skin.ja.php、        もしくはpukiwiki.skin.en.phpを同じところに設置します。

    5.  pukiwiki.php内で指定したデータファイルディレクトリを
        属性 777 で作成する。(ディフォルトは wiki )

    6.  pukiwiki.php内で指定した差分ファイルディレクトリを
        属性 777 で作成する。(ディフォルトは diff )

    7.  自動バックアップ機能(ディフォルトでは off)を使う場合、
        pukiwiki.php内で指定した差分ファイルディレクトリを
        属性 777 で作成する。(ディフォルトは diff )

    8.  pukiwiki.phpにブラウザからアクセスします。

  パーミッション

            ファイル             パーミッション      転送モード
            pukiwiki.php         644                 ASCII
            pukiwiki.ini.php     644                 ASCII
            pukiwiki.skin.en.php 644                 ASCII
            pukiwiki.skin.ja.php 644                 ASCII
            en.lng               644                 ASCII
            ja.lng               644                 ASCII
            pukiwiki.gif         644                 BINARY

            ディレクトリ         パーミッション
            wiki                 777
            diff                 777
            backup               777
            plug-in              777

   データのバックアップ方法

            データファイルディレクトリ以下をバックアップすればよい。
            (ディフォルトディレクトリ名は wiki )

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
    *   連続した複数行はフィルされて表示されます。

    *   空行は段落`<p>'の区切りとなります。

    *   HTMLのタグは書けません。

    *   ''ボールド''のようにシングルクォート二つではさむと、
        ボールド`<b>'になります。

    *   '''イタリック'''のようにシングルクォート三つではさむと、
        イタリック`<i>'になります。

    *   ----のようにマイナス4つがあると、 水平線`<hr>'になります。

    *   行を*ではじめると、 大見出し`<h2>'になります。

    *   行を**ではじめると、 中見出し`<h3>'になります。

    *   行を***ではじめると、 小見出し`<h3>'になります。

    *   #contents を行頭に書くと、大見出しと小見出しの目次が作成されます。 

    *   行をマイナス-ではじめると、 箇条書き`<ul>'になります。
        マイナスの数が増えるとレベルが下がります（3レベルまで）

            -項目1
            --項目1-1
            --項目1-2
            -項目2
            -項目3
            --項目3-1
            ---項目3-1-1
            ---項目3-1-2
            --項目3-2

    *   コロンを使うと、 用語と解説文のリスト`<dl>'が書けます。

            :用語1:いろいろ書いた解説文1
            :用語2:いろいろ書いた解説文2
            :用語3:いろいろ書いた解説文3

    *   行頭から | で文字列を区切ると表組みになります。

            |''Category:A''|''Category:B''|''Category:C''|
            |Objective|for AI|Other|
            |Java|LISP|Assembla|

    *   リンク

        *   LinkToSomePageやFrontPageのように、
            英単語の最初の一文字を大文字にしたものが
            二つ以上連続したものはPukiWikiのページ名となり、
            それが文章中に含まれるとリンクになります。

        *   二重の大かっこ[[ ]]でくくった文字列も、
            PukiWikiのページ名になります。
            大かっこの中にはスペースを含めてはいけません。
            日本語も使えます。

        *   また、[[factage:http://factage.com/]] のようにすると factage の文字に
            http://factage.com/ へのリンクが貼れます。

        *   [[サーバ名:WikiName]] のようにすると InterWikiName になります。

        *   http://factage.com/sng/ のようなURLは自動的にリンクになります。

        *   sng@factage.com のようなメールアドレスも自動的にリンクになります。

    *   行頭がスペースやタブで始まっていると、
        それは整形済みの段落`<pre>'として扱われます。
        プログラムの表示などに使うと便利です。

    *   行を > ではじめると、 引用文`<blockquote>'が書けます。
        >の数が多いとインデントが深くなります（3レベルまで）。

            >文章
            >文章
            >>さらなる引用
            >文章

    *   行を // で始めるとコメントアウト`<!-- -->'が書けます。

    *   #comment を行頭に書くとコメントを挿入できるフォームが埋め込まれます。

    *   #related を書くと、現在のページ名を含む別のページ(関連ページ)へのリンクを表示します。 

    * #norelated を行頭に書くと、そのページの一番下に表示される関連ページを非表示にします。 

    * #calendar_read(200202) を行頭に書くと、その日付のページを表示するカレンダーが表示されます。括弧内は年月を表しますが、省略すると現在の年月が使用されます。(日記向け) 

    * #calendar_edit(200202) を行頭に書くと、その日付のページを編集するカレンダーが表示されます。括弧内は年月を表しますが、省略すると現在の年月が使用されます。(日記向け) 

    *   その他、pukiwiki.php を編集することにより他のルールをスクリプト設置者が定義できます。

InterWiki
    1.11 からInterWikiが実装されました。

    InterWiki とは、Wikiサーバーをつなげる機能です。最初はそうだったんで InterWiki という
    名前なのだそうですが、今は、Wikiサーバーだけではなくて、いろんなサーバーを引けます。
    なかなか便利です。そうなると InterWiki という名前はあまり機能を表していないことに
    なります。 この機能は Tiki からほぼ完全に移植しています。

  サーバーリストへの追加
    InterWikiName のページに以下のようにサーバの定義をする。 

    *   [URL サーバ名] タイプ
    *   [http://factage.com/sng/pukiwiki/pukiwiki.php?read&page= sng] pw


  InterWikiNameの追加 
    サーバ名:WikiNameをBracketNameで作ればInterWikiNameの完成 

    *   [[サーバ名:WikiName]]
    *   [[sng:FrontPage]]

  WikiNameの挿入位置 
    要求しようとするURLへのWikiNameの挿入位置を $1 で指定することができます。
    省略するとお尻にくっつきます。 

    *   [http://factage.com/sng/pukiwiki/pukiwiki.php?backup&page=$1&age=1 sng] pw


  文字コード変換タイプ 
    PukiWikiページ以外にも飛ばせます。日本語をURLに含む可能性もあるのでその場合の
    エンコーディングの指定をタイプとして指定できます。 

    *   [http://factage.com/sng/pukiwiki/pukiwiki.php?read&page=$1 sng] pw


    *   std 省略時
        *   内部文字エンコーディング(標準はSJIS)のままURLエンコードします。 

    *   raw asis
        *   URLエンコードしないでそのまま使用。 

    *   sjis
        *   文字列をSJISに変換し、URLエンコードします。(mb_stringのSJISへのエイリアスです) 

    *   euc
        *   文字列を日本語EUCに変換し、URLエンコードします。(mb_stringのEUC-JPへのエイリアスです) 

    *   utf8
        *   文字列をUTF-8に変換し、URLエンコードします。(mb_stringのUTF-8へのエイリアスです) 

    *   yw
        *   YukiWiki系へのエンコーディング。 

    *   moin
        *   MoinMoin用に変換します。 

    *   その他、PHP4のmb_stringでサポートされている以下のエンコード文字が使用できます。 

        *   UCS-4, UCS-4BE, UCS-4LE, UCS-2, UCS-2BE, UCS-2LE, UTF-32, UTF-32BE, UTF-32LE, UCS-2LE, UTF-16, UTF-16BE, UTF-16LE, UTF-8, UTF-7, ASCII, EUC-JP, SJIS, eucJP-win, SJIS-win, ISO-2022-JP, JIS, ISO-8859-1, ISO-8859-2, ISO-8859-3, ISO-8859-4, ISO-8859-5, ISO-8859-6, ISO-8859-7, ISO-8859-8, ISO-8859-9, ISO-8859-10, ISO-8859-13, ISO-8859-14, ISO-8859-15, byte2be, byte2le, byte4be, byte4le, BASE64, 7bit, 8bit, UTF7-IMAP 


  YukiWiki系へのエンコーディング 

    *   WikiNameのものへはそのままURLエンコード。 
    *   BracketNameのものは[[ ]]を付加してURLエンコード。 

RDF/RSS
    1.2.1から、RecentChangesのRDF/RSSを出力できるようになりました。
    実用できるかはわからないですが、将来何かに使えれば、と思ってます。

  RSS 0.91 の出力方法の例

    *   http://factage.com/sng/pukiwiki/pukiwiki.php?rss

  RSS 1.0 の出力方法の例

    *   http://factage.com/sng/pukiwiki/pukiwiki.php?rss10

更新履歴
    *   2002-03-18 1.3

        ある文字列へWikiName/BracketNameへのリンクを貼る。(エイリアス機能)
        疑似ディレクトリ構想。./ や ../ などをBracketNameとして使用することで実現。 
        カレンダー機能で、prefixを指定できるようにする。
        Tiki:TikiPluginSandBoxにあるような対話型InterWiki(lookup)。
        多言語化に対応できるように、各種メッセージなどを編集可能にする。 
        ページに添付ファイルを添付することができる。
        一部の整形ルールをプラグイン化する。
        Win32でも正常に動作するように修正

    *   2002-02-15 1.2.12

        バックアップの挙動の変更 
        現在表示しているページのみのバックアップ一覧を表示する 
        現在表示しているページにバックアップがなければ、すべてのページのものを表示 
        バックアップ差分を、前回のバックアップとの差分に 
        ファイル名一覧の追加 
        タイムスタンプを変更しないチェックボックスの追加 
        更新の衝突のチェックにMD5でチェックサムを使うように変更 
        コメント挿入時、行頭ではない#comment部分に挿入してしまうバグを修正 
        patさんの要望により、表組みルールを追加 
        patさんの要望によりHTMLコメントアウトルールを追加 
        kawara?さんの要望により見出しを一つ増やした 
        #norelated を行頭に書くと関連ページを表示しないルールを追加 
        関連ページの区切り文字を整形ルール用と分けた 

    *   2002-02-09 1.2.11 関連リンク常時表示機能、経過時間表示機能、セキュリティ対策、コマンドを cmd= に修正。その他バグ修正。 

    *   2002-02-09 1.2.1 バグ修正、高速化、RDF/RSS(1.0,0.91)の実装。

    *   2002-02-07 1.2.0 設定ファイルを外部へ、InterWiki搭載、関連ページルール、注釈ルール、httpリンクルール、バグ修正。

    *   2002-02-05 1.10 スキン機能、コメント挿入、見出し目次作成、その他バグ修正。

    *   2002-02-01 1.07 追加機能、ユーザ定義ルール、単語AND/OR検索の実装。

    *   2001-01-22 1.06 ページ編集時エラーの修正。ページタイトルの[[]]も取り除くように。

    *   2001-12-12 1.05 差分アルゴリズムの修正、自動バックアップ機能追加。

    *   2001-12-10 1.01 メールアドレスリンクの不備の修正(thanks to s.sawada)

    *   2001-12-05 1.00 正式公開。検索結果からのハイライト表示機能の削除。

    *   2001-11-29 0.96 またまたいくつかのバグの修正。差分の追加。まだまだ未完、とりあえず。 

    *   2001-11-28 0.94 いくつかのバグの修正。日付・時刻挿入ルールの追加。 

    *   2001-11-27 0.93 コードの清書。検索結果からのページ表示時ハイライト表示。 

    *   2001-11-26 0.92 データファイル名を YukiWiki と共通の変換方法にした。 

    *   2001-11-25 0.91 即日にして単語検索機能が追加。差分は結構かかりそう。 

    *   2001-11-25 0.90 一応公開。YukiWiki の検索と差分はまだ。

TODO
        - 予定なし、これから実装される YukiWiki の機能を移植

作者
        Copyright (C) 2001,2002 by sng.
        sng <sng@factage.com>
        http://factage.com/sng/
        http://factage.com/sng/pukiwiki/

    質問、意見、バグ報告は sng@factage.com にメールしてください。

配布条件
    PukiWikiは、 GNU General Public Licenseにて公開します。

    PukiWikiはフリーソフトです。 ご自由にお使いください。

謝辞
    YukiWiki のクローン化を許可していただいた結城浩さんに感謝します。

    本家のWikiWikiを作ったCunningham & Cunningham, Inc.に 感謝します。

参照リンク
    *   sngのホームページ http://factage.com/sng/

    *   PukiWikiホームページ http://factage.com/sng/pukiwiki/

    *   結城浩さんのホームページ http://www.hyuki.com/

    *   YukiWikiホームページ http://www.hyuki.com/yukiwiki/

    *   Tiki http://todo.org/cgi-bin/jp/tiki.cgi

    *   本家のWikiWiki http://c2.com/cgi/wiki?WikiWikiWeb

    *   本家のWikiWikiの作者(Cunningham & Cunningham, Inc.) http://c2.com/
