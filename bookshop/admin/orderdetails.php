<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if ($order_id <= 0) {
  echo "<p class='text-danger text-center mt-4'>Không tìm thấy đơn hàng!</p>";
  exit;
}

// Lấy thông tin đơn hàng + người dùng
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT o.*, u.name, u.email, u.phone 
  FROM orders o
  JOIN users u ON o.user_id = u.user_id
  WHERE o.order_id = $order_id
"));

if (!$order) {
  echo "<p class='text-danger text-center mt-4'>Đơn hàng không tồn tại!</p>";
  exit;
}

// Lấy chi tiết đơn hàng
$details = mysqli_query($conn, "
  SELECT od.*, b.name AS book_name 
  FROM order_details od
  JOIN books b ON od.book_id = b.book_id
  WHERE od.order_id = $order_id
");
?>

<?php require('includes/header.php'); ?>

<div class="container my-5">
  <h3 class="mb-4"><a href="listdonhang.php">← Tất cả đơn hàng</a> > Đơn hàng #<?= $order_id ?></h3>

  <div class="card p-4 mb-4">
    <h5 class="mb-3 text-primary fw-bold">📦 Thông tin đơn hàng</h5>
    <p><strong>Người đặt:</strong> <?= htmlspecialchars($order['name']) ?> (<?= $order['email'] ?> | <?= $order['phone'] ?>)</p>
    <p><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
    <p><strong>Phương thức thanh toán:</strong> <?= $order['payment_method'] ?></p>
    <p><strong>Ngày đặt:</strong> <?= $order['order_date'] ?></p>
    <p><strong>Giá trị sản phẩm:</strong> <?= number_format($order['total_price'])?>₫</p>
    <p><strong>Phí giao hàng:</strong> <?= number_format($order['shipping_fee']) ?> ₫</p>
    <p><strong>Tổng đơn hàng:</strong> <?= number_format($order['final_amount']) ?> ₫</p>
    <p><strong>Trạng thái:</strong> <?= $order['status'] ?></p>
  </div>

  <div class="card p-4">
    <h5 class="mb-3 text-success fw-bold">📚 Danh sách sản phẩm</h5>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Tên sách</th>
          <th class="text-center">Số lượng</th>
          <th class="text-end">Đơn giá</th>
          <th class="text-end">Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        <?php while($item = mysqli_fetch_assoc($details)): ?>
        <tr>
          <td><?= htmlspecialchars($item['book_name']) ?></td>
          <td class="text-center"><?= $item['quantity'] ?></td>
          <td class="text-end"><?= number_format($item['price_out']) ?> ₫</td>
          <td class="text-end"><?= number_format($item['subtotal']) ?> ₫</td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require('includes/footer.php'); ?>