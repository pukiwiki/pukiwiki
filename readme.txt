NAME
    PukiWiki - 自由にページを追加・削除・編集できるWebページ構築PHPスクリプト

        PukiWiki 1.3.8rc1
        Copyright (C) 2001-2004 by sng, PukiWiki Developers Team
	License is GNU/GPL.
	Based on "PukiWiki" 1.3 by yu-ji
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
    1.3まではyu-ji(sng)さんが作成し、1.3.1b以降はPukiWiki Developers Team
    によって開発が続けられています。

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

    3.  アーカイブの内容をサーバに転送します。
        ファイルの転送モードについては次項を参照してください。

    4.  pukiwiki.ini.php内で指定した以下のディレクトリを作成します。

        データの格納ディレクトリ               (デフォルトはwiki)
        差分ファイル格納ディレクトリ           (デフォルトはdiff)
        バックアップファイル格納ディレクトリ   (デフォルトはbackup)
        キャッシュファイル格納ディレクトリ     (デフォルトはcache)
        添付ファイル格納ディレクトリ           (デフォルトはattach)
        カウンタファイル格納ディレクトリ       (デフォルトはcounter)

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
        image          755
        image/face     755
        plugin         755
        skin           755
        wiki           777

        ファイル       パーミッション 転送モード
        *.php          644            ASCII
        *.lng          644            ASCII

        cache/*        666            ASCII
        image/*        644            BINARY
        image/face/*   644            BINARY
        plugin/*       644            ASCII
        skin/*         644            ASCII
        wiki/*         666            ASCII

データのバックアップ方法

        データファイルディレクトリ以下をバックアップします。
        (デフォルトディレクトリ名は wiki)

        必要に応じて他のディレクトリの内容をバックアップします。
        (デフォルトディレクトリ名は attach,backup,counter,cache,diff)

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

    *   行をプラス+ではじめると、 箇条書き`<ol>'になります。
        プラスの数が増えるとレベルが下がります（3レベルまで）

            +項目1
            ++項目1-1
            ++項目1-2
            +項目2
            +項目3
            ++項目3-1
            +++項目3-1-1
            +++項目3-1-2
            ++項目3-2

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

        *   また、[[pukiwiki:http://pukiwiki.org/]] のようにすると factage の文字に
            http://pukiwiki.org/ へのリンクが貼れます。

        *   [[サーバ名:WikiName]] のようにすると InterWikiName になります。

        *   http://pukiwiki.org/ のようなURLは自動的にリンクになります。

        *   team@pukiwiki.org のようなメールアドレスも自動的にリンクになります。

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

更新履歴
    *   2004-04-04 1.3.7 by PukiWiki Developers Team
        BugTrack/547 renameプラグインが正常に動作しない?
            [[cvs:plugin/rename.inc.php]](v1.3:r1.1.2.1)
        BugTrack/566 [cvs] $Id が無い/二つある (マイグレーション作業向け) 
            [[cvs:skin/default.en.css]](v1.3:r1.13.2.1)
            [[cvs:skin/default.ja.css]](v1.3:r1.14.2.1)
            [[cvs:skin/default.js]](v1.3:r1.1.2.1)
            [[cvs:skin/pukiwiki.skin.en.php]](v1.3:r1.14.2.4)
            [[cvs:skin/pukiwiki.skin.ja.php]](v1.3:r1.15.2.4)
            [[cvs:plugin/recent.inc.php]](v1.3:r1.5.2.1)
            [[cvs:plugin/showrss.inc.php]](v1.3:r1.1.2.2)
            [[cvs:en.lng]](v1.3:r1.8.2.2)
            [[cvs:ja.lng]](v1.3:r1.10.2.1)
            [[cvs:make_link.php]](v1.3:r1.6.2.1)
        デフォルトの$adminpassが md5("pass") ではなかった 
            [[cvs:pukiwiki.ini.php]](v1.3:r1.16.2.2)

    *   2003-11-10 1.3.6 by PukiWiki Developers Team
        BugTrack/278 配布ファイル展開だけで必須ディレクトリを作成する
        BugTrack/351 imgプラグインでインライン要素のタグにclear属性が
                     指定されている
        BugTrack/362 記号で始まるページがあると、一覧で記号パートの
                     </ul>がなくてずれる
        BugTrack/372 img.inc.phpのURL文字列精査の誤りで画像が出ない
        BugTrack/386 memoプラグインにXSS脆弱性
        BugTrack/387 pulgin/anchor.inc.php にスクリプト混入問題
        BugTrack/442 ポート80以外の場合のRSS
        BugTrack/458 #contents利用時ユーザ定義が表示される
        BugTrack/479 CGI版PHPの場合、HTTPSで利用できない
        BugTrack/486 headerでキャッシュ無効を
        InterWikiNameページ修正(PukiWiki.org => utf8)

    *   2003-05-28 1.3.5 by PukiWiki Developers Team
        XSS脆弱性をfix
        その他バグ修正

    *   2003-03-15 1.3.4 by PukiWiki Developers Team
        重要なセキュリティ上の問題をfix(BugTrack/210 null byte attack)
        XSS脆弱性を多数fix
        その他いくつかのバグを修正
        :heart:は廃止されました。代わりに &heart;を使用してください。

    *   2002-12-04 1.3.3 by PukiWiki Developers Team

        make_link()を独立
        LEFT:/CENTER:/RIGHT:書式をconvert_html()内で処理
        以前のリリースに含まれていたXSS脆弱性を修正。
        その他多数のバグを修正
        いくつかのプラグインを同梱

    *   2002-07-15 1.3.2 by PukiWiki Developers Team

        開発をPukiWiki Developers Team (http://pukiwiki.org/)に移行して初めてのリリース。
        以前のリリースに含まれていたXSS脆弱性を修正。
        HTMLがHTML4.01に準拠するように修正
        その他多数のバグを修正

    *   2002-06-10 1.3.1beta MASUI'z Edition

        PukiWiki 1.3をベースに、MASUIが勝手にプラグインとかまとめてみました。
        ソースファイルを分割。
        calendar2, include, article, memo, aname, anchor, counter, vote, ls, yetlist, recent, source, imgプラグインを添付。
        attach, commentプラグインバージョンアップ。
        本文に、タグが入っていた場合、編集がうまくできなかった不具合を修正。
        更新衝突時にdiffアルゴリズムで差分をとり、マージを行う様に変更。
	&amp; &lt;などを含んだ文章を編集すると、それが消えてしまう場合がある不具合を修正。
	自動テンプレート機能を追加。[[SandBox/template]]
        ソースファイルを分割。

    *   2002-03-18 1.3 by sng.

        ある文字列へWikiName/BracketNameへのリンクを貼る。(エイリアス機能)
        疑似ディレクトリ構想。./ や ../ などをBracketNameとして使用することで実現。 
        カレンダー機能で、prefixを指定できるようにする。
        Tiki:TikiPluginSandBoxにあるような対話型InterWiki(lookup)。
        多言語化に対応できるように、各種メッセージなどを編集可能にする。 
        ページに添付ファイルを添付することができる。
        一部の整形ルールをプラグイン化する。
        Win32でも正常に動作するように修正

    *   2002-02-15 1.2.12 by sng.

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

    *   2002-02-09 1.2.11 by sng. 関連リンク常時表示機能、経過時間表示機能、セキュリティ対策、コマンドを cmd= に修正。その他バグ修正。 

    *   2002-02-09 1.2.1 by sng. バグ修正、高速化、RDF/RSS(1.0,0.91)の実装。

    *   2002-02-07 1.2.0 by sng. 設定ファイルを外部へ、InterWiki搭載、関連ページルール、注釈ルール、httpリンクルール、バグ修正。

    *   2002-02-05 1.10 by sng. スキン機能、コメント挿入、見出し目次作成、その他バグ修正。

    *   2002-02-01 1.07 by sng. 追加機能、ユーザ定義ルール、単語AND/OR検索の実装。

    *   2001-01-22 1.06 by sng. ページ編集時エラーの修正。ページタイトルの[[]]も取り除くように。

    *   2001-12-12 1.05 by sng. 差分アルゴリズムの修正、自動バックアップ機能追加。

    *   2001-12-10 1.01 by sng. メールアドレスリンクの不備の修正(thanks to s.sawada)

    *   2001-12-05 1.00 by sng. 正式公開。検索結果からのハイライト表示機能の削除。

    *   2001-11-29 0.96 by sng. またまたいくつかのバグの修正。差分の追加。まだまだ未完、とりあえず。 

    *   2001-11-28 0.94 by sng. いくつかのバグの修正。日付・時刻挿入ルールの追加。 

    *   2001-11-27 0.93 by sng. コードの清書。検索結果からのページ表示時ハイライト表示。 

    *   2001-11-26 0.92 by sng. データファイル名を YukiWiki と共通の変換方法にした。 

    *   2001-11-25 0.91 by sng. 即日にして単語検索機能が追加。差分は結構かかりそう。 

    *   2001-11-25 0.90 by sng. 一応公開。YukiWiki の検索と差分はまだ。

TODO
        - http://pukiwiki.org/?BugTrack

作者
        PukiWiki 1.3.3 by PukiWiki Developers Team
         Copyright (C) 2002 by sng & PukiWiki Developers Team
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
