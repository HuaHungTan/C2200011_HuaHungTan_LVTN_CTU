<?php
session_start();
require('../database/conn.php');

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Lấy ID danh mục từ URL
$category_id = intval($_GET['id'] ?? 0);

// Nếu không có ID thì trả về luôn
if ($category_id <= 0) {
  echo "<script>alert('❌ Không xác định được danh mục.'); window.location.href='listdanhmuc.php';</script>";
  exit;
}

// Cập nhật trạng thái is_deleted
mysqli_query($conn, "UPDATE categories SET is_deleted = 1 WHERE category_id = $category_id");

// Quay lại trang danh mục
echo "<script>alert('✅ Đã ẩn danh mục thành công.'); window.location.href='listdanhmuc.php';</script>";
?>