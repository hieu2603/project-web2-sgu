<?php

session_start();

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
      <h2 class="form-weight-bold">Payment</h2>
      <hr class="mx-auto">
    </div>
    <div class="mx-auto container text-center">
      <p><?php if (isset($_GET['order_status'])) {
            echo $_GET['order_status'];
          } ?></p>
      <?php if (
        isset($_SESSION['total']) && ($_SESSION['total'] != 0) ||
        (isset($_GET['order_status']) && $_GET['order_status'] === 'Not Paid')
      ) { ?>
        <p>Total payment: $<?php if (isset($_SESSION['total'])) {
                              echo $_SESSION['total'];
                            } ?></p>
        <input type="submit" class="btn btn-primary" value="Pay Now">
      <?php } else { ?>
        <p>You don't have an order!</p>
      <?php } ?>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>