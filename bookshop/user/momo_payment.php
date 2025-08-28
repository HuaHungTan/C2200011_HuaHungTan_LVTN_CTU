<?php

// Nhận dữ liệu từ form hoặc tự tạo
$ma_donhang = $_POST['ma_donhang'] ?? time();
$so_tien = $_POST['so_tien'] ?? 10000; // Mặc định 10,000đ nếu không có
$noi_dung = $_POST['noi_dung'] ?? 'Thanh toan don hang';

$status = 'Đã thanh toán';
$payment_method = 'Online';

// Encode các tham số để đưa vào URL
$status_encoded = urlencode($status);
$payment_encoded = urlencode($payment_method);
$paid_at = urlencode(date('Y-m-d H:i:s')); // thời gian hiện tại



// Thông tin cấu hình MoMo test
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$accessKey = "F8BBA842ECF85";
$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";
$partnerCode = "MOMO";
$orderId = $ma_donhang . '_' . time(); // Ví dụ: 22_1754640375
$requestId = time() . "";
$orderInfo = $noi_dung;
$amount = $so_tien;

// Thay bằng domain thật của bạn
// $redirectUrl = "http://localhost/bookshop/user/orderdetails.php?id=$ma_donhang&status=$status_encoded&payment_method=$payment_encoded&paid_at=$paid_at";
$redirectUrl = "http://localhost/bookshop/user/orderdetails.php?id=$ma_donhang";
$ipnUrl = "https://yourdomain.com/ipn.php";         // URL để xử lý kết quả IPN
$extraData = "";
$orderGroupId = "";
$autoCapture = true;
$lang = 'vi';
$requestType = "payWithMethod";

// Tạo chuỗi chữ ký
$rawSignature = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
$signature = hash_hmac("sha256", $rawSignature, $secretKey);

// Tạo mảng dữ liệu gửi đi
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "MoMo QR Test",
    'storeId' => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => $lang,
    'requestType' => $requestType,
    'autoCapture' => $autoCapture,
    'extraData' => $extraData,
    'orderGroupId' => $orderGroupId,
    'signature' => $signature
);

// Gửi request tới MoMo
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen(json_encode($data))
));

$response = curl_exec($ch);
curl_close($ch);

// Giải mã JSON
$result = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán bằng Mã QR MoMo</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }

        h2 {
            color: #6f1d1b;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }

        .qr-img {
            margin: 20px 0;
            border: 6px solid #f5f5f5;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .qr-img:hover {
            transform: scale(1.05);
        }

        a {
            color: #fff;
            background: #a4133c;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #800f2f;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        pre {
            text-align: left;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thanh toán đơn hàng: #<?= htmlspecialchars($ma_donhang) ?></h2>
        <p><strong>Số tiền:</strong> <?= number_format($amount) ?> VNĐ</p>
        <p><strong>Nội dung:</strong> <?= htmlspecialchars($orderInfo) ?></p>

        <?php if (isset($result['payUrl'])): ?>
            <p>👉 Quét mã QR bên dưới bằng ứng dụng MoMo:</p>
            <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlencode($result['payUrl']) ?>" alt="QR Code MoMo">
            <br>
            <a href="<?= $result['payUrl'] ?>" target="_blank">Hoặc bấm vào đây để thanh toán</a>
        <?php else: ?>
            <p class="error">❌ Không thể tạo thanh toán. Vui lòng thử lại.</p>
            <pre><?= print_r($result, true) ?></pre>
        <?php endif; ?>
    </div>
</body>
</html>

