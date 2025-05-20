<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

if (isset($_POST['add_category_btn'])) {
  $category_name = $_POST['category_name'];

  if (empty($category_name)) {
    header('location: add_category.php?error=Thông tin không được để trống');
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
  $stmt->bind_param('s', $category_name);

  if ($stmt->execute()) {
    header('location: categories.php');
    exit;
  } else {
    header('location: add_category.php?error=Lỗi khi tạo mới phân loại');
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
  <title>Add Category</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <div class="col py-3">
        <h2 class="text-center mb-3">Thêm phân loại sản phẩm mới</h2>
        <div class="container mx-auto">
          <form action="add_category.php" method="post">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="form-group col-md-6 mt-2">
                  <label class="form-label" for="category-name">Tên phân loại</label>
                  <input type="text" name="category_name" class="form-control" id="category-name">
                </div>
              </div>
              <span class="text-danger"><?php if (isset($_GET['error'])) echo $_GET['error']; ?></span>
              <div class="row mb-2">
                <div class="form-group col-md-4 mt-2">
                  <input class="btn btn-primary" type="submit" name="add_category_btn" value="Thêm">
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
</body>

</html>