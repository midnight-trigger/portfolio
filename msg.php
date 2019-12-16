<?php
require('function.php');
require('auth.php');

if (!empty($_GET)) {
  try {
    $dbh = dbConnect();
    $sql = 'SELECT b.id AS bId, b.created_at, u.id AS uId, u.name AS uName, u.age, u.zip, u.address, u.tel, u.pic, p.name AS pName, p.price, p.pic1 FROM boards AS b LEFT JOIN users AS u ON b.sale_user = u.id LEFT JOIN products AS p ON b.product_id = p.id WHERE b.Id = :id AND b.delete_flg = 0';
    $data = [':id' => $_GET['m_id']];
    $stmt = queryPost($dbh, $sql, $data);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$results) {
      header('Location: index.php');
    }

    $sql = 'SELECT body, send_date, u.pic, u.id AS fromId FROM messages AS m LEFT JOIN users AS u ON m.from_user = u.id WHERE board_id = :board_id';
    $data = [':board_id' => $results['bId']];
    $stmt = queryPost($dbh, $sql, $data);
    $messages = $stmt->fetchAll();
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

if (!empty($_POST['message'])) {
  require('auth.php');
  try {
    $dbh = dbConnect();
    $sql = 'INSERT INTO messages (send_date, board_id, to_user, from_user, body) VALUES (:send_date, :board_id, :to_user, :from_user, :body)';
    $data = [
      ':send_date' => date('Y-m-d H:i:s'),
      ':board_id' => $results['bId'],
      ':to_user' => $results['uId'],
      ':from_user' => $_SESSION['user_id'],
      ':body' => $_POST['message']
    ];
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $messages = [];
      $sql = 'SELECT body, send_date, u.pic, u.id AS fromId FROM messages AS m LEFT JOIN users AS u ON m.from_user = u.id WHERE board_id = :board_id';
      $data = [':board_id' => $results['bId']];
      $stmt = queryPost($dbh, $sql, $data);
      $messages = $stmt->fetchAll();
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'Message';
require('components/header.php');
?>
<main>
<div class="container">
  <div class="information-area">
    <div class="user-info">
      <img src="<?= $results['pic'] ?>">
      <div class="user">
        <p>Seller: <strong><?= $results['uName'] . '(' . $results['age'] . ')' ?></strong></p>
        <p>〒<?= $results['zip'] ?></p>
        <p><?= $results['address'] ?></p>
        <p>TEL: <?= $results['tel'] ?></p>
      </div>
    </div>
    <div class="product-info">
      <img src="<?= $results['pic1'] ?>">
      <div class="product">
        <p>Product: <?= $results['pName'] ?></p>
        <p>Price: $<?= number_format($results['price']) ?></p>
        <p>The date: <?= $results['created_at'] ?></p>
      </div>
    </div>
  </div>
  <div class="message-area">
    <div class="message">
    <?php
      foreach ($messages as $message) :
        if ($message['fromId'] === $results['uId']) :
    ?>
      <div class="talk 1">
        <img src="<?= $message['pic'] ?>">
        <p><?= $message['body'] ?></p>
      </div>
    <?php else : ?>
      <div class="talk 2">
        <p><?= $message['body'] ?></p>
        <img src="<?= $message['pic'] ?>">
      </div>
    <?php
        endif;
      endforeach
    ?>
    </div>
    <div class="send">
      <form method="post">
        <textarea name="message"></textarea>
        <p><input type="submit" value="Send"></p>
      </form>
    </div>
  </div>
</div>
</main>
<?php require('components/footer.php') ?>
