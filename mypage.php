<?php
require('function.php');
require('auth.php');

try {
  $dbh = dbConnect();
  $sql = 'SELECT id, name, price, pic1 FROM products WHERE user_id = :user_id';
  $data = [':user_id' => $_SESSION['user_id']];
  $stmt = queryPost($dbh, $sql, $data);
  $products = $stmt->fetchAll();

  $sql = 'SELECT b.id, b.updated_at, sale_user, buy_user, sale.name AS sname, buy.name AS bname FROM boards AS b LEFT JOIN users AS sale ON b.sale_user = sale.id LEFT JOIN users AS buy ON b.buy_user = buy.id WHERE sale_user = :sale_user OR buy_user = :buy_user';
  $data = [
    ':sale_user' => $_SESSION['user_id'],
    ':buy_user' => $_SESSION['user_id']
  ];
  $stmt = queryPost($dbh, $sql, $data);
  $board_infos = $stmt->fetchAll();
} catch (PDOException $e) {
  $e->getMessage();
  exit;
}

$siteTitle = 'MY PAGE';
require('components/header.php');
?>
<main id='mypage'>
<div class="container mypage">
  <div class="contents">
    <h1>My page</h1>
    <h2>My products</h2>
    <div class="myProducts">
    <?php foreach ($products as $product) : ?>
      <div class="product">
        <a href="registProduct.php?p_id=<?= $product['id'] ?>">
          <img src="<?= h($product['pic1']) ?>">
          <h3><?= h($product['name']) ?></h3>
          <p><?= h(number_format($product['price'])) ?></p>
        </a>
      </div>
    <?php endforeach ?>
    </div>
    <h2>My boards</h2>
    <div class="myBoards">
      <table class="board" border='1'>
        <thead>
          <tr>
            <td>The last send date</td><td>The client</td><td>The last Message</td>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($board_infos as $board_info) : ?>
          <tr>
            <td><a href='msg.php?m_id=<?= $board_info['id'] ?>'><?= $board_info['updated_at'] ?></a></td>
            <?php if ($board_info['sale_user'] === $_SESSION['user_id']) : ?>
            <td><?= $board_info['bname'] ?></td>
            <?php else : ?>
            <td><?= $board_info['sname'] ?></td>
            <?php endif ?>
          </tr>
        <?php endforeach ?>
        </tbody>
      </table>
    </div>
    <h2>My favorites</h2>
    <div class="myFavorites">
      
    </div>
  </div>
  <?php require('components/side-bar.php') ?>
</div>
</main>
<?php require('components/footer.php') ?>
