<?php

session_start();

include '../server/connection.php';

$account_id = $_GET['account_id'];

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

if (!isset($_GET['account_id']) || !is_numeric($_GET['account_id'])) {
  header('location: accounts.php');
  exit;
} else {
  $account_id = $_GET['account_id'];
  $account_stmt = $conn->prepare("SELECT * FROM accounts WHERE account_id = ?");
  $account_stmt->bind_param('i', $account_id);
  $account_stmt->execute();

  $account = $account_stmt->get_result();
}

if (isset($_POST['edit_account_btn'])) {
  $account_name = $_POST['account_name'];
  $account_email = $_POST['account_email'];
  $account_password = trim($_POST['account_password']);
  $account_role = $_POST['account_role'];

  if (!empty($account_password)) {
    if (strlen($account_password) < 8) {
      header('location: edit_account.php?error=Mật khẩu phải từ 8 ký tự trở lên&account_id=' . $account_id);
      exit;
    }
    $stmt = $conn->prepare("UPDATE accounts
                            SET account_name = ?, account_email = ?, account_password = ?, account_role = ?
                            WHERE account_id = ?");
    $stmt->bind_param('ssssi', $account_name, $account_email, md5($account_password), $account_role, $account_id);
  } else {
    $stmt = $conn->prepare("UPDATE accounts
                            SET account_name = ?, account_email = ?, account_role = ?
                            WHERE account_id = ?");
    $stmt->bind_param('sssi', $account_name, $account_email, $account_role, $account_id);
  }

  if ($stmt->execute()) {
    header('location: accounts.php');
    exit;
  } else {
    header('location: edit_account.php?error=Lỗi khi chỉnh sửa thông tin người dùng&account_id=' . $account_id);
    exit;
  }
}

if (isset($_POST['lock_account_btn'])) {
  $stmt = $conn->prepare("UPDATE accounts SET account_status = 'Inactive' WHERE account_id = ?");
  $stmt->bind_param('i', $account_id);

  if ($stmt->execute()) {
    header('location: accounts.php');
    exit;
  } else {
    header('location: edit_account.php?error=Lỗi khi khóa tài khoản&account_id=' . $account_id);
    exit;
  }
} elseif (isset($_POST['unlock_account_btn'])) {
  $stmt = $conn->prepare("UPDATE accounts SET account_status = 'Active' WHERE account_id = ?");
  $stmt->bind_param('i', $account_id);

  if ($stmt->execute()) {
    header('location: accounts.php');
    exit;
  } else {
    header('location: edit_account.php?error=Lỗi khi mở khóa tài khoản&account_id=' . $account_id);
    exit;
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
  <title>Edit Account</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <div class="col py-3">
        <h2 class="text-center mb-3">Chỉnh sửa thông tin tài khoản</h2>
        <div class="container mx-auto">
          <form action="edit_account.php?account_id=<?php echo $_GET['account_id']; ?>" method="post">
            <div class="container-fluid">
              <?php while ($row = $account->fetch_assoc()) { ?>
                <div class="row mb-2">
                  <div class="form-group col-md-6 mt-2">
                    <label class="form-label" for="account-name">Tên tài khoản</label>
                    <input value="<?php echo $row['account_name']; ?>" type="text" name="account_name" class="form-control" id="account-name">
                  </div>
                  <div class="form-group col-md-6 mt-2">
                    <label class="form-label" for="account-email">Email</label>
                    <input value="<?php echo $row['account_email']; ?>" type="email" name="account_email" class="form-control" id="account-email">
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="form-group col-md-6 mt-2">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <span class="mx-4 text-danger form-text">Chỉ nhập khi cần thay đổi mật khẩu</span>
                    <input type="password" name="account_password" class="form-control" id="password">
                  </div>
                  <div class="form-group col-md-6 mt-2">
                    <label class="form-label" for="account-role">Vai trò</label>
                    <select name="account_role" id="account-role" class="form-select">
                      <option value="User" <?php echo $row['account_role'] === 'User' ? 'selected' : ''; ?>>User</option>
                      <option value="Admin" <?php echo $row['account_role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
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
                    <input class="btn btn-primary" type="submit" name="edit_account_btn" value="Lưu">
                    <?php if ($row['account_status'] == 'Active') { ?>
                      <input class="btn btn-danger ms-2" type="submit" name="lock_account_btn" value="Khóa">
                    <?php } elseif ($row['account_status'] == 'Inactive') { ?>
                      <input class="btn btn-success ms-2" type="submit" name="unlock_account_btn" value="Mở khóa">
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
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