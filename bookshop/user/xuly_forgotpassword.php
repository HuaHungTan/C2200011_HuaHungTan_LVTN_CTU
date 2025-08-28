<?php
require('../database/conn.php');
require('../lib/mail_sender.php'); // file bạn đã viết ở lib

$email = trim($_POST['email'] ?? '');

// 🔎 Kiểm tra email
$user = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM users WHERE email = '$email' AND is_deleted = 0
"));
if (!$user) {
  echo "<script>alert('✅ Nếu email hợp lệ, bạn sẽ nhận được liên kết đặt lại mật khẩu.'); window.location.href='login.php';</script>";
  exit;
}

// 🔐 Sinh token + thời gian
date_default_timezone_set('Asia/Ho_Chi_Minh');
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', time() + 900); // 15 phút

// 📝 Lưu vào CSDL
mysqli_query($conn, "
  UPDATE users SET reset_token = '$token', reset_token_expiry = '$expiry'
  WHERE user_id = {$user['user_id']}
");

// ✉️ Gửi email
//$link = "http://localhost/bookshop/user/resetpassword.php?token=$token";/
$host = $_SERVER['HTTP_HOST'];       // localhost:9999
$path = dirname($_SERVER['PHP_SELF']);
$link = "http://$host$path/resetpassword.php?token=$token";
$body = "<p>Nhấn vào liên kết sau để đặt lại mật khẩu (hết hạn sau 15 phút):</p>
         <a href='$link'>$link</a>";

sendEmail($email, '🔐 Đặt lại mật khẩu tài khoản trên Yêu Sách', $body);

echo "<script>alert('✅ Liên kết đặt lại mật khẩu đã được gửi!'); window.location.href='login.php';</script>";