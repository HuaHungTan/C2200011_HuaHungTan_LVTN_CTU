<?php
session_start();
require('../database/conn.php');
require('../lib/mail_sender.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$order_id = intval($_POST['order_id'] ?? 0);

// ✅ Kiểm tra đơn hàng
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE order_id = $order_id AND user_id = $user_id AND is_deleted = 0
"));

if (!$order) {
  echo "<script>alert('❌ Không tìm thấy đơn hàng để xử lý.'); window.location.href='userprofile.php#order';</script>";
  exit;
}

// ✅ Cập nhật trạng thái và phương thức thanh toán
mysqli_query($conn, "
  UPDATE orders 
  SET status = 'Đã thanh toán', 
      paid_at = NOW(), 
      payment_method = 'Online'
  WHERE order_id = $order_id AND user_id = $user_id
");

// ✅ Lấy thông tin người dùng
$user_info = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT name, email FROM users WHERE user_id = $user_id
"));

// ✅ Lấy chi tiết đơn hàng
$order_items = mysqli_query($conn, "
  SELECT od.quantity, od.price_out, b.name 
  FROM order_details od 
  JOIN books b ON od.book_id = b.book_id 
  WHERE od.order_id = $order_id
");

$items_text = '';
while ($item = mysqli_fetch_assoc($order_items)) {
  $items_text .= "- {$item['name']} ({$item['quantity']} x " . number_format($item['price_out']) . "₫)<br>";
}

$order_date = date('Y-m-d', strtotime($order['order_date']));
$shipping_address = $order['shipping_address'] ?? '(Không có)';
$final_amount = number_format($order['final_amount']);
$shipping_fee = number_format($order['shipping_fee']);

// ✅ Soạn nội dung email
$mail_content = "
  <h3>Chào {$user_info['name']},</h3>
  <p>✅ Bạn đã thanh toán thành công đơn hàng <strong>#$order_id</strong> bằng hình thức <strong>Online</strong>.</p>
  <p><strong>Ngày đặt:</strong> $order_date</p>
  <p><strong>Địa chỉ giao hàng:</strong> $shipping_address</p>
  <p><strong>Phí vận chuyển:</strong> {$shipping_fee}₫</p>
  <p><strong>Chi tiết đơn hàng:</strong><br>$items_text</p>
  <p><strong>Tổng thanh toán:</strong> {$final_amount}₫</p>
  <p>Cảm ơn bạn đã tin tưởng Yêu Sách ❤️</p>
";

// ✅ Gửi mail
sendEmail($user_info['email'], "Xác nhận thanh toán đơn hàng #$order_id", $mail_content);



// ✅ Chuyển hướng kèm thông báo
echo "<script>
  alert('✅ Đã xác nhận thanh toán đơn hàng #$order_id bằng hình thức Online!');
  window.location.href='orderdetails.php?id=$order_id';
</script>";
?>