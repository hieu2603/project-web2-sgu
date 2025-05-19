<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
  header('location: products.php');
  exit;
}

// Get categories
include '../admin/utils/get_categories.php';

$product_id = (int)$_GET['product_id'];

$product_stmt = $conn->prepare("SELECT products.*, categories.* 
                                  FROM products 
                                  INNER JOIN categories 
                                  ON products.category_id=categories.category_id 
                                  WHERE product_id = ?");

$product_stmt->bind_param('i', $product_id);
$product_stmt->execute();

$product = $product_stmt->get_result();
$row = $product->fetch_assoc();

if (isset($_POST['edit_product_btn'])) {
  $product_name = $_POST['product_name'] ?? "";
  $product_category = $_POST['product_category'] ?? "";
  $product_price = $_POST['product_price'] ?? "";
  $product_color = $_POST['product_color'] ?? "";
  $product_description = $_POST['product_description'] ?? "";

  $imageDir = "../assets/img/";
  $thumbnailPath = $row['product_image'];
  $imagePaths = [
    $row['product_image2'],
    $row['product_image3'],
    $row['product_image4']
  ];

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
  }

  // Xử lý ảnh phụ
  $subImages = ['sub-image-1', 'sub-image-2', 'sub-image-3'];
  foreach ($subImages as $index => $key) {
    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
      $fileName = time() . '_sub_' . $index . '_' . basename($_FILES[$key]['name']);
      $targetFilePath = $imageDir . $fileName;
      if (move_uploaded_file($_FILES[$key]['tmp_name'], $targetFilePath)) {
        $imagePaths[$index] = $targetFilePath;
      } else {
        echo "<script>alert('Tải lên ảnh phụ thất bại'); window.history.back();</script>";
        exit;
      }
    }
  }

  // Cập nhật vào database
  $stmt = $conn->prepare("UPDATE products
                          SET product_name = ?, category_id = ?, product_description = ?,
                          product_price = ?, product_color = ?, product_image = ?, 
                          product_image2 = ?, product_image3 = ?, product_image4 = ?
                          WHERE product_id = ?");
  $stmt->bind_param(
    'sisisssssi',
    $product_name,
    $product_category,
    $product_description,
    $product_price,
    $product_color,
    $thumbnailPath,
    $imagePaths[0],
    $imagePaths[1],
    $imagePaths[2],
    $product_id
  );

  if ($stmt->execute()) {
    header('location: products.php');
    exit;
  } else {
    header('location: edit_product.php?error=Lỗi khi chỉnh sửa sản phẩm');
  }
}

if (isset($_POST['delete_product_btn'])) {
  $count_stmt = $conn->prepare("SELECT COUNT(*) AS total 
                                FROM order_items
                                WHERE product_id = ?");

  $count_stmt->bind_param('i', $product_id);
  $count_stmt->execute();

  $result = $count_stmt->get_result();
  $check = $result->fetch_assoc();

  if ($check['total'] > 0) {
    $update_product_stmt = $conn->prepare("UPDATE products
                                           SET product_status = 'Ngừng bán'
                                           WHERE product_id = ?");

    $update_product_stmt->bind_param('i', $product_id);
    if ($update_product_stmt->execute()) {
      header('location: edit_product.php?product_id=' . $product_id . '&error=Sản phẩm đã bán ra không thể xóa. Hệ thống sẽ ẩn sản phẩm');
      exit;
    } else {
      header('location: edit_product.php?product_id=' . $product_id . '&error=Lỗi khi ẩn sản phẩm');
      exit;
    }
  } else {
    $delete_product_stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $delete_product_stmt->bind_param('i', $product_id);
    if ($delete_product_stmt->execute()) {
      header('location: products.php?success=Xóa thành công');
      exit;
    } else {
      header('location: edit_product.php?product_id=' . $product_id . '&error=Lỗi khi xóa sản phẩm');
      exit;
    }
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
  <title>Edit Product</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <div class="col py-3">
        <h2>Chỉnh sửa thông tin sản phẩm</h2>
        <div class="container mx-auto">
          <form id="uploadForm" action="edit_product.php?product_id=<?php echo $_GET['product_id']; ?>" method="post" enctype="multipart/form-data">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-name">Tên sản phẩm</label>
                  <input value="<?php echo $row['product_name']; ?>" type="text" name="product_name" class="form-control" id="product-name">
                </div>
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-price">Giá</label>
                  <input value="<?php echo $row['product_price']; ?>" type="number" name="product_price" min="0" class="form-control" id="product-price">
                </div>
              </div>

              <div class="row mb-2">
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-category">Phân loại</label>
                  <select name="product_category" id="product-category" class="form-select">
                    <?php while ($cat = $categories->fetch_assoc()) { ?>
                      <option value="<?php echo $cat['category_id']; ?>"
                        <?php echo ($cat['category_id'] == $row['category_id']) ? 'selected' : ''; ?>>
                        <?php echo $cat['category_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="product-color">Màu sắc</label>
                  <input value="<?php echo $row['product_color']; ?>" type="text" name="product_color" class="form-control" id="product-color">
                </div>
              </div>

              <div class="row mb-2">
                <div class="form-group col-md-12 mt-2">
                  <label class="form-label" for="product-description">Mô tả</label>
                  <textarea class="form-control" name="product_description" id="product-description" rows="3"><?php echo $row['product_description']; ?></textarea>
                </div>
              </div>

              <div class="row mb-2">
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="thumbnail">Ảnh đại diện</label>
                  <input class="form-control" type="file" name="thumbnail" id="thumbnail" accept="image/*">
                  <div id="thumbnail-preview" class="mt-2">
                    <img src="<?php echo $row['product_image']; ?>" class="img-thumbnail" style="width: 150px; height: 150px;">
                  </div>
                </div>

                <!-- Ảnh phụ 1 -->
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="sub-image-1">Ảnh phụ 1</label>
                  <input class="form-control" type="file" name="sub-image-1" id="sub-image-1" accept="image/*">
                  <div id="sub-preview-1" class="mt-2 d-flex gap-2 flex-wrap">
                    <img src="<?php echo $row['product_image2']; ?>" class="img-thumbnail" style="width: 150px; height: 150px;">
                  </div>
                </div>

                <!-- Ảnh phụ 2 -->
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="sub-image-2">Ảnh phụ 2</label>
                  <input class="form-control" type="file" name="sub-image-2" id="sub-image-2" accept="image/*">
                  <div id="sub-preview-2" class="mt-2 d-flex gap-2 flex-wrap">
                    <img src="<?php echo $row['product_image3']; ?>" class="img-thumbnail" style="width: 150px; height: 150px;">
                  </div>
                </div>

                <!-- Ảnh phụ 3 -->
                <div class="form-group col-md-3 mt-2">
                  <label class="form-label" for="sub-image-3">Ảnh phụ 3</label>
                  <input class="form-control" type="file" name="sub-image-3" id="sub-image-3" accept="image/*">
                  <div id="sub-preview-3" class="mt-2 d-flex gap-2 flex-wrap">
                    <img src="<?php echo $row['product_image4']; ?>" class="img-thumbnail" style="width: 150px; height: 150px;">
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <div class="form-group col-md-4 mt-2">
                  <input class="btn btn-primary" type="submit" name="edit_product_btn" value="Lưu">
                  <input class="btn btn-danger ms-2" type="submit" name="delete_product_btn" value="Xóa">
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