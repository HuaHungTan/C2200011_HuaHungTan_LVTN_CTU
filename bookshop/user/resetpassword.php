<?php
require('../database/conn.php');

$token = $_GET['token'] ?? '';
$user = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM users WHERE reset_token = '$token' AND is_deleted = 0
"));
if (!$user || strtotime($user['reset_token_expiry']) < time()) {
  echo "<script>alert('❌ Liên kết đã hết hạn hoặc không hợp lệ.'); window.location.href='login.php';</script>";
  exit;
}

// ✅ Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pass = $_POST['password'] ?? '';
  $confirm = $_POST['confirm_password'] ?? '';

  if (strlen($pass) < 6) {
    echo "<script>alert('❌ Mật khẩu phải từ 6 ký tự.');</script>";
  } elseif ($pass !== $confirm) {
    echo "<script>alert('❌ Mật khẩu xác nhận không trùng khớp.');</script>";
  } else {
    // Nếu dùng hash: $pass = password_hash($pass, PASSWORD_DEFAULT);
    mysqli_query($conn, "
      UPDATE users SET password = '$pass', reset_token = NULL, reset_token_expiry = NULL
      WHERE user_id = {$user['user_id']}
    ");
    echo "<script>alert('✅ Mật khẩu đã được cập nhật!'); window.location.href='login.php';</script>";
    exit;
  }
}
?>

<?php include("includes/header.php"); ?>
<div class="container my-5">
  <h4 class="text-primary text-center">🔐 Đặt lại mật khẩu</h4>
  <div class="row justify-content-center">
    <form method="post" class="col-md-6">
      <label class="form-label">Mật khẩu mới:</label>
      <input type="password" name="password" class="form-control mb-3" required>

      <label class="form-label">Xác nhận mật khẩu:</label>
      <input type="password" name="confirm_password" class="form-control mb-3" required>

      <div class="text-end">
        <button type="submit" class="btn btn-success">💾 Lưu mật khẩu</button>
      </div>
    </form>
  </div>
</div>
<?php include("includes/footer.php"); ?>