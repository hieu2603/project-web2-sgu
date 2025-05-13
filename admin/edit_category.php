<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

$category_id = $_GET['category_id'];

if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
  header('location: categories.php');
  exit;
} else {
  $category_stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
  $category_stmt->bind_param('i', $category_id);
  $category_stmt->execute();

  $category = $category_stmt->get_result();
}

if (isset($_POST['edit_category_btn'])) {
  $category_name = $_POST['category_name'];

  $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
  $stmt->bind_param('si', $category_name, $category_id);

  if ($stmt->execute()) {
    header('location: categories.php');
    exit;
  } else {
    header('location: edit_category.php?error=Lỗi khi chỉnh sửa phân loại');
  }
}

if (isset($_POST['delete_category_btn'])) {
  $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id = ?");
  $stmt->bind_param('i', $category_id);
  $stmt->execute();

  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row['total'] > 0) {
    header('location: edit_category.php?error=Không thể xóa loại sản phẩm này vì đã được liên kết với sản phẩm');
    exit;
  } else {
    $delete_category_stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $delete_category_stmt->bind_param('i', $category_id);
    $delete_category_stmt->execute();

    header('location: categories.php');
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
  <title>Edit Category</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <div class="col py-3">
        <h2>Sửa thông tin phân loại sản phẩm</h2>
        <div class="container mx-auto">
          <form action="edit_category.php?category_id=<?php echo $_GET['category_id']; ?>" method="post">
            <div class="container-fluid">
              <?php while ($row = $category->fetch_assoc()) { ?>
                <div class="row mb-2">
                  <div class="form-group col-md-6 mt-2">
                    <label class="form-label" for="category-name">Tên phân loại</label>
                    <input value="<?php echo $row['category_name']; ?>" type="text" name="category_name" class="form-control" id="category-name">
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="form-group col-md-4 mt-2">
                    <input class="btn btn-primary" type="submit" name="edit_category_btn" value="Lưu">
                    <input class="btn btn-danger ms-2" type="submit" name="delete_category_btn" value="Xóa">
                  </div>
                </div>
            </div>
          <?php } ?>
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