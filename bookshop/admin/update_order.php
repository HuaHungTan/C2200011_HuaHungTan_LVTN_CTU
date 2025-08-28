<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$new_status = isset($_POST['status']) ? $_POST['status'] : '';
$new_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';


if ($order_id <= 0 || $new_status === '') {
  echo "<script>alert('Thiếu thông tin!'); window.history.back();</script>";
  exit;
}

// Lấy trạng thái cũ của đơn hàng
$old_order = mysqli_query($conn, "SELECT status, user_id, final_amount FROM orders WHERE order_id = $order_id");
$old_data = mysqli_fetch_assoc($old_order);
$old_status = $old_data['status'];
$user_id = $old_data['user_id'];
$amount = $old_data['final_amount']; // dùng cho bảng refunds

// Lấy chi tiết sách trong đơn
$order_details = mysqli_query($conn, "SELECT book_id, quantity FROM order_details WHERE order_id = $order_id");

if ($new_status === 'Đã hủy') {
  // Trả lại sách về kho
  while ($item = mysqli_fetch_assoc($order_details)) {
    $book_id = $item['book_id'];
    $qty = $item['quantity'];
    mysqli_query($conn, "UPDATE books SET quantity = quantity + $qty WHERE book_id = $book_id");
  }

  // Nếu trạng thái cũ là 'Đã thanh toán', thì hoàn tiền
  if ($old_status === 'Đã thanh toán') {
    $reason = 'Admin hủy đơn'; // lý do cố định vì admin thao tác
    $insertRefund = "
      INSERT INTO refunds (order_id, user_id, amount, refund_reason)
      VALUES ($order_id, $user_id, $amount, '$reason')
    ";
    mysqli_query($conn, $insertRefund);
  }
} elseif ($new_status === 'Hoàn thành') {
  // Cộng sách vào số lượng đã bán
  while ($item = mysqli_fetch_assoc($order_details)) {
    $book_id = $item['book_id'];
    $qty = $item['quantity'];
    mysqli_query($conn, "UPDATE books SET sold = sold + $qty WHERE book_id = $book_id");
  }
}

// Cập nhật trạng thái đơn hàng
if ($new_status === 'Đã thanh toán') {
  $update_sql = "
    UPDATE orders 
    SET status = '$new_status', payment_method = '$new_method', updated_date = CURRENT_DATE, paid_at = CURRENT_TIMESTAMP 
    WHERE order_id = $order_id
  ";
} else {
  $update_sql = "
    UPDATE orders 
    SET status = '$new_status', payment_method = '$new_method', updated_date = CURRENT_DATE 
    WHERE order_id = $order_id
  ";
}
mysqli_query($conn, $update_sql);

// Trở về trang danh sách đơn
header("Location: listdonhang.php");
exit;
?>