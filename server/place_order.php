<?php

session_start();
include 'connection.php';

// If user is not logged in
if (!isset($_SESSION['logged_in'])) {
  header('location: ../checkout.php?message=Please login to place order');
  exit;

  // If user is logged in
} else {
  if (isset($_POST['place_order'])) {
    // 1. Get user info and store it in database
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $ward = $_POST['ward'];
    $address = $_POST['address'];
    $order_cost = $_SESSION['total'];
    $order_status = $payment_method == 'Tiền mặt' ? 'Chưa xác nhận' : 'Chờ thanh toán';
    $user_id = $_SESSION['user_id'];

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $order_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, 
                            account_id, receiver_name, phone_number, province, 
                            district, ward, address, order_date, payment_method) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
      'isissssssss',
      $order_cost,
      $order_status,
      $user_id,
      $name,
      $phone,
      $province,
      $district,
      $ward,
      $address,
      $order_date,
      $payment_method
    );

    $order_stmt = $stmt->execute();

    if (!$order_stmt) {
      header('location: index.php');
      exit;
    }

    // 1.5 Get last inserted order
    $order_id = $stmt->insert_id;

    // 2. Get products from cart (from session) and store in order_items database
    foreach ($_SESSION['cart'] as $key => $value) {
      $product = $_SESSION['cart'][$key];

      $product_id = $product['product_id'];
      // $product_name = $product['product_name'];
      // $product_image = $product['product_image'];
      // $product_price = $product['product_price'];
      $product_quantity = $product['product_quantity'];

      $stmt1 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_quantity)
                               VALUES (?, ?, ?)");

      $stmt1->bind_param('iii', $order_id, $product_id, $product_quantity);

      $stmt1->execute();
    }

    // 3. Remove products from cart -> delay until payment is done
    // unset($_SESSION['cart']);

    header('location: ../payment.php?order_status="Order placed successfully"');
  }
}
