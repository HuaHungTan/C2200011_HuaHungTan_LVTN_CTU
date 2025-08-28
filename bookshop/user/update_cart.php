<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$book_id = intval($_POST['book_id'] ?? 0);
$quantity = max(1, intval($_POST['quantity'] ?? 1));

// Lấy giỏ của người dùng
$cart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cart_id FROM carts WHERE user_id = $user_id"));
if (!$cart) {
  echo "<script>alert('❌ Không tìm thấy giỏ hàng.'); window.location.href='cart.php';</script>";
  exit;
}

$cart_id = $cart['cart_id'];

// Lấy giá sách hiện tại
$book = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT price_out, price_discount, quantity AS stock 
  FROM books 
  WHERE book_id = $book_id AND is_deleted = 0
"));
if (!$book) {
  echo "<script>alert('❌ Sách không tồn tại.'); window.location.href='cart.php';</script>";
  exit;
}

// Giới hạn số lượng tối đa theo tồn kho
if ($quantity > $book['stock']) {
  $quantity = $book['stock'];
}

// Xác định giá áp dụng
$price = ($book['price_discount'] > 0 && $book['price_discount'] < $book['price_out']) 
         ? $book['price_discount'] : $book['price_out'];
$subtotal = $price * $quantity;

// Cập nhật sách trong giỏ
mysqli_query($conn, "
  UPDATE cart_details 
  SET quantity = $quantity, price_out = $price, subtotal = $subtotal 
  WHERE cart_id = $cart_id AND book_id = $book_id
");

// Tính lại tổng đơn
$total_query = mysqli_query($conn, "
  SELECT SUM(subtotal) AS total FROM cart_details WHERE cart_id = $cart_id
");
$total_price = floatval(mysqli_fetch_assoc($total_query)['total']);
$shipping_fee = ($total_price >= 500000) ? 0 : 30000;
$final_amount = $total_price + $shipping_fee;

// Cập nhật bảng carts
mysqli_query($conn, "
  UPDATE carts 
  SET total_price = $total_price, shipping_fee = $shipping_fee, final_amount = $final_amount
  WHERE cart_id = $cart_id
");

// Quay về lại trang giỏ
echo "<script>alert('✅ Đã cập nhật giỏ hàng!'); window.location.href='cart.php';</script>";
?>