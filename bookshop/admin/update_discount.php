<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

require('../database/conn.php');
require('includes/header.php');

// Lấy discount_id
$discount_id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM discount WHERE discount_id = $discount_id";
$discount = mysqli_fetch_assoc(mysqli_query($conn, $sql));

if (!$discount) {
    echo "<script>alert('❌ Không tìm thấy khuyến mại.'); window.location.href='listdiscount.php';</script>";
    exit;
}

// Lấy danh sách sách để chọn lại nếu cần
$books = mysqli_query($conn, "SELECT book_id, name FROM books WHERE is_deleted = 0");
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">✏️ Chỉnh sửa khuyến mại</h4>
  <form method="post" action="xuly_updatediscount.php" class="col-md-8">
    <input type="hidden" name="discount_id" value="<?= $discount['discount_id'] ?>">

    <label class="form-label">Chọn sách áp dụng:</label>
    <select name="book_id" class="form-select" required>
      <?php while ($book = mysqli_fetch_assoc($books)): ?>
        <option value="<?= $book['book_id'] ?>" <?= ($book['book_id'] == $discount['book_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($book['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label class="form-label mt-3">% Giảm giá:</label>
    <input type="number" name="discount_percent" class="form-control" value="<?= $discount['discount_percent'] ?>" step="0.01" min="0" max="100" required>

    <label class="form-label mt-3">Ngày bắt đầu:</label>
    <input type="date" name="start_date" class="form-control" value="<?= $discount['start_date'] ?>" required>

    <label class="form-label mt-3">Ngày kết thúc:</label>
    <input type="date" name="end_date" class="form-control" value="<?= $discount['end_date'] ?>" required>

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success px-4">Cập nhật</button>
      <a href="listkhuyenmai.php" class="btn btn-secondary">Quay lại</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>