<?php

session_start();

include './server/connection.php';

if (!isset($_SESSION['logged_in'])) {
  header('location: login.php');
  exit;
}

$shipping_address_id = (int)$_GET['shipping_address_id'];
$account_id = $_SESSION['user_id'];

if (!isset($_GET['shipping_address_id']) || !is_numeric($_GET['shipping_address_id'])) {
  header('location: account.php');
  exit;
}

$shipping_address_stmt = $conn->prepare("SELECT * FROM shipping_addresses WHERE shipping_address_id = ?");
$shipping_address_stmt->bind_param('i', $shipping_address_id);
$shipping_address_stmt->execute();
$shipping_address_stmt_result = $shipping_address_stmt->get_result();
$row = $shipping_address_stmt_result->fetch_assoc();

if (isset($_POST['edit_address_btn'])) {
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $payment_method = $_POST['payment_method'];
  $province = $_POST['province'];
  $district = $_POST['district'];
  $ward = $_POST['ward'];
  $address = $_POST['address'];

  $stmt = $conn->prepare("UPDATE shipping_addresses 
                          SET receiver_name = ?, phone_number = ?, payment_method = ?,
                          province = ?, district = ?, ward = ?, address = ?
                          WHERE shipping_address_id = ?");
  $stmt->bind_param(
    'sssssssi',
    $name,
    $phone,
    $payment_method,
    $province,
    $district,
    $ward,
    $address,
    $shipping_address_id
  );

  if ($stmt->execute()) {
    header('location: edit_address.php?shipping_address_id=' . $shipping_address_id . '&success=Cập nhật thông tin giao hàng thành công');
    exit;
  } else {
    header('location: edit_address.php?shipping_address_id=' . $shipping_address_id . '&error=Lỗi khi chỉnh sửa thông tin giao hàng');
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
  <title>Chỉnh sửa thông tin giao hàng</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Checkout -->
  <section class="my-5 py-5">
    <div class="container text-center mt-3">
      <h2 class="form-weight-bold">Chỉnh sửa thông tin giao hàng</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
      <form id="checkout-form" action="edit_address.php?shipping_address_id=<?php echo $shipping_address_id; ?>" method="post">
        <p class="text-success text-center">
          <?php if (isset($_GET['success'])) echo $_GET['success']; ?>
        </p>
        <p class="text-danger text-center">
          <?php if (isset($_GET['error'])) echo $_GET['error']; ?>
        </p>
        <div class="row mb-3">
          <div class="form-group col-md-4">
            <label class="form-label" for="checkout-name">Tên người nhận</label>
            <input type="text" class="form-control" id="checkout-name" name="name" placeholder="Nguyen Van A" value="<?php echo $row['receiver_name']; ?>" required>
          </div>
          <div class="form-group col-md-4">
            <label class="form-label" for="checkout-phone">Số điện thoại</label>
            <input type="text" class="form-control" id="checkout-phone" name="phone" placeholder="Phone" value="<?php echo $row['phone_number']; ?>" required>
          </div>
          <div class="form-group col-md-4">
            <label class="form-label" for="selectPaymentMethod">Hình thức thanh toán</label>
            <select name="payment_method" id="selectPaymentMethod" class="form-select">
              <option value="Tiền mặt" <?php if ($row['payment_method'] == 'Tiền mặt') echo 'selected'; ?>>Tiền mặt</option>
              <option value="Trực tuyến" <?php if ($row['payment_method'] == 'Trực tuyến') echo 'selected'; ?>>Trực tuyến</option>
            </select>
          </div>
        </div>
        <div class="row mb-3">
          <div class="form-group col-md-4">
            <input type="hidden" id="selectedProvince" value="<?php echo $row['province']; ?>">
            <label class="form-label" for="selectProvince">Tỉnh/Thành phố</label>
            <select name="province" id="selectProvince" class="form-select">
              <option value="">Chọn tỉnh/thành phố</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <input type="hidden" id="selectedDistrict" value="<?php echo $row['district']; ?>">
            <label class="form-label" for="selectDistrict">Quận/Huyện</label>
            <select name="district" id="selectDistrict" class="form-select">
              <option value="">Chọn quận/huyện</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <input type="hidden" id="selectedWard" value="<?php echo $row['ward']; ?>">
            <label class="form-label" for="selectWard">Phường/Xã</label>
            <select name="ward" id="selectWard" class="form-select">
              <option value="">Chọn phường/xã</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="checkout-address">Địa chỉ chi tiết</label>
          <input type="text" class="form-control" id="checkout-address" name="address" placeholder="Số nhà, đường, tòa nhà..." value="<?php echo $row['address']; ?>" required>
        </div>
        <input type="submit" class="btn btn-primary mt-3" name="edit_address_btn" value="Lưu">
      </form>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

  <script src="assets/js/index.js"></script>
</body>

</html>