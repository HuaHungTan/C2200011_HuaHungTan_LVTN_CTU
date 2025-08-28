<?php
session_start();
require('../database/conn.php');
require('includes/header.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$book_id = intval($_GET['id'] ?? 0);
$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE book_id = $book_id"));

if (!$book) {
  echo "<script>alert('❌ Không tìm thấy sách.'); window.location.href='listsanpham.php';</script>";
  exit;
}
?>

<div class="container my-5">
  <h4 class="fw-bold text-primary mb-4">📦 Nhập thêm sách: <?= htmlspecialchars($book['name']) ?></h4>
  <form method="post" action="xuly_addoldbook.php" class="row g-3">
    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">

    <div class="col-md-6">
      <label class="form-label">Giá nhập hiện tại</label>
      <input type="text" class="form-control" value="<?= number_format($book['price_in'], 0, ',', '.') ?>₫" readonly>
    </div>
    <div class="col-md-6">
      <label class="form-label">Số lượng hiện tại</label>
      <input type="text" class="form-control" value="<?= $book['quantity'] ?>" readonly>
    </div>
    <div class="col-md-6 mt-2">
      <label class="form-label">Giá nhập mới</label>
      <input type="number" name="price_in" class="form-control" required min="0">
    </div>
    <div class="col-md-6 mt-2">
      <label class="form-label">Số lượng nhập thêm</label>
      <input type="number" name="quantity" class="form-control" required min="1">
    </div>
    <div class="col-12 mt-2 text-end">
      <button type="submit" class="btn btn-success px-4">📥 Nhập thêm</button>
      <a href="listsanpham.php" class="btn btn-secondary">↩️ Quay lại</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>