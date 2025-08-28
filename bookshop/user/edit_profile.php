<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);

// 📌 Lấy thông tin người dùng
$user = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM users WHERE user_id = $user_id AND is_deleted = 0
"));

if (!$user) {
  echo "<script>alert('❌ Không tìm thấy người dùng.'); window.location.href='userprofile.php';</script>";
  exit;
}

// ✅ Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name    = trim($_POST['name'] ?? '');
  $email   = trim($_POST['email'] ?? '');
  $phone   = trim($_POST['phone'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $avt_path = $user['avt'];

  // 🔎 Kiểm tra bắt buộc
  if ($name === '' || $email === '') {
    echo "<script>alert('❌ Họ tên và Email không được để trống.'); window.history.back();</script>";
    exit;
  }

  // 📧 Kiểm tra email hợp lệ
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('❌ Email không hợp lệ.'); window.history.back();</script>";
    exit;
  }
  // 📱 Kiểm tra số điện thoại hợp lệ (chỉ số, bắt đầu bằng 0, có 10–11 chữ số)
  if (!preg_match('/^0\d{9}$/', $phone)) {
    echo "<script>alert('❌ Số điện thoại không hợp lệ.'); window.history.back();</script>";
    exit;
  }

  // 🖼️ Xử lý ảnh nếu có
  if (!empty($_FILES['avt']['name'])) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    $size_limit = 1_000_000; // 1MB
    $file_type = mime_content_type($_FILES['avt']['tmp_name']);
    $file_size = $_FILES['avt']['size'];

    if (!in_array($file_type, $allowed)) {
      echo "<script>alert('❌ File ảnh không hợp lệ. Chỉ nhận JPG, PNG, GIF.'); window.history.back();</script>";
      exit;
    }

    if ($file_size > $size_limit) {
      echo "<script>alert('❌ File ảnh quá lớn. Giới hạn là 1MB.'); window.history.back();</script>";
      exit;
    }

    $folder = "../data_image/avatar/";
    $filename = time() . "_" . basename($_FILES['avt']['name']);
    $target = $folder . $filename;

    if (move_uploaded_file($_FILES['avt']['tmp_name'], $target)) {
      if ($avt_path !== 'data_image/avatar/default.jpg' && file_exists("../" . $avt_path)) {
        unlink("../" . $avt_path); // xoá ảnh cũ nếu không mặc định
      }
      $avt_path = "data_image/avatar/" . $filename;
    }
  }

  // ✅ Cập nhật
  mysqli_query($conn, "
    UPDATE users SET
      name = '$name',
      email = '$email',
      phone = '$phone',
      address = '$address',
      avt = '$avt_path'
    WHERE user_id = $user_id
  ");

  echo "<script>alert('✅ Đã cập nhật thông tin!'); window.location.href='userprofile.php';</script>";
  exit;
}
?>

<?php include("includes/header.php"); ?>

<div class="container my-5">
  <h4 class="text-primary mb-4">✏️ Chỉnh sửa thông tin cá nhân</h4>

  <form action="" method="post" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light">
    <div class="row mb-3">
      <div class="col-md-3 text-center">
        <img src="../<?= $user['avt'] ?>" class="img-thumbnail rounded-circle mb-2"
             style="width:150px; height:150px; object-fit:cover;">
        <input type="file" name="avt" class="form-control mt-2">
      </div>
      <div class="col-md-9">
        <div class="mb-3">
          <label class="form-label">Họ tên</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Điện thoại</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Địa chỉ</label>
          <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" class="form-control">
        </div>
      </div>
    </div>

    <div class="text-end">
      <a href="userprofile.php" class="btn btn-secondary">↩️ Quay lại</a>
      <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
    </div>
  </form>
</div>

<?php include("includes/footer.php"); ?>