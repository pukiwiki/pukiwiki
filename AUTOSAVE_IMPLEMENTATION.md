# 自動保存機能の実装と修正 (v1.2.1)

## 実装内容

### 新機能
- **自動保存機能**: 編集中の内容を60秒ごとに自動的に下書きとして保存
- **国際化対応**: 日本語・英語のメッセージを動的に読み込み
- **UIフィードバック**: 保存状態をリアルタイムで表示

### 修正した問題

#### 🔴 重大な問題（3件）
1. **JavaScriptの変数スコープ問題**
   - `var AUTOSAVE_MESSAGES` → `window.AUTOSAVE_MESSAGES`に変更
   - 国際化が正常に機能するようになった

2. **msgのnullチェック不足**
   - `textarea[name="msg"]`の存在チェックを追加
   - JavaScriptエラーを防止

3. **lastContentの更新タイミング問題**
   - 保存開始時点の内容を`contentToSave`に保存
   - 保存中にユーザーが入力を続けても、次回の自動保存で正しく検出される

#### 🟡 中程度の問題（5件）
4. **既存下書き通知の検出が不正確**
   - `.alert-info` → `#draft_notice`に変更
   - より正確に既存下書き通知を検出

5. **必須フィールドの存在チェック不足**
   - `page`, `digest`, `ticket`フィールドの存在チェックを追加
   - フォーム構造の変更に対してより堅牢に

6. **自動保存の成功判定が不正確**
   - レスポンステキストに`alert-success`が含まれているかをチェック
   - 実際の保存成功/失敗を正確に判定

7. **保存中に再度保存が試みられる**
   - `isSaving`フラグを追加
   - 同時実行を防止

8. **時刻表示の時間部分にゼロパディングがない**
   - 時間部分にもゼロパディングを追加（例: "09:05"）

### TODO.mdで指摘された問題の修正

#### 問題#1: FormData送信フィールドの限定
- `new FormData(form)` → 必要最小限のフィールド（`cmd`, `page`, `msg`, `digest`, `ticket`, `draft_save`）のみを手動で追加
- `preview`/`write`等のsubmitボタン値を含めないことで、`plugin/edit.inc.php`の分岐処理が正しく`draft_save`を検出

#### 問題#2: 既存下書きの無確認上書き防止
- 既存下書き通知（`#draft_notice`）が表示されている間は自動保存を停止
- ユーザーが下書きを読み込むまで自動保存を延期

## 変更ファイル

### JavaScript
- `skin/js/autosave.js` (新規作成)
  - 自動保存ロジックの実装
  - エラーハンドリング
  - 国際化対応

### PHP
- `lib/html.php`
  - `window.AUTOSAVE_MESSAGES`の設定
  - `#draft_notice`のID追加
  - `autosave.js`の読み込み

### 言語ファイル
- `ja.lng.php` (既存の変数を使用)
  - `$_msg_draft_autosave_saving`
  - `$_msg_draft_autosave_saved`
  - `$_msg_draft_autosave_failed`
  - `$_msg_draft_autosave_error`

- `en.lng.php` (既存の変数を使用)
  - 同上（英語版）

## 技術仕様

- **自動保存間隔**: 60秒
- **対象ブラウザ**: モダンブラウザ（Chrome, Firefox, Safari, Edge）
- **依存関係**: Vanilla JavaScript（jQueryなし）
- **セキュリティ**: CSRF保護（ticketフィールド）、権限チェック（check_editable）

## 検証済み項目

- ✅ セキュリティ: CSRF保護、権限チェック完備
- ✅ 国際化: 日本語・英語の両方で動作
- ✅ エラーハンドリング: 適切に実装
- ✅ PHP構文エラー: なし
- ✅ 堅牢性: エッジケースに対応

## 既知の制限事項

- IE11は非サポート（`fetch` APIと`Promise.finally()`を使用）
- セッションタイムアウト後は自動保存が失敗（PukiWiki全体の問題）
