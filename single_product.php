<?php

include 'server/connection.php';

if (isset($_GET['product_id'])) {
  $product_id = $_GET['product_id'];

  $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
  $stmt->bind_param("i", $product_id);

  $stmt->execute();

  $product = $stmt->get_result();
} else {
  header("location: index.php");
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

  <title>Single Product</title>
</head>

<body>
  <?php include 'layouts/header.php'; ?>

  <!-- Single product -->
  <section class="container single-product my-5 pt-5">
    <div class="row mt-5">
      <?php while ($row = $product->fetch_assoc()) { ?>
        <div class="col-lg-5 col-md-6 col-sm-12">
          <img src="<?php echo $row['product_image']; ?>" alt="product_1_img" class="img-fluid w-100 pb-1" id="mainImg">
          <div class="small-img-group">
            <div class="small-img-col">
              <img src="<?php echo $row['product_image']; ?>" alt="featured_2_img" width="100%" class="small-img">
            </div>
            <div class="small-img-col">
              <img src="<?php echo $row['product_image2']; ?>" alt="featured_2_img" width="100%" class="small-img">
            </div>
            <div class="small-img-col">
              <img src="<?php echo $row['product_image3']; ?>" alt="featured_2_img" width="100%" class="small-img">
            </div>
            <div class="small-img-col">
              <img src="<?php echo $row['product_image4']; ?>" alt="featured_2_img" width="100%" class="small-img">
            </div>
          </div>
        </div>


        <div class="col-lg-6 col-md-12 col-sm-12">
          <h6>Men/Shoes</h6>
          <h3 class="py-4"><?php echo $row['product_name']; ?></h3>
          <h2><?php echo $row['product_price']; ?></h2>
          <form action="cart.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>">
            <input type="number" name="product_quantity" value="1">
            <button type="submit" name="add_to_cart" class="buy-btn">Add To Cart</button>
          </form>
          <h4 class="mt-5 mb-5">Product details</h4>
          <span><?php echo $row['product_description']; ?></span>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- Related products -->
  <section id="related-products" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
      <h3>Related Products</h3>
      <hr class="mx-auto">
    </div>
    <div class="row mx-auto container-fluid">
      <div class="product text-center col-lg-3 col-md-4 col-sm-12">
        <img src="/assets/img/featured_1.jpg" alt="featured_1_img" class="img-fluid mb-3">
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
        <img src="/assets/img/featured_2.jpg" alt="featured_2_img" class="img-fluid mb-3">
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
        <img src="/assets/img/featured_3.jpg" alt="featured_3_img" class="img-fluid mb-3">
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
        <img src="/assets/img/featured_4.jpg" alt="featured_4_img" class="img-fluid mb-3">
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

  <script>
    const mainImg = document.getElementById("mainImg");
    const smallImg = document.getElementsByClassName("small-img");

    for (let i = 0; i < 4; i++) {
      smallImg[i].onclick = function() {
        mainImg.src = smallImg[i].src;
      }
    }
  </script>
</body>

</html>