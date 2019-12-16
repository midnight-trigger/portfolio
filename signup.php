<?php
require('function.php');

if (!empty($_POST)) {
  // バリデーション
  try {
    $dbh = dbConnect();
    $sql = 'SELECT email FROM users WHERE email = :email AND delete_flg = 0';
    $data = [':email' => $_POST['email']];
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($result)) {
      $dbh = dbConnect();
      $sql = 'INSERT INTO users (email, password, created_at) VALUES (:email, :password, :created_at)';
      $data = [
        ':email' => $_POST['email'],
        ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ':created_at' => date('Y-m-d H:i:s')
      ];
      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        $sesLimit = 60 * 60;

        $_SESSION['login_date'] = time();
        $_SESSIOM['login_limit'] = $sesLimit;
        $_SESSION['user_id'] = $dbh->lastInsertId();

        header('Location: index.php');
      }
    } else {
      $err_msg['common'] = MSG3;
    }
  } catch(PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'SIGNUP';
require('components/header.php');
?>
<main>
  <div class="form-area">
    <h1>Signup</h1>
    <form action='' method="post">
      <p class='err'><?php if(!empty($err_msg['common'])) echo $err_msg['common'] ?></p>
      <label for="email">E-Mail</label>
      <p class='err'><?php  ?></p>
      <input type="email" name="email" value="<?php if(!empty($_POST)) echo $_POST['email'] ?>" required><br>
      <label for="password">Password</label>
      <p class='err'><?php  ?></p>
      <input type="password" name="password" value="<?php if(!empty($_POST)) echo $_POST['password'] ?>" required><br>
      <label for="password">Password *again</label><br>
      <input type="password" value="" required><br>
      <input type="submit" value="Signup">
    </form>
  </div>
</main>
<?php require('components/footer.php') ?>
