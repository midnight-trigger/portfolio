<?php
require('function.php');

var_dump($_SESSION['activation-code']);
if (!empty($_POST)) {

  if ($_SESSION['activate-limit'] > time()) {

    if ($_POST['activation-code'] === $_SESSION['activation-code']) {
      header('Location: login.php');
    } else {
      $err_msg['common'] = 'Wrong activation code!';
    }

  } else {
    session_unset();
    header('Location: passwordReminder.php');
  }
}

$siteTitle = 'Password reminder';
require('components/header.php');
?>
<main id='password-reminder'>
<div class="form-area">
  <form class='edit-form' method="post">
    <h1>Password reminder</h1>
    <p>Sent activation code to your email address.<br>Please confirm and write your activation code.</p>
    <p class='err'><?php if (!empty($err_msg['common'])) echo $err_msg['common'] ?></p>
    <label for="activate-code">Activation code</label>
    <input type="text" name="activation-code" required>
    <input type="submit" value="Send">
    <a href="login.php">Back to login page</a>
  </form>
</div>
</main>
<?php require('components/footer.php') ?>
