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
} else {
  header("location: index.php");
}

function calculateTotalCart()
{
  $total = 0;
  foreach ($_SESSION['cart'] as $key => $value) {
    $product = $_SESSION['cart'][$key];

    $quantity = $product['product_quantity'];
    $price = $product['product_price'];

    $total += $quantity * $price;
  }

  $_SESSION['total'] = $total;
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
  <title>Cart</title>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
      <img src="assets/img/logo.jpg" alt="logo_img">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

          <li class="nav-item">
            <a class="nav-link" href="index.html">Home</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="shop.html">Shop</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#">Blog</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="contact.html">Contact Us</a>
          </li>

          <li class="nav-item">
            <a href="cart.html"><i class="fa-solid fa-cart-shopping"></i></a>
            <a href="account.html"><i class="fa-solid fa-user"></i></a>
          </li>

        </ul>
      </div>
    </div>
  </nav>

  <!-- Cart -->
  <section class="cart container my-5 py-5">
    <div class="container mt-5">
      <h2 class="font-weight-bolde">Your Cart</h2>
      <hr>
    </div>

    <table class="mt-5 pt-5">
      <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Subtotal</th>
      </tr>

      <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
        <tr>
          <td>
            <div class="product-info">
              <img src="assets/img/<?php echo $value['product_image'] ?>" alt="featured_1_img">
              <div>
                <p><?php echo $value['product_name'] ?></p>
                <small><span>$</span><?php echo $value['product_price'] ?></small>
                <br>
                <form action="cart.php" method="post">
                  <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                  <input type="submit" name="remove_product" class="remove-btn" value="Remove">
                </form>
              </div>
            </div>
          </td>

          <td>
            <form action="cart.php" method="post">
              <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
              <input type="number" name="product_quantity" value="<?php echo $value['product_quantity'] ?>" min="1" max="9">
              <input type="submit" name="edit_quantity" class="edit-btn" value="Edit">
            </form>
          </td>

          <td>
            <span class="product-price">$<?php echo $value['product_quantity'] * $value['product_price']; ?></span>
          </td>
        </tr>
      <?php } ?>
    </table>
    </div>

    <div class="cart-total">
      <table>
        <!-- <tr>
          <td>Subtotal</td>
          <td>$69</td>
        </tr> -->
        <tr>
          <td>Total</td>
          <td>$<?php echo $_SESSION['total']; ?></td>
        </tr>
      </table>
    </div>

    <div class="checkout-container">
      <button class="btn checkout-btn">Checkout</button>
    </div>
  </section>

  <!-- Footer -->
  <footer class="mt-5 py-5">
    <div class="row container mx-auto pt-5">
      <div class="footer-one col-lg-3 col-md-6 col-sm-12">
        <img src="assets/img/logo.jpg" alt="logo_img">
        <p class="pt-3">We provide the best products for the most affordable prices</p>
      </div>

      <div class="footer-one col-lg-3 col-md-6 col-sm-12">
        <h5 class="pb-2">Featured</h5>
        <ul class="text-uppercase">
          <li><a href="#">Men</a></li>
          <li><a href="#">Women</a></li>
          <li><a href="#">Boys</a></li>
          <li><a href="#">Girls</a></li>
          <li><a href="#">New Arrivals</a></li>
          <li><a href="#">Clothes</a></li>
        </ul>
      </div>

      <div class="footer-one col-lg-3 col-md-6 col-sm-12">
        <h5 class="pb-2">Contact Us</h5>
        <div>
          <h6 class="text-uppercase">Address</h6>
          <p>Saigon University (SGU)</p>
        </div>
        <div>
          <h6 class="text-uppercase">Phone Number</h6>
          <p>(84-8) 38.354409 - 38.352309</p>
        </div>
        <div>
          <h6 class="text-uppercase">Email</h6>
          <p>vanphong@sgu.edu.vn</p>
        </div>
      </div>

      <div class="footer-one col-lg-3 col-md-6 col-sm-12">
        <h5 class="pb-2">Facebook</h5>
        <div class="row">
          <img src="assets/img/featured_1.jpg" alt="featured_1_img" class="img-fluid w-25 h-100 m-2">
          <img src="assets/img/featured_2.jpg" alt="featured_2_img" class="img-fluid w-25 h-100 m-2">
          <img src="assets/img/featured_3.jpg" alt="featured_3_img" class="img-fluid w-25 h-100 m-2">
          <img src="assets/img/featured_4.jpg" alt="featured_4_img" class="img-fluid w-25 h-100 m-2">
        </div>
      </div>
    </div>

    <div class="copyright mt-5">
      <div class="row container mx-auto">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
          <img src="assets/img/payment.jpg" alt="payment_img">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4 text-nowrap">
          <p>E-Commerce © 2025 All Right Reserved</p>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
          <a href="#"><i class="fa-brands fa-facebook"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>