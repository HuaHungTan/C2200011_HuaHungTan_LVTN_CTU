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
  <h4 class="fw-bold text-primary mb-4">🛠️ Cập nhật sách</h4>
  <form method="post" action="xuly_updatebook.php" class="row g-3">
    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">


    <!-- 🎯 Cột trái: Tên, Giá nhập, Số lượng -->
    <div class="col-md-6 ">

        <div>
            <label class="form-label">Tên sách</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($book['name']) ?>" required>
        
            <label class="form-label mt-2">Giá nhập</label>
            <input type="number" name="price_in" class="form-control" value="<?= $book['price_in'] ?>" required min="0">
        
            <label class="form-label mt-2">Số lượng</label>
            <input type="number" name="quantity" class="form-control" value="<?= $book['quantity'] ?>" required min="0">
        </div>

    </div>

    <!-- 📚 Cột phải: Thể loại, Tác giả, Nhà xuất bản -->
    <div class="col-md-6 ">
            <div>
            <label class="form-label">Thể loại</label><br>
            <select name="category_id" class="form-select" required>
                <?php
                $categories = mysqli_query($conn, "SELECT category_id, name FROM categories WHERE is_deleted = 0");
                while ($cat = mysqli_fetch_assoc($categories)) {
                $selected = ($cat['category_id'] == $book['category_id']) ? 'selected' : '';
                echo "<option value='{$cat['category_id']}' $selected>{$cat['name']}</option>";
                }
                ?>
            </select>
            </div>

            <div class="mt-4">
            <label class="form-label">Tác giả</label><br>
            <select name="author_id" class="form-select" required>
                <?php
                $authors = mysqli_query($conn, "SELECT author_id, name FROM authors WHERE is_deleted = 0");
                while ($au = mysqli_fetch_assoc($authors)) {
                $selected = ($au['author_id'] == $book['author_id']) ? 'selected' : '';
                echo "<option value='{$au['author_id']}' $selected>{$au['name']}</option>";
                }
                ?>
            </select>
            </div>

            <div class="mt-4">
            <label class="form-label">Nhà xuất bản</label><br>
            <select name="publisher_id" class="form-select" required>
                <?php
                $pubs = mysqli_query($conn, "SELECT publisher_id, name FROM publishers WHERE is_deleted = 0");
                while ($pub = mysqli_fetch_assoc($pubs)) {
                $selected = ($pub['publisher_id'] == $book['publisher_id']) ? 'selected' : '';
                echo "<option value='{$pub['publisher_id']}' $selected>{$pub['name']}</option>";
                }
                ?>
            </select>
            </div>
        </div>
    
    <div class="col-12">
      <label class="form-label">Mô tả</label>
      <textarea name="description" rows="4" class="form-control"><?= htmlspecialchars($book['description']) ?></textarea>
    </div>

    <div class="col-12 text-end mt-3">
      <button type="submit" class="btn btn-success px-4">💾 Lưu cập nhật</button>
      <a href="listsanpham.php" class="btn btn-secondary">↩️ Quay lại</a>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>