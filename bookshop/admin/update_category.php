<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('../database/conn.php');
require('includes/header.php');

// Lấy thông tin danh mục
$category_id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM categories WHERE category_id = $category_id";
$category = mysqli_fetch_assoc(mysqli_query($conn, $sql));
if (!$category) {
  echo "<script>alert('❌ Không tìm thấy danh mục.'); window.location.href='listdanhmuc.php';</script>";
  exit;
}
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">✏️ Cập nhật danh mục</h4>
  <form method="post" action="xuly_updatecategory.php" class="col-md-6">
    <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
    <label class="form-label">Tên danh mục:</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Cập nhật</button>
      <a href="listdanhmuc.php" class="btn btn-secondary">Quay lại</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>