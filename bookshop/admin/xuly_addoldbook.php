<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$book_id = intval($_POST['book_id'] ?? 0);
$new_price_in = floatval($_POST['price_in'] ?? 0);
$new_quantity = intval($_POST['quantity'] ?? 0);
$admin_id = $_SESSION['user_id'];
$added_date = date('Y-m-d');

$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT quantity, price_in FROM books WHERE book_id = $book_id"));
$old_qty = $book['quantity'];
$old_price_in = $book['price_in'];

// Tính lại giá nhập sách
$total_qty = $old_qty + $new_quantity;
$avg_price_in = (($old_price_in * $old_qty) + ($new_price_in * $new_quantity)) / $total_qty;

mysqli_query($conn, "
  UPDATE books 
  SET quantity = $total_qty,
      price_in = $avg_price_in
  WHERE book_id = $book_id
");

mysqli_query($conn, "
  INSERT INTO added_book (book_id, price_in, quantity, type, added_by, added_date)
  VALUES ($book_id, $new_price_in, $new_quantity, 'Nhập thêm sách có sẵn', $admin_id, '$added_date')
");

echo "<script>alert('✅ Nhập thêm thành công!'); window.location.href='listsanpham.php';</script>";