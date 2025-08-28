<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('Bạn cần đăng nhập để thực hiện.'); window.location.href='login.php';</script>";
  exit;
}

$user_id = intval($_SESSION['user_id']);
$book_id = intval($_POST['book_id'] ?? 0);

if ($book_id < 1) {
  echo "<script>alert('Sách không hợp lệ.'); window.location.href='userprofile.php#favorites';</script>";
  exit;
}

// Xóa sách khỏi yêu thích
$delete = mysqli_query($conn, "DELETE FROM favorites WHERE user_id = $user_id AND book_id = $book_id");

if ($delete) {
  echo "<script>alert('❌ Đã xóa sách khỏi yêu thích.'); window.location.href='userprofile.php#favorites';</script>";
} else {
  echo "<script>alert('Không thể xóa sách khỏi yêu thích.'); window.location.href='userprofile.php#favorites';</script>";
}
?>