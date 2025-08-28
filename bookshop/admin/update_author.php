<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('../database/conn.php');
require('includes/header.php');

// Lấy ID tác giả
$author_id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM authors WHERE author_id = $author_id";
$author = mysqli_fetch_assoc(mysqli_query($conn, $sql));

if (!$author) {
  echo "<script>alert('❌ Không tìm thấy tác giả.'); window.location.href='listtacgia.php';</script>";
  exit;
}
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">✏️ Chỉnh sửa thông tin tác giả</h4>
  <form method="post" action="xuly_updateauthor.php" enctype="multipart/form-data" class="col-md-8">

    <input type="hidden" name="author_id" value="<?= $author['author_id'] ?>">

    <label class="form-label">Tên tác giả:</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($author['name']) ?>" required>

    <label class="form-label mt-3">Quốc tịch:</label>
    <input type="text" name="nationality" class="form-control" value="<?= htmlspecialchars($author['nationality']) ?>">

    <label class="form-label mt-3">Giới thiệu:</label>
    <textarea name="bio" rows="5" class="form-control"><?= htmlspecialchars($author['bio']) ?></textarea>

    <label class="form-label mt-3">Ảnh đại diện mới:</label>
    <input type="file" name="img_url" accept="image/*" class="form-control">

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Cập nhật</button>
      <a href="listtacgia.php" class="btn btn-secondary">Quay lại</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>