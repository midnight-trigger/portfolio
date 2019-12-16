<?php
require('function.php');

if (!empty($_POST) && !empty($_SESSION['user_id'])) {
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM likes WHERE product_id = :product_id AND user_id = :user_id AND delete_flg = 0';
    $data = [
      ':product_id' => $_POST['productId'],
      ':user_id' => $_SESSION['user_id']
    ];
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      $sql = 'DELETE FROM likes WHERE product_id = :product_id AND user_id = :user_id';
      $data = [
        ':product_id' => $result['product_id'],
        ':user_id' => $result['user_id']
      ];
      $stmt = queryPost($dbh, $sql, $data);
    } else {
      $sql = 'INSERT INTO likes (product_id, user_id, created_at) VALUES (:product_id, :user_id, :created_at)';
      $data = [
        ':product_id' => $_POST['productId'],
        ':user_id' => $_SESSION['user_id'],
        ':created_at' => date('Y-m-d H:i:s')
      ];
      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
} else {
  return false;
}
