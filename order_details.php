<?php

session_start();

include 'server/connection.php';

if (!isset($_SESSION['user_id'])) {
  header('location: login.php');
  exit;
}

$account_id = $_SESSION['user_id'];

if (isset($_POST['cancel_order_btn']) && isset($_POST['order_id'])) {
  $order_id = $_POST['order_id'];

  $cancel_order_stmt = $conn->prepare("UPDATE orders 
                                       SET order_status = 'Hủy đơn' 
                                       WHERE order_id = ?
                                       AND account_id = ?");

  $cancel_order_stmt->bind_param('ii', $order_id, $account_id);

  if ($cancel_order_stmt->execute()) {
    header('location: account.php');
    exit;
  }
}

if (!isset($_GET['order_id'])) {
  header('location: account.php');
  exit;
}

$order_id = $_GET['order_id'];

$order_stmt = $conn->prepare("SELECT orders.*, accounts.account_name 
                              FROM orders
                              INNER JOIN accounts
                              ON orders.account_id=accounts.account_id 
                              WHERE order_id = ?");

$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows == 0) {
  header('location: account.php');
  exit;
}

$order_row = $order_result->fetch_assoc();
$order_status = $order_row['order_status'];
$payment_method = $order_row['payment_method'];

$stmt = $conn->prepare("SELECT products.product_name, products.product_image, 
                        products.product_price, order_items.product_quantity 
                        FROM products 
                        INNER JOIN order_items
                        ON products.product_id=order_items.product_id
                        WHERE order_items.order_id = ?");

$stmt->bind_param('i', $order_id);
$stmt->execute();
$order_details = $stmt->get_result();

function calculateTotalOrderPrice($order_details)
{
  $total = 0;

  foreach ($order_details as $row) {
    $product_price = $row['product_price'];
    $product_quantity = $row['product_quantity'];

    $total += $product_price * $product_quantity;
  }

  return $total;
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
  <title>Chi tiết đơn hàng</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Order Details -->
  <section id="orders" class="orders order_items container my-5 py-5">
    <div class="container mt-3">
      <h2 class="font-weight-bolde text-center">Chi tiết đơn hàng</h2>
      <hr class="mx-auto">
    </div>

    <div class="row">
      <div class="col-md-4">
        <p><strong>Mã đơn hàng:</strong> <?php echo $order_row['order_id']; ?></p>
        <p><strong>Tài khoản đặt hàng:</strong> <?php echo $order_row['account_name']; ?></p>
        <p><strong>Tỉnh/thành phố:</strong> <?php echo $order_row['province']; ?></p>
        <p><strong>Địa chỉ:</strong> <?php echo $order_row['address']; ?></p>
      </div>
      <div class="col-md-4">
        <p><strong>Tổng tiển:</strong> <?php echo number_format($order_row['order_cost'], 0, ',', '.'); ?>đ</p>
        <p><strong>Người nhận:</strong> <?php echo $order_row['receiver_name']; ?></p>
        <p><strong>Quận/huyện:</strong> <?php echo $order_row['district']; ?></p>
        <p><strong>Ngày đặt hàng:</strong> <?php echo $order_row['order_date']; ?></p>
      </div>
      <div class="col-md-4">
        <p class="<?php include '../admin/utils/order_status_color.php'; ?>">
          <strong style="color: black;">Trạng thái đơn hàng:</strong>
          <?php echo $order_row['order_status']; ?>
        </p>
        <p><strong>Số điện thoại:</strong> <?php echo $order_row['phone_number']; ?></p>
        <p><strong>Phường/xã:</strong> <?php echo $order_row['ward']; ?></p>
        <p><strong>Hình thức thanh toán:</strong> <?php echo $order_row['payment_method']; ?></p>
      </div>
    </div>

    <table class="text-center mt-3">
      <tr>
        <th class="text-start">Tên sản phẩm</th>
        <th>Giá</th>
        <th>Số lượng</th>
      </tr>
      <?php foreach ($order_details as $row) { ?>
        <tr>
          <td>
            <div class="product-info">
              <img src="<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_image']; ?>">
              <div>
                <p class="mt-3"><?php echo $row['product_name']; ?></p>
              </div>
            </div>
          </td>

          <td>
            <span><?php echo number_format($row['product_price'], 0, ',', '.'); ?>đ</span>
          </td>

          <td>
            <span><?php echo $row['product_quantity']; ?></span>
          </td>
        </tr>
      <?php } ?>
    </table>

    <?php if (($order_status != "Thành công") && ($order_status != "Hủy đơn")) { ?>
      <div class="text-end mt-2">
        <?php if ($payment_method == 'Trực tuyến' && $order_status == 'Chờ thanh toán') { ?>
          <form method="post" action="payment.php" style="display: inline-block;">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="submit" name="order_pay_btn" class="btn btn-primary me-2" value="Thanh toán">
          </form>
        <?php } ?>

        <form action="order_details.php" method="post" style="display: inline-block;">
          <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
          <input type="submit" name="cancel_order_btn" class="btn btn-danger" value="Hủy đơn">
        </form>
      </div>
    <?php } ?>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>