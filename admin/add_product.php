<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

// Get categories
include '../admin/utils/get_categories.php';

if (isset($_POST['add_product_btn'])) {
  $product_name = $_POST['product_name'] ?? "";
  $product_category = $_POST['product_category'] ?? "";
  $product_price = $_POST['product_price'] ?? "";
  $product_color = $_POST['product_color'] ?? "";
  $product_description = $_POST['product_description'] ?? "";

  $imageDir = "../assets/img/";
  $thumbnailPath = "";
  $imagePaths = ["", "", ""];

  // Xử lý ảnh đại diện
  if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $fileName = time() . '_thumb_' . basename($_FILES['thumbnail']['name']);
    $targetFilePath = $imageDir . $fileName;
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFilePath)) {
      $thumbnailPath = $targetFilePath;
    } else {
      echo "<script>alert('Tải lên ảnh đại diện thất bại'); window.history.back();</script>";
      exit;
    }
  } else {
    echo "<script>alert('Vui lòng tải lên ảnh đại diện.'); window.history.back();</script>";
    exit;
  }

  // Xử lý ảnh phụ
  $requiredFiles = ['sub-image-1', 'sub-image-2', 'sub-image-3'];

  foreach ($requiredFiles as $index => $inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
      $fileName = time() . "_sub_{$index}_" . basename($_FILES[$inputName]['name']);
      $targetFilePath = $imageDir . $fileName;

      if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetFilePath)) {
        $imagePaths[$index] = $targetFilePath;
      } else {
        echo "<script>alert('Tải lên ảnh phụ thất bại: $fileName'); window.history.back();</script>";
        exit;
      }
    } else {
      echo "<script>alert('Vui lòng chọn đầy đủ 3 ảnh phụ.'); window.history.back();</script>";
      exit;
    }
  }

  // Cập nhật vào database
  $stmt = $conn->prepare("INSERT INTO products
                          (product_name, category_id, product_description,
                          product_image, product_image2, product_image3, product_image4,
                          product_price, product_color)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param(
    'sisssssis',
    $product_name,
    $product_category,
    $product_description,
    $thumbnailPath,
    $imagePaths[0],
    $imagePaths[1],
    $imagePaths[2],
    $product_price,
    $product_color
  );

  if ($stmt->execute()) {
    header('location: products.php');
    exit;
  } else {
    header('location: add_product.php?error=Lỗi khi thêm sản phẩm mới');
    exit;
  }
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
  <title>Thêm sản phẩm mới</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <div class="col py-3">
        <h2 class="text-center mb-3">Thêm sản phẩm mới</h2>
        <div class="container mx-auto">
          <form id="uploadForm" action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-name">Tên sản phẩm</label>
                  <input type="text" name="product_name" class="form-control" id="product-name">
                </div>
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-price">Giá</label>
                  <input type="number" name="product_price" min="0" class="form-control" id="product-price">
                </div>
              </div>

              <div class="row mb-2">
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-category">Phân loại</label>
                  <select name="product_category" id="product-category" class="form-select">
                    <?php while ($row = $categories->fetch_assoc()) { ?>
                      <option value="<?php echo $row['category_id']; ?>">
                        <?php echo $row['category_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-color">Màu sắc</label>
                  <input type="text" name="product_color" class="form-control" id="product-color">
                </div>
              </div>

              <div class="row mb-2">
                <div class="form-group col-md-12 mt-2">
                  <label class="form-label" for="product-description">Mô tả</label>
                  <textarea class="form-control" name="product_description" id="product-description" rows="3"></textarea>
                </div>
              </div>

              <div class="row mb-2">
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="thumbnail">Ảnh đại diện</label>
                  <input class="form-control" type="file" name="thumbnail" id="thumbnail" accept="image/*">
                  <div id="thumbnail-preview" class="mt-2"></div>
                </div>

                <!-- Ảnh phụ 1 -->
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="sub-image-1">Ảnh phụ 1</label>
                  <input class="form-control" type="file" name="sub-image-1" id="sub-image-1" accept="image/*">
                  <div id="sub-preview-1" class="mt-2 d-flex gap-2 flex-wrap"></div>
                </div>

                <!-- Ảnh phụ 2 -->
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="sub-image-2">Ảnh phụ 2</label>
                  <input class="form-control" type="file" name="sub-image-2" id="sub-image-2" accept="image/*">
                  <div id="sub-preview-2" class="mt-2 d-flex gap-2 flex-wrap"></div>
                </div>

                <!-- Ảnh phụ 3 -->
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="sub-image-3">Ảnh phụ 3</label>
                  <input class="form-control" type="file" name="sub-image-3" id="sub-image-3" accept="image/*">
                  <div id="sub-preview-3" class="mt-2 d-flex gap-2 flex-wrap"></div>
                </div>
              </div>

              <span class="text-danger"><?php if (isset($_GET['error'])) echo $_GET['error']; ?></span>

              <div class="row mb-2">
                <div class="form-group col-md-4 mt-2">
                  <input class="btn btn-primary" type="submit" name="add_product_btn" value="Thêm">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

  <script>
    function previewImage(inputId, previewId) {
      const input = document.getElementById(inputId);
      const preview = document.getElementById(previewId);

      input.addEventListener("change", function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; height: auto;" class="img-thumbnail">`;
          };
          reader.readAsDataURL(file);
        } else {
          preview.innerHTML = '';
        }
      });
    }

    previewImage("thumbnail", "thumbnail-preview");
    previewImage("sub-image-1", "sub-preview-1");
    previewImage("sub-image-2", "sub-preview-2");
    previewImage("sub-image-3", "sub-preview-3");
  </script>

</body>

</html>