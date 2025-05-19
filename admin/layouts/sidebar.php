<?php

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

<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark d-flex flex-column min-vh-100">
  <!-- Logo -->
  <a href="#" class="d-flex align-items-center justify-content-center py-3 mb-3 text-white text-decoration-none border-bottom">
    <span class="fs-4 d-none d-sm-inline">Admin Panel</span>
  </a>

  <!-- Navigation -->
  <ul class="nav nav-pills flex-column mb-auto px-2">
    <li class="nav-item">
      <a href="dashboard.php" class="nav-link text-white sidebar-link">
        <i class="fa-solid fa-gauge sidebar-icon"></i>
        <span class="sidebar-text">Trang chủ</span>
      </a>
    </li>
    <li>
      <a href="orders.php" class="nav-link text-white sidebar-link">
        <i class="fa-solid fa-receipt sidebar-icon"></i>
        <span class="sidebar-text">Đơn hàng</span>
      </a>
    </li>
    <li>
      <a href="accounts.php" class="nav-link text-white sidebar-link">
        <i class="fa-solid fa-users sidebar-icon"></i>
        <span class="sidebar-text">Tài khoản</span>
      </a>
    </li>
    <li>
      <a href="products.php" class="nav-link text-white sidebar-link">
        <i class="fa-solid fa-box sidebar-icon"></i>
        <span class="sidebar-text">Sản phẩm</span>
      </a>
    </li>
    <li>
      <a href="categories.php" class="nav-link text-white sidebar-link">
        <i class="fa-solid fa-tag sidebar-icon"></i>
        <span class="sidebar-text">Phân Loại</span>
      </a>
    </li>
  </ul>

  <!-- Dropdown Bottom -->
  <div class="d-flex justify-content-center mb-4">
    <div class="dropdown open">
      <button
        class="btn btn-secondary dropdown-toggle"
        type="button"
        id="triggerId"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false">
        Account
      </button>
      <div class="dropdown-menu" aria-labelledby="triggerId">
        <a class="dropdown-item" href="#">Settings</a>
        <a class="dropdown-item" href="dashboard.php?logout=true">Logout</a>
      </div>
    </div>
  </div>

</div>