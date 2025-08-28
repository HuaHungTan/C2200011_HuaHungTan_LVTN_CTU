<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$order_id = intval($_GET['id'] ?? 0);



// Sau khi cập nhật, lấy lại đơn hàng mới nhất
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE order_id = $order_id AND user_id = $user_id AND is_deleted = 0
"));

// ✅ Lấy đơn hàng hợp lệ
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE order_id = $order_id AND user_id = $user_id AND is_deleted = 0
"));

if (!$order) {
  echo "<script>alert('❌ Không tìm thấy đơn hàng.'); window.location.href='userprofile.php#order';</script>";
  exit;
}

// ✅ Lấy sản phẩm trong đơn hàng
$items = mysqli_query($conn, "
  SELECT od.*, b.name AS book_name, bi.img_url 
  FROM order_details od 
  JOIN books b ON od.book_id = b.book_id 
  LEFT JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1 
  WHERE od.order_id = $order_id
");

// ✅ Lấy thông tin người dùng
$user = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT name, email, phone 
  FROM users 
  WHERE user_id = $user_id
"));

include("includes/header.php");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div class="container my-5">
  <h4 class="text-primary mb-4">🧾 Chi tiết đơn hàng #<?= $order['order_id'] ?></h4>

  <div class="mb-3">
    <p><strong>Ngày đặt:</strong> <?= $order['order_date'] ?></p>
    <p><strong>Trạng thái:</strong> <?= $order['status'] ?></p>
    <p><strong>Phương thức thanh toán:</strong> <?= $order['payment_method'] ?></p>
  </div>

  <table class="table table-bordered align-middle">
    <thead class="table-secondary">
      <tr>
        <th>Ảnh</th>
        <th>Tên sách</th>
        <th>Đơn giá</th>
        <th>Số lượng</th>
        <th>Thành tiền</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($item = mysqli_fetch_assoc($items)) { ?>
        <tr>
          <td class="text-center align-middle" style="width:100px">
            <img src="../<?= $item['img_url'] ?: 'data_image/book/default.jpg' ?>" 
                 onerror="this.src='../data_image/book/default.jpg'" 
                 class="img-fluid rounded" style="height:80px; object-fit:cover;">
          </td>
          <td><?= $item['book_name'] ?></td>
          <td class="text-danger"><?= number_format($item['price_out']) ?>₫</td>
          <td><?= $item['quantity'] ?></td>
          <td><?= number_format($item['subtotal']) ?>₫</td>
        </tr>
      <?php } ?>
      <tr>
        <td colspan="4" class="text-end fw-bold">Tổng tiền hàng</td>
        <td class="text-danger fw-bold"><?= number_format($order['total_price']) ?>₫</td>
      </tr>
      <tr>
        <td colspan="4" class="text-end fw-bold">Phí vận chuyển</td>
        <td class="text-success fw-bold"><?= number_format($order['shipping_fee']) ?>₫</td>
      </tr>
      <tr>
        <td colspan="4" class="text-end fw-bold text-primary">Tổng thanh toán</td>
        <td class="text-primary fw-bold"><?= number_format($order['final_amount']) ?>₫</td>
      </tr>
      <?php if ($order['status'] === 'Đã thanh toán') { ?>
        <tr>
          <td colspan="5" class="text-center text-success fw-bold">✅ Đơn hàng đã được thanh toán vào <?= $order['paid_at'] ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <div class="border rounded p-3 bg-light mb-3">
    <p><strong>📍 Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
  </div>
  <!-- thanh toán -->

  <?php if (in_array($order['status'], ['Chờ duyệt', 'Đang giao'])) { ?>
    <div class="text-end mb-3">
        <!-- ✅ Nút chính -->
        <button class="btn btn-success px-4 fw-bold" onclick="togglePaymentOptions()">💸 Thanh toán</button>

        <!-- ✅ Khối lựa chọn thanh toán -->
        <div id="payment-options" style="display: none; margin-top: 15px;">
            <div class="d-flex justify-content-end gap-3">
                <!-- MoMo -->
                <form method="post" action="payment.php">
                    <input type="hidden" name="ma_donhang" value="<?= $order_id ?>">
                    <input type="hidden" name="so_tien" value="<?= $order['final_amount'] ?>">
                    <input type="hidden" name="noi_dung" value="Thanh toan don hang <?= $order_id ?>">
                    <button type="submit" style="border: none; background: none;">
                        <img src="../assets/MoMo_Logo.png" alt="MoMo" style="height: 50px;">
                    </button>
                </form>

                <!-- ZaloPay -->
                <form method="post" action="payment2.php">
                    <input type="hidden" name="ma_donhang" value="<?= $order_id ?>">
                    <input type="hidden" name="so_tien" value="<?= $order['final_amount'] ?>">
                    <input type="hidden" name="noi_dung" value="Thanh toan don hang <?= $order_id ?>">
                    <button type="submit" style="border: none; background: none;">
                        <img src="../assets/Zalopay_Logo.png" alt="ZaloPay" style="height: 50px;">
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ✅ Script hiệu ứng -->
    <script>
        function togglePaymentOptions() {
            const options = document.getElementById('payment-options');
            if (options.style.display === 'none' || options.style.display === '') {
                options.style.display = 'block';
            } else {
                options.style.display = 'none';
            }
        }
    </script>
<?php } ?>

  <!-- ✅ Nút hủy đơn -->
  <?php if (in_array($order['status'], ['Chờ duyệt', 'Đang giao','Đã thanh toán'])) { ?>
    <div class="text-end mb-3">
      <a href="cancel_order.php?id=<?= $order_id ?>" 
         class="btn btn-danger" 
         onclick="return confirm('❗ Bạn có chắc muốn hủy đơn hàng này?')">
        ❌ Hủy đơn hàng
      </a>
    </div>
  <?php } ?>

  <div class="text-end mt-4">
    <a href="userprofile.php#order" class="btn btn-secondary">↩️ Quay lại hồ sơ</a>
  </div>
</div>

<script>
  function showQR() {
    const qrSection = document.getElementById('qrSection');
    qrSection.style.display = 'flex';
    qrSection.style.justifyContent = 'center';
    qrSection.style.alignItems = 'center'; // nếu cần căn giữa cả chiều dọc
    qrSection.style.flexDirection = 'column'; // tránh layout nằm ngang nếu dùng flex
  }
  function hideQR() {
    const qrSection = document.getElementById('qrSection');
    qrSection.style.display = 'none';
  }
</script>

<?php include("includes/footer.php"); ?>