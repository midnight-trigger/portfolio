<?php
require('function.php');

if (!empty($_GET)) {
  try {
    $dbh = dbConnect();
    $sql = 'SELECT products.id AS productId, products.name AS productName, body, price, user_id, pic1, pic2, pic3, categories.name AS categoryName FROM products INNER JOIN categories ON products.category_id = categories.id WHERE products.id = :id AND products.delete_flg = :delete_flg';
    $data = [
      ':id' => $_GET['p_id'],
      ':delete_flg' => 0
    ];
    $stmt = queryPost($dbh, $sql, $data);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$results) {
      header('Location: index.php');
    }
    $pic1 = (!empty($results['pic1'])) ? $results['pic1'] : 'img/empty.jpg';
    $pic2 = (!empty($results['pic2'])) ? $results['pic2'] : 'img/empty.jpg';
    $pic3 = (!empty($results['pic3'])) ? $results['pic3'] : 'img/empty.jpg';
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

if (!empty($_POST)) {
  require('auth.php');
  try {
    $dbh = dbConnect();
    $sql = 'INSERT INTO boards (sale_user, buy_user, product_id, created_at) VALUES (:sale_user, :buy_user, :product_id, :created_at)';
    $data = [
      ':sale_user' => $results['user_id'],
      ':buy_user' => $_SESSION['user_id'],
      ':product_id' => $results['productId'],
      ':created_at' => date('Y-m-d H:i:s')
    ];
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      header('Location: msg.php?m_id=' . $dbh->lastInsertId());
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'Product detail';
require('components/header.php');
?>
<main id='productDetail'>
<div class="container">
<p class='product-title'><span class='category-tag'><?= h($results['categoryName']) ?></span><?= h($results['productName']) ?></p>
<i class="icofont-heart-alt icn-like js-click-like" data-productid='<?= h($results['productId']) ?>'></i>
<div class="product-detail">
  <div class="main-img">
    <img src="<?= $pic1 ?>" id='js-changeImg-main'>
  </div>
  <div class="sub-img">
    <img src="<?= $pic2 ?>" class='js-changeImg-sub'>
    <img src="<?= $pic3 ?>" class='js-changeImg-sub'>
  </div>
</div>
<div class="body"><?= h($results['body']) ?></div>
<div class="product-below">
  <p><a href='index.php'>＜商品一覧に戻る</a></p>
  <p class='price'>$ <?= h(number_format($results['price'])) ?> -</p>
  <?php if ($_SESSION['user_id'] !== $results['user_id']) : ?>
    <form method="post">
      <input class='buy' type="submit" name='submit' value="Get in touch!">
    </form>
  <?php else : ?>
    <div class="cannot">Your product</div>
  <?php endif ?>
</div>
</div>
</main>
<?php require('components/footer.php') ?>
