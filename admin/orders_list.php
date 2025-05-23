<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

$account_id = (int)$_GET['account_id'];
$fromDay = isset($_POST['fromDay']) && $_POST['fromDay'] !== '' ? $_POST['fromDay'] : null;
$toDay = isset($_POST['toDay']) && $_POST['toDay'] !== '' ? $_POST['toDay'] : null;

$base_url = "orders_list.php?account_id=" . $account_id;

if (!$account_id || !is_numeric($account_id)) {
  header('location: dashboard.php');
  exit;
}

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

$sql1 = "SELECT COUNT(*) AS total_records 
         FROM orders 
         WHERE account_id = ? AND order_status = 'Thành công'";
$params1 = [$account_id];
$types1 = "i";

if ($fromDay && $toDay) {
  $sql1 .= " AND order_date BETWEEN ? AND ?";
  $params1[] = $fromDay . ' 00:00:00';
  $params1[] = $toDay . ' 23:59:59';
  $types1 .= "ss";
} elseif ($fromDay) {
  $sql1 .= " AND order_date >= ?";
  $params1[] = $fromDay . ' 00:00:00';
  $types1 .= "s";
} elseif ($toDay) {
  $sql1 .= " AND order_date <= ?";
  $params1[] = $toDay . ' 23:59:59';
  $types1 .= "s";
}

$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param($types1, ...$params1);
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();
$total_no_of_pages = ceil($total_records / $records_per_page);

$sql2 = "SELECT * 
         FROM orders 
         WHERE account_id = ? AND order_status = 'Thành công'";
$params2 = [$account_id];
$types2 = "i";

if ($fromDay && $toDay) {
  $sql2 .= " AND order_date BETWEEN ? AND ?";
  $params2[] = $fromDay . ' 00:00:00';
  $params2[] = $toDay . ' 23:59:59';
  $types2 .= "ss";
} elseif ($fromDay) {
  $sql2 .= " AND order_date >= ?";
  $params2[] = $fromDay . ' 00:00:00';
  $types2 .= "s";
} elseif ($toDay) {
  $sql2 .= " AND order_date <= ?";
  $params2[] = $toDay . ' 23:59:59';
  $types2 .= "s";
}

$sql2 .= " ORDER BY order_date DESC LIMIT ?, ?";
$params2[] = $offset;
$params2[] = $records_per_page;
$types2 .= "ii";

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param($types2, ...$params2);
$stmt2->execute();
$orders = $stmt2->get_result();

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
  <title>Danh sách đơn hàng</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <!-- Content (optional) -->
      <div class="col py-3">
        <h2 class="text-center mb-3">Danh sách đơn hàng</h2>

        <?php if ($total_no_of_pages > 0) { ?>
          <table class="table text-center">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Trạng thái đơn</th>
                <th scope="col">Tỉnh/thành phố</th>
                <th scope="col">Quận/huyện</th>
                <th scope="col">Phường/xã</th>
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
                                              echo $base_url . "&page=" . ($page - 1);
                                            } ?>"><</a>
              </li>

              <?php if ($total_no_of_pages >= 3) { ?>
                <li class="page-item">
                  <a class="page-link" href=<?php echo $base_url . "&page=1"; ?>>1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href=<?php echo $base_url . "&page=2"; ?>>2</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">...</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href=<?php echo $base_url . "&page=" . $total_no_of_pages; ?>><?php echo $total_no_of_pages; ?></a>
                </li>
              <?php } elseif ($total_no_of_pages == 1) { ?>
                <li class="page-item">
                  <a class="page-link" href=<?php echo $base_url . "&page=1" ?>>1</a>
                </li>
              <?php } else { ?>
                <li class="page-item">
                  <a class="page-link" href=<?php echo $base_url . "&page=1" ?>>1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href=<?php echo $base_url . "&page=2" ?>>2</a>
                </li>
              <?php } ?>

              <li class="page-item <?php if ($page >= $total_no_of_pages) {
                                      echo "disabled";
                                    } ?>">
                <a class="page-link" href="<?php if ($page >= $total_no_of_pages) {
                                              echo "#";
                                            } else {
                                              echo $base_url . "&page=" . ($page + 1);
                                            } ?>">></a>
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
</body>

</html>