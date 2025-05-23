<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

if (isset($_POST['add_account_btn'])) {
  $account_name = $_POST['account_name'];
  $account_email = $_POST['account_email'];
  $account_password = $_POST['account_password'];
  $account_role = $_POST['account_role'];

  if (empty($account_name) || empty($account_email) || empty($account_password) || empty($account_role)) {
    header("location: add_account.php?error=Thông tin không được để trống");
    exit;
  } elseif (strlen($account_password) < 8) {
    header("location: add_account.php?error=Mật khẩu phải từ 8 ký tự trở lên");
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO accounts (account_name, account_email, account_password, account_role) 
                          VALUES (?, ?, ?, ?)");
  $stmt->bind_param('ssss', $account_name, $account_email, md5($account_password), $account_role);

  if ($stmt->execute()) {
    header('location: accounts.php');
    exit;
  } else {
    header('location: add_account.php?error=Lỗi khi tạo mới tài khoản');
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
  <link rel="stylesheet" href="../admin/assets/css/style.css">
  <title>Thêm tài khoản mới</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <div class="col py-3">
        <h2 class="text-center mb-3">Thêm tài khoản mới</h2>
        <div class="container mx-auto">
          <form action="add_account.php" method="post">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="account-name">Tên tài khoản</label>
                  <input type="text" name="account_name" class="form-control" id="account-name" placeholder="Nguyen Van A">
                </div>
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="account-email">Email</label>
                  <input type="email" name="account_email" class="form-control" id="account-email" placeholder="account@gmail.com">
                </div>
              </div>
              <div class="row mb-2">
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="password">Mật khẩu</label>
                  <input type="password" name="account_password" class="form-control" id="password">
                </div>
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="account-role">Vai trò</label>
                  <select name="account_role" id="account-role" class="form-select">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                  </select>
                </div>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="checkboxPassword">
                <label class="form-check-label" for="checkboxPassword">
                  Hiện mật khẩu
                </label>
              </div>
              <span class="text-danger"><?php if (isset($_GET['error'])) echo $_GET['error']; ?></span>
              <div class="row mb-2">
                <div class="form-group col-md-4 mt-2">
                  <input class="btn btn-primary" type="submit" name="add_account_btn" value="Thêm">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

  <script src="../main.js"></script>
</body>

</html>