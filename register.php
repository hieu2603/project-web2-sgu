<?php

session_start();

include 'server/connection.php';

if (isset($_POST['register'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];

  if ($password !== $confirmPassword) {
    header("location: register.php?error=Xác nhận mật khẩu không khớp");
  } elseif (strlen($password) < 8) {
    header("location: register.php?error=Mật khẩu phải có ít nhất 8 ký tự");
  } else {
    // Check email existed
    $stmt = $conn->prepare("SELECT COUNT(*) FROM accounts WHERE account_email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($num_rows);
    $stmt->store_result();
    $stmt->fetch();

    if ($num_rows != 0) {
      header('location: register.php?error=Email đã tồn tại');
    } else {
      // Create new user
      $stmt1 = $conn->prepare("INSERT INTO accounts (account_name, account_email, account_password) 
                               VALUES (?, ?, ?)");
      $stmt1->bind_param('sss', $name, $email, md5($password));
      $stmt1->execute();

      $account_id = $stmt1->insert_id;
      $_SESSION['user_id'] = $account_id;
      $_SESSION["user_email"] = $email;
      $_SESSION['user_name'] = $name;
      $_SESSION['logged_in'] = true;

      header("location: account.php?register_success=Đăng ký tài khoản thành công");
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
  <title>Đăng Ký</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Register -->
  <section class="my-5 py-5">
    <div class="container text-center mt-3">
      <h2 class="form-weight-bold">Đăng Ký</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
      <form id="register-form" action="register.php" method="post">
        <p class="text-danger text-center"><?php if (isset($_GET["error"])) echo $_GET['error']; ?></p>
        <div class="form-group">
          <label for="register-name">Tên tài khoản</label>
          <input type="text" class="form-control" id="register-name" name="name" placeholder="Nguyễn Văn A" required>
        </div>
        <div class="form-group">
          <label for="register-email">Email</label>
          <input type="text" class="form-control" id="register-email" name="email" placeholder="nguyenvana@gmail.com" required>
        </div>
        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="********"
            required>
        </div>
        <div class="form-group">
          <label for="confirmPassword">Xác nhận mật khẩu</label>
          <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
            placeholder="********" required>
        </div>
        <div class="form-group">
          <div class="form-check my-2">
            <input class="form-check-input" type="checkbox" id="checkboxPassword">
            <label class="form-check-label" for="checkboxPassword">
              Hiện mật khẩu
            </label>
          </div>
          <div class="form-group">
            <input type="submit" class="btn w-100" id="register-btn" name="register" value="ĐĂNG KÝ">
          </div>
          <div class="form-group text-center mt-2">
            <a id="login-url" class="btn" href="login.php">Đã có tài khoản? Đăng nhập ngay</a>
          </div>
      </form>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

  <script src="./main.js"></script>
</body>

</html>