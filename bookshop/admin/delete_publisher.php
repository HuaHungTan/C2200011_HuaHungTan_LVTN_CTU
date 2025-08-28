<?php
session_start();
require('../database/conn.php');

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Lấy ID NXB từ URL
$publisher_id = intval($_GET['id'] ?? 0);

// Kiểm tra hợp lệ
if ($publisher_id <= 0) {
  echo "<script>alert('❌ Không xác định được nhà xuất bản.'); window.location.href='listnxb.php';</script>";
  exit;
}

// Cập nhật trạng thái ẩn
$sql = "UPDATE publishers SET is_deleted = 1 WHERE publisher_id = $publisher_id";
mysqli_query($conn, $sql);

// Quay về trang danh sách
echo "<script>alert('✅ Đã ẩn nhà xuất bản thành công.'); window.location.href='listnxb.php';</script>";
?>