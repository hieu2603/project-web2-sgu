<?php

session_start();

include 'server/connection.php';

if (!isset($_SESSION['logged_in'])) {
  header('location: login.php');
  exit;
}

// Logout
if (isset($_GET['logout'])) {
  if (isset($_SESSION['logged_in'])) {
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    header('location: login.php');
    exit;
  }
}

// Change password
if (isset($_POST['change_password_btn'])) {
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];
  $user_email = $_SESSION['user_email'];

  if ($password !== $confirmPassword) {
    header("location: account.php?error=Password doesn't match");
  } elseif (strlen($password) < 8) {
    header("location: account.php?error=Password must be at least 8 characters");
  } else {
    $stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_email = ?");
    $stmt->bind_param('ss', md5($password), $user_email);

    if ($stmt->execute()) {
      header('location: account.php?message=Password has been updated successfully');
    } else {
      header('location: account.php?error=Could not update password');
    }
  }
}

// Get orders
if (isset($_SESSION['logged_in'])) {
  $user_id = $_SESSION['user_id'];

  $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");

  $stmt->bind_param('i', $user_id);

  $stmt->execute();

  $orders = $stmt->get_result();
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
  <title>Account</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Account -->
  <section class="my-5 py-5">
    <div class="row container mx-auto">
      <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
        <p class="text-center" style="color: green;"><?php if (isset($_GET["register_success"])) echo $_GET['register_success']; ?></p>
        <p class="text-center" style="color: green;"><?php if (isset($_GET["login_success"])) echo $_GET['login_success']; ?></p>
        <h3 class="font-weight-bold">Account Info</h3>
        <hr class="mx-auto">
        <div class="account-info">
          <p>Name <span><?php if (isset($_SESSION['user_name'])) echo $_SESSION['user_name']; ?></span></p>
          <p>Email <span><?php if (isset($_SESSION['user_email'])) echo $_SESSION['user_email']; ?></span></p>
          <p><a href="#orders" id="orders-btn">Your orders</a></p>
          <p><a href="account.php?logout=true" id="logout-btn">Logout</a></p>
        </div>
      </div>

      <div class="col-lg-6 col-md-12 col-sm-12">
        <form id="account-form" method="post" action="account.php">
          <p class="text-center" style="color: red;"><?php if (isset($_GET["error"])) echo $_GET['error']; ?></p>
          <p class="text-center" style="color: green;"><?php if (isset($_GET["message"])) echo $_GET['message']; ?></p>
          <h3>Change Password</h3>
          <hr class="mx-auto">
          <div class="form-group">
            <label for="account-password">Password</label>
            <input type="password" class="form-control" id="account-password" name="password" placeholder="Password"
              required>
          </div>
          <div class="form-group">
            <label for="account-confirm-password">Confirm Password</label>
            <input type="password" class="form-control" id="account-confirm-password" name="confirmPassword"
              placeholder="Confirm Password" required>
          </div>
          <div class="form-group">
            <input type="submit" value="Change Password" name="change_password_btn" class="btn" id="change-password-btn">
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Orders -->
  <section id="orders" class="orders container my-5 py-3">
    <div class="container">
      <h2 class="font-weight-bolde text-center">Your Orders</h2>
      <hr class="mx-auto">
    </div>

    <table class="mt-5 pt-5">
      <tr>
        <th>ID</th>
        <th>Cost</th>
        <th>Status</th>
        <th>Date</th>
        <th>Details</th>
      </tr>
      <?php while ($row = $orders->fetch_assoc()) { ?>
        <tr>
          <td>
            <span><?php echo $row['order_id']; ?></span>
          </td>

          <td>
            <span>$<?php echo $row['order_cost']; ?></span>
          </td>

          <td>
            <span><?php echo $row['order_status']; ?></span>
          </td>

          <td>
            <span><?php echo $row['order_date']; ?></span>
          </td>

          <td>
            <form method="post" action="order_details.php">
              <input type="hidden" value="<?php echo $row['order_status']; ?>" name="order_status">
              <input type="hidden" value="<?php echo $row['order_id']; ?>" name="order_id">
              <input class="btn order-details-btn" name="order_details_btn" type="submit" value="Details">
            </form>
          </td>
        </tr>
      <?php } ?>
    </table>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>