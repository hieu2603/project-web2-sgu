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
  $query = "SELECT * " . $baseQuery;

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

  $stmt2 = $conn->prepare("SELECT * FROM products LIMIT $offset, $records_per_page");
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
          <input type="text" id="searchInput" name="search" class="form-control flex-7" placeholder="Nhập thông tin tìm kiếm...">
          <input type="submit" value="Tìm kiếm" name="search_btn" class="btn btn-primary flex-1">
          <input type="button" value="Lọc" id="filterToggleBtn" name="filter_btn" class="btn btn-secondary flex-1">
        </div>

        <div id="filterContainer">
          <label for="selectCategory" class="form-label">Category</label>
          <select name="category" id="selectCategory" class="form-select">
            <option value="all">All</option>
            <?php while ($row = $categories->fetch_assoc()) { ?>
              <option value="<?php echo $row['category_id']; ?>">
                <?php echo $row['category_name']; ?>
              </option>
            <?php } ?>
          </select>

          <label for="price" class="form-label">Price</label>
          <input style="margin: 0;" name="minPrice" class="form-control" type="number" min="0" max="10000000" placeholder="From">
          <span style="margin: 0 10px;">-</span>
          <input name="maxPrice" class="form-control" type="number" min="0" max="10000000" placeholder="To">

          <label for="sortedBy" class="form-label">Sorted By</label>
          <select style="width: 200px;" name="sortedBy" id="sortedBy" class="form-select">
            <option value="new">Hàng mới</option>
            <option value="best">Bán chạy</option>
            <option value="asc">Giá thấp đến cao</option>
            <option value="desc">Giá cao đến thấp</option>
          </select>
        </div>
      </form>
    </div>
    <div class="row mx-auto container">
      <?php while ($row = $products->fetch_assoc()) { ?>
        <div onclick="window.location.href='single_product.html'" class="product text-center col-lg-3 col-md-4 col-sm-12">
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
        <ul class="pagination mt-5 mx-auto">
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