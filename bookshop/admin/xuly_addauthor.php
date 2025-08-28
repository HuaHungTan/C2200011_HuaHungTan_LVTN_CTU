<?php
session_start();
require('../database/conn.php');

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Nhận dữ liệu
$name = trim($_POST['name'] ?? '');
$nationality = trim($_POST['nationality'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$img_path = 'data_image/author/default.jpg';

// Kiểm tra tên hợp lệ
if ($name === '') {
  echo "<script>alert('❌ Tên tác giả không được để trống.'); window.history.back();</script>";
  exit;
}

// Xử lý ảnh nếu có
if (isset($_FILES['img_url']) && $_FILES['img_url']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['img_url']['tmp_name'];
  $filename = time() . '_' . basename($_FILES['img_url']['name']);
  $img_path = 'data_image/author_images/' . $filename;
  move_uploaded_file($tmp, '../' . $img_path);
}

// Thêm vào cơ sở dữ liệu
$sql = "INSERT INTO authors (name, nationality, bio, img_url) VALUES ('$name', '$nationality', '$bio', '$img_path')";
mysqli_query($conn, $sql);

// Quay về
echo "<script>alert('✅ Đã thêm tác giả mới thành công!'); window.location.href='listtacgia.php';</script>";
?>