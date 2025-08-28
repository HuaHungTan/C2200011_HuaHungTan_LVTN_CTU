<?php
session_start();
require('../database/conn.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id AND is_deleted = 0");
$user = mysqli_fetch_assoc($user_query);

// Đơn hàng đang xử lý
$orders_pending = mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE user_id = $user_id AND status != 'Hoàn thành' AND is_deleted = 0 
  ORDER BY order_date DESC
");

// Đơn hàng đã hoàn thành
$orders_done = mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE user_id = $user_id AND status = 'Hoàn thành' AND is_deleted = 0 
  ORDER BY order_date DESC
");

// Sách yêu thích
$favorites = mysqli_query($conn, "
  SELECT b.book_id, b.name, bi.img_url
  FROM favorites f
  JOIN books b ON f.book_id = b.book_id
  JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
  WHERE f.user_id = $user_id
");
?>

<?php include("includes/header.php"); ?>

<div class="container my-5">

  <!-- 👤 Thông tin cá nhân -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold text-primary">👤 Thông tin cá nhân</div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-3 text-center">
          <img src="../<?= $user['avt'] ?>" class="img-thumbnail rounded-circle" style="width:150px; height:150px; object-fit:cover;">
        </div>
        <div class="col-md-9">
          <p><strong>Họ tên:</strong> <?= $user['name'] ?></p>
          <p><strong>Email:</strong> <?= $user['email'] ?></p>
          <p><strong>Điện thoại:</strong> <?= $user['phone'] ?></p>
          <p><strong>Địa chỉ:</strong> <?= $user['address'] ?></p>
          <a href="edit_profile.php" class="btn btn-sm btn-outline-primary">✏️ Chỉnh sửa</a>
          <a href="change_password.php" class="btn btn-sm btn-outline-danger ms-2">🔑 Đổi mật khẩu</a>

        </div>
      </div>
    </div>
  </div>

  <!-- 📦 Đơn hàng hiện tại -->
  <div class="card mb-4 shadow-sm" id="order">
    <div class="card-header fw-bold text-warning">📦 Đơn hàng đang xử lý</div>
    <div class="card-body">
      <?php if (mysqli_num_rows($orders_pending) > 0): ?>
        <ul class="list-group">
          <?php while ($order = mysqli_fetch_assoc($orders_pending)) { ?>
            <li class="list-group-item">
              <strong>Mã đơn:</strong> <?= $order['order_id'] ?> |
              <strong>Ngày:</strong> <?= $order['order_date'] ?> |
              <strong>Trạng thái:</strong> <?= $order['status'] ?> |
              <a href="orderdetails.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
            </li>
          <?php } ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">Bạn chưa có đơn hàng nào đang xử lý.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- 🛍️ Lịch sử mua hàng -->
  <div class="card mb-4 shadow-sm" id="history">
    <div class="card-header fw-bold text-success">🛍️ Lịch sử mua hàng</div>
    <div class="card-body">
      <?php if (mysqli_num_rows($orders_done) > 0): ?>
        <ul class="list-group">
          <?php while ($order = mysqli_fetch_assoc($orders_done)) { ?>
            <li class="list-group-item">
              ✅ Mã đơn: <?= $order['order_id'] ?> |
              Ngày: <?= $order['order_date'] ?> |
              Tổng: <?= number_format($order['final_amount']) ?>₫ |
              <a href="history.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-secondary">Xem chi tiết</a>
            </li>
          <?php } ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">Bạn chưa có đơn nào đã hoàn thành.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- ❤️ Sách yêu thích -->
  <div class="card mb-4 shadow-sm" id="favorites">
    <div class="card-header fw-bold text-danger">❤️ Sách yêu thích</div>
    <div class="card-body">
      <div class="row g-3">
        <?php if (mysqli_num_rows($favorites) > 0): ?>
          <?php while ($book = mysqli_fetch_assoc($favorites)) { ?>
            <div class="col-md-3">
              <div class="card h-100 text-center">
                <img src="../<?= $book['img_url'] ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                <div class="card-body">
                  <h6 class="card-title"><?= $book['name'] ?></h6>
                  <div class="d-flex justify-content-center gap-2 mt-2">
                    <a href="productdetails.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-danger">Xem sách</a>
                    <form action="remove_favorite.php" method="post" class="">
                        <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100">❌ Bỏ yêu thích</button>
                    </form>
                  </div>  
                </div>
              </div>
            </div>
          <?php } ?>
        <?php else: ?>
          <p class="text-muted mt-3 text-center">Bạn chưa yêu thích sách nào.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<?php include("includes/footer.php"); ?>