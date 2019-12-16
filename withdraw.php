<?php
require('function.php');
require('auth.php');

if (!empty($_POST)) {
  try {
    $dbh = dbConnect();
    $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
    $data = [':id' => $_SESSION['user_id']];
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      session_destroy();
      header('Location: signup.php');
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'WITHDRAW';
require('components/header.php');
?>
<main id='withdraw'>
<div class="container mypage">
  <div class="">
    <h1>退会する</h1>
    <form method="post">
      <input type="submit" name="submit" value="退会する">
    </form>
  </div>
  <?php require('components/side-bar.php') ?>
</div>
</main>
<?php require('components/footer.php') ?>
