<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Nhận dữ liệu
$author_id = intval($_POST['author_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$nationality = trim($_POST['nationality'] ?? '');
$bio = trim($_POST['bio'] ?? '');

if ($author_id <= 0 || $name === '') {
  echo "<script>alert('❌ Dữ liệu không hợp lệ.'); window.history.back();</script>";
  exit;
}

// Xử lý ảnh nếu có
if (isset($_FILES['img_url']) && $_FILES['img_url']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['img_url']['tmp_name'];
  $filename = time() . '_' . basename($_FILES['img_url']['name']);
  $path = 'data_image/author_images/' . $filename;
  move_uploaded_file($tmp, '../' . $path);

  // Cập nhật có ảnh
  $sql = "UPDATE authors SET name='$name', nationality='$nationality', bio='$bio', img_url='$path' WHERE author_id = $author_id";
} else {
  // Cập nhật không ảnh
  $sql = "UPDATE authors SET name='$name', nationality='$nationality', bio='$bio' WHERE author_id = $author_id";
}

mysqli_query($conn, $sql);

// Quay về
echo "<script>alert('✅ Cập nhật tác giả thành công!'); window.location.href='listtacgia.php';</script>";
?>