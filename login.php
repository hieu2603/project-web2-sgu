<?php

session_start();

include 'server/connection.php';

if (isset($_SESSION['logged_in'])) {
  header('location: account.php');
  exit;
}

if (isset($_POST['login_btn'])) {
  $email = $_POST['email'];
  $password = md5($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ? AND user_password = ? LIMIT 1");

  $stmt->bind_param('ss', $email, $password);

  if ($stmt->execute()) {
    $stmt->bind_result($user_id, $user_name, $user_email, $user_password);
    $stmt->store_result();

    if ($stmt->num_rows() == 1) {
      $stmt->fetch();

      $_SESSION['user_id'] = $user_id;
      $_SESSION['user_name'] = $user_name;
      $_SESSION['user_email'] = $user_email;
      $_SESSION['logged_in'] = true;

      header('location: account.php?login_success=Logged in successfully');
    } else {
      header('location: login.php?error=Wrong credentials');
    }
  } else {
    header('location: login.php?Something went wrong. Please try again');
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/d32f1bec50.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <title>Login</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Login -->
  <section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
      <h2 class="form-weight-bold">Login</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
      <form id="login-form" method="post" action="login.php">
        <p style="color: red;"><?php if (isset($_GET["error"])) echo $_GET['error']; ?></p>
        <div class="form-group">
          <label for="login-email">Email</label>
          <input type="text" class="form-control" id="login-email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
          <label for="login-password">Password</label>
          <input type="password" class="form-control" id="login-password" name="password" placeholder="Password"
            required>
        </div>
        <div class="form-group">
          <input type="submit" class="btn" id="login-btn" name="login_btn" value="Login">
        </div>
        <div class="form-group">
          <a id="register-url" class="btn" href="register.php">Don't have an account? Register</a>
        </div>
      </form>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>