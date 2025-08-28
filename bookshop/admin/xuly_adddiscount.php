<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');

// Nhận dữ liệu từ form
$book_id = intval($_POST['book_id'] ?? 0);
$discount_percent = floatval($_POST['discount_percent'] ?? 0);
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

$errors = [];

// Kiểm tra dữ liệu đầu vào
if ($book_id <= 0) {
  $errors[] = "❌ Vui lòng chọn sách áp dụng.";
}
if ($discount_percent < 0 || $discount_percent > 100) {
  $errors[] = "❌ Phần trăm giảm giá phải từ 0 đến 100.";
}
if (strtotime($start_date) > strtotime($end_date)) {
  $errors[] = "❌ Ngày bắt đầu không được sau ngày kết thúc.";
}

// Kiểm tra sách đã có khuyến mại trùng thời gian (optional)
$check = mysqli_query($conn, "
  SELECT * FROM discount 
  WHERE book_id = $book_id 
    AND is_deleted = 0 
    AND (
      ('$start_date' BETWEEN start_date AND end_date) OR
      ('$end_date' BETWEEN start_date AND end_date)
    )
");
if (mysqli_num_rows($check) > 0) {
  $errors[] = "❌ Sách đã có khuyến mại trùng thời gian.";
}

// Nếu có lỗi → thông báo
if (!empty($errors)) {
  echo "<script>alert('" . implode("\\n", $errors) . "'); window.history.back();</script>";
  exit;
}

// Thêm khuyến mại mới
$sql = "INSERT INTO discount (book_id, discount_percent, start_date, end_date) 
        VALUES ($book_id, $discount_percent, '$start_date', '$end_date')";

if (mysqli_query($conn, $sql)) {
  echo "<script>alert('✅ Thêm khuyến mại thành công!'); window.location.href='listkhuyenmai.php';</script>";
} else {
  echo "<script>alert('❌ Lỗi khi thêm: " . mysqli_error($conn) . "'); window.history.back();</script>";
}
?>