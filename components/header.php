<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Free Market | <?= $siteTitle ?></title>
  <link rel="stylesheet" href="style.min.css">
  <link rel="stylesheet" href="components/icofont/icofont.min.css">
</head>
<body>
<!-- Header -->
<header>
<div class="container">
  <h1><a href="index.php">Free Market</a></h1>
  <ul>
    <?php if (empty($_SESSION['user_id'])) : ?>
      <li><a href="login.php">Login</a></li>
      <li><a href="signup.php">Signup</a></li>
    <?php else : ?>
      <li><a href="logout.php">Logout</a></li>
      <li><a href="mypage.php">My Page</a></li>
    <?php endif ?>
  </ul>
</div>
</header>
<body>
