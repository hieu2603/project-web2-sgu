<?php

session_start();

include '../server/connection.php';

$admin_name = $_SESSION['admin_name'];

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

?>

<?php
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
    $baseQuery .= " AND product_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
  }

  // Filter by category
  if (!empty($category) && $category !== 'all') {
    $baseQuery .= " AND product_category = ?";
    $params[] = $category;
    $types .= "s";
  }

  // Filter by price range
  if ($minPrice !== '' && $maxPrice !== '') {
    $baseQuery .= " AND product_price BETWEEN ? AND ?";
    $params[] = (int)$minPrice;
    $params[] = (int)$maxPrice;
    $types .= "ii";
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
  if ($sortedBy === 'asc') {
    $query .= " ORDER BY product_price ASC";
  } elseif ($sortedBy === 'desc') {
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
        <h2>Quản lý sản phẩm</h2>
        <a class="btn btn-primary" href="add_product.php">Thêm</a>
        <table class="table">
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
                <td>$<?php echo $product['product_price']; ?></td>
                <td><?php echo $product['category_name']; ?></td>
                <td><?php echo $product['product_color']; ?></td>
                <td><?php echo $product['product_status']; ?></td>
                <td>
                  <a class="btn btn-warning" href="edit_product.php?product_id=<?php echo $product['product_id']; ?>">
                    Edit
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
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>