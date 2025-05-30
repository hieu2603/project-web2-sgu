<?php

session_start();

include 'server/connection.php';

if (!isset($_SESSION['logged_in'])) {
  header('location: login.php');
  exit;
}

// Logout
if (isset($_GET['logout'])) {
  if (isset($_SESSION['logged_in'])) {
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    header('location: login.php');
    exit;
  }
}

// Change password
if (isset($_POST['change_password_btn'])) {
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];
  $account_email = $_SESSION['user_email'];

  if ($password !== $confirmPassword) {
    header("location: account.php?error=Xác nhận mật khẩu không khớp");
  } elseif (strlen($password) < 8) {
    header("location: account.php?error=Mật khẩu phải có ít nhất 8 ký tự");
  } else {
    $stmt = $conn->prepare("UPDATE accounts SET account_password = ? WHERE account_email = ?");
    $stmt->bind_param('ss', md5($password), $account_email);

    if ($stmt->execute()) {
      header('location: account.php?message=Mật khẩu đã được cập nhật');
    } else {
      header('location: account.php?error=Không thể cập nhật mật khẩu');
    }
  }
}

// Get orders
if (isset($_SESSION['logged_in'])) {
  $account_id = $_SESSION['user_id'];

  $stmt = $conn->prepare("SELECT * 
                          FROM orders 
                          WHERE account_id = ?
                          ORDER BY order_date DESC");

  $stmt->bind_param('i', $account_id);

  $stmt->execute();

  $orders = $stmt->get_result();
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
  <title>Tài khoản</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Account -->
  <section class="mt-5 pt-5">
    <div class="row container mx-auto">
      <div class="mt-3 text-center col-lg-6 col-md-12 col-sm-12">
        <p class="text-center" style="color: green;"><?php if (isset($_GET["register_success"])) echo $_GET['register_success']; ?></p>
        <p class="text-center" style="color: green;"><?php if (isset($_GET["login_success"])) echo $_GET['login_success']; ?></p>
        <h3 class="font-weight-bold">Thông tin tài khoản</h3>
        <hr class="mx-auto">
        <div class="account-info">
          <p>Tên tài khoản: <span><?php if (isset($_SESSION['user_name'])) echo $_SESSION['user_name']; ?></span></p>
          <p>Email: <span><?php if (isset($_SESSION['user_email'])) echo $_SESSION['user_email']; ?></span></p>
          <p><a href="account_addresses.php?account_id=<?php echo $_SESSION['user_id']; ?>" id="account-addresses-btn">Thông tin giao hàng</a></p>
          <p><a href="account.php?logout=true" id="logout-btn">Đăng xuất</a></p>
        </div>
      </div>

      <div class="mt-3 col-lg-6 col-md-12 col-sm-12">
        <form id="account-form" method="post" action="account.php">
          <p class="text-center" style="color: red;"><?php if (isset($_GET["error"])) echo $_GET['error']; ?></p>
          <p class="text-center" style="color: green;"><?php if (isset($_GET["message"])) echo $_GET['message']; ?></p>
          <h3>Đổi mật khẩu</h3>
          <hr class="mx-auto">
          <div class="form-group">
            <label for="account-password">Mật khẩu</label>
            <input type="password" class="form-control" id="account-password" name="password" placeholder="********"
              required>
          </div>
          <div class="form-group">
            <label for="account-confirm-password">Xác nhận mật khẩu</label>
            <input type="password" class="form-control" id="account-confirm-password" name="confirmPassword"
              placeholder="********" required>
          </div>
          <div class="form-group">
            <input type="submit" value="Đổi mật khẩu" name="change_password_btn" class="btn" id="change-password-btn">
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Orders -->
  <section id="orders" class="orders container mt-3">
    <div class="container">
      <h2 class="font-weight-bolde text-center">Đơn hàng của bạn</h2>
      <hr class="mx-auto">
    </div>

    <table class="mt-3 text-center">
      <tr>
        <th>ID</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Hình thức thanh toán</th>
        <th>Ngày đặt hàng</th>
        <th>Chi tiết đơn hàng</th>
      </tr>
      <?php while ($row = $orders->fetch_assoc()) { ?>
        <tr>
          <td>
            <span><?php echo $row['order_id']; ?></span>
          </td>

          <td>
            <span><?php echo number_format($row['order_cost'], 0, ',', '.'); ?>đ</span>
          </td>

          <td>
            <span><?php echo $row['order_status']; ?></span>
          </td>

          <td>
            <span><?php echo $row['payment_method']; ?></span>
          </td>

          <td>
            <span><?php echo $row['order_date']; ?></span>
          </td>

          <td>
            <form method="get" action="order_details.php">
              <input type="hidden" value="<?php echo $row['order_id']; ?>" name="order_id">
              <input class="btn order-details-btn" type="submit" value="Chi tiết">
            </form>
          </td>
        </tr>
      <?php } ?>
    </table>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>