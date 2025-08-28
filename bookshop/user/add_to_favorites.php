<?php
session_start();
require('../database/conn.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('Bạn cần đăng nhập để thêm vào yêu thích.'); window.location.href='login.php';</script>";
  exit;
}

$user_id = intval($_SESSION['user_id']);
$book_id = intval($_POST['book_id'] ?? 0);

// Kiểm tra giá trị hợp lệ
if ($book_id < 1) {
  echo "<script>alert('Sách không hợp lệ.'); window.location.href='index.php';</script>";
  exit;
}

// Kiểm tra đã tồn tại trong favorites chưa
$check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id=$user_id AND book_id=$book_id");
if (mysqli_num_rows($check) > 0) {
  echo "<script>alert('Bạn đã yêu thích sách này rồi.'); window.location.href='productdetails.php?id=$book_id#favorites';</script>";
  exit;
}

// Thêm vào favorites
$insert = mysqli_query($conn, "INSERT INTO favorites (user_id, book_id) VALUES ($user_id, $book_id)");
if ($insert) {
  echo "<script>alert('✅ Đã thêm vào sách yêu thích!'); window.location.href='productdetails.php?id=$book_id#favorites';</script>";
} else {
  echo "<script>alert('❌ Không thể thêm vào yêu thích. Vui lòng thử lại.'); window.location.href='productdetails.php?id=$book_id';</script>";
}
?>