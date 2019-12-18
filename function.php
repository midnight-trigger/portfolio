<?php
ini_set('display_errors', 1);
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime ', 60*60*24*30);
session_start();
session_regenerate_id();

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
  // Heroku Information
  $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
  $db['dbname'] = ltrim($db['path'], '/');
  $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
  $user = $db['user'];
  $password = $db['pass'];

  // MAMP Information
  // $dsn = 'mysql:dbname=output1;host=localhost;charset=utf8';
  // $user = 'root';
  // $password = '4318';

  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  ];
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
        case UPLOAD_ERR_OK:
        break;
        case UPLOAD_ERR_NO_FILE:
        throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
        throw new RuntimeException('ファイルサイズが大きすぎます');
        default:
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
