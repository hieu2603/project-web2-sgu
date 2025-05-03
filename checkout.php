<?php

session_start();

if (!empty($_SESSION['cart']) && isset($_POST['checkout'])) {
  // Let user in checkout page

  // Go to home page
} else {
  header("location: index.php");
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
  <title>Checkout</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Checkout -->
  <section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
      <h2 class="form-weight-bold">Checkout</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
      <form id="checkout-form" action="server/place_order.php" method="post">
        <div class="form-group checkout-small-element">
          <label for="checkout-name">Name</label>
          <input type="text" class="form-control" id="checkout-name" name="name" placeholder="Name" required>
        </div>
        <div class="form-group checkout-small-element">
          <label for="checkout-email">Email</label>
          <input type="text" class="form-control" id="checkout-email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group checkout-small-element">
          <label for="checkout-phone">Phone</label>
          <input type="tel" class="form-control" id="checkout-phone" name="phone" placeholder="Phone" required>
        </div>
        <div class="form-group checkout-small-element">
          <label for="checkout-city">City</label>
          <input type="text" class="form-control" id="checkout-city" name="city" placeholder="City" required>
        </div>
        <div class="form-group checkout-large-element">
          <label for="checkout-address">Address</label>
          <input type="text" class="form-control" id="checkout-address" name="address" placeholder="Address" required>
        </div>
        <div class="form-group checkout-btn-container">
          <p>Total amount: $<?php echo $_SESSION['total'] ?></p>
          <input type="submit" name="place_order" class="btn" id="checkout-btn" value="Place Order">
        </div>
      </form>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>