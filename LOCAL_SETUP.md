# PukiWiki Draft機能 ローカル環境セットアップガイド

## 概要

このドキュメントは、macOS環境でPukiWiki Draft機能をローカルで動作検証するためのセットアップ手順を説明します。

## 前提条件

- **OS**: macOS 10.15以上
- **PHP**: 8.3以上（8.4推奨、PHP 9.0対応も視野）
- **Webサーバー**: Apache 2.4またはnginx
- **ブラウザ**: 最新版のChrome/Safari/Firefox

## 方法1: Homebrew + PHP-FPM（推奨）

### 1.1 PHPのインストール

```bash
# Homebrewの更新
brew update

# PHP 8.4のインストール
brew install php@8.4

# PHPのバージョン確認
php -v

# 結果例:
# PHP 8.4.0 (cli) (built: Nov 21 2024 10:00:00) ( NTS )
```

### 1.2 PHP-FPMの起動

```bash
# PHP-FPMをLaunchAgentとして登録
brew services start php@8.4

# 起動確認
brew services list | grep php

# 結果例:
# php@8.4       started
```

### 1.3 Apacheの設定

```bash
# Apache用PHP-FPMモジュール設定
sudo cp /usr/local/etc/httpd/extra/httpd-default.conf.bak /usr/local/etc/httpd/extra/httpd-default.conf.bak.original 2>/dev/null || true

# /usr/local/etc/httpd/httpd.confを編集
sudo vim /usr/local/etc/httpd/httpd.conf
```

`httpd.conf`に以下を追加：

```apache
# PHP-FPM設定
<IfModule proxy_fcgi_module>
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/usr/local/var/run/php-fpm.sock|fcgi://localhost"
    </FilesMatch>
</IfModule>

# PukiWiki用VirtualHost
<VirtualHost *:80>
    ServerName pukiwiki.local
    ServerAlias www.pukiwiki.local
    DocumentRoot "/Users/tgoto/Library/Mobile Documents/com~apple~CloudDocs/my web site/pukiwiki_draft2"

    <Directory "/Users/tgoto/Library/Mobile Documents/com~apple~CloudDocs/my web site/pukiwiki_draft2">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ログ設定
    ErrorLog "/var/log/apache2/pukiwiki_error.log"
    CustomLog "/var/log/apache2/pukiwiki_access.log" combined
</VirtualHost>
```

### 1.4 ホスト設定

```bash
# /etc/hostsを編集
sudo vim /etc/hosts

# 以下を追加:
# 127.0.0.1   pukiwiki.local
# ::1         pukiwiki.local
```

### 1.5 Apacheの起動

```bash
# Apacheの起動
sudo apachectl start

# ステータス確認
sudo apachectl status

# 設定エラーチェック
sudo apachectl configtest

# 結果: "Syntax OK" が表示されれば成功
```

## 方法2: PHP組み込みサーバー（簡易版）

より簡単なセットアップ方法です。本格的なテストには方法1を推奨します。

```bash
# プロジェクトディレクトリに移動
cd "/Users/tgoto/Library/Mobile Documents/com~apple~CloudDocs/my web site/pukiwiki_draft2"

# PHP組み込みサーバーを起動
php -S localhost:8000

# アクセス: http://localhost:8000
```

## PukiWiki設定の確認

### 2.1 必要な設定

[pukiwiki.ini.php](pukiwiki.ini.php)に以下の設定があることを確認：

```php
// Draft機能用ディレクトリ定義
define('DRAFT_DIR', DATA_DIR . 'draft/');

// 読み取り専用モードの確認
define('PKWK_READONLY', 0);  // 編集を許可

// 認証設定（オプション）
$edit_auth = 0;  // 認証なしで編集可能（テスト用）
```

### 2.2 パーミッション設定

```bash
# draftディレクトリのパーミッション確認・設定
chmod 777 draft/
ls -ld draft/

# 結果例:
# drwxrwxrwx  4 tgoto  staff  128 Nov 22 12:54 draft/

# その他の書き込み可能ディレクトリ
chmod 777 wiki/ attach/ backup/ cache/ counter/ diff/

# データディレクトリ全体の確認
ls -ld wiki/ attach/ backup/ cache/ counter/ diff/ draft/
```

## アクセステスト

### 3.1 PukiWikiへのアクセス

```
http://pukiwiki.local/
```

または PHP組み込みサーバーの場合：
```
http://localhost:8000/
```

### 3.2 ステータス確認

ブラウザで以下にアクセス：

```
http://pukiwiki.local/?cmd=help
```

Draft機能が利用可能か確認します。

## 動作検証チェックリスト

### 4.1 基本機能テスト

- [ ] **下書き保存**
  1. `http://pukiwiki.local/?cmd=edit&page=TestPage` にアクセス
  2. テキストを入力
  3. 「下書き保存」ボタンをクリック
  4. 確認メッセージが表示される
  5. `draft/` ディレクトリにファイルが作成される

