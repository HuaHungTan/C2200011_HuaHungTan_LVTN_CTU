<?php
session_start();
require('../database/conn.php');

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

// ✅ Lấy ID người dùng cần xóa
$user_id = intval($_GET['id'] ?? 0);
if ($user_id > 0) {
  mysqli_query($conn, "UPDATE users SET is_deleted = 0 WHERE user_id = $user_id");
}

header("Location: listnguoidung.php"); // quay về danh sách
exit;
?>