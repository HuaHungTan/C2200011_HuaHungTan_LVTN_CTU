<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');
require('includes/header.php');

// Lấy danh sách sách chưa có khuyến mại hoặc đang hiển thị
$books = mysqli_query($conn, "
  SELECT book_id, name FROM books 
  WHERE is_deleted = 0
");
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">➕ Thêm khuyến mại mới</h4>
  <form method="post" action="xuly_adddiscount.php" class="col-md-8">

    <label class="form-label">Chọn sách áp dụng:</label>
    <select name="book_id" class="form-select" required>
      <option value="">-- Chọn sách --</option>
      <?php while ($book = mysqli_fetch_assoc($books)): ?>
        <option value="<?= $book['book_id'] ?>"><?= htmlspecialchars($book['name']) ?></option>
      <?php endwhile; ?>
    </select>

    <label class="form-label mt-3">% Giảm giá:</label>
    <input type="number" name="discount_percent" class="form-control" step="0.01" min="0" max="100" required>

    <label class="form-label mt-3">Ngày bắt đầu:</label>
    <input type="date" name="start_date" class="form-control" required>

    <label class="form-label mt-3">Ngày kết thúc:</label>
    <input type="date" name="end_date" class="form-control" required>

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Thêm mới</button>
      <a href="listkhuyenmai.php" class="btn btn-secondary">Quay lại</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>