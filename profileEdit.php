<?php
require('function.php');
require('auth.php');

if (!empty($_POST)) {
  try {
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic']) : '';
    $dbh = dbConnect();
    $sql = 'UPDATE users SET name = :name, tel = :tel, zip = :zip, address = :address, age = :age, email = :email, pic = :pic WHERE id = :id AND delete_flg = 0';
    $data = [
      ':name' => $_POST['name'],
      ':tel' => $_POST['tel'],
      ':zip' => $_POST['zip'],
      ':address' => $_POST['address'],
      ':age' => $_POST['age'],
      ':email' => $_POST['email'],
      ':pic' => $pic,
      ':id' => $_SESSION['user_id']
    ];
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) {
      $suc_msg['common'] = MSG4;
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
} else {
  try {
    $dbh = dbConnect();
    $sql = 'SELECT name, tel, zip, address, age, email FROM users WHERE id = :id';
    $data = [':id' => $_SESSION['user_id']];
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'Profile Edit';
require('components/header.php');
?>
<main id='profileEdit'>
<div class="container mypage">
  <form class='edit-form' method="post" enctype="multipart/form-data">
    <h1>Profile edit</h1>
    <p class='suc'><?php if (!empty($suc_msg)) echo $suc_msg['common'] ?></p>

    <!-- Name -->
    <label for="name">Name</label>
    <input type="text" name="name" value="<?php if (!empty($_POST)) {echo $_POST['name'];} else {echo h($result['name']);} ?>">

    <!-- Phone -->
    <label for="tel">Phone Number</label>
    <input type="text" name="tel" value="<?php if (!empty($_POST)) {echo $_POST['tel'];} else {echo h($result['tel']);} ?>">

    <!-- Zip -->
    <label for="zip">Zip Number</label>
    <input type="text" name="zip" value="<?php if (!empty($_POST)) {echo $_POST['zip'];} else {echo h($result['zip']);} ?>">

    <!-- Address -->
    <label for="address">Address</label>
    <input type="text" name="address" value="<?php if (!empty($_POST)) {echo $_POST['address'];} else {echo h($result['address']);} ?>">

    <!-- Age -->
    <label for="">Age</label>
    <input type="number" name="age" value="<?php if (!empty($_POST)) {echo $_POST['age'];} else {echo h($result['age']);} ?>">

    <!-- Email -->
    <label for="email">Email</label>
    <input type="email" name="email" value="<?php if (!empty($_POST)) {echo $_POST['email'];} else {echo h($result['email']);} ?>">

    <!-- Picture -->
    <p>Picture</p>
    <div class="img-box">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?= h(MAX_FILE_SIZE) ?>">
      <input type="file" name="pic" class="live-preview">
      <img src="" class='img-preview'>
    </div>

    <input type="submit" value="Preserve">
  </form>
  <?php require('components/side-bar.php') ?>
</div>
</main>
<?php require('components/footer.php') ?>
