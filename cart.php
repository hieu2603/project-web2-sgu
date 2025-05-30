<?php

session_start();

if (isset($_POST['add_to_cart'])) {
  // if user has already added a cart product to cart
  if (isset($_SESSION['cart'])) {
    $products_array_id = array_column($_SESSION['cart'], 'product_id'); // Get all product id
    // if product has already been added to cart or not
    if (!in_array($_POST['product_id'], $products_array_id)) {
      $product_id = $_POST['product_id'];

      $product_array = array(
        'product_id' => $_POST['product_id'],
        'product_name' => $_POST['product_name'],
        'product_price' => $_POST['product_price'],
        'product_image' => $_POST['product_image'],
        'product_quantity' => $_POST['product_quantity'],
      );

      $_SESSION['cart'][$product_id] = $product_array;

      // product has already been added
    } else {
      echo '<script>alert("Product was already added to cart")</script>';
    }

    // if this is the first product
  } else {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $product_array = array(
      'product_id' => $product_id,
      'product_name' => $product_name,
      'product_price' => $product_price,
      'product_image' => $product_image,
      'product_quantity' => $product_quantity,
    );

    $_SESSION['cart'][$product_id] = $product_array;
  }

  // Calculate total price
  calculateTotalCart();

  // Remove product from the cart
} elseif (isset($_POST['remove_product'])) {
  $product_id = $_POST['product_id'];
  unset($_SESSION['cart'][$product_id]);

  // Calculate total price
  calculateTotalCart();
} elseif (isset($_POST['edit_quantity'])) {
  // Get product id and new quantity
  $product_id = $_POST['product_id'];
  $product_quantity = $_POST['product_quantity'];

  // Get product need to update the quantity
  $product_array = $_SESSION['cart'][$product_id];

  // Update new quantity
  $product_array['product_quantity'] = $product_quantity;

  // Update product in session
  $_SESSION['cart'][$product_id] = $product_array;

  // Calculate total price
  calculateTotalCart();

  // Redirect to login page
} elseif (!$_SESSION['logged_in']) {
  header("location: login.php");
}

function calculateTotalCart()
{
  $total_price = 0;

  foreach ($_SESSION['cart'] as $key => $value) {
    $product = $_SESSION['cart'][$key];

    $quantity = $product['product_quantity'];
    $price = $product['product_price'];

    $total_price += $quantity * $price;
  }

  $_SESSION['total'] = $total_price;
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
  <title>Giỏ hàng</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Cart -->
  <section class="cart container my-5 py-5">
    <div class="container mt-3 text-center mx-auto">
      <h2 class="font-weight-bold">Giỏ hàng</h2>
      <hr class="mx-auto">
    </div>

    <?php if (isset($_SESSION['total']) && $_SESSION['total'] > 0) { ?>
      <table class="mt-3 pt-5">
        <tr>
          <th>Sản phẩm</th>
          <th>Số lượng</th>
          <th class="text-end">Tổng cộng</th>
        </tr>

        <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
          <tr>
            <td>
              <div class="product-info">
                <img src="<?php echo $value['product_image']; ?>" alt="featured_1_img">
                <div>
                  <p><?php echo $value['product_name']; ?></p>
                  <p><?php echo number_format($value['product_price'], 0, ',', '.'); ?>đ</p>
                  <form action="cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                    <input type="submit" name="remove_product" class="remove-btn" value="Xóa">
                  </form>
                </div>
              </div>
            </td>

            <td>
              <form action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                <input type="number" name="product_quantity" value="<?php echo $value['product_quantity'] ?>" min="1" max="9">
                <input type="submit" name="edit_quantity" class="edit-btn" value="Sửa">
              </form>
            </td>

            <td>
              <p class="product-price mb-0 text-end"><?php echo number_format($value['product_quantity'] * $value['product_price'], 0, ',', '.'); ?>đ</p>
            </td>
          </tr>
        <?php } ?>
      </table>
      </div>

      <div class="cart-total">
        <table>
          <tr class="text-end">
            <td style="font-weight: bold;">Tổng tiền:</td>
            <td style="font-weight: bold;"><span class="text-success fs-5"><?php echo number_format($_SESSION['total'], 0, ',', '.'); ?>đ</span></td>
          </tr>
        </table>
      </div>

      <div class="checkout-container">
        <form action="checkout.php" method="post">
          <input type="submit" name="checkout" class="btn checkout-btn" value="Đặt hàng">
        </form>
      </div>
    <?php } else { ?>
      <p class="text-center">Giỏ hàng trống!</p>
    <?php } ?>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>