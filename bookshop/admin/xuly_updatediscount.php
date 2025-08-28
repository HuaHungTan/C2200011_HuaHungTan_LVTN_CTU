<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');

// Lấy dữ liệu từ form
$discount_id = intval($_POST['discount_id'] ?? 0);
$book_id = intval($_POST['book_id'] ?? 0);
$discount_percent = floatval($_POST['discount_percent'] ?? 0);
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

// Kiểm tra dữ liệu hợp lệ
$errors = [];

if ($discount_percent < 0 || $discount_percent > 100) {
  $errors[] = "❌ Phần trăm giảm giá phải nằm trong khoảng từ 0 đến 100.";
}

if (strtotime($start_date) > strtotime($end_date)) {
  $errors[] = "❌ Ngày bắt đầu không được sau ngày kết thúc.";
}

if ($discount_id <= 0 || $book_id <= 0) {
  $errors[] = "❌ Dữ liệu không hợp lệ.";
}

if (!empty($errors)) {
  echo "<script>alert('" . implode("\\n", $errors) . "'); window.history.back();</script>";
  exit;
}

// Thực hiện cập nhật
$sql = "UPDATE discount SET
          book_id = $book_id,
          discount_percent = $discount_percent,
          start_date = '$start_date',
          end_date = '$end_date'
        WHERE discount_id = $discount_id";

if (mysqli_query($conn, $sql)) {
  echo "<script>alert('✅ Cập nhật khuyến mại thành công.'); window.location.href='listkhuyenmai.php';</script>";
} else {
  echo "<script>alert('❌ Lỗi khi cập nhật: " . mysqli_error($conn) . "'); window.history.back();</script>";
}
?>