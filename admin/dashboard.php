<?php

session_start();

include '../server/connection.php';

$admin_name = $_SESSION['admin_name'];

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

// Tính doanh thu 30 ngày gần nhất
$revenue_last_30_days_stmt = $conn->prepare("SELECT SUM(order_cost) AS revenue_last_30_days
                                        FROM orders
                                        WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                                        AND order_status = 'Thành công'");
$revenue_last_30_days_stmt->execute();
$revenue_last_30_days_result = $revenue_last_30_days_stmt->get_result();
$revenue_last_30_days_row = $revenue_last_30_days_result->fetch_assoc();

// Tính số đơn hàng chưa xác nhận
$order_stmt = $conn->prepare("SELECT COUNT(*) AS total
                              FROM orders
                              WHERE order_status = 'Chưa xác nhận'");
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order_row = $order_result->fetch_assoc();

// Tính số tài khoản đang hoạt động
$account_stmt = $conn->prepare("SELECT COUNT(*) AS total
                                FROM accounts
                                WHERE account_status = 'Active'");
$account_stmt->execute();
$account_result = $account_stmt->get_result();
$account_row = $account_result->fetch_assoc();

// Tính số sản phẩm đang bán
$product_stmt = $conn->prepare("SELECT COUNT(*) AS total
                                FROM products
                                WHERE product_status = 'Đang bán'");
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product_row = $product_result->fetch_assoc();

$date_condition = "";
$params = [];
$types = "";

if (isset($_GET['search_btn'])) {
  if (!empty($_GET['fromDay']) && !empty($_GET['toDay'])) {
    $date_condition = "AND o.order_date BETWEEN ? AND ?";
    $params[] = $_GET['fromDay'] . " 00:00:00";
    $params[] = $_GET['toDay'] . " 23:59:59";
    $types .= "ss";
  } elseif (!empty($_GET['fromDay'])) {
    $date_condition = "AND o.order_date >= ?";
    $params[] = $_GET['fromDay'] . " 00:00:00";
    $types .= "s";
  } elseif (!empty($_GET['toDay'])) {
    $date_condition = "AND o.order_date <= ?";
    $params[] = $_GET['toDay'] . " 23:59:59";
    $types .= "s";
  }
}

$total_spent_stmt = $conn->prepare("SELECT a.account_id, a.account_name, 
                                    SUM(o.order_cost) AS total_spent
                                    FROM accounts a 
                                    JOIN orders o ON a.account_id = o.account_id
                                    WHERE o.order_status = 'Thành công' $date_condition
                                    GROUP BY a.account_id, a.account_name
                                    ORDER BY total_spent DESC
                                    LIMIT 5");

if (!empty($params)) {
  $total_spent_stmt->bind_param($types, ...$params);
}

$total_spent_stmt->execute();
$total_spent_result = $total_spent_stmt->get_result();

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
  <title>Admin Dashboard</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <!-- Content (optional) -->
      <div class="col py-3">
        <h2>Thống kê</h2>
        <div class="row mb-4">
          <div class="col-12 col-md-3">
            <div class="card shadow">
              <div class="card-body py-4">
                <h6 class="mb-2 fw-bold">
                  Doanh thu 30 ngày gần nhất
                </h6>
                <p class="mb-2">
                  <?php echo $revenue_last_30_days_row['revenue_last_30_days']; ?>đ
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="card shadow">
              <div class="card-body py-4">
                <h6 class="mb-2 fw-bold">
                  Đơn hàng chưa xác nhận
                </h6>
                <p class="mb-2">
                  <?php echo $order_row['total']; ?> đơn hàng
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="card shadow">
              <div class="card-body py-4">
                <h6 class="mb-2 fw-bold">
                  Tài khoản đang hoạt động
                </h6>
                <p class="mb-2">
                  <?php echo $account_row['total']; ?> tài khoản
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="card shadow">
              <div class="card-body py-4">
                <h6 class="mb-2 fw-bold">
                  Sản phẩm đang bán
                </h6>
                <p class="mb-2">
                  <?php echo $product_row['total']; ?> sản phẩm
                </p>
              </div>
            </div>
          </div>
        </div>
        <form id="searchForm" action="dashboard.php" method="get">
          <div class="row mb-3">
            <div class="col-md-3 d-flex align-items-center">
              <label for="fromDay" class="form-label mb-0 me-2" style="white-space: nowrap;">Từ ngày</label>
              <input name="fromDay" id="fromDay" class="form-control" type="date" value="<?php if (isset($_GET['fromDay'])) echo $_GET['fromDay']; ?>">
            </div>
            <div class="col-md-3 d-flex align-items-center">
              <label for="toDay" class="form-label mb-0 me-2" style="white-space: nowrap;">Đến ngày</label>
              <input name="toDay" id="toDay" class="form-control" type="date" value="<?php if (isset($_GET['toDay'])) echo $_GET['toDay']; ?>">
            </div>
            <div class="col-md-2">
              <input type="submit" value="Tìm kiếm" name="search_btn" class="btn btn-outline-primary">
              <a href="dashboard.php" class="btn btn-secondary ms-2">Đặt lại</a>
            </div>
          </div>
        </form>

        <?php if ($total_spent_result->num_rows > 0) { ?>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Tên tài khoản</th>
                <th scope="col">Tổng tiền đã mua</th>
                <th scope="col">Danh sách đơn hàng đã mua</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($total_spent_row = $total_spent_result->fetch_assoc()) { ?>
                <tr class="align-middle">
                  <th scope="row"><?php echo $total_spent_row['account_id']; ?></th>
                  <td><?php echo $total_spent_row['account_name']; ?></td>
                  <td><?php echo $total_spent_row['total_spent']; ?></td>
                  <td>
                    <form action="orders_list.php?account_id=<?php echo $total_spent_row['account_id']; ?>" method="post">
                      <input type="hidden" name="fromDay" value="<?php if (isset($_GET['fromDay'])) echo $_GET['fromDay']; ?>">
                      <input type="hidden" name="toDay" value="<?php if (isset($_GET['toDay'])) echo $_GET['toDay']; ?>">
                      <input type="submit" class="btn btn-warning" name="orders_list_btn" value="Xem">
                    </form>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <p>Không tìm thấy kết quả</p>
        <?php } ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>