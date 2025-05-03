<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/d32f1bec50.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <title>Home</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Home -->
  <section id="home">
    <div class="container">
      <h5>NEW ARRIVALS</h5>
      <h1><span>Best Prices</span> This Season</h1>
      <p>Eshop offers the best products for the most affordable prices</p>
      <button>Shop Now</button>
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
          <h2>30% OFF Shoes</h2>
          <button class="text-uppercase">Shop Now</button>
        </div>
      </div>

      <!-- Two -->
      <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
        <img src="assets/img/2.jpg" alt="2_img" class="img-fluid">
        <div class="details">
          <h2>Awesome Jacket</h2>
          <button class="text-uppercase">Shop Now</button>
        </div>
      </div>

      <!-- Three -->
      <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
        <img src="assets/img/3.jpg" alt="3_img" class="img-fluid">
        <div class="details">
          <h2>Extremely Awesome T-Shirt</h2>
          <button class="text-uppercase">Shop Now</button>
        </div>
      </div>
    </div>
  </section>

  <!-- Featured -->
  <section id="featured" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
      <h3>Our Featured</h3>
      <hr class="mx-auto">
      <p>Here you can check out our featured products</p>
    </div>
    <div class="row mx-auto container-fluid">

      <?php include('server/get_featured_products.php'); ?>

      <?php while ($row = $featured_products->fetch_assoc()) { ?>

        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
          <img src="/assets/img/<?php echo $row['product_image']; ?>" alt="featured_1_img" class="img-fluid mb-3">
          <div class="star">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo $row['product_name'] ?></h5>
          <h4 class="p-price">$<?php echo $row['product_price'] ?></h4>
          <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>">
            <button class="buy-btn">Buy Now</button>
          </a>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- Banner -->
  <section id="banner" class="my-5 py-5">
    <div class="container">
      <h4>MID SEASON SALE</h4>
      <h1>Autumn Collection <br> Up to 30% OFF</h1>
      <button class="text-uppercase">Shop Now</button>
    </div>
  </section>

  <!-- Clothes -->
  <section id="clothes" class="my-5">
    <div class="container text-center mt-5 py-5">
      <h3>Dresses & Coats</h3>
      <hr class="mx-auto">
      <p>Here you can check out our clothes</p>
    </div>
    <div class="row mx-auto container-fluid">

      <?php include('server/get_coats.php'); ?>

      <?php while ($row = $coats_products->fetch_assoc()) { ?>
        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
          <img src="/assets/img/<?php echo $row['product_image']; ?>" alt="featured_1_img" class="img-fluid mb-3">
          <div class="star">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
          <h4 class="p-price">$<?php echo $row['product_price'] ?></h4>
          <button class="buy-btn">Buy Now</button>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- Shoes -->
  <section id="shoes" class="my-5">
    <div class="container text-center mt-5 py-5">
      <h3>Shoes</h3>
      <hr class="mx-auto">
      <p>Here you can check out our amazing shoes</p>
    </div>
    <div class="row mx-auto container-fluid">
      <div class="product text-center col-lg-3 col-md-4 col-sm-12">
        <img src="/assets/img/shoes_1.jpg" alt="shoes_1_img" class="img-fluid mb-3">
        <div class="star">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
        </div>
        <h5 class="p-name">Sport Shoes</h5>
        <h4 class="p-price">$69.9</h4>
        <button class="buy-btn">Buy Now</button>
      </div>
      <div class="product text-center col-lg-3 col-md-4 col-sm-12">
        <img src="/assets/img/shoes_2.jpg" alt="shoes_2_img" class="img-fluid mb-3">
        <div class="star">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
        </div>
        <h5 class="p-name">Sport Shoes</h5>
        <h4 class="p-price">$69.9</h4>
        <button class="buy-btn">Buy Now</button>
      </div>
      <div class="product text-center col-lg-3 col-md-4 col-sm-12">
        <img src="/assets/img/shoes_3.jpg" alt="shoes_3_img" class="img-fluid mb-3">
        <div class="star">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
        </div>
        <h5 class="p-name">Sport Shoes</h5>
        <h4 class="p-price">$69.9</h4>
        <button class="buy-btn">Buy Now</button>
      </div>
      <div class="product text-center col-lg-3 col-md-4 col-sm-12">
        <img src="/assets/img/shoes_4.jpg" alt="shoes_4_img" class="img-fluid mb-3">
        <div class="star">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
        </div>
        <h5 class="p-name">Sport Shoes</h5>
        <h4 class="p-price">$69.9</h4>
        <button class="buy-btn">Buy Now</button>
      </div>
    </div>
  </section>

  <?php include 'layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>