<?php
session_start();
require('../database/conn.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Xử lý form gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $current_password = $_POST['current_password'] ?? '';
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  // Lấy mật khẩu hiện tại từ DB
  $result = mysqli_query($conn, "SELECT password FROM users WHERE user_id = $user_id AND is_deleted = 0");
  $row = mysqli_fetch_assoc($result);

  if ($current_password !== $row['password']) {
  $errors[] = "❌ Mật khẩu hiện tại không đúng.";
    } elseif (strlen($new_password) < 6) {
    $errors[] = "⚠️ Mật khẩu mới phải có ít nhất 6 ký tự.";
    } elseif ($new_password !== $confirm_password) {
    $errors[] = "⚠️ Mật khẩu xác nhận không khớp.";
    } else {
    mysqli_query($conn, "UPDATE users SET password = '$new_password' WHERE user_id = $user_id");
    echo "<script>
            alert('✅ Mật khẩu đã được thay đổi thành công!');
            window.location.href='userprofile.php?id=" . $_SESSION['user_id'] . "';
            </script>";
            exit;

    }
}
?>

<?php include("includes/header.php"); ?>

<div class="container my-5" style="max-width: 500px;">
  <h3 class="text-primary">🔒 Đổi mật khẩu</h3>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= $err ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>


  <form method="POST">
    <div class="mb-3">
      <label>Mật khẩu hiện tại</label>
      <input type="password" name="current_password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Mật khẩu mới</label>
      <input type="password" name="new_password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Nhập lại mật khẩu mới</label>
      <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">🔁 Đổi mật khẩu</button>
    <a href="userprofile.php?id=<?=$_SESSION['user_id']?>" class="btn btn-secondary ms-2">⬅️ Quay lại</a>
  </form>
</div>

<?php include("includes/footer.php"); ?>