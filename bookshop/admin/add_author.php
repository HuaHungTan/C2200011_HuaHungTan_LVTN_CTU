<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('includes/header.php');
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">➕ Thêm tác giả mới</h4>
  <form method="post" action="xuly_addauthor.php" enctype="multipart/form-data" class="col-md-8">

    <label class="form-label">Tên tác giả:</label>
    <input type="text" name="name" class="form-control" required>

    <label class="form-label mt-3">Quốc tịch:</label>
    <input type="text" name="nationality" class="form-control">

    <label class="form-label mt-3">Giới thiệu:</label>
    <textarea name="bio" rows="5" class="form-control"></textarea>

    <label class="form-label mt-3">Ảnh đại diện:</label>
    <input type="file" name="img_url" accept="image/*" class="form-control">

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Lưu tác giả</button>
      <a href="listtacgia.php" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>