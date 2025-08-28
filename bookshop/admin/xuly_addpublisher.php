<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$name = trim($_POST['name'] ?? '');
$logo_path = 'data_image/publisher/default.jpg';

// Kiểm tra tên hợp lệ
if ($name === '') {
  echo "<script>alert('❌ Tên nhà xuất bản không được để trống.'); window.history.back();</script>";
  exit;
}
// Kiểm tra trùng tên NXB
$check_sql = "SELECT * FROM publishers WHERE LOWER(name) = LOWER('$name') AND is_deleted = 0";
$check = mysqli_fetch_assoc(mysqli_query($conn, $check_sql));
if ($check) {
  echo "<script>alert('❌ Nhà xuất bản này đã tồn tại.'); window.history.back();</script>";
  exit;
}

// Xử lý logo nếu có
if (isset($_FILES['logo_url']) && $_FILES['logo_url']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['logo_url']['tmp_name'];
  $filename = time() . '_' . basename($_FILES['logo_url']['name']);
  $logo_path = 'data_image/publisher_images/' . $filename;
  move_uploaded_file($tmp, '../' . $logo_path);
}

// Thêm vào CSDL
$sql = "INSERT INTO publishers (name, logo_url) VALUES ('$name', '$logo_path')";
mysqli_query($conn, $sql);

// Quay về
echo "<script>alert('✅ Đã thêm nhà xuất bản thành công!'); window.location.href='listnxb.php';</script>";
?>