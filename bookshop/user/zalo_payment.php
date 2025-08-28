<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Nhận dữ liệu từ form POST
$ma_donhang = isset($_POST['ma_donhang']) ? $_POST['ma_donhang'] : rand(1000, 9999);
$amount     = isset($_POST['so_tien']) ? (int)$_POST['so_tien'] : 0;
$description = isset($_POST['noi_dung']) ? $_POST['noi_dung'] : "Thanh toán đơn hàng #" . $ma_donhang;

// ==== CẤU HÌNH SANDBOX ZALOPAY ====
$endpoint = "https://sb-openapi.zalopay.vn/v2/create";
$config = [
    'app_id' => '2553',
    'key1'   => 'PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL',
    'key2'   => 'kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz'
];

// ==== DỮ LIỆU ĐƠN HÀNG ====
$app_trans_id = date("ymd") . "_" . rand(100000, 999999); // YYMMDD_xxxxxx
$app_user     = "user123";
$app_time     = round(microtime(true) * 1000); // milliseconds

$item = json_encode([
    ["itemid" => "sp001", "itemname" => "Sản phẩm A", "itemprice" => 50000, "itemquantity" => 1]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$embed_data = json_encode([
    "redirecturl" => "http://localhost/bookshop/user/orderdetails.php?id=$ma_donhang"
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// ==== CHUỖI KÝ ====
// app_id|app_trans_id|app_user|amount|app_time|embed_data|item
$data = $config["app_id"] . "|"
      . $app_trans_id . "|"
      . $app_user . "|"
      . $amount . "|"
      . $app_time . "|"
      . $embed_data . "|"
      . $item;

// ==== TẠO MAC ====
$mac = hash_hmac("sha256", $data, $config["key1"]);

// ==== TẠO REQUEST ====
$params = [
    "app_id"        => $config["app_id"],
    "app_trans_id"  => $app_trans_id,
    "app_user"      => $app_user,
    "app_time"      => $app_time,
    "amount"        => $amount,
    "embed_data"    => $embed_data,
    "item"          => $item,
    "description"   => $description,
    "mac"           => $mac,
    "callback_url"  => "https://your-public-domain.com/zalo_callback.php"
];

// ==== GỬI REQUEST ====
$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_POSTFIELDS     => http_build_query($params)
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
    exit;
}
curl_close($ch);

// ==== XỬ LÝ KẾT QUẢ ====
$result = json_decode($response, true);

if (isset($result['return_code']) && $result['return_code'] == 1) {
    echo "<h2 style='text-align: center;'>Quét mã QR để thanh toán</h2>";

    // Nhận lại thông tin đơn hàng từ POST
    $ma_donhang = isset($_POST['ma_donhang']) ? htmlspecialchars($_POST['ma_donhang']) : 'Không xác định';
    $so_tien    = isset($_POST['so_tien']) ? number_format((int)$_POST['so_tien'], 0, ',', '.') . ' VND' : '0 VND';
    $noi_dung   = isset($_POST['noi_dung']) ? htmlspecialchars($_POST['noi_dung']) : 'Không có nội dung';

    $qrData = urlencode($result['qr_code']);
    $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$qrData}";

    // Căn giữa nội dung bằng CSS inline
    echo "<div style='text-align: center; margin-top: 30px;'>
            <p><strong>Mã đơn hàng:</strong> {$ma_donhang}</p>
            <p><strong>Số tiền:</strong> {$so_tien}</p>
            <p><strong>Nội dung:</strong> {$noi_dung}</p>
            <img src='{$qrImageUrl}' alt='QR Code'><br><br>
            <a href='" . htmlspecialchars($result['order_url']) . "' target='_blank'
               style='display: inline-block; padding: 10px 20px; background: #0068ff; color: white; text-decoration: none; border-radius: 6px;'>
               Thanh toán ngay
            </a>
          </div>";
} else {
    echo "Lỗi: " . htmlspecialchars($response);
}