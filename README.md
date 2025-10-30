SecuLearn - 簡易マニュアル

1. データベース初期化
   - database/init.sql を MySQL に読み込んでください（users テーブルや初期ユーザーを作成）。
   - admin 用に 'admin' ユーザーを作成しておくと管理画面が使えます。

2. 設定変更
   - include/db_connect.php の DB_HOST / DB_USER / DB_PASS / DB_NAME を環境に合わせて修正します。

3. 配置
   - XAMPP の htdocs に SecuLearn フォルダを置くか、任意の Apache/PHP 環境に設置。

4. 起動と動作確認
   - ブラウザで http://localhost/SecuLearn/index.php を開く。
