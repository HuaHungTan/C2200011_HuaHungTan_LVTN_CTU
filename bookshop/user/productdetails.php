<?php
session_start();
include("includes/header.php"); 
require('../database/conn.php');

// Kiểm tra ID sách
if (!isset($_GET['id'])) {
  echo "<p class='text-danger text-center mt-5'>❌ Không tìm thấy ID sách.</p>";
  include("includes/footer.php");
  exit;
}

$book_id = intval($_GET['id']);

// Truy vấn thông tin sách + ảnh chính
$sql = "
  SELECT b.*, a.name AS author_name, p.name AS publisher_name, bi.img_url AS main_img
  FROM books b
  JOIN authors a ON b.author_id = a.author_id
  JOIN publishers p ON b.publisher_id = p.publisher_id
  JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
  WHERE b.book_id = $book_id AND b.is_deleted = 0
";
$result = mysqli_query($conn, $sql);
$book = mysqli_fetch_assoc($result);

if (!$book) {
  echo "<p class='text-danger text-center mt-5'>❌ Không tìm thấy sách.</p>";
  include("includes/footer.php");
  exit;
}

// Ảnh phụ
$images = mysqli_query($conn, "SELECT img_url FROM book_images WHERE book_id = $book_id ORDER BY is_primary DESC");

// Đánh giá người dùng
$reviews = mysqli_query($conn, "
  SELECT u.email, r.rating, r.comment, r.created_at
  FROM review r
  JOIN users u ON r.user_id = u.user_id
  WHERE r.book_id = $book_id AND r.status = 'Đã duyệt'
  ORDER BY r.created_at DESC
");
?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const quantityInput = document.getElementById("quantity");
    const max = <?= $book['quantity'] ?>;

    quantityInput.addEventListener("blur", function () {
      let val = parseInt(this.value);
      if (isNaN(val) || val < 1) {
        this.value = 1;
      } else if (val > max) {
        this.value = max;
      }
    });
  });
</script>
<div class="container my-5">
  <div class="row">
    <!-- Ảnh -->
    <div class="col-md-5">
      <img id="mainImage" src="../<?= $book['main_img'] ?>" alt="<?= $book['name'] ?>"
           style="width:100%; height:600px; object-fit:cover; border-radius:6px; border:2px solid #ccc;">
      <div class="d-flex justify-content-center mt-3 flex-wrap gap-2">
        <?php while ($img = mysqli_fetch_assoc($images)) { ?>
          <img src="../<?= $img['img_url'] ?>"
               onclick="document.getElementById('mainImage').src = this.src;"
               style="width:60px; height:60px; object-fit:cover; cursor:pointer; border-radius:4px; border:1px solid #666;">
        <?php } ?>
      </div>
    </div>

    <!-- Thông tin sách -->
    <div class="col-md-7">
      <h3 class="fw-bold text-primary"><?= $book['name'] ?></h3>
      <p><strong>Tác giả:</strong> <?= $book['author_name'] ?></p>
      <p><strong>Nhà xuất bản:</strong> <?= $book['publisher_name'] ?></p>
      <p><strong>Đánh giá:</strong> ⭐ <?= $book['rating'] ?>/5.0</p>

      <!-- ✅ Giá và giảm giá -->
      <?php
        $price_out = $book['price_out'];
        $price_discount = $book['price_discount'];
        if ($price_discount && $price_discount < $price_out) {
          $discount_percent = round((($price_out - $price_discount) / $price_out) * 100);
      ?>
        <p class="fs-5 fw-bold text-danger mb-1">
          <?= number_format($price_discount) ?>₫ 
          <span class="badge bg-success  ms-2">-<?= $discount_percent ?>%</span>
        </p>
        <p class="text-muted text-decoration-line-through"><?= number_format($price_out) ?>₫</p>
      <?php } else { ?>
        <p class="fs-5 fw-bold text-danger"><?= number_format($price_out) ?>₫</p>
      <?php } ?>

      <p class="text-muted"><?= nl2br($book['description']) ?></p>

      <p><strong>Số lượng còn lại:</strong> <?= $book['quantity'] ?> cuốn</p>

      <?php if ($book['quantity'] == 0): ?>
        <div class="alert alert-danger mt-2">⚠️ Sách tạm thời hết hàng. Bạn vui lòng quay lại sau!</div>
      <?php else: ?>
        <!-- 🛒 Đặt hàng + ❤️ Yêu thích -->
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
          <div class="mb-3 w-25">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $book['quantity'] ?>" class="form-control">
          </div>

          <div class="row g-2 mt-3">
            <div class="col-md-6">
              <button type="submit" class="btn btn-success w-100">🛒 Thêm vào giỏ hàng</button>
            </div>
        </form>

        <div class="col-md-6">
          <form action="add_to_favorites.php" method="post">
            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
            <button type="submit" class="btn btn-outline-danger w-100">❤️ Yêu thích</button>
          </form>
        </div>
        
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- 💬 Đánh giá -->
<div class="container my-5">
  <h4 class="fw-bold text-secondary mb-3">💬 Đánh giá từ người đọc</h4>
  <?php if (mysqli_num_rows($reviews) > 0): ?>
    <?php while ($r = mysqli_fetch_assoc($reviews)) { ?>
      <div class="border rounded p-3 mb-3 shadow-sm">
        <p><strong><?= $r['email'] ?></strong> đã đánh giá ⭐ <?= $r['rating'] ?>/5</p>
        <p><?= nl2br($r['comment']) ?></p>
        <small class="text-muted">Ngày: <?= $r['created_at'] ?></small>
      </div>
    <?php } ?>
  <?php else: ?>
    <p class="text-muted">Chưa có đánh giá nào cho sách này.</p>
  <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>