<?php

session_start();

include '../server/connection.php';

$admin_name = $_SESSION['admin_name'];

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

$orderStatus = $_GET['orderStatus'] ?? '';
$fromDay = $_GET['fromDay'] ?? '';
$toDay = $_GET['toDay'] ?? '';
$province = $_GET['province'] ?? '';
$district = $_GET['district'] ?? '';
$ward = $_GET['ward'] ?? '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 8;
$offset = ($page - 1) * $records_per_page;

$base_query_string = "orderStatus=" . urlencode($orderStatus)
  . "&fromDay=" . urlencode($fromDay)
  . "&toDay=" . urlencode($toDay)
  . "&province=" . urlencode($province)
  . "&district=" . urlencode($district)
  . "&ward=" . urlencode($ward)
  . "&search_btn=1";

if (isset($_GET['search_btn'])) {
  $baseQuery = "FROM orders WHERE 1=1";
  $params = [];
  $types = "";

  // Search by order status
  if (!empty($orderStatus) && $orderStatus !== 'Tất cả') {
    $baseQuery .= " AND order_status = ?";
    $params[] = $orderStatus;
    $types .= 's';
  }

  if (!empty($fromDay)) {
    $baseQuery .= " AND order_date >= ?";
    $params[] = $fromDay . " 00:00:00";
    $types .= 's';
  }

  if (!empty($toDay)) {
    $baseQuery .= " AND order_date <= ?";
    $params[] = $toDay . " 23:59:59";
    $types .= 's';
  }

  if (!empty($province)) {
    $baseQuery .= " AND province = ?";
    $params[] = $province;
    $types .= 's';
  }

  if (!empty($district)) {
    $baseQuery .= " AND district = ?";
    $params[] = $district;
    $types .= 's';
  }

  if (!empty($ward)) {
    $baseQuery .= " AND ward = ?";
    $params[] = $ward;
    $types .= 's';
  }

  // Get count of orders
  $countQuery = "SELECT COUNT(*) " . $baseQuery;
  $stmt_count = $conn->prepare($countQuery);
  if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
  }
  $stmt_count->execute();
  $stmt_count->bind_result($total_records);
  $stmt_count->fetch();
  $stmt_count->close();

  $total_no_of_pages = ceil($total_records / $records_per_page);

  // Get orders
  $query = "SELECT * " . $baseQuery;

  $query .= " ORDER BY order_date DESC";
  $query .= " LIMIT ?, ?";
  $params[] = $offset;
  $params[] = $records_per_page;
  $types .= "ii";

  $stmt = $conn->prepare($query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $orders = $stmt->get_result();

  // Return all products
} else {
  // Get total orders
  $stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records FROM orders");
  $stmt1->execute();
  $stmt1->bind_result($total_records);
  $stmt1->store_result();
  $stmt1->fetch();

  $total_no_of_pages = ceil($total_records / $records_per_page);

  $stmt2 = $conn->prepare("SELECT * 
                           FROM orders 
                           ORDER BY order_date DESC
                           LIMIT $offset, $records_per_page");
  $stmt2->execute();
  $orders = $stmt2->get_result();
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
  <title>Orders Dashboard</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <!-- Content (optional) -->
      <div class="col py-3">
        <h2>Quản lý đơn hàng</h2>
        <form id="searchForm" action="orders.php" method="get">
          <div class="mb-3" id="searchContainer">
            <input type="submit" value="Tìm kiếm" name="search_btn" class="btn btn-outline-primary">
            <input type="button" value="Lọc" id="filterToggleBtn" name="filter_btn" class="btn btn-secondary">
          </div>

          <div id="filterContainer">
            <!-- Hàng 1 -->
            <div class="row mb-3">
              <div class="col-md-4 d-flex align-items-center">
                <label for="selectOrderStatus" class="form-label-inline">Trạng thái đơn</label>
                <select name="orderStatus" id="selectOrderStatus" class="form-select">
                  <option value="Tất cả">Tất cả</option>
                  <option value="Chưa xác nhận">Chưa xác nhận</option>
                  <option value="Chờ thanh toán">Chờ thanh toán</option>
                  <option value="Đã xác nhận">Đã xác nhận</option>
                  <option value="Thành công">Thành công</option>
                  <option value="Hủy đơn">Hủy đơn</option>
                </select>
              </div>
              <div class="col-md-4 d-flex align-items-center">
                <label for="fromDay" class="form-label-inline">Từ ngày</label>
                <input name="fromDay" id="fromDay" class="form-control" type="date">
              </div>
              <div class="col-md-4 d-flex align-items-center">
                <label for="toDay" class="form-label-inline">Đến ngày</label>
                <input name="toDay" id="toDay" class="form-control" type="date">
              </div>
            </div>

            <!-- Hàng 2 -->
            <div class="row mb-3">
              <div class="col-md-4 d-flex align-items-center">
                <label for="province" class="form-label-inline">Tỉnh/Thành phố</label>
                <select name="province" id="selectProvince" class="form-select">
                  <option value="">Chọn tỉnh/thành</option>
                </select>
              </div>
              <div class="col-md-4 d-flex align-items-center">
                <label for="district" class="form-label-inline">Quận/Huyện</label>
                <select name="district" id="selectDistrict" class="form-select">
                  <option value="">Chọn quận/huyện</option>
                </select>
              </div>
              <div class="col-md-4 d-flex align-items-center">
                <label for="ward" class="form-label-inline">Phường/Xã</label>
                <select name="ward" id="selectWard" class="form-select">
                  <option value="">Chọn phường/xã</option>
                </select>
              </div>
            </div>
          </div>
        </form>

        <?php if ($total_no_of_pages > 0) { ?>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Trạng thái đơn</th>
                <th scope="col">Tỉnh/thành phố</th>
                <th scope="col">Quận/huyện</th>
                <th scope="col">Phường/xã</th>
                <th scope="col">Địa chỉ</th>
                <th scope="col">Ngày đặt hàng</th>
                <th scope="col">Chi tiết đơn hàng</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($order_row = $orders->fetch_assoc()) { ?>
                <tr class="align-middle">
                  <th scope="row"><?php echo $order_row['order_id']; ?></th>
                  <td class="<?php include '../admin/utils/order_status_color.php'; ?>">
                    <?php echo $order_row['order_status']; ?>
                  </td>
                  <td><?php echo $order_row['province']; ?></td>
                  <td><?php echo $order_row['district']; ?></td>
                  <td><?php echo $order_row['ward']; ?></td>
                  <td><?php echo $order_row['address']; ?></td>
                  <td><?php echo $order_row['order_date']; ?></td>
                  <td>
                    <a class="btn btn-primary" href="order_details.php?order_id=<?php echo $order_row['order_id']; ?>">
                      Chi tiết
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
          <nav aria-label="Page Navigation Example">
            <ul class="pagination mt-5 justify-content-center">
              <li class="page-item <?php if ($page <= 1) {
                                      echo "disabled";
                                    } ?>">
                <a class="page-link" href="<?php if ($page <= 1) {
                                              echo "#";
                                            } else {
                                              echo "?" . $base_query_string . "&page=" . ($page - 1);
                                            } ?>">Previous</a>
              </li>

              <?php if ($total_no_of_pages >= 3) { ?>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo $base_query_string; ?>&page=1">1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo $base_query_string; ?>&page=2">2</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">...</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo $base_query_string; ?>&page=<?php echo $total_no_of_pages; ?>"><?php echo $total_no_of_pages; ?></a>
                </li>
              <?php } elseif ($total_no_of_pages == 1) { ?>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo $base_query_string; ?>&page=1">1</a>
                </li>
              <?php } else { ?>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo $base_query_string; ?>&page=1">1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo $base_query_string; ?>&page=2">2</a>
                </li>
              <?php } ?>

              <li class="page-item <?php if ($page >= $total_no_of_pages) {
                                      echo "disabled";
                                    } ?>">
                <a class="page-link" href="<?php if ($page >= $total_no_of_pages) {
                                              echo "#";
                                            } else {
                                              echo "?" . $base_query_string . "&page=" . ($page + 1);
                                            } ?>">Next</a>
              </li>
            </ul>
          </nav>
        <?php } else { ?>
          <p>Không tìm thấy kết quả</p>
        <?php } ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

  <script>
    document.getElementById('filterToggleBtn').addEventListener('click', function() {
      document.getElementById('filterContainer').classList.toggle('show');
    });
  </script>

  <script src="../assets/js/index.js"></script>
</body>

</html>