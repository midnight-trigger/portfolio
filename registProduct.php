<?php
require('function.php');
require('auth.php');

$getFlg = false;

try {
  $dbh = dbConnect();
  $sql = 'SELECT id, name FROM categories';
  $data = [];
  $stmt = queryPost($dbh, $sql, $data);
  $categories = $stmt->fetchAll();
} catch (PDOException $e) {
  $e->getMessage();
  exit;
}

if (!empty($_GET)) {
  $getFlg = true;
  $dbh = dbConnect();
  $sql = 'SELECT name, category_id, body, price FROM products WHERE id = :id';
  $data = [':id' => $_GET['p_id']];
  $stmt = queryPost($dbh, $sql, $data);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!empty($_POST)) {
  try {
    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1']) : '';
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2']) : '';
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3']) : '';
    $dbh = dbConnect();
    if ($getFlg) {
      $sql = 'UPDATE products SET name = :name, category_id = :category_id, body = :body, price = :price, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE id = :id';
      $data = [
        ':name' => $_POST['name'],
        ':body' => $_POST['body'],
        ':price' => $_POST['price'],
        ':category_id' => $_POST['category'],
        ':pic1' => $pic1,
        ':pic2' => $pic2,
        ':pic3' => $pic3,
        ':id' => $_GET['p_id']
      ];
    } else {
      $sql = 'INSERT INTO products (name, body, price, category_id, pic1, pic2, pic3, user_id, created_at) VALUES (:name, :body, :price, :category_id, :pic1, :pic2, :pic3, :user_id, :created_at)';
      $data = [
        ':name' => $_POST['name'],
        ':body' => $_POST['body'],
        ':price' => $_POST['price'],
        ':category_id' => $_POST['category'],
        ':pic1' => $pic1,
        ':pic2' => $pic2,
        ':pic3' => $pic3,
        ':user_id' => $_SESSION['user_id'],
        ':created_at' => date('Y-m-d H:i:s')
      ];
    }
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) {
      $suc_msg['common'] = 'MSG04';
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = $getFlg ? 'Edit product' :'Regist product';
require('components/header.php');
?>
<main>
<div class="container mypage">
  <form class="edit-form" method="post" enctype='multipart/form-data'>
    <h1><?= $getFlg ? 'Edit product' : 'Regist product'; ?></h1>
    <p class='err'><?php if (!empty($err_msg['common'])) echo $err_msg['common'] ?></p>
    <p class='suc'><?php if (!empty($suc_msg['common'])) echo $suc_msg['common'] ?></p>

    <!-- Name -->
    <label for="name">Name</label>
    <input id='name' type="text" name="name" value="<?php if (!empty($_POST['name'])) echo h($_POST['name']) ?>">

    <!-- Category -->
    <label for="category">Category</label>
    <select id='category' name="category">
      <option selected>選択して下さい</option>
      <?php foreach ($categories as $category) : ?>
        <option value="<?= $category['id'] ?>"><?= h($category['name']) ?></option>
      <?php endforeach ?>
    </select>

    <!-- Body -->
    <label for="body">Detail</label>
    <textarea id='body' name="body"><?php if (!empty($_POST['body'])) echo h($_POST['body']) ?></textarea>

    <!-- Price -->
    <label for="price">Price</label>
    <input id='price' type="text" name="price" value="<?php if (!empty($_POST['price'])) echo h($_POST['price']) ?>">

    <!-- Pictures -->
    <div class="pictures">
      <div class="picture">
        <label for="pic1">Picture 1</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php h(MAX_FILE_SIZE) ?>">
        <input id='pic1' type="file" name="pic1">
      </div>
      <div class="picture">
        <label for="pic2">Picture 2</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php h(MAX_FILE_SIZE) ?>">
        <input id='pic2' type="file" name="pic2">
      </div>
      <div class="picture">
        <label for="pic3">Picture 3</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php h(MAX_FILE_SIZE) ?>">
        <input id='pic3' type="file" name="pic3">
      </div>
    </div>

    <input type="submit" value="Regist">
  </form>
  <?php require('components/side-bar.php') ?>
</div>
</main>
<?php require('components/footer.php') ?>
