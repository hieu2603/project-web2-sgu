<?php

session_start();

include '../server/connection.php';

if (isset($_SESSION['admin_logged_in'])) {
  header("location: dashboard.php");
  exit;
}

if (isset($_POST['login_btn'])) {
  $email = $_POST['email'];
  $password = md5($_POST['password']);

  if (empty($email) || empty($password)) {
    header('location: login.php?error=Vui lòng điền đầy đủ thông tin');
    exit;
  }

  $stmt = $conn->prepare("SELECT account_id, account_name, account_email, account_status, account_role 
                          FROM accounts WHERE account_email = ? AND account_password = ?");
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

      if ($account_role != 'Admin') {
        header('location: login.php?error=Tài khoản của bạn không có quyền truy cập vào trang này');
        exit;
      }

      $_SESSION['admin_id'] = $account_id;
      $_SESSION['admin_name'] = $account_name;
      $_SESSION['admin_email'] = $account_email;
      $_SESSION['admin_logged_in'] = true;

      header('location: dashboard.php');
      exit;
    } else {
      header('location: login.php?error=Tài khoản hoặc mật khẩu không chính xác');
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
  <link rel="stylesheet" href="../admin/assets/css/login_style.css">
  <title>Trang Đăng Nhập Admin</title>
</head>

<body>
  <div class="login">
    <h2 class="text-center">Đăng Nhập Admin</h2>
    <form action="login.php" method="post">
      <p class="text-center" style="color: red;"><?php if (isset($_GET['error'])) echo $_GET['error']; ?></p>
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" name="email" type="email" id="email">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Mật khẩu</label>
        <input class="form-control" name="password" type="password" id="password">
      </div>
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="checkboxPassword">
        <label class="form-check-label" for="checkboxPassword">
          Hiện mật khẩu
        </label>
      </div>
      <input class="btn btn-primary w-100" name="login_btn" type="submit" value="ĐĂNG NHẬP">
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

  <script src="../main.js"></script>
</body>

</html>