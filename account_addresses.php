<?php

session_start();

include 'server/connection.php';

if (!isset($_SESSION['logged_in'])) {
  header('location: login.php');
  exit;
}

$account_id = $_GET['account_id'];

if (!isset($_GET['account_id']) || !is_numeric($_GET['account_id'])) {
  header('location: account.php');
  exit;
}

if ($account_id != $_SESSION['user_id']) {
  header('location: account.php');
  exit;
}

$stmt = $conn->prepare("SELECT * FROM shipping_addresses WHERE account_id = ?");
$stmt->bind_param('i', $account_id);
$stmt->execute();
$stmt_result = $stmt->get_result();

if (isset($_POST['delete_address_btn']) && isset($_POST['shipping_address_id'])) {
  $shipping_address_id = $_POST['shipping_address_id'];

  $delete_address_stmt = $conn->prepare("DELETE FROM shipping_addresses WHERE shipping_address_id = ?");
  $delete_address_stmt->bind_param('i', $shipping_address_id);

  if ($delete_address_stmt->execute()) {
    header('location: account_addresses.php?account_id=' . $account_id);
    exit;
  } else {
    header('location: account_addresses.php?error=Lỗi khi xóa thông tin');
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
  <link rel="stylesheet" href="assets/css/style.css">
  <title>Account</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Account Addresses -->
  <section id="addresses" class="addresses container my-5 py-5">
    <div class="container text-center mt-3">
      <h2 class="form-weight-bold">Danh sách thông tin</h2>
      <hr class="mx-auto">
    </div>
    <a href="add_address.php" class="btn btn-primary mt-2">Tạo mới</a>
    <table class="mt-2 pt-5 text-center">
      <tr>
        <th class="text-start">Thông tin giao hàng</th>
        <th>Chọn</th>
        <th>Sửa</th>
        <th>Xóa</th>
      </tr>
      <?php while ($row = $stmt_result->fetch_assoc()) { ?>
        <tr>
          <td class="text-center align-middle">
            <div class="text-start">
              <span><?php echo $row['receiver_name']; ?></span><br>
              <span><?php echo $row['phone_number']; ?></span><br>
              <span><?php echo $row['payment_method']; ?></span><br>
              <span><?php echo $row['province'] . ', ' . $row['district'] . ', ' . $row['ward']; ?></span><br>
              <span><?php echo $row['address']; ?></span>
            </div>
          </td>

          <td>
            <form action="checkout.php" method="post">
              <input type="hidden" name="shipping_address_id" value="<?php echo $row['shipping_address_id']; ?>">
              <input type="submit" class="btn btn-primary" name="choose_address" value="Chọn">
            </form>
          </td>

          <td>
            <a href="edit_address.php?shipping_address_id=<?php echo $row['shipping_address_id']; ?>" class="btn btn-warning">Sửa</a>
          </td>

          <td>
            <form action="account_addresses.php?account_id=<?php echo $account_id; ?>" method="post">
              <input type="hidden" name="shipping_address_id" value="<?php echo $row['shipping_address_id']; ?>">
              <input type="submit" class="btn btn-danger" name="delete_address_btn" value="Xóa">
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