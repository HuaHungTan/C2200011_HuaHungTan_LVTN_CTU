<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$book_id = intval($_GET['book_id'] ?? 0);

// Lấy giỏ hàng
$cart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cart_id FROM carts WHERE user_id = $user_id"));
if (!$cart) {
  echo "<script>alert('❌ Không tìm thấy giỏ hàng.'); window.location.href='cart.php';</script>";
  exit;
}

$cart_id = $cart['cart_id'];

// Xóa sách khỏi giỏ
mysqli_query($conn, "
  DELETE FROM cart_details 
  WHERE cart_id = $cart_id AND book_id = $book_id
");

// Tính lại tổng sau khi xóa
$totals = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT SUM(subtotal) AS total FROM cart_details WHERE cart_id = $cart_id
"));
$total_price = floatval($totals['total'] ?? 0);
$shipping_fee = ($total_price >= 500000) ? 0 : ($total_price > 0 ? 30000 : 0);
$final_amount = $total_price + $shipping_fee;

// Cập nhật bảng carts
mysqli_query($conn, "
  UPDATE carts 
  SET total_price = $total_price, shipping_fee = $shipping_fee, final_amount = $final_amount 
  WHERE cart_id = $cart_id
");

echo "<script>alert('🗑️ Đã xóa khỏi giỏ hàng!'); window.location.href='cart.php';</script>";
?>