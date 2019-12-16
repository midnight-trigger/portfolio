<?php
require('function.php');
require('auth.php');
$err_msg = [];
if (!empty($_POST)) {
  try {
    $dbh = dbConnect();
    $sql = 'SELECT password FROM users WHERE id = :id AND delete_flg = 0';
    $data = [':id' => $_SESSION['user_id']];
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($_POST['password_old'], $result['password'])) {

      if ($_POST['password_new_re'] === $_POST['password_new']) {
        try {
          $dbh = dbConnect();
          $sql = 'UPDATE users SET password = :password WHERE id = :id AND delete_flg = 0';
          $data = [':password' => password_hash($_POST['password_new'], PASSWORD_DEFAULT), ':id' => $_SESSION['user_id']];
          $stmt = queryPost($dbh, $sql, $data);
          if ($stmt) {
            header('Location: mypage.php');
          }
        } catch (PDOException $e) {
          $e->getMessage();
          exit;
        }

      } else {
        $err_msg['common'] = 'There is a difference between the new passwords you wrote!';
      }
    } else {
      $err_msg['common'] = 'Wrong Password!';
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'Change Password';
require('components/header.php');
?>
<main id ='chenge-password'>
<div class="container mypage">
  <div class="">
    <form class='edit-form' method="post">
      <h1>Chenge your password</h1>
      <p class='err'><?php if (!empty($err_msg['common'])) echo $err_msg['common'] ?></p>
      <label for="password_old">Current password</label>
      <input type="password" name="password_old" id='password_old' required>
      <label for="password_new">New password</label>
      <input type="password" name="password_new" id='password_new' required>
      <label for="password_new_re">New password *again</label>
      <input type="password" name="password_new_re" id='password_new_re' required>
      <input type="submit" value="Change">
    </form>
  </div>
  <?php require('components/side-bar.php') ?>
</div>
</main>
<?php require('components/footer.php') ?>
