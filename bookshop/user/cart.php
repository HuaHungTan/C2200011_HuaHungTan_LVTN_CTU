<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);

$shipping_address = $_SESSION['shipping_address'] ?? '';
if (!$shipping_address) {
  $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT address FROM users WHERE user_id = $user_id"));
  $shipping_address = $user['address'] ?? '';
}

// Truy vấn giỏ hàng
$cart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM carts WHERE user_id = $user_id"));
$cart_id = $cart['cart_id'] ?? 0;
$items = [];

if ($cart_id) {
  $result = mysqli_query($conn, "
    SELECT cd.*, b.name AS book_name, b.price_out, b.price_discount,
           bi.img_url
    FROM cart_details cd
    JOIN books b ON cd.book_id = b.book_id
    LEFT JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
    WHERE cd.cart_id = $cart_id
  ");
  while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
  }
}

include("includes/header.php");
?>

<div class="container my-5">
  <h4 class="text-primary mb-4">🛒 Giỏ hàng của bạn</h4>

  <?php if (empty($items)) { ?>
    <p class="text-muted">Giỏ hàng trống.</p>
  <?php } else { ?>
    <table class="table table-bordered align-middle">
      <thead class="table-secondary">
        <tr>
          <th>Ảnh</th>
          <th>Sách</th>
          <th>Giá</th>
          <th>Số lượng</th>
          <th>Tạm tính</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item) { ?>
          <tr>
            <td class="text-center align-middle" style="width:100px">
              <img src="../<?= $item['img_url'] ?: 'data_image/book/default.jpg' ?>" class="img-fluid rounded" style="height:80px; object-fit:cover;">
            </td>
            <td><?= $item['book_name'] ?></td>
            <td class="text-danger"><?= number_format($item['price_discount'] ?: $item['price_out']) ?>₫</td>
            <td>
              <form action="update_cart.php" method="post" class="d-flex">
                <input type="hidden" name="book_id" value="<?= $item['book_id'] ?>">
                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1"
                       class="form-control me-2" style="width:80px;">
                <button class="btn btn-sm btn-outline-success">Cập nhật</button>
              </form>
            </td>
            <td><?= number_format($item['subtotal']) ?>₫</td>
            <td>
              <a href="remove_from_cart.php?book_id=<?= $item['book_id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Xóa khỏi giỏ hàng?')">Xóa</a>
            </td>
          </tr>
        <?php } ?>

        <!-- Tổng cộng -->
        <tr>
          <td colspan="4" class="text-end fw-bold">Tổng tiền hàng</td>
          <td class="text-danger fw-bold"><?= number_format($cart['total_price']) ?>₫</td>
          <td></td>
        </tr>
        <tr>
          <td colspan="4" class="text-end fw-bold">Phí vận chuyển</td>
          <td class="text-success fw-bold"><?= number_format($cart['shipping_fee']) ?>₫</td>
          <td></td>
        </tr>
        <tr>
          <td colspan="4" class="text-end fw-bold text-primary">Tổng thanh toán</td>
          <td class="text-primary fw-bold"><?= number_format($cart['final_amount']) ?>₫</td>
          <td></td>
        </tr>
      </tbody>
    </table>
    <form method="post" action="confirm_address.php" class="mb-4">
      <label class="form-label fw-bold">📍 Địa chỉ giao hàng</label>
      <div class="input-group">
        <input type="text" name="shipping_address" class="form-control"
              value="<?= htmlspecialchars($shipping_address) ?>" required>
        <button type="submit" class="btn btn-outline-primary">✔️ Xác nhận địa chỉ</button>
      </div>
    </form>
    <div class="text-end d-flex justify-content-end gap-2">
      <a href="products.php" class="btn btn-outline-secondary px-4">🛒 Tiếp tục mua hàng</a>
      <a href="place_order.php" class="btn btn-warning px-4">📝 Đặt hàng</a>
    </div>

  <?php } ?>
</div>

<?php include("includes/footer.php"); ?>