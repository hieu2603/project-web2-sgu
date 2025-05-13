<?php

session_start();

include '../server/connection.php';

$admin_name = $_SESSION['admin_name'];

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

// Logout
if (isset($_GET['logout'])) {
  if (isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    header('location: login.php');
    exit;
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
  <title>Admin Orders</title>
</head>

<body>
  <?php include '../admin/layouts/sidebar.php'; ?>

  <!-- <h2>Orders</h2> -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>