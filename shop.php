<?php

include 'server/connection.php';

// Get categories
include './admin/utils/get_categories.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$minPrice = $_GET['minPrice'] ?? '';
$maxPrice = $_GET['maxPrice'] ?? '';
$sortedBy = $_GET['sortedBy'] ?? '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 8;
$offset = ($page - 1) * $records_per_page;

$base_query_string = "search=" . urlencode($search)
  . "&category=" . urlencode($category)
  . "&minPrice=" . urlencode($minPrice)
  . "&maxPrice=" . urlencode($maxPrice)
  . "&sortedBy=" . urlencode($sortedBy)
  . "&search_btn=1";

if (isset($_GET['search_btn'])) {
  $baseQuery = "FROM products WHERE product_status = 'Đang bán'";
  $params = [];
  $types = "";

  // Search by keyword
  if (!empty($search)) {
    $baseQuery .= " AND product_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
  }

  // Filter by category
  if (!empty($category) && $category !== 'Tất cả') {
    $baseQuery .= " AND category_id = ?";
    $params[] = $category;
    $types .= "s";
  }

  // Filter by price range
  if (!empty($minPrice)) {
    $baseQuery .= " AND product_price >= ?";
    $params[] = (int)$minPrice;
    $types .= "i";
  }

  if (!empty($maxPrice)) {
    $baseQuery .= " AND product_price <= ?";
    $params[] = (int)$maxPrice;
    $types .= "i";
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
  $query = "SELECT * " . $baseQuery;

  // Filter condition
  if ($sortedBy === 'Giá thấp đến cao') {
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

  $stmt2 = $conn->prepare("SELECT * 
                           FROM products 
                           WHERE product_status = 'Đang bán' 
                           ORDER BY product_id DESC
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
  <link rel="stylesheet" href="assets/css/style.css">

  <style>
    .product img {
      width: 100%;
      height: auto;
      box-sizing: border-box;
      object-fit: cover;
    }

    .pagination a {
      color: coral;
    }

    .pagination li:hover a {
      color: #FFF;
      background-color: coral;
    }
  </style>

  <title>Shop</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Shop -->
  <section id="shop" class="my-5 py-5">
    <div class="container mt-5 py-5">
      <h3>Our Products</h3>
      <hr>
      <p>Here you can check out our featured products</p>
      <form id="searchForm" action="shop.php" method="get">
        <div class="mb-3" id="searchContainer">
          <input type="text" id="searchInput" name="search" class="form-control flex-7" placeholder="Nhập thông tin tìm kiếm..." value="<?php if (isset($_GET['search_btn'])) echo $_GET['search'] ?>">
          <input type="submit" value="Tìm kiếm" name="search_btn" class="btn btn-primary flex-1">
          <input type="button" value="Lọc" id="filterToggleBtn" name="filter_btn" class="btn btn-outline-success flex-1">
          <a href="shop.php" class="btn btn-secondary">Đặt lại</a>
        </div>

        <div id="filterContainer">
          <label for="selectCategory" class="form-label">Category</label>
          <select name="category" id="selectCategory" class="form-select">
            <option value="Tất cả" <?php if (isset($_GET['category']) && $_GET['category'] == 'Tất cả') echo 'selected'; ?>>Tất cả</option>
            <?php while ($category_row = $categories->fetch_assoc()) { ?>
              <option value="<?php echo $category_row['category_id']; ?>" <?php if (isset($_GET['category']) && $_GET['category'] == $category_row['category_id']) echo 'selected'; ?>>
                <?php echo $category_row['category_name']; ?>
              </option>
            <?php } ?>
          </select>

          <label for="price" class="form-label">Price</label>
          <input style="margin: 0;" name="minPrice" class="form-control" type="number" min="0" max="10000000" placeholder="From" value="<?php if (isset($_GET['minPrice'])) echo $_GET['minPrice']; ?>">
          <span style="margin: 0 10px;">-</span>
          <input name="maxPrice" class="form-control" type="number" min="0" max="10000000" placeholder="To" value="<?php if (isset($_GET['maxPrice'])) echo $_GET['maxPrice']; ?>">

          <label for="sortedBy" class="form-label">Sorted By</label>
          <select style="width: 200px;" name="sortedBy" id="sortedBy" class="form-select">
            <option value="Hàng mới" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Hàng mới') echo 'selected'; ?>>Hàng mới</option>
            <option value="Giá thấp đến cao" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Giá thấp đến cao') echo 'selected'; ?>>Giá thấp đến cao</option>
            <option value="Giá cao đến thấp" <?php if (isset($_GET['sortedBy']) && $_GET['sortedBy'] == 'Giá cao đến thấp') echo 'selected'; ?>>Giá cao đến thấp</option>
          </select>
        </div>
      </form>
    </div>
    <?php if ($total_no_of_pages > 0) { ?>
      <div class="row mx-auto container">
        <?php while ($row = $products->fetch_assoc()) { ?>
          <div onclick="window.location.href='single_product.php?product_id=<?php echo $row['product_id']; ?>'" class="product text-center col-lg-3 col-md-4 col-sm-12">
            <img src="<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_image']; ?>" class="img-fluid mb-3">
            <div class="star">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
            </div>
            <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
            <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
            <a class="btn shop-buy-btn" href="<?php echo "single_product.php?product_id=" . $row['product_id']; ?>">Buy Now</a>
          </div>
        <?php } ?>

        <nav aria-label="Page Navigation Example">
          <ul class="pagination mt-5 justify-content-center">
            <li class="page-item <?php if ($page <= 1) {
                                    echo "disabled";
                                  } ?>">
              <a class="page-link" href="<?php if ($page <= 1) {
                                            echo "#";
                                          } else {
                                            echo "?" . $base_query_string . "&page=" . ($page - 1);
                                          } ?>">
                << /a>
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
      </div>
    <?php } else { ?>
      <p class="container">Không tìm thấy kết quả</p>
    <?php } ?>
  </section>

  <?php include 'layouts/footer.php'; ?>

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