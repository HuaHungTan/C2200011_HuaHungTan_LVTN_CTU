<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('includes/header.php');
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">➕ Thêm danh mục mới</h4>
  <form method="post" action="xuly_addcategory.php" class="col-md-6">
    <label class="form-label">Tên danh mục:</label>
    <input type="text" name="name" class="form-control" required>

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Lưu</button>
      <a href="listdanhmuc.php" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>