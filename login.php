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

  $stmt = $conn->prepare("SELECT account_id, account_name, account_email, account_status, account_role 
                          FROM accounts 
                          WHERE account_email = ? 
                          AND account_password = ?");

  $stmt->bind_param('ss', $email, $password);

  if ($stmt->execute()) {
    $stmt->bind_result($account_id, $account_name, $account_email, $account_status, $account_role);
    $stmt->store_result();

    if ($stmt->num_rows() == 1) {
      $stmt->fetch();

      if ($account_status == 'Inactive') {
        header('location: login.php?error=Tài khoản của bạn đã bị khóa');
        exit;
      }

      if ($account_role != 'User') {
        header('location: login.php?error=Tài khoản của bạn không có quyền truy cập vào trang này');
        exit;
      }

      $_SESSION['user_id'] = $account_id;
      $_SESSION['user_name'] = $account_name;
      $_SESSION['user_email'] = $account_email;
      $_SESSION['logged_in'] = true;

      header('location: account.php?login_success=Đăng nhập thành công');
    } else {
      header('location: login.php?error=Mật khẩu hoặc tài khoản không chính xác');
    }
  } else {
    header('location: login.php?error=Có lỗi xảy ra. Vui lòng thử lại sau');
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
  <title>Đăng Nhập</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Login -->
  <section class="my-5 py-5">
    <div class="container text-center mt-3">
      <h2 class="form-weight-bold">Đăng Nhập</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
      <form id="login-form" method="post" action="login.php">
        <p class="text-danger text-center"><?php if (isset($_GET["error"])) echo $_GET['error']; ?></p>
        <div class="form-group">
          <label for="login-email">Email</label>
          <input type="text" class="form-control" id="login-email" name="email" placeholder="nguyenvana@gmail.com" required>
        </div>
        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="********"
            required>
        </div>
        <div class="form-group">
          <div class="my-2">
            <input class="form-check-input" type="checkbox" id="checkboxPassword">
            <label class="form-check-label" for="checkboxPassword">
              Hiện mật khẩu
            </label>
          </div>
        </div>
        <div class="form-group">
          <input type="submit" class="btn w-100" id="login-btn" name="login_btn" value="ĐĂNG NHẬP">
        </div>
        <div class="form-group text-center mt-2">
          <a id="register-url" class="btn" href="register.php">Chưa có tài khoản? Đăng ký ngay</a>
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