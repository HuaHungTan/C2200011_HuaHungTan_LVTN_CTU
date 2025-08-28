<?php
session_start();
require('../database/conn.php');

// ✅ Kiểm tra quyền truy cập admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// ✅ Nhận ID khuyến mãi từ GET
$discount_id = $_GET['id'] ?? null;

if ($discount_id) {
  // ✅ Cập nhật is_deleted = 1
  $stmt = $conn->prepare("UPDATE discount SET is_deleted = 1 WHERE discount_id = ?");
  $stmt->bind_param("i", $discount_id);
  $stmt->execute();
  $stmt->close();
}

// ✅ Quay về danh sách
header("Location: listkhuyenmai.php");
exit;
?>