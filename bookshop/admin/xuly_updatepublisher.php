<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$publisher_id = intval($_POST['publisher_id'] ?? 0);
$name = trim($_POST['name'] ?? '');

if ($publisher_id <= 0 || $name === '') {
  echo "<script>alert('❌ Dữ liệu không hợp lệ.'); window.history.back();</script>";
  exit;
}

// Xử lý logo nếu có
if (isset($_FILES['logo_url']) && $_FILES['logo_url']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['logo_url']['tmp_name'];
  $filename = time() . '_' . basename($_FILES['logo_url']['name']);
  $path = 'data_image/publisher_images/' . $filename;
  move_uploaded_file($tmp, '../' . $path);

  // Cập nhật có logo
  $sql = "UPDATE publishers SET name = '$name', logo_url = '$path' WHERE publisher_id = $publisher_id";
} else {
  // Cập nhật không logo
  $sql = "UPDATE publishers SET name = '$name' WHERE publisher_id = $publisher_id";
}

mysqli_query($conn, $sql);

echo "<script>alert('✅ Đã cập nhật NXB thành công!'); window.location.href='listnxb.php';</script>";
?>