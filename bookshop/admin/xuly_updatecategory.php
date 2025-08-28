<?php
session_start();
require('../database/conn.php');

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Nhận dữ liệu
$category_id = intval($_POST['category_id'] ?? 0);
$name = trim($_POST['name'] ?? '');

if ($category_id <= 0 || $name === '') {
  echo "<script>alert('❌ Dữ liệu không hợp lệ.'); window.history.back();</script>";
  exit;
}

// Kiểm tra trùng tên nếu cần
$check_sql = "SELECT * FROM categories WHERE name = '$name' AND category_id != $category_id AND is_deleted = 0";
$check = mysqli_fetch_assoc(mysqli_query($conn, $check_sql));
if ($check) {
  echo "<script>alert('❌ Tên danh mục này đã tồn tại.'); window.history.back();</script>";
  exit;
}

// Cập nhật
$update_sql = "UPDATE categories SET name = '$name' WHERE category_id = $category_id";
mysqli_query($conn, $update_sql);

// Quay lại
echo "<script>alert('✅ Cập nhật danh mục thành công!'); window.location.href='listdanhmuc.php';</script>";
?>