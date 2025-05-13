<?php

session_start();

include '../server/connection.php';

$admin_name = $_SESSION['admin_name'];

if (!isset($_SESSION['admin_logged_in'])) {
  header('location: login.php');
  exit;
}

?>

<?php

$stmt = $conn->prepare("SELECT * FROM accounts");
$stmt->execute();
$accounts = $stmt->get_result();

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
  <title>Accounts Dashboard</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include '../admin/layouts/sidebar.php'; ?>

      <!-- Content (optional) -->
      <div class="col py-3">
        <h2>Quản lý tài khoản</h2>
        <a class="btn btn-primary" href="add_account.php">Thêm</a>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Tên tài khoản</th>
              <th scope="col">Email</th>
              <th scope="col">Vai trò</th>
              <th scope="col">Trạng thái</th>
              <th scope="col">Chỉnh sửa</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($accounts as $account) { ?>
              <tr class="align-middle">
                <th scope="row"><?php echo $account['account_id']; ?></th>
                <td><?php echo $account['account_name'] ?></td>
                <td><?php echo $account['account_email']; ?></td>
                <td><?php echo $account['account_role']; ?></td>
                <td style="color: <?php if ($account['account_status'] == "Active") echo "green";
                                  else echo "red"; ?>">
                  <?php echo $account['account_status']; ?>
                </td>
                <td>
                  <a class="btn btn-warning" href="edit_account.php?account_id=<?php echo $account['account_id']; ?>">
                    Sửa
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