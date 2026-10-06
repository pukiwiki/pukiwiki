(function () {
    var form = document.querySelector('._plugin_edit_edit_form');
    if (!form) return;
    var msg = form.querySelector('textarea[name="msg"]');
    if (!msg) return;
    var status = document.getElementById('autosave_status');
    var lastContent = msg.value;

    // Get localized messages (set by html.php)
    var messages = window.AUTOSAVE_MESSAGES || {
        saving: 'Saving...',
        saved: 'Auto-saved at ',
        failed: 'Auto-save failed',
        error: 'Auto-save error'
    };

    var isSaving = false; // 保存中フラグ

    // トースト通知を表示する関数
    function showAutosaveNotification(message) {
        // 既存の通知があれば削除
        var existingToast = document.getElementById('autosave_toast');
        if (existingToast) {
            existingToast.remove();
        }

        // トースト要素を作成
        var toast = document.createElement('div');
        toast.id = 'autosave_toast';
        toast.style.cssText = 'position: fixed; top: 20px; right: 20px; ' +
            'background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d; ' +
            'padding: 15px 20px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); ' +
            'z-index: 10000; font-size: 14px; animation: slideIn 0.3s ease-out;';
        toast.textContent = message;

        // アニメーションのスタイルを追加
        if (!document.getElementById('autosave_toast_style')) {
            var style = document.createElement('style');
            style.id = 'autosave_toast_style';
            style.textContent = '@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }' +
                '@keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }';
            document.head.appendChild(style);
        }

        document.body.appendChild(toast);

        // 3秒後にフェードアウトして削除
        setTimeout(function () {
            toast.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(function () {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }, 3000);
    }

    setInterval(function () {
        // 既存下書き通知が表示されている場合は自動保存しない（問題#2対策）
        var draftNotice = document.getElementById('draft_notice');
        if (draftNotice) return;

        // 保存中の場合はスキップ
        if (isSaving) return;

        if (msg.value !== lastContent && msg.value.length > 0) {
            // 問題#1対策: FormData(form)ではなく、必要最小限のフィールドのみを送信
            // preview/write等のsubmitボタン値を含めないことで、plugin/edit.inc.phpの
            // 分岐処理が正しくdraft_saveを検出できるようにする

            // 必須フィールドの存在チェック
            var pageInput = form.querySelector('input[name="page"]');
            var digestInput = form.querySelector('input[name="digest"]');
            var ticketInput = form.querySelector('input[name="ticket"]');

            if (!pageInput || !digestInput || !ticketInput) {
                console.error('Autosave: Required form fields not found');
                return;
            }

            isSaving = true; // 保存開始
            var contentToSave = msg.value; // 保存開始時点の内容を記録
            var formData = new FormData();
            formData.append('cmd', 'edit');
            formData.append('page', pageInput.value);
            formData.append('msg', contentToSave);
            formData.append('digest', digestInput.value);
            formData.append('ticket', ticketInput.value);
            formData.append('draft_save', '1');

            if (status) status.textContent = messages.saving;

            fetch(form.action, {
                method: 'POST',
                body: formData
            }).then(function (response) {
                if (response.ok) {
                    // レスポンステキストをチェックして実際の成功/失敗を判定
                    return response.text().then(function (text) {
                        // 成功時は.alert-successが含まれる
                        if (text.indexOf('alert-success') !== -1) {
                            var now = new Date();
                            var hours = now.getHours() < 10 ? '0' + now.getHours() : now.getHours();
                            var minutes = now.getMinutes() < 10 ? '0' + now.getMinutes() : now.getMinutes();
                            var time = hours + ':' + minutes;
                            if (status) status.textContent = messages.saved + time;
                            lastContent = contentToSave; // 保存開始時点の内容で更新

                            // トースト通知を表示
                            showAutosaveNotification(messages.saved + time);
                        } else {
                            // 失敗時は.alert-dangerが含まれる
                            if (status) status.textContent = messages.failed;
                        }
                    });
                } else {
                    if (status) status.textContent = messages.failed;
                }
            }).catch(function (err) {
                console.error(err);
                if (status) status.textContent = messages.error;
            }).finally(function () {
                isSaving = false; // 保存完了（成功/失敗に関わらず）
            });
        }
    }, 30000); // 30秒ごとに自動保存
})();
