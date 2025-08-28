<?php
session_start();
require('../database/conn.php');

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$user_id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>

<?php require('includes/header.php'); ?>

<div class="container mt-4">
  <h3>Chỉnh sửa người dùng</h3>
  <form action="xuly_updateuser.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">

    <div class="mb-3">
      <label>Email:</label>
      <input type="email" name="email" class="form-control" value="<?= $row['email'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Họ tên:</label>
      <input type="text" name="name" class="form-control" value="<?= $row['name'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Địa chỉ:</label>
      <input type="text" name="address" class="form-control" value="<?= $row['address'] ?>">
    </div>

    <div class="mb-3">
      <label>Số điện thoại:</label>
      <input type="text" name="phone" class="form-control" value="<?= $row['phone'] ?>">
    </div>

    <div class="mb-3">
      <label>Vai trò:</label>
      <select name="role" class="form-control">
        <option value="customer" <?= $row['role'] === 'customer' ? 'selected' : '' ?>>Người dùng</option>
        <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>
    <div class="mb-3">
        <label>Ảnh đại diện:</label><br>
        <img src="../../<?= $row['avt'] ?>" style="max-height:80px;"><br><br>
        <input type="file" name="avt" accept="image/*" class="form-control">
    </div>


    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="listnguoidung.php" class="btn btn-secondary ms-2">Quay lại</a>
  </form>
</div>

<?php require('includes/footer.php'); ?>