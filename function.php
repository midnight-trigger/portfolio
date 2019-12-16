<?php
ini_set('display_errors', "On");

session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime ', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();// DB接続

// 定数管理
define('MAX_FILE_SIZE', 1 * 1024 * 1024);
define("MSG1", '入力内容が正しくありません');
define("MSG2", 'パスワードが正しく入力されていません');
define("MSG3", '既に登録済みのメールアドレスです');
define("MSG4", '正常に処理が完了しました');

$err_msg = [];

function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function dbConnect() {
  $dsn = 'mysql:dbname=output1;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = [
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  ];
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

function queryPost($dbh, $sql, $data = []) {
  $stmt = $dbh->prepare($sql);
  if (!$stmt->execute($data)) {
    return false;
  }
  return $stmt;
}

// Password reminder
function random ($length = 6) {
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
}

// Upload image
function uploadImg($file) {
  try {
    if (isset($file['error']) && is_int($file['error'])) {
      switch ($file['error']) {
        case UPLOAD_ERR_OK: // OK
        break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択の場合
        throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズが超過した場合
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
        throw new RuntimeException('ファイルサイズが大きすぎます');
        default: // その他の場合
        throw new RuntimeException('その他のエラーが発生しました');
      }

      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        throw new RuntimeException('画像形式が未対応です');
      }

      $path = 'upload/' . sha1_file($file['tmp_name']) . image_type_to_extension($type);
      if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }

      chmod($path, 0644);
      return $path;
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}
