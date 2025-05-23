<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/d32f1bec50.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <title>Trang chủ</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Home -->
  <section id="home">
    <div class="container">
      <h5 style="font-weight: 600;">Sản Phẩm Mới</h5>
      <h1 class="mb-3"><span>Giá Tốt Nhất</span> Mùa Hè Này</h1>
      <a href="shop.php" class="button">Mua ngay</a>
    </div>
  </section>

  <!-- Brand -->
  <section id="brand" class="container">
    <div class="row">
      <img src="/assets/img/brand_1.png" alt="brand_1_img" class="img-fluid col-lg-3 col-md-6 col-sm-12">
      <img src="/assets/img/brand_2.png" alt="brand_2_img" class="img-fluid col-lg-3 col-md-6 col-sm-12">
      <img src="/assets/img/brand_3.png" alt="brand_3_img" class="img-fluid col-lg-3 col-md-6 col-sm-12">
      <img src="/assets/img/brand_4.png" alt="brand_4_img" class="img-fluid col-lg-3 col-md-6 col-sm-12">
    </div>
  </section>

  <!-- New -->
  <section id="new" class="w-100">
    <div class="row p-0 m-0">
      <!-- One -->
      <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
        <img src="assets/img/1.jpg" alt="1_img" class="img-fluid">
        <div class="details">
          <h2>Giày Giảm 30%</h2>
          <a href="shop.php" class="button">Mua ngay</a>
        </div>
      </div>

      <!-- Two -->
      <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
        <img src="assets/img/2.jpg" alt="2_img" class="img-fluid">
        <div class="details">
          <h2>Áo Khoác Cực Cool</h2>
          <a href="shop.php" class="button">Mua ngay</a>
        </div>
      </div>

      <!-- Three -->
      <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
        <img src="assets/img/3.jpg" alt="3_img" class="img-fluid">
        <div class="details">
          <h2>Áo Thun Đỉnh Của Chóp</h2>
          <a href="shop.php" class="button">Mua ngay</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Featured -->
  <section id="featured" class="my-5 pb-3">
    <div class="container text-center mt-3 py-5">
      <h3>Sản Phẩm Nổi Bật</h3>
      <hr class="mx-auto">
    </div>
    <div class="row mx-auto container-fluid">
      <?php include('server/get_featured_products.php'); ?>

      <?php while ($row = $featured_products->fetch_assoc()) { ?>

        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
          <img src="<?php echo $row['product_image']; ?>" alt="featured_1_img" class="img-fluid mb-3">
          <div class="star">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo $row['product_name'] ?></h5>
          <h4 class="p-price"><?php echo number_format($row['product_price'], 0, ',', '.'); ?>đ</h4>
          <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>">
            <button class="buy-btn">Mua Ngay</button>
          </a>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- Banner -->
  <section id="banner" class="my-5 py-5">
    <div class="container">
      <h4>GIẢM GIÁ SỐC MÙA HÈ</h4>
      <h1>BỘ SƯU TẬP HÈ <br> GIẢM ĐẾN 30%</h1>
      <button onclick="window.location.href='shop.php'" class="text-uppercase">Mua Ngay</button>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>