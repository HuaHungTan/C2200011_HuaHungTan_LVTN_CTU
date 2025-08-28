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

// Khôi phục trạng thái
$sql = "UPDATE publishers SET is_deleted = 0 WHERE publisher_id = $publisher_id";
mysqli_query($conn, $sql);

// Quay về danh sách
echo "<script>alert('✅ Nhà xuất bản đã được khôi phục!'); window.location.href='listnxb.php';</script>";
?>