<?php

session_start();

include 'server/connection.php';

if (isset($_POST['order_details_btn']) && isset($_POST['order_id'])) {
  $order_id = $_POST['order_id'];
  $order_status = $_POST['order_status'];

  $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");

  $stmt->bind_param('i', $order_id);

  $stmt->execute();

  $order_details = $stmt->get_result();
} else {
  header('location: account.php');
  exit;
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
      <?php while ($row = $order_details->fetch_assoc()) { ?>
        <tr>
          <td>
            <div class="product-info">
              <img src="/assets/img/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_image']; ?>">
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

    <?php if ($order_status == "Not Paid") { ?>
      <form style="float: right;" action="">
        <input type="submit" class="btn btn-primary" value="Pay Now">
      </form>
    <?php } ?>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>