<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');
require('includes/header.php');

?>


<div class="container my-5">
  <h4 class="fw-bold text-primary mb-4">➕ Thêm sách mới</h4>
  <form method="post" action="xuly_addbook.php" enctype="multipart/form-data">
    <div class="row g-4">
      <!-- Cột trái: Tên, Giá nhập, Số lượng -->
      <div class="col-md-6">
        <label class="form-label">Tên sách</label>
        <input type="text" name="name" class="form-control" required>

        <label class="form-label mt-3">Giá nhập</label>
        <input type="number" name="price_in" class="form-control" required min="0">

        <label class="form-label mt-3">Số lượng</label>
        <input type="number" name="quantity" class="form-control" required min="0">
      </div>

      <!-- Cột phải: Thể loại, Tác giả, NXB -->
      <div class="col-md-6">
        <div>
            <label class="form-label">Thể loại</label> <br>
            <select name="category_id" class="form-select" required>
            <option value="" selected disabled>-- Chọn --</option>
            <?php
            $categories = mysqli_query($conn, "SELECT category_id, name FROM categories WHERE is_deleted = 0");
            while ($cat = mysqli_fetch_assoc($categories)) {
                echo "<option value='{$cat['category_id']}'>{$cat['name']}</option>";
            }
            ?>
            </select>
        </div>
        <div>
            <label class="form-label mt-3">Tác giả</label> <br>
            <select name="author_id" class="form-select" required>
            <option value="" selected disabled>-- Chọn --</option>
            <?php
            $authors = mysqli_query($conn, "SELECT author_id, name FROM authors WHERE is_deleted = 0");
            while ($au = mysqli_fetch_assoc($authors)) {
                echo "<option value='{$au['author_id']}'>{$au['name']}</option>";
            }
            ?>
            </select>
        </div>

        <div>
            <label class="form-label mt-3">Nhà xuất bản</label> <br>
            <select name="publisher_id" class="form-select" required>
            <option value="" selected disabled>-- Chọn --</option>
            <?php
            $pubs = mysqli_query($conn, "SELECT publisher_id, name FROM publishers WHERE is_deleted = 0");
            while ($pub = mysqli_fetch_assoc($pubs)) {
                echo "<option value='{$pub['publisher_id']}'>{$pub['name']}</option>";
            }
            ?>
            </select>
        </div>
      </div>

      <!-- Hàng mô tả -->
      <div class="col-12">
        <label class="form-label">Mô tả</label>
        <textarea name="description" rows="4" class="form-control"></textarea>
      </div>

      <!-- Hàng ảnh: 2 cột -->
      <div class="col-md-6">
        <label class="form-label">Ảnh bìa chính</label>
        <input type="file" name="cover" class="form-control" accept="image/*" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Ảnh phụ (chọn nhiều)</label>
        <input type="file" name="extras[]" class="form-control" accept="image/*" multiple>
      </div>
      
      <!-- Nút hành động -->
      <div class="col-12 text-end mt-2">
        <button type="submit" class="btn btn-success px-4">➕ Thêm sách</button>
        <a href="listsanpham.php" class="btn btn-secondary">↩️ Quay lại</a>
      </div>
    </div>
  </form>
</div>

<?php require('includes/footer.php'); ?>