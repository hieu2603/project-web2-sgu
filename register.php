<?php

session_start();

include 'server/connection.php';

if (isset($_POST['register'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];

  if ($password !== $confirmPassword) {
    header("location: register.php?error=Password doesn't match");
  } elseif (strlen($password) < 8) {
    header("location: register.php?error=Password must be at least 8 characters");
  } else {
    // Check email existed
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($num_rows);
    $stmt->store_result();
    $stmt->fetch();

    if ($num_rows != 0) {
      header('location: register.php?error=A user with this email already exists');
    } else {
      // Create new user
      $stmt1 = $conn->prepare("INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)");
      $stmt1->bind_param('sss', $name, $email, md5($password));
      $stmt1->execute();

      $user_id = $stmt1->insert_id;
      $_SESSION['user_id'] = $user_id;
      $_SESSION["user_email"] = $email;
      $_SESSION['user_name'] = $name;
      $_SESSION['logged_in'] = true;

      header("location: account.php?register_success=You registered successfully");
    }
  }
  // If user has already registered, redirect user to account page
} elseif (isset($_SESSION["logged_in"])) {
  header("location: account.php");
  exit;
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
  <title>Register</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Register -->
  <section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
      <h2 class="form-weight-bold">Register</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
      <form id="register-form" action="register.php" method="post">
        <p style="color: red;"><?php if (isset($_GET["error"])) echo $_GET['error']; ?></p>
        <div class="form-group">
          <label for="register-name">Name</label>
          <input type="text" class="form-control" id="register-name" name="name" placeholder="Name" required>
        </div>
        <div class="form-group">
          <label for="register-email">Email</label>
          <input type="text" class="form-control" id="register-email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
          <label for="register-password">Password</label>
          <input type="password" class="form-control" id="register-password" name="password" placeholder="Password"
            required>
        </div>
        <div class="form-group">
          <label for="register-confirm-password">Confirm Password</label>
          <input type="password" class="form-control" id="register-confirm-password" name="confirmPassword"
            placeholder="Confirm Password" required>
        </div>
        <div class="form-group">
          <input type="submit" class="btn" id="register-btn" name="register" value="Register">
        </div>
        <div class="form-group">
          <a id="login-url" class="btn" href="login.php">Have an account? Login</a>
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