<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$book_id = intval($_POST['book_id'] ?? 0);
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$price_in = floatval($_POST['price_in'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);
$author_id = intval($_POST['author_id'] ?? 0);
$publisher_id = intval($_POST['publisher_id'] ?? 0);
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

if ($book_id === 0 || $name === '' || $price_in <= 0 || $quantity < 0) {
  echo "<script>alert('❌ Dữ liệu không hợp lệ.'); window.location.href='update_book.php?id=$book_id';</script>";
  exit;
}

mysqli_query($conn, "
  UPDATE books SET 
    name = '$name',
    price_in = $price_in,
    quantity = $quantity,
    category_id = $category_id,
    author_id = $author_id,
    publisher_id = $publisher_id,
    description = '$description'
  WHERE book_id = $book_id
");

echo "<script>alert('✅ Đã cập nhật sách!'); window.location.href='listsanpham.php';</script>";