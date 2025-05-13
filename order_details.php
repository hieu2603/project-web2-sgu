<?php

session_start();

include 'server/connection.php';

if (isset($_POST['order_details_btn']) && isset($_POST['order_id'])) {
  $order_id = $_POST['order_id'];
  $order_status = $_POST['order_status'];

  $stmt = $conn->prepare("SELECT products.product_name, products.product_image, 
                          products.product_price, order_items.product_quantity 
                          FROM products 
                          INNER JOIN order_items
                          ON products.product_id=order_items.product_id
                          WHERE order_id = ?");

  $stmt->bind_param('i', $order_id);

  $stmt->execute();

  $order_details = $stmt->get_result();

  $order_total_price = calculateTotalOrderPrice($order_details);
} else {
  header('location: account.php');
  exit;
}

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
  <title>Order Details</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Order Details -->
  <section id="orders" class="orders order_items container my-5 py-5">
    <div class="container">
      <h2 class="font-weight-bolde text-center">Order Details</h2>
      <hr class="mx-auto">
    </div>

    <table class="mt-5 pt-5">
      <tr>
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>
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
            <span>$<?php echo $row['product_price']; ?></span>
          </td>

          <td>
            <span><?php echo $row['product_quantity']; ?></span>
          </td>
        </tr>
      <?php } ?>
    </table>

    <?php if ($order_status != "Hoàn thành") { ?>
      <div class="text-end mt-4">
        <form method="post" action="payment.php" style="display: inline-block;">
          <input type="hidden" name="order_total_price" value="<?php echo $order_total_price; ?>">
          <input type="hidden" name="order_status" value="<?php echo $order_status; ?>">
          <input type="submit" name="order_pay_btn" class="btn btn-primary me-2" value="Thanh toán">
        </form>

        <form action="order_details.php" method="post" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
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