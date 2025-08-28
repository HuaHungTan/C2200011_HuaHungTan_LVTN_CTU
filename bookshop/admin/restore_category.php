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

if ($category_id <= 0) {
  echo "<script>alert('❌ Không xác định được danh mục.'); window.location.href='listdanhmuc.php';</script>";
  exit;
}

// Khôi phục trạng thái
$restore_sql = "UPDATE categories SET is_deleted = 0 WHERE category_id = $category_id";
mysqli_query($conn, $restore_sql);

// Quay về trang danh mục
echo "<script>alert('✅ Đã khôi phục danh mục thành công.'); window.location.href='listdanhmuc.php';</script>";
?>