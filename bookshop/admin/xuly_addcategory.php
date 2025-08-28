<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');

// Nhận dữ liệu
$name = trim($_POST['name'] ?? '');

// Kiểm tra rỗng
if ($name === '') {
  echo "<script>alert('❌ Tên danh mục không được để trống.'); window.history.back();</script>";
  exit;
}

// Kiểm tra trùng (nếu cần)
$check_sql = "SELECT * FROM categories WHERE name = '$name' AND is_deleted = 0";
$check = mysqli_fetch_assoc(mysqli_query($conn, $check_sql));
if ($check) {
  echo "<script>alert('❌ Danh mục này đã tồn tại.'); window.history.back();</script>";
  exit;
}

// Thêm danh mục
$sql = "INSERT INTO categories (name) VALUES ('$name')";
mysqli_query($conn, $sql);

// Quay về
echo "<script>alert('✅ Thêm danh mục thành công!'); window.location.href='listdanhmuc.php';</script>";
?>