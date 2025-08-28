<?php
require_once('../database/conn.php');

// Thông tin cấu hình MoMo
$accessKey = "F8BBA842ECF85";
$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";

// Nhận dữ liệu từ MoMo
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra chữ ký
$rawSignature = "accessKey=" . $accessKey .
                "&amount=" . $data['amount'] .
                "&extraData=" . $data['extraData'] .
                "&message=" . $data['message'] .
                "&orderId=" . $data['orderId'] .
                "&orderInfo=" . $data['orderInfo'] .
                "&orderType=" . $data['orderType'] .
                "&partnerCode=" . $data['partnerCode'] .
                "&payType=" . $data['payType'] .
                "&requestId=" . $data['requestId'] .
                "&responseTime=" . $data['responseTime'] .
                "&resultCode=" . $data['resultCode'] .
                "&transId=" . $data['transId'];

$signature = hash_hmac("sha256", $rawSignature, $secretKey);

// So sánh chữ ký
if ($signature !== $data['signature']) {
    http_response_code(400);
    echo "Sai chữ ký!";
    exit;
}

// Nếu thanh toán thành công
if ($data['resultCode'] == 0) {
    $orderId = $data['orderId']; // Ví dụ: 22_1754640375
    $amount = $data['amount'];
    $paidAt = date('Y-m-d H:i:s');

    // Tách mã đơn hàng gốc nếu có thêm timestamp
    $ma_donhang = explode('_', $orderId)[0];

    // Cập nhật đơn hàng trong CSDL
    $stmt = $conn->prepare("UPDATE orders SET status='Đã thanh toán', payment_method='Online', paid_at=? WHERE order_id=?");
    $stmt->bind_param("iss", $amount, $paidAt, $ma_donhang);
    $stmt->execute();

    // Ghi log
    file_put_contents("ipn_log.txt", date('Y-m-d H:i:s') . " - Đơn hàng $ma_donhang đã thanh toán $amount\n", FILE_APPEND);

    echo "OK";
} else {
    http_response_code(200);
    echo "Thanh toán thất bại hoặc bị hủy.";
}
?>