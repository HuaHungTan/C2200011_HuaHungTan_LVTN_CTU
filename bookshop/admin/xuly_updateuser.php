<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$user_id = intval($_POST['user_id']);
$email = $_POST['email'] ?? '';
$name = $_POST['name'] ?? '';
$address = $_POST['address'] ?? '';
$phone = $_POST['phone'] ?? '';

// 🛡️ Kiểm tra role được gửi hay không
$role = $_POST['role'] ?? null;
if (!isset($_POST['role'])) {
  $query = mysqli_query($conn, "SELECT role FROM users WHERE user_id = $user_id");
  if ($old = mysqli_fetch_assoc($query)) {
    $role = $old['role'];
  }
}

// 🖼️ Xử lý upload avatar nếu có
$avt_path = '';
if (isset($_FILES['avt']) && $_FILES['avt']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['avt']['tmp_name'];
  $filename = 'data_image/avatar/' . time() . '_' . basename($_FILES['avt']['name']);
  move_uploaded_file($tmp, '../' . $filename);
  $avt_path = $filename;
}

// 📝 Câu lệnh UPDATE
$sql = "UPDATE users SET 
          email = '$email',
          name = '$name',
          address = '$address',
          phone = '$phone',
          role = '$role'";

if (!empty($avt_path)) {
  $sql .= ", avt = '$avt_path'";
}

$sql .= " WHERE user_id = $user_id";
mysqli_query($conn, $sql);

header("Location: listnguoidung.php");
exit;
?>