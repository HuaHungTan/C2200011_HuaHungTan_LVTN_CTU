<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$order_id = intval($_GET['id'] ?? 0);

// ✅ Xử lý gửi đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
  $book_id = intval($_POST['book_id']);
  $rating = intval($_POST['rating']);
  $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));

  $exists = mysqli_query($conn, "
    SELECT * FROM review 
    WHERE user_id = $user_id AND book_id = $book_id AND order_id = $order_id
  ");

  if (mysqli_num_rows($exists) === 0) {
    mysqli_query($conn, "
      INSERT INTO review (user_id, book_id, order_id, rating, comment) 
      VALUES ($user_id, $book_id, $order_id, $rating, '$comment')
    ");
    echo "<script>alert('✅ Bạn đã đánh giá sách thành công!');</script>";
  } else {
    echo "<script>alert('⚠️ Bạn đã đánh giá sách này trong đơn hàng này rồi.');</script>";
  }
}

// ✅ Lấy thông tin đơn hàng
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE order_id = $order_id AND user_id = $user_id AND is_deleted = 0
"));

if (!$order) {
  echo "<script>alert('❌ Không tìm thấy đơn hàng.'); window.location.href='userprofile.php#order';</script>";
  exit;
}

if ($order['status'] !== 'Hoàn thành') {
  header("Location: orderdetails.php?id=$order_id");
  exit;
}

// ✅ Lấy danh sách sách trong đơn
$items = mysqli_query($conn, "
  SELECT od.*, b.book_id, b.name AS book_name, bi.img_url 
  FROM order_details od 
  JOIN books b ON od.book_id = b.book_id 
  LEFT JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1 
  WHERE od.order_id = $order_id
");

include("includes/header.php");
?>

<div class="container my-5">
  <h4 class="text-success mb-4">📦 Đơn hàng đã hoàn thành #<?= $order['order_id'] ?></h4>

  <div class="mb-3">
    <p><strong>📅 Ngày đặt:</strong> <?= $order['order_date'] ?></p>
    <p><strong>📅 Ngày hoàn thành:</strong> <?= $order['updated_date'] ?></p>
    <p><strong>📍 Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
    <p><strong>💳 Thanh toán:</strong> <?= $order['payment_method'] ?></p>
    <p><strong>✅ Trạng thái:</strong> <?= $order['status'] ?></p>
  </div>

  <table class="table table-bordered align-middle">
    <thead class="table-secondary">
      <tr>
        <th>Ảnh</th>
        <th>Tên sách</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Thành tiền</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($item = mysqli_fetch_assoc($items)) { 
        $book_id = $item['book_id'];
        $reviewed = mysqli_fetch_assoc(mysqli_query($conn, "
          SELECT * FROM review 
          WHERE user_id = $user_id AND book_id = $book_id AND order_id = $order_id
        "));
      ?>
        <tr>
          <td style="width:100px">
            <img src="../<?= $item['img_url'] ?: 'data_image/book/default.jpg' ?>" 
                 class="img-fluid rounded" style="height:80px; object-fit:cover;">
          </td>
          <td><?= $item['book_name'] ?></td>
          <td class="text-danger"><?= number_format($item['price_out']) ?>₫</td>
          <td><?= $item['quantity'] ?></td>
          <td><?= number_format($item['subtotal']) ?>₫</td>
        </tr>
        <tr>
          <td colspan="5">
            <?php if (!$reviewed) { ?>
              <form method="post" class="d-flex gap-3 flex-wrap mt-2">
                <input type="hidden" name="book_id" value="<?= $book_id ?>">
                <label class="form-label fw-bold mb-0">⭐ Đánh giá:</label>

                <select name="rating" class="form-select w-auto" required>
                  <option value="" disabled selected>-- chọn sao --</option>
                  <?php for ($i = 1; $i <= 5; $i++) echo "<option value='$i'>$i ⭐</option>"; ?>
                </select>
                <input type="text" name="comment" class="form-control w-50" placeholder="Nhận xét của bạn..." required>
                <button type="submit" class="btn btn-primary">Gửi</button>
              </form>
            <?php } else { ?>
              <div class="border rounded p-2 bg-light">
                <strong>🗨️ Bạn đã đánh giá:</strong> <?= $reviewed['rating'] ?> ⭐ — <?= htmlspecialchars($reviewed['comment']) ?>
              </div>
            <?php } ?>
          </td>
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
    </tbody>
  </table>

  <div class="text-end mt-4">
    <a href="userprofile.php#order" class="btn btn-secondary">↩️ Quay lại hồ sơ</a>
  </div>
</div>

<?php include("includes/footer.php"); ?>