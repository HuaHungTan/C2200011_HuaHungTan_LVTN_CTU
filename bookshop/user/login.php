<?php
session_start();
session_destroy();
session_start();
$email = '';
$password = '';
require('../database/conn.php');
if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  if ($user && $password === $user['password'] && $user['is_deleted'] == 0) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['avt'] = $user['avt'];

    // Điều hướng theo vai trò
    if ($user['role'] === 'admin') {
      header("Location: ../admin/index.php");
    } else {
      header("Location: index.php");
    }
    exit;
  }else if ($user && $user['is_deleted'] == 1) {
    echo "<p class='text-warning text-center mt-3'>Tài khoản của bạn đã bị vô hiệu hóa!</p>";
    $email = '';
    $password = '';
  }else {
    echo "<p class='text-danger text-center mt-3'>Sai email hoặc mật khẩu!</p>";
    $email = '';
    $password = '';

  }
}
?>

<?php 
include("includes/header.php"); 
?>
<div class="container mt-5 mb-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow border-0 p-4">
        <h4 class="text-center text-primary mb-4 fw-bold">🔐 Đăng Nhập</h4>
        <form method="post" action="login.php">
          <div class="mb-3">
            <!-- trình duyệt tự kiểm tra định dạng type=email -->
            <input type="email" class="form-control" id="email" name="email"
                value="<?= htmlspecialchars($email) ?>" placeholder="Nhập email..." required>
          </div>
          <div class="mb-3">
            <input type="password" class="form-control" id="password" name="password"
                value="<?= htmlspecialchars($password) ?>" placeholder="Nhập mật khẩu..." required>

          </div>
          <div class="mb-3 text-end">
            <a href="forgot_password.php" class="link-danger small">Quên mật khẩu?</a>
          </div>
          <button type="submit" name="login" class="btn btn-primary w-100 fw-bold">Đăng nhập</button>
        </form>

        <hr class="my-4" style="border-top: 3px solid #ccc;">

        <p class="text-center mb-0">Chưa có tài khoản?
          <a href="register.php" class="link-primary fw-bold">Đăng ký ngay</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php"); ?>