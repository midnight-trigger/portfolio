<?php
if (!empty($_SESSION['login_date'])) {
  if ($_SESSION['login_date'] + $_SESSION['login_limit'] < time()) {
    session_destroy();
    header('Location: login.php');
  } else {
    $_SESSION['login_date'] = time();
    if (basename($_SERVER['PHP_SELF']) === 'login.php') {
      header('Location: mypage.php');
    }
  }
} else {
  if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
  }
}
