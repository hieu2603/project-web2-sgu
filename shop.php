<?php

include 'server/connection.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$minPrice = $_GET['minPrice'] ?? '';
$maxPrice = $_GET['maxPrice'] ?? '';
$sortedBy = $_GET['sortedBy'] ?? '';

if (isset($_GET['search_btn'])) {
  $query = "SELECT * FROM products WHERE 1=1";
  $params = [];
  $types = "";

  // Lọc theo từ khóa tên sản phẩm
  if (!empty($search)) {
    $query .= " AND product_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
  }

  // Lọc theo loại
  if (!empty($category) && $category !== 'all') {
    $query .= " AND product_category = ?";
    $params[] = $category;
    $types .= "s";
  }

  // Lọc theo khoảng giá
  if ($minPrice !== '' && $maxPrice !== '') {
    $query .= " AND product_price BETWEEN ? AND ?";
    $params[] = (int)$minPrice;
    $params[] = (int)$maxPrice;
    $types .= "ii";
  }

  // Lọc theo điều kiện
  if ($sortedBy === 'asc') {
    $query .= " ORDER BY product_price ASC";
  } elseif ($sortedBy === 'desc') {
    $query .= " ORDER BY product_price DESC";
  }

  $stmt = $conn->prepare($query);

  if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
  }

  $stmt->execute();
  $products = $stmt->get_result();

  // Return all products
} else {
  $stmt = $conn->prepare("SELECT * from products");

  $stmt->execute();

  $products = $stmt->get_result();
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
            <option value="shoes">Shoes</option>
            <option value="coats">Coats</option>
            <option value="watches">Watches</option>
            <option value="bags">Bags</option>
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
          <img src="/assets/img/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_image']; ?>" class="img-fluid mb-3">
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
        <ul class="pagination mt-5">
          <li class="page-item">
            <a class="page-link" href="#">Previous</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="#">1</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="#">2</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="#">3</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="#">Next</a>
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