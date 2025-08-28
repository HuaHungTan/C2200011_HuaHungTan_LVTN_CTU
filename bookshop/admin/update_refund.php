<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');

$refund_id = intval($_POST['refund_id'] ?? 0);
$new_status = $_POST['new_status'] ?? '';

if ($refund_id <= 0 || $new_status !== 'Đã hoàn') {
  echo "<script>alert('Dữ liệu không hợp lệ!'); window.history.back();</script>";
  exit;
}

$update_sql = "UPDATE refunds SET status='Đã hoàn', updated_at=CURRENT_TIMESTAMP WHERE refund_id=$refund_id";
mysqli_query($conn, $update_sql);

header("Location: listhoantien.php");
exit;
?>