<?php
require('function.php');
require('auth.php');
if (!empty($_POST)) {
  try {
    $dbh = dbConnect();
    $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
    $data = [':email' => $_POST['email']];
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($_POST['password'], array_shift($result))) {
      $sesLimit = 60 * 60;
      $_SESSION['login_date'] = time();

      if ($_POST['prolong']) {
        $_SESSION['login_limit'] = $sesLimit * 24 * 30;
      } else {
        $_SESSION['login_limit'] = $sesLimit;
      }

      $_SESSION['user_id'] = $result['id'];
      header('Location: index.php');

    } else {
      $err_msg['common'] = MSG1;
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'LOGIN';
require('components/header.php');
?>
<main>
  <div class="form-area">
    <h1>Login</h1>
    <form method="post">
      <p class='err'><?php if(!empty($err_msg['common'])) echo $err_msg['common'] ?></p>
      <label for="email">E-Mail</label>
      <input type="email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'] ?>">
      <label for="">Password</label>
      <input type="password" name="password">
      <label>
        <input type="checkbox" name="prolong">次回ログイン省略する
      </label>
      <input type="submit" value="Login">
      <p>パスワード忘れた方は<a href='passwordReminder.php'>こちら</a></p>
    </form>
  </div>
</main>
<?php require('components/footer.php') ?>
