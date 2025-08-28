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

// ✅ Lấy thông tin sách
$book = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT price_out, price_discount, quantity 
  FROM books 
  WHERE book_id = $book_id AND is_deleted = 0
"));

if (!$book) {
  echo "<script>alert('❌ Không tìm thấy sách.'); window.location.href='product.php';</script>";
  exit;
}

// ✅ Giới hạn tồn kho
if ($quantity > $book['quantity']) {
  $quantity = $book['quantity'];
}

// ✅ Xác định giá theo khuyến mãi
$price = ($book['price_discount'] > 0 && $book['price_discount'] < $book['price_out']) 
         ? $book['price_discount'] : $book['price_out'];
$subtotal = $price * $quantity;

// ✅ Lấy hoặc tạo giỏ hàng
$cart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cart_id FROM carts WHERE user_id = $user_id"));
if (!$cart) {
  mysqli_query($conn, "INSERT INTO carts (user_id, total_price, shipping_fee, final_amount) 
                       VALUES ($user_id, 0, 0, 0)");
  $cart_id = mysqli_insert_id($conn);
} else {
  $cart_id = $cart['cart_id'];
}

// ✅ Thêm hoặc cập nhật sách trong giỏ
$existing = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT quantity FROM cart_details 
  WHERE cart_id = $cart_id AND book_id = $book_id
"));

if ($existing) {
  $new_qty = $existing['quantity'] + $quantity;
  $new_subtotal = $new_qty * $price;
  mysqli_query($conn, "
    UPDATE cart_details 
    SET quantity = $new_qty, price_out = $price, subtotal = $new_subtotal 
    WHERE cart_id = $cart_id AND book_id = $book_id
  ");
} else {
  mysqli_query($conn, "
    INSERT INTO cart_details (cart_id, book_id, quantity, price_out, subtotal)
    VALUES ($cart_id, $book_id, $quantity, $price, $subtotal)
  ");
}

// ✅ Tính lại tổng tiền cho giỏ
$totals = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT SUM(subtotal) AS total FROM cart_details WHERE cart_id = $cart_id
"));

$total_price = floatval($totals['total']);
$shipping_fee = ($total_price >= 500000) ? 0 : 30000;
$final_amount = $total_price + $shipping_fee;

// ✅ Cập nhật bảng carts
mysqli_query($conn, "
  UPDATE carts 
  SET total_price = $total_price, shipping_fee = $shipping_fee, final_amount = $final_amount
  WHERE cart_id = $cart_id
");

// ✅ Hoàn tất
echo "<script>alert('✅ Đã thêm sách vào giỏ hàng!'); window.location.href='cart.php';</script>";
?>