<?php

session_start();

include 'server/connection.php';

if (!isset($_SESSION['user_id'])) {
  header('location: login.php');
  exit;
}

if (!isset($_POST['order_pay_btn']) && !isset($_POST['order_id'])) {
  header('location: index.php');
  exit;
}

$order_id = $_POST['order_id'];

$order_stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();

$order = $order_stmt->get_result();
$order_row = $order->fetch_assoc();

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
  <title>Payment</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Payment -->
  <section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
      <h2 class="form-weight-bold">Trang Thanh Toán</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container text-center">
      <p><strong>Mã đơn hàng:</strong> #<?php echo $order_row['order_id']; ?></p>
      <p><strong>Tổng tiền:</strong> <?php echo $order_row['order_cost']; ?>đ</p>
      <p><strong>Ngày đặt hàng:</strong> <?php echo $order_row['order_date']; ?></p>

      <form action="order_success.php" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="submit" name="pay_btn" class="btn btn-primary" value="Thanh toán">
      </form>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>