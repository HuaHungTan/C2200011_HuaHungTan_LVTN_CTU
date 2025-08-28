<?php
session_start();
require('../database/conn.php');

$name = $email = $password = $confirm = $phone = $address = '';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm  = $_POST['confirm'];
  $phone    = trim($_POST['phone']);
  $address  = trim($_POST['address']);

  // Kiểm tra email tồn tại
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $exist = $stmt->get_result()->fetch_assoc();
  
  // Kiểm tra dữ liệu
  

  if ($exist) {
    $error = "📛 Email đã tồn tại. Vui lòng chọn email khác.";
  } elseif (!preg_match("/^(0|\+84)[0-9]{9}$/", $phone)) {
    $error = "📵 Số điện thoại không hợp lệ. Vui lòng nhập đúng định dạng.";
  } elseif ($password !== $confirm) {
    $error = "⚠️ Mật khẩu xác nhận không khớp.";
  } else {
    // ✅ Lưu plain password (chỉ nên dùng khi học)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, avt, phone, address) 
                        VALUES (?, ?, ?, 'customer', 'data_image/avatar/default.jpg', ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $phone, $address);
    $stmt->execute();

    $success = "🎉 Đăng ký thành công! Bạn có thể đăng nhập ngay.";
    $name = $email = $phone = $address = '';
  }
  
}
?>

<?php include("includes/header.php"); ?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow p-4 border-0">
        <h4 class="text-center text-primary mb-4">📝 Đăng ký tài khoản</h4>

        <?php if ($success): ?>
          <div class="alert alert-success text-center"><?= $success ?></div>
        <?php elseif ($error): ?>
          <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="mb-3">
            <label for="name" class="form-label">👤 Họ tên</label>
            <input type="text" name="name" id="name" required class="form-control"
                   value="<?= htmlspecialchars($name) ?>">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">📧 Email</label>
            <input type="email" name="email" id="email" required class="form-control"
                   value="<?= htmlspecialchars($email) ?>">
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">📞 Số điện thoại</label>
            <input type="text" name="phone" id="phone" required class="form-control"
                  value="<?= htmlspecialchars($phone) ?>">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">🏠 Địa chỉ</label>
            <textarea name="address" id="address" rows="2" required class="form-control"><?= htmlspecialchars($address) ?></textarea>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">🔒 Mật khẩu</label>
            <input type="password" name="password" id="password" required class="form-control">
          </div>
          <div class="mb-3">
            <label for="confirm" class="form-label">🔁 Nhập lại mật khẩu</label>
            <input type="password" name="confirm" id="confirm" required class="form-control">
          </div>
          <button type="submit" class="btn btn-success w-100 fw-bold">Đăng ký</button>
        </form>

        <hr class="my-4">
        <p class="text-center">Đã có tài khoản?
          <a href="login.php" class="text-decoration-none fw-bold">👉 Đăng nhập ngay</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php"); ?>