<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
$price_in = floatval($_POST['price_in'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);
$author_id = intval($_POST['author_id'] ?? 0);
$publisher_id = intval($_POST['publisher_id'] ?? 0);
$cover = $_FILES['cover'] ?? null;
$extras = $_FILES['extras'] ?? [];

if ($name === '' || $price_in <= 0 || $quantity <= 0) {
  echo "<script>alert('❌ Thiếu thông tin hoặc dữ liệu không hợp lệ.'); window.location.href='themsanpham.php';</script>";
  exit;
}

// ✅ Thêm sách mới (không cần price_out)
mysqli_query($conn, "
  INSERT INTO books (name, description, price_in, quantity, category_id, author_id, publisher_id)
  VALUES ('$name', '$description', $price_in, $quantity, $category_id, $author_id, $publisher_id)
");

$book_id = mysqli_insert_id($conn);

// ✅ Upload ảnh chính
if ($cover && $cover['size'] > 0) {
  $path = 'data_image/book_images/' . time() . '_' . basename($cover['name']);
  move_uploaded_file($cover['tmp_name'], '../' . $path);
  mysqli_query($conn, "
    INSERT INTO book_images (book_id, img_url, is_primary)
    VALUES ($book_id, '$path', 1)
  ");
}

// ✅ Upload ảnh phụ
for ($i = 0; $i < count($extras['name']); $i++) {
  if ($extras['size'][$i] > 0) {
    $path = 'data_image/book_images/' . time() . '_' . basename($extras['name'][$i]);
    move_uploaded_file($extras['tmp_name'][$i], '../' . $path);
    mysqli_query($conn, "
      INSERT INTO book_images (book_id, img_url, is_primary)
      VALUES ($book_id, '$path', 0)
    ");
  }
}

// ✅ Ghi nhận nhập kho ban đầu
$added_by = $_SESSION['user_id'];
$added_date = date('Y-m-d');
mysqli_query($conn, "
  INSERT INTO added_book (book_id, price_in, quantity, type, added_by, added_date)
  VALUES ($book_id, $price_in, $quantity, 'Thêm sách mới', $added_by, '$added_date')
");

echo "<script>alert('✅ Đã thêm sách mới!'); window.location.href='listsanpham.php';</script>";
?>