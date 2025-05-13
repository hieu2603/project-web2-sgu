<?php

session_start();

include '../server/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

$stmt = $conn->prepare("SELECT * FROM categories ORDER BY category_id ASC");
$stmt->execute();
$categories = $stmt->get_result();

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
  <title>Categories Dashboard</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <!-- Content (optional) -->
      <div class="col py-3">
        <h2>Quản lý loại phân loại</h2>
        <a class="btn btn-primary" href="add_category.php">Thêm</a>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Tên Phân Loại</th>
              <th scope="col">Chỉnh sửa</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $category) { ?>
              <tr class="align-middle">
                <th scope="row"><?php echo $category['category_id']; ?></th>
                <td><?php echo $category['category_name']; ?></td>
                <td>
                  <a class="btn btn-warning" href="edit_category.php?category_id=<?php echo $category['category_id']; ?>">
                    Edit
                  </a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>