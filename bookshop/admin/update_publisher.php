<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('../database/conn.php');
require('includes/header.php');

// Lấy dữ liệu NXB
$publisher_id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM publishers WHERE publisher_id = $publisher_id";
$publisher = mysqli_fetch_assoc(mysqli_query($conn, $sql));

if (!$publisher) {
  echo "<script>alert('❌ Không tìm thấy NXB.'); window.location.href='listnxb.php';</script>";
  exit;
}
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">✏️ Chỉnh sửa nhà xuất bản</h4>
  <form method="post" action="xuly_updatepublisher.php" enctype="multipart/form-data" class="col-md-8">

    <input type="hidden" name="publisher_id" value="<?= $publisher['publisher_id'] ?>">

    <label class="form-label">Tên nhà xuất bản:</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($publisher['name']) ?>" required>

    <label class="form-label mt-3">Logo hiện tại:</label><br>
    <img src="../../<?= $publisher['logo_url'] ?>" style="max-height: 100px;"><br>

    <label class="form-label mt-3">Logo mới:</label>
    <input type="file" name="logo_url" accept="image/*" class="form-control">

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Cập nhật</button>
      <a href="listnxb.php" class="btn btn-secondary">Quay lại</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>