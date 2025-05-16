<?php
session_start();

include 'server/connection.php';

if (!isset($_SESSION['user_id'])) {
  header('location: login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_btn']) && isset($_POST['order_id'])) {
  $order_id = $_POST['order_id'];

  $pay_order_stmt = $conn->prepare("UPDATE orders
                                    SET order_status = 'Đã xác nhận'
                                    WHERE order_id = ?");

  $pay_order_stmt->bind_param('i', $order_id);

  $pay_order_stmt->execute();

  header('location: order_success.php?order_id=' . $order_id);
  exit;
}

if (!isset($_GET['order_id'])) {
  header('location: shop.php');
  exit;
}

$order_id = $_GET['order_id'];

?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đặt hàng thành công</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .success-icon {
      font-size: 60px;
      color: #28a745;
    }

    .order-box {
      border: 1px solid #ccc;
      padding: 25px;
      border-radius: 10px;
      background-color: #f8f9fa;
    }
  </style>
</head>

<body>

  <div class="container text-center">
    <div class="row justify-content-center">
      <div class="col-md-5 order-box shadow-sm">
        <div class="mb-2">
          <i class="fas fa-check-circle success-icon"></i>
        </div>
        <h2 class="text-success">Đặt hàng thành công!</h2>

        <h5>Đơn hàng sẽ được hệ thống xác nhận trong giây lát...</h5>

        <div class="d-grid gap-2 mt-4 mx-auto" style="width: 300px;">
          <a href="order_details.php?order_id=<?php echo $order_id; ?>" class="btn btn-warning w-100">
            Xem chi tiết đơn hàng
          </a>
          <a href="shop.php" class="btn btn-outline-primary w-100">
            Tiếp tục mua sắm
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/d32f1bec50.js" crossorigin="anonymous"></script>
</body>

</html>