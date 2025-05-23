<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
  header('location: orders.php');
  exit;
}

$order_id = (int)$_GET['order_id'];

$order_stmt = $conn->prepare("SELECT orders.*, accounts.account_name 
                              FROM orders
                              INNER JOIN accounts
                              ON orders.account_id=accounts.account_id 
                              WHERE order_id = ?");
$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();
$order = $order_stmt->get_result();

if ($order->num_rows === 0) {
  header('location: orders.php');
  exit;
}

$order_row = $order->fetch_assoc();

$order_items_stmt = $conn->prepare("SELECT order_items.item_id, products.product_name, 
                                    products.product_image, products.product_price, 
                                    order_items.product_quantity 
                                    FROM products 
                                    INNER JOIN order_items
                                    ON products.product_id=order_items.product_id
                                    WHERE order_items.order_id = ?");

$order_items_stmt->bind_param('i', $order_id);
$order_items_stmt->execute();
$order_items = $order_items_stmt->get_result();

if (isset($_POST['confirm_order_btn'])) {
  $confirm_order_stmt = $conn->prepare("UPDATE orders
                                        SET order_status = 'Đã xác nhận'
                                        WHERE order_id = ?");

  $confirm_order_stmt->bind_param('i', $order_id);

  if ($confirm_order_stmt->execute()) {
    header('location: orders.php');
    exit;
  } else {
    header('location: order_details.php?error=Không thể xác nhận đơn hàng&order_id=' . $order_id);
  }
}

if (isset($_POST['complete_order_btn'])) {
  $complete_order_stmt = $conn->prepare("UPDATE orders
                                        SET order_status = 'Thành công'
                                        WHERE order_id = ?");

  $complete_order_stmt->bind_param('i', $order_id);

  if ($complete_order_stmt->execute()) {
    header('location: orders.php');
    exit;
  } else {
    header('location: order_details.php?error=Không thể hoàn thành đơn hàng&order_id=' . $order_id);
  }
}

if (isset($_POST['cancel_order_btn'])) {
  $cancel_order_stmt = $conn->prepare("UPDATE orders
                                        SET order_status = 'Hủy đơn'
                                        WHERE order_id = ?");

  $cancel_order_stmt->bind_param('i', $order_id);

  if ($cancel_order_stmt->execute()) {
    header('location: orders.php');
    exit;
  } else {
    header('location: order_details.php?error=Không thể hủy đơn hàng&order_id=' . $order_id);
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
  <title>Order Details</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <div class="col py-3">
        <h2 class="text-center mb-3">Chi tiết đơn hàng</h2>
        <div class="container mx-auto">
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

          <form action="order_details.php?order_id=<?php echo $order_id; ?>" method="post">
            <?php if ($order_row['order_status'] == 'Chưa xác nhận') { ?>
              <input class="btn btn-primary" type="submit" name="confirm_order_btn" value="Xác nhận">
              <input class="btn btn-danger ms-2" type="submit" name="cancel_order_btn" value="Từ chối">
            <?php } elseif ($order_row['order_status'] == 'Đã xác nhận') { ?>
              <input class="btn btn-primary" type="submit" name="complete_order_btn" value="Hoàn thành">
            <?php } elseif ($order_row['order_status'] == 'Chờ thanh toán') { ?>
              <input class="btn btn-danger" type="submit" name="cancel_order_btn" value="Từ chối">
            <?php } ?>
          </form>

          <div class="row">
            <table class="table text-center">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Hình ảnh</th>
                  <th scope="col">Tên sản phẩm</th>
                  <th scope="col">Giá</th>
                  <th scope="col">Số lượng</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($order_items_row = $order_items->fetch_assoc()) { ?>
                  <tr class="align-middle">
                    <th><?php echo $order_items_row['item_id']; ?></th>
                    <td><img src="<?php echo $order_items_row['product_image']; ?>" style="width: 70px; height: 70px;"></td>
                    <td><?php echo $order_items_row['product_name']; ?></td>
                    <td><?php echo number_format($order_items_row['product_price'], 0, ',', '.'); ?>đ</td>
                    <td><?php echo $order_items_row['product_quantity']; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>