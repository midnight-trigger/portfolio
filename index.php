<?php
require('function.php');


try {
  $dbh = dbConnect();
  $sql = 'SELECT products.id, products.name AS productName, price, pic1, categories.name AS categoryName FROM products INNER JOIN categories ON products.category_id = categories.id';
  $sql2 = 'SELECT count(*) FROM products';

  $sort = '';
  $category_sort = '';
  $data= [];
  if (!empty($_POST['category'])) {
    $sql .= ' WHERE category_id = :category_id';
    $sql2 .= ' WHERE category_id = :category_id';
    $data = [':category_id' => $_POST['category']];
  }

  if (!empty($_POST['sort']) && $_POST['sort'] === '1') {
    $sql .= ' ORDER BY price';
  } elseif (!empty($_POST['sort']) && $_POST['sort'] === '2') {
    $sql .= ' ORDER BY price DESC';
  }
  $stmt = queryPost($dbh, $sql, $data);
  $stmt2 = queryPost($dbh, $sql2, $data);
  $count = $stmt2->fetch(PDO::FETCH_ASSOC);

  if ($stmt) {
    $results = $stmt->fetchAll();
  }

  $sql = 'SELECT id, name FROM categories';
  $data = [];
  $stmt = queryPost($dbh, $sql, $data);
  $categories = $stmt->fetchAll();


} catch (PDOException $e) {
  $e->getMessage();
  exit;
}

$siteTitle = 'HOME';
require('components/header.php');
?>
<div class="container main-display">

  <div class="side-bar">
    <form method='post'>
      <label for='categories'>カテゴリー</label>
      <select name="category" id="categories">
        <option value='0'>選択して下さい</option>
        <?php foreach ($categories as $category) : ?>
          <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php endforeach ?>
      </select><br>
      <label for="sort">表示順</label><br>
      <select name="sort" id="sort">
        <option value='0'>選択して下さい</option>
        <option value="1">安価順</option>
        <option value="2">高価順</option>
      </select><br>
      <input type="submit" value='検索'>
    </form>
  </div>

  <div class="main-wrapper">
    <div class="display-area">
      <p><strong><?= $count['count(*)'] ?></strong> 件の商品が見つかりました</p>
      <p>件</p>
    </div>
    <div class='products-area'>
      <?php foreach ($results as $result) : ?>
        <div class="product-area">
          <div class="category-tag"><?= $result['categoryName']  ?></div>
          <img src="<?= $result['pic1'] ?>" alt="">
          <h2><a href='productDetail.php?p_id=<?= $result['id'] ?>'><?= $result['productName'] ?></a></h2>
          <p>$ <?= number_format($result['price']) ?></p>
        </div>
      <?php endforeach ?>
    </div>
  </div>

</div>
<?php require('components/footer.php') ?>
</body>
</html>
