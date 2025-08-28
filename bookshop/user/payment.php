<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL
require('../database/conn.php');
// Lấy mã đơn hàng và số tiền từ POST
$ma_donhang = isset($_POST['ma_donhang']) ? (int)$_POST['ma_donhang'] : rand(1000, 9999);
$so_tien = isset($_POST['so_tien']) ? (int)str_replace(',', '', $_POST['so_tien']) : 0;
// Nội dung thanh toán
$noi_dung = "Thanh toan don hang " . $ma_donhang;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán MoMo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container text-center mt-5">
    <h2 class="mb-4"> Thanh toán bằng ví MoMo </h2>

    <p><strong>Số tiền:</strong> <?= number_format($so_tien, 0, ',', '.') ?> VND</p>
    <p><strong>Nội dung thanh toán:</strong> <?= htmlspecialchars($noi_dung) ?></p>

    <form method="POST" action="momo_payment.php" target="_self">
        <input type="hidden" name="ma_donhang" value="<?= $ma_donhang ?>">
        <input type="hidden" name="so_tien" value="<?= $so_tien ?>">
        <input type="hidden" name="noi_dung" value="<?= $noi_dung ?>">
        <button type="submit" class="btn btn-primary btn-lg">🟣 Thanh toán qua MoMo</button>
    </form>

    <div class="mt-4">
        <a href="orderdetails.php?id=<?= $ma_donhang ?>" class="btn btn-secondary">⬅️ Quay lại</a>
    </div>

    <div class="alert alert-info mt-4">
        ⚠️ Bạn sẽ được chuyển hướng đến trang thanh toán MoMo.
    </div>
</div>
</body>
</html>
