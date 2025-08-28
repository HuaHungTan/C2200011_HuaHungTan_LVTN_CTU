<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('includes/header.php');
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">🏢 Thêm nhà xuất bản mới</h4>
  <form method="post" action="xuly_addpublisher.php" enctype="multipart/form-data" class="col-md-8">

    <label class="form-label">Tên nhà xuất bản:</label>
    <input type="text" name="name" class="form-control" required>

    <label class="form-label mt-3">Logo NXB:</label>
    <input type="file" name="logo_url" accept="image/*" class="form-control">

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Lưu NXB</button>
      <a href="listnxb.php" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>