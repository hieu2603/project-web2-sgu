<?php

session_start();

include '../server/connection.php';

$admin_name = $_SESSION['admin_name'];

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

include '../admin/utils/get_categories.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$minPrice = $_GET['minPrice'] ?? '';
$maxPrice = $_GET['maxPrice'] ?? '';
$sortedBy = $_GET['sortedBy'] ?? '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 5;
$offset = ($page - 1) * $records_per_page;

$base_query_string = "search=" . urlencode($search)
  . "&category=" . urlencode($category)
  . "&minPrice=" . urlencode($minPrice)
  . "&maxPrice=" . urlencode($maxPrice)
  . "&sortedBy=" . urlencode($sortedBy)
  . "&search_btn=1";

if (isset($_GET['search_btn'])) {
  $baseQuery = "FROM products WHERE 1=1";
  $params = [];
  $types = "";

  // Search by keyword
  if (!empty($search)) {
    $baseQuery .= " AND (product_id LIKE ? OR product_name LIKE ? OR product_color LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $types .= 'sss';
  }

  // Filter by category
  if (!empty($category) && $category !== 'Tất cả') {
    $baseQuery .= " AND products.category_id = ?";
    $params[] = $category;
    $types .= "s";
  }

  if (!empty($minPrice)) {
    $baseQuery .= " AND product_price >= ?";
    $params[] = (int)$minPrice;
    $types .= 'i';
  }

  if (!empty($maxPrice)) {
    $baseQuery .= " AND product_price <= ?";
    $params[] = (int)$maxPrice;
    $types .= 'i';
  }

  if ($sortedBy === 'Đang bán' || $sortedBy === 'Ngừng bán') {
    $baseQuery .= " AND product_status = ?";
    $params[] = $sortedBy;
    $types .= 's';
  }

  // Get count of products
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

  // Get products
  $query = "SELECT products.*, categories.category_name " .
    "FROM products INNER JOIN categories ON products.category_id = categories.category_id " .
    substr($baseQuery, strlen("FROM products"));

  // Filter condition
  if ($sortedBy === 'Hàng mới') {
    $query .= " ORDER BY product_id DESC";
  } elseif ($sortedBy === 'Giá thấp đến cao') {
    $query .= " ORDER BY product_price ASC";
  } elseif ($sortedBy === 'Giá cao đến thấp') {
    $query .= " ORDER BY product_price DESC";
  }

  $query .= " LIMIT ?, ?";
  $params[] = $offset;
  $params[] = $records_per_page;
  $types .= "ii";

  $stmt = $conn->prepare($query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $products = $stmt->get_result();

  // Return all products
} else {
  // Get total products
  $stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records FROM products");
  $stmt1->execute();
  $stmt1->bind_result($total_records);
  $stmt1->store_result();
  $stmt1->fetch();

  $previous_page = $page - 1;
  $next_page = $page + 1;

  $adjacents = "2";
  $total_no_of_pages = ceil($total_records / $records_per_page);

  $stmt2 = $conn->prepare("SELECT products.*, categories.category_name
                           FROM products 
                           INNER JOIN categories 
                           ON products.category_id=categories.category_id
                           ORDER BY products.product_id DESC
                           LIMIT $offset, $records_per_page");
  $stmt2->execute();
  $products = $stmt2->get_result();
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
  <title>Products Dashboard</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <!-- Content (optional) -->
      <div class="col py-3">
        <h2 class="text-center mb-3">Quản Lý Sản Phẩm</h2>

        <form id="searchForm" action="products.php" method="get">
          <div class="mb-3" id="searchContainer">
            <a class="btn btn-primary" href="add_product.php">Thêm</a>
            <input type="text" name="search" class="form-control" style="width: 30%;" placeholder="Tìm kiếm... (ID, tên sản phẩm, màu sắc)" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
            <input type="submit" value="Tìm kiếm" name="search_btn" class="btn btn-outline-primary">
            <input type="button" value="Lọc" id="filterToggleBtn" name="filter_btn" class="btn btn-outline-success">
            <a href="products.php" class="btn btn-secondary">Đặt lại</a>
          </div>

          <div id="filterContainer">
            <div class="row mb-3">
              <div class="col-md-3 d-flex align-items-center">
                <label for="selectCategory" class="form-label-inline" style="min-width: 70px;">Phân loại</label>
                <select name="category" id="selectCategory" class="form-select">
                  <option value="Tất cả" <?php if (isset($_GET['category']) && $_GET['category'] == "Tất cả") echo 'selected'; ?>>Tất cả</option>
                  <?php while ($category_row = $categories->fetch_assoc()) { ?>
                    <option value="<?php echo $category_row['category_id']; ?>" <?php if (isset($_GET['category']) && $_GET['category'] == $category_row['category_id']) echo 'selected'; ?>>
                      <?php echo $category_row['category_name']; ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-2 d-flex align-items-center">
                <label for="minPrice" class="form-label-inline" style="min-width: 30px;">Giá</label>
                <input style="width: 150px;" name="minPrice" id="minPrice" class="form-control" type="number" placeholder="Từ" value="<?php if (isset($_GET['minPrice'])) echo $_GET['minPrice']; ?>">
              </div>
              <div class="col-md-2 d-flex align-items-center">
                <label for="maxPrice" class="form-label-inline" style="min-width: 30px;">Giá</label>
                <input style="width: 150px;" name="maxPrice" id="maxPrice" class="form-control" type="number" placeholder="Đến" value="<?php if (isset($_GET['maxPrice'])) echo $_GET['maxPrice']; ?>">
              </div>
              <div class="col-md-3 d-flex align-items-center">
                <label for="sortedBy" class="form-label-inline" style="min-width: 70px;">Lọc theo</label>
                <select name="sortedBy" id="sortedBy" class="form-select">
                  <option value="Hàng mới" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Hàng mới') echo 'selected'; ?>>Hàng mới</option>
                  <option value="Giá thấp đến cao" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Giá thấp đến cao') echo 'selected'; ?>>Giá thấp đến cao</option>
                  <option value="Giá cao đến thấp" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Giá cao đến thấp') echo 'selected'; ?>>Giá cao đến thấp</option>
                  <option value="Đang bán" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Đang bán') echo 'selected'; ?>>Đang bán</option>
                  <option value="Ngừng bán" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Ngừng bán') echo 'selected'; ?>>Ngừng bán</option>
                </select>
              </div>
            </div>
          </div>
        </form>

        <?php if ($total_no_of_pages > 0) { ?>
          <table class="table text-center">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Hình ảnh</th>
                <th scope="col">Tên sản phẩm</th>
                <th scope="col">Giá</th>
                <th scope="col">Phân loại</th>
                <th scope="col">Màu sắc</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Chỉnh sửa</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product) { ?>
                <tr class="align-middle">
                  <th scope="row"><?php echo $product['product_id']; ?></th>
                  <td><img src="<?php echo $product['product_image']; ?>" style="width: 70px; height: 70px;"></td>
                  <td><?php echo $product['product_name']; ?></td>
                  <td><?php echo number_format($product['product_price'], 0, ',', '.'); ?>đ</td>
                  <td><?php echo $product['category_name']; ?></td>
                  <td><?php echo $product['product_color']; ?></td>
                  <td><?php echo $product['product_status']; ?></td>
                  <td>
                    <a class="btn btn-warning" href="edit_product.php?product_id=<?php echo $product['product_id']; ?>">
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

  <script>
    document.getElementById('filterToggleBtn').addEventListener('click', function() {
      document.getElementById('filterContainer').classList.toggle('show');
    });
  </script>
</body>

</html>