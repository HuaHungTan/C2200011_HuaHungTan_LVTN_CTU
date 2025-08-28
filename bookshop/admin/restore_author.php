<?php
session_start();
require('../database/conn.php');

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// Nhận ID tác giả
$author_id = intval($_GET['id'] ?? 0);

// Kiểm tra hợp lệ
if ($author_id <= 0) {
  echo "<script>alert('❌ Không xác định được tác giả.'); window.location.href='listtacgia.php';</script>";
  exit;
}

// Khôi phục trạng thái
$sql = "UPDATE authors SET is_deleted = 0 WHERE author_id = $author_id";
mysqli_query($conn, $sql);

// Quay về
echo "<script>alert('✅ Tác giả đã được khôi phục thành công!'); window.location.href='listtacgia.php';</script>";
?>