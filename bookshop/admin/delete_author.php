<?php
session_start();
require('../database/conn.php');

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Lấy ID tác giả từ URL
$author_id = intval($_GET['id'] ?? 0);

// Kiểm tra ID hợp lệ
if ($author_id <= 0) {
  echo "<script>alert('❌ Không xác định được tác giả.'); window.location.href='listtacgia.php';</script>";
  exit;
}

// Cập nhật trạng thái is_deleted
$update_sql = "UPDATE authors SET is_deleted = 1 WHERE author_id = $author_id";
mysqli_query($conn, $update_sql);

// Quay về trang danh sách tác giả
echo "<script>alert('✅ Đã ẩn tác giả thành công.'); window.location.href='listtacgia.php';</script>";
?>