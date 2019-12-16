<?php
require('function.php');

if (!empty($_POST)) {
  try {
    $dbh = dbConnect();
    $sql = 'SELECT email FROM users WHERE email = :email AND delete_flg = 0';
    $data = [':email' => $_POST['email']];
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      $activateCode = random();
      $_SESSION['activation-code'] = $activateCode;
      $_SESSION['email'] = $_POST['email'];
      $_SESSION['activate-limit'] = time() + (60 * 10);

      $to = $_POST['email'];
      $title = '【Free Market】Password Reminder';
      $message = 'Hello '.$_POST['name']."! We're Free Market.<br>Your activation code is".$activateCode;
      $headers = 'From: junya.nishiwaki@fullspeedtechnologies.com';
      mail($to, $title, $message, $message);

      header('Location: passwordReminder2.php');
    } else {
      $err_msg['common'] = 'There is no matched record with the one you wrote!';
    }
  } catch (PDOException $e) {
    $e->getMessage();
    exit;
  }
}

$siteTitle = 'Password reminder';
require('components/header.php');
?>
<main id='password-reminder'>
<div class="form-area">
  <form class='edit-form' method="post">
    <h1>Password reminder</h1>
    <p class='err'><?php if (!empty($err_msg['common'])) echo $err_msg['common'] ?></p>
    <label for="email">Your email</label>
    <input type="email" name="email" id='email' value="<?php if (!empty($_POST['email'])) echo $_POST['email'] ?>" required>
    <input type="submit" value="Send">
    <a href="login.php">Back to login page</a>
  </form>
</div>
</main>
<?php require('components/footer.php') ?>
