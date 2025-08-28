<?php
session_start();
require('../database/conn.php');
require('../lib/mail_sender.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$order_id = intval($_GET['id'] ?? 0);

// ✅ Lấy đơn hàng & kiểm tra quyền sở hữu
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE order_id = $order_id AND user_id = $user_id AND is_deleted = 0
"));

if (!$order) {
  echo "<script>alert('❌ Không tìm thấy đơn hàng của bạn.'); window.location.href='userprofile.php#order';</script>";
  exit;
}

$old_status = $order['status'];
$amount = $order['final_amount'];
$shipping_address = $order['shipping_address'];
$order_date = date('Y-m-d', strtotime($order['order_date']));
$shipping_fee=floatval($order['shipping_fee']);


// ✅ Lấy danh sách sản phẩm để trả sách về kho
$order_items = mysqli_query($conn, "
  SELECT od.book_id, od.quantity, od.price_out, od.subtotal, b.name 
  FROM order_details od 
  JOIN books b ON od.book_id = b.book_id 
  WHERE od.order_id = $order_id
");

while ($item = mysqli_fetch_assoc($order_items)) {
  $book_id = $item['book_id'];
  $qty = $item['quantity'];
  mysqli_query($conn, "UPDATE books SET quantity = quantity + $qty WHERE book_id = $book_id");
}

// ✅ Nếu đơn đã thanh toán → hoàn tiền vào bảng refunds
if ($old_status === 'Đã thanh toán') {
  $reason = 'Khách hàng hủy đơn';
  $refund_method = 'Chuyển khoản'; // mặc định
  $insertRefund = "
    INSERT INTO refunds (order_id, user_id, amount, refund_method, refund_reason)
    VALUES ($order_id, $user_id, $amount, '$refund_method', '$reason')
  ";
  mysqli_query($conn, $insertRefund);
}

// ✅ Cập nhật trạng thái đơn
mysqli_query($conn, "
  UPDATE orders 
  SET status = 'Đã hủy', updated_date = CURRENT_DATE 
  WHERE order_id = $order_id
");

// ✅ Lấy thông tin người dùng để gửi mail
$user_info = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT name, email FROM users WHERE user_id = $user_id
"));

// ✅ Soạn nội dung email
$items_text = '';
mysqli_data_seek($order_items, 0);
while ($item = mysqli_fetch_assoc($order_items)) {
  $items_text .= "- {$item['name']} ({$item['quantity']} x " . number_format($item['price_out']) . "₫)<br>";
}

$mail_content = "
  <h3>Chào {$user_info['name']},</h3>
  <p>❌ Đơn hàng của bạn (#$order_id) đã được hủy thành công.</p>
  <p><strong>Ngày đặt:</strong> $order_date</p>
  <p><strong>Ngày hủy:</strong> " . date('Y-m-d') . "</p>
  <p><strong>Địa chỉ giao hàng:</strong> $shipping_address</p>
  <p><strong>Phí vận chuyển:</strong> " . number_format($shipping_fee)."₫</p>
  <p><strong>Chi tiết đơn hàng:</strong><br>$items_text</p>
  <p><strong>Tổng tiền:</strong> " . number_format($amount) . "₫</p>
  <p>Nếu đây là nhầm lẫn, bạn có thể đặt lại đơn hàng bất cứ lúc nào.</p>
  <p>Yêu Sách luôn sẵn sàng phục vụ bạn ❤️</p>
";

// ✅ Gửi mail
sendEmail($user_info['email'], "Đơn hàng #$order_id đã được hủy", $mail_content);


// ✅ Thông báo và chuyển hướng
echo "<script>alert('✅ Đơn hàng đã được hủy thành công.'); window.location.href='userprofile.php#order';</script>";
exit;
?>