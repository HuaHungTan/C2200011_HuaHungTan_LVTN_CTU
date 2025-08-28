<?php
session_start();
require('../database/conn.php');
require('../lib/mail_sender.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$date_now = date('Y-m-d');
$payment_method = 'COD'; // Mặc định — có thể mở rộng chọn sau

// lấy địa chỉ người dùng

$shipping_address = $_SESSION['shipping_address'] ?? '';

if (!$shipping_address || trim($shipping_address) === '') {
  $user_id = intval($_SESSION['user_id']);
  $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT address FROM users WHERE user_id = $user_id"));
  $shipping_address = $user['address'] ?? '';
}
//lấy thông tin cho mail
$user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM users WHERE user_id = $user_id"));

// ✅ Lấy giỏ hàng
$cart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM carts WHERE user_id = $user_id"));
if (!$cart) {
  echo "<script>alert('❌ Không tìm thấy giỏ hàng.'); window.location.href='cart.php';</script>";
  exit;
}

$cart_id = $cart['cart_id'];
$total_price = floatval($cart['total_price']);
$shipping_fee = floatval($cart['shipping_fee']);
$final_amount = floatval($cart['final_amount']);

// ✅ Lấy danh sách sách trong giỏ
$items = mysqli_query($conn, "
  SELECT cd.*, b.quantity AS stock ,b.name
  FROM cart_details cd 
  JOIN books b ON cd.book_id = b.book_id 
  WHERE cart_id = $cart_id
");

if (mysqli_num_rows($items) === 0) {
  echo "<script>alert('❌ Giỏ hàng trống.'); window.location.href='cart.php';</script>";
  exit;
}

// ✅ Tạo đơn hàng
mysqli_query($conn, "
  INSERT INTO orders (
    user_id, shipping_address, total_price, shipping_fee, final_amount, payment_method,
    status, order_date, updated_date, is_deleted
  ) VALUES (
    $user_id, '$shipping_address', $total_price, $shipping_fee, $final_amount, '$payment_method',
    'Chờ duyệt', '$date_now', '$date_now', 0
  )
") or die(mysqli_error($conn));
$order_id = mysqli_insert_id($conn);

// ✅ Thêm chi tiết đơn + giảm kho
while ($item = mysqli_fetch_assoc($items)) {
  $book_id = $item['book_id'];
  $qty = intval($item['quantity']);
  $price = floatval($item['price_out']);
  $subtotal = floatval($item['subtotal']);

  // Thêm vào `order_details`
  mysqli_query($conn, "
    INSERT INTO order_details (order_id, book_id, quantity, price_out, subtotal)
    VALUES ($order_id, $book_id, $qty, $price, $subtotal)
  ");

  // Giảm số lượng sách
  mysqli_query($conn, "
    UPDATE books SET quantity = quantity - $qty WHERE book_id = $book_id
  ");
}

// ✅ Dọn giỏ
mysqli_query($conn, "DELETE FROM cart_details WHERE cart_id = $cart_id");
mysqli_query($conn, "
  UPDATE carts 
  SET total_price = 0, shipping_fee = 0, final_amount = 0
  WHERE cart_id = $cart_id
");
//gửi mail cho khách hàng
$items_text = '';
mysqli_data_seek($items, 0);
while ($item = mysqli_fetch_assoc($items)) {
  $items_text .= "- {$item['name']} ({$item['quantity']} x " . number_format($item['price_out']) . "₫)<br>";
}

$mail_content = "
  <h3>Chào {$user_info['name']},</h3>
  <p>✅ Bạn đã đặt hàng thành công (#$order_id)</p>
  <p><strong>Ngày:</strong> $date_now</p>
  <p><strong>Địa chỉ giao hàng:</strong> $shipping_address</p>
  <p><strong>Tổng tiền:</strong> " . number_format($final_amount) . "₫</p>
  <p><strong>Chi tiết đơn hàng:</strong><br>$items_text</p>
  <p><strong>Phí giao hàng:</strong> " . number_format($shipping_fee) . "₫</p>
  <p>Cảm ơn bạn đã chọn Yêu Sách ❤️</p>
";

sendEmail($user_info['email'], "Xác nhận đơn hàng #$order_id", $mail_content);

// ✅ Trả về hồ sơ cá nhân
echo "<script>alert('✅ Đặt hàng thành công!'); window.location.href='userprofile.php#order';</script>";
?>