- [ ] **下書き読み込み**
  1. 同じページの編集画面を再度開く
  2. 「下書きが保存されています」という通知が表示される
  3. 「下書きから復帰」ボタンで内容を読み込める

- [ ] **下書き一覧**
  1. `http://pukiwiki.local/?cmd=draft` にアクセス
  2. 保存した下書きが一覧表示される
  3. 各下書きの保存日時が正しく表示される

- [ ] **下書き削除**
  1. 下書き一覧から「削除」ボタンをクリック
  2. 確認ダイアログで「OK」をクリック
  3. 下書きが削除される

- [ ] **下書き公開**
  1. 下書き一覧から「公開」ボタンをクリック
  2. 本文がない場合、そのまま公開される
  3. 本文がある場合、競合ダイアログが表示される

### 4.2 セキュリティテスト

- [ ] **CSRF保護**
  1. 下書き操作時にチケット検証が実施される
  2. 無効なチケットでのリクエストは拒否される
  3. POST以外のメソッドでは拒否される

- [ ] **権限チェック**
  1. 編集不可能なページの下書きは操作できない
  2. 削除対象の下書きに対して権限チェックが実施される

- [ ] **XSS対策**
  1. HTMLタグを含む内容を下書き保存
  2. ブラウザで実行されないことを確認
  3. 下書き一覧表示時も安全に表示される

### 4.3 PHP互換性テスト

```bash
# PHP 8.3でのテスト
php -v

# ファイルの構文チェック
php -l lib/draft.php
php -l plugin/draft.inc.php
php -l plugin/edit.inc.php

# 結果: "No syntax errors detected in..." が表示される
```

### 4.4 ブラウザ互換性テスト

以下のブラウザでテストすることを推奨：

- [ ] Chrome（最新版）
- [ ] Safari（最新版）
- [ ] Firefox（最新版）
- [ ] Edge（Windows環境がある場合）

## トラブルシューティング

### 問題: PHP-FPMが起動しない

```bash
# ログを確認
tail -f /usr/local/var/log/php-fpm.log

# PHP-FPMを再起動
brew services restart php@8.4
```

### 問題: Apacheが起動しない

```bash
# 設定エラーを確認
sudo apachectl configtest

# エラーログを確認
tail -f /var/log/apache2/error_log
tail -f /var/log/apache2/pukiwiki_error.log
```

### 問題: draftディレクトリにファイルが作成されない

```bash
# パーミッション確認
ls -ld draft/

# Apache実行ユーザーで書き込み可能か確認
ls -la draft/

# 必要に応じてパーミッション変更
chmod 777 draft/

# Apache実行ユーザー確認
ps aux | grep apache
```

### 問題: 下書きが読み込めない

1. `draft/` ディレクトリ内にファイルが存在するか確認
2. ファイル名がページ名の16進数エンコード形式か確認
3. PHPエラーログを確認
   ```bash
   tail -f /var/log/php-fpm.log
   ```

### 問題: Apacheの起動に権限エラー

```bash
# sudoで起動
sudo apachectl start

# ポート80の確認（別プロセスが使用していないか）
lsof -i :80

# 代替案：カスタムポート（8080）を使用
# httpd.conf の Listen を "Listen 8080" に変更
```

## パフォーマンステスト（オプション）

### 5.1 複数ページでの同時編集テスト

```bash
# 複数のテストページを作成・編集
for i in {1..5}; do
  curl "http://pukiwiki.local/?cmd=edit&page=TestPage$i" -d "msg=Test%20Content%20$i&draft_save=true"
done
```

### 5.2 ファイルロックテスト

```bash
# 同時にdraft/ディレクトリへのアクセスがデッドロックしないか確認
ab -n 100 -c 10 "http://pukiwiki.local/?cmd=draft"
```

## ログ確認

### 6.1 Apache アクセスログ

```bash
tail -f /var/log/apache2/pukiwiki_access.log
```

### 6.2 Apache エラーログ

```bash
tail -f /var/log/apache2/pukiwiki_error.log
```

### 6.3 PHP-FPM ログ

```bash
tail -f /usr/local/var/log/php-fpm.log
```

## クリーンアップ

作業完了後、以下コマンドで環境を停止：

```bash
# PHP-FPMの停止
brew services stop php@8.4

# Apacheの停止
sudo apachectl stop
```

## 参考リンク

- [PukiWiki公式](https://pukiwiki.osdn.jp/)
- [PHP公式](https://www.php.net/)
- [Apache公式](https://httpd.apache.org/)
- [Homebrew PHP](https://formulae.brew.sh/formula/php@8.4)

---

**作成日**: 2025-11-22
**バージョン**: 1.0.0
