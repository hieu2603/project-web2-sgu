<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

$search = $_GET['search'] ?? '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 8;
$offset = ($page - 1) * $records_per_page;

$base_query_string = "search=" . urlencode($search)
  . "&search_btn=1";

if (isset($_GET['search_btn'])) {
  $baseQuery = "FROM categories WHERE 1=1";
  $params = [];
  $types = "";

  // Search by keyword (id, name)
  if (!empty($search)) {
    $baseQuery .= " AND (category_id LIKE ? OR category_name LIKE ?)";
    $searchParam = '%' . trim($search) . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
  }

  // Get count of accounts
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

  $query .= " ORDER BY category_id DESC";
  $query .= " LIMIT ?, ?";
  $params[] = $offset;
  $params[] = $records_per_page;
  $types .= "ii";

  $stmt = $conn->prepare($query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $categories = $stmt->get_result();

  // Return all accounts
} else {
  // Get total accounts
  $stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records FROM categories");
  $stmt1->execute();
  $stmt1->bind_result($total_records);
  $stmt1->store_result();
  $stmt1->fetch();

  $total_no_of_pages = ceil($total_records / $records_per_page);

  $stmt2 = $conn->prepare("SELECT * 
                           FROM categories 
                           ORDER BY category_id DESC
                           LIMIT $offset, $records_per_page");
  $stmt2->execute();
  $categories = $stmt2->get_result();
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
  <title>Categories Dashboard</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <!-- Content (optional) -->
      <div class="col py-3">
        <h2 class="text-center mb-3">Quản Lý Phân Loại</h2>

        <form id="searchForm" action="categories.php" method="get">
          <div class="mb-3" id="searchContainer">
            <a class="btn btn-primary" href="add_category.php">Thêm</a>
            <input type="text" name="search" class="form-control" style="width: 30%;" placeholder="Tìm kiếm..." value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
            <input type="submit" value="Tìm kiếm" name="search_btn" class="btn btn-outline-primary">
            <a href="categories.php" class="btn btn-secondary">Đặt lại</a>
          </div>
        </form>

        <?php if ($total_no_of_pages > 0) { ?>
          <table class="table text-center">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Tên phân loại</th>
                <th scope="col">Chỉnh sửa</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $category) { ?>
                <tr class="align-middle">
                  <th scope="row"><?php echo $category['category_id']; ?></th>
                  <td><?php echo $category['category_name']; ?></td>
                  <td>
                    <a class="btn btn-warning" href="edit_category.php?category_id=<?php echo $category['category_id']; ?>">
                      Sửa
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
                                            } ?>"><</a>
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