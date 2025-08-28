<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$discount_id = $_GET['id'] ?? null;

if ($discount_id) {
  // ✅ Cập nhật is_deleted = 0
  $stmt = $conn->prepare("UPDATE discount SET is_deleted = 0 WHERE discount_id = ?");
  $stmt->bind_param("i", $discount_id);
  $stmt->execute();
  $stmt->close();
}

header("Location: listkhuyenmai.php");
exit;
?>