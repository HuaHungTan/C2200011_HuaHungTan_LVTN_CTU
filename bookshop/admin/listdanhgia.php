<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
?>

<?php
require('includes/header.php');
require('../database/conn.php');
?>

<div>
    <h3 class="mb-3"><a href="index.php">Bảng điều khiển</a>><a href="listdanhgia.php">Tất cả đánh giá</a></h3>
    <br>
    <?php 
    // Truy vấn danh sách sách
        $books_q = mysqli_query($conn, "SELECT book_id, name FROM books ORDER BY name");

    // Truy vấn danh sách người dùng
        $users_q = mysqli_query($conn, "SELECT user_id, name FROM users ORDER BY name");
    ?>
    <style>
  .filter-form label {
    font-weight: 500;
    margin-bottom: 4px;
  }
  .filter-form .form-section {
    display: flex;
    flex-direction: column;
    min-width: 160px;
  }
</style>

<form method="GET" class="mb-4 d-flex flex-wrap filter-form align-items-end gap-3">

  <!-- Sách -->
  <div class="form-section">
    <label for="book_id">📘 Sách</label>
    <select name="book_id" class="form-select form-select-sm">
      <option value="">-- Tất cả --</option>
      <?php mysqli_data_seek($books_q, 0); while($b = mysqli_fetch_assoc($books_q)): ?>
        <option value="<?= $b['book_id'] ?>" <?= ($_GET['book_id'] ?? '')==$b['book_id'] ? 'selected' : '' ?>>
          <?= $b['name'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <!-- Người dùng -->
  <div class="form-section">
    <label for="user_id">👤 Khách hàng</label>
    <select name="user_id" class="form-select form-select-sm">
      <option value="">-- Tất cả --</option>
      <?php mysqli_data_seek($users_q, 0); while($u = mysqli_fetch_assoc($users_q)): ?>
        <option value="<?= $u['user_id'] ?>" <?= ($_GET['user_id'] ?? '')==$u['user_id'] ? 'selected' : '' ?>>
          <?= $u['name'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <!-- Số sao -->
  <div class="form-section">
    <label for="rating">⭐ Số sao</label>
    <select name="rating" class="form-select form-select-sm">
      <option value="">-- Chọn --</option>
      <?php for ($i=1; $i<=5; $i++): ?>
        <option value="<?= $i ?>" <?= ($_GET['rating'] ?? '')==$i ? 'selected' : '' ?>><?= $i ?> ⭐</option>
      <?php endfor; ?>
    </select>
  </div>

  <!-- Trạng thái -->
  <div class="form-section">
    <label for="status">📌 Trạng thái</label>
    <select name="status" class="form-select form-select-sm">
      <option value="">-- Chọn --</option>
      <option value="Chờ duyệt" <?= ($_GET['status'] ?? '')=='Chờ duyệt' ? 'selected' : '' ?>>Chờ duyệt</option>
      <option value="Đã duyệt" <?= ($_GET['status'] ?? '')=='Đã duyệt' ? 'selected' : '' ?>>Đã duyệt</option>
    </select>
  </div>

  <!-- Nút -->
  <div class="form-section">
    <label>&nbsp;</label>
    <div class="d-flex">
      <button type="submit" class="btn btn-sm btn-primary ml-2">Lọc kết quả</button>
      <a href="listdanhgia.php" class="btn btn-sm btn-secondary ml-2">Xóa lọc</a>
    </div>
  </div>

</form>
    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ ĐÁNH GIÁ</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-dark fw-normal" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">STT</th>
                                            <th class="text-center">Sách</th>
                                            <th class="text-center">Khách hàng</th>
                                            <th class="text-center">Đánh giá</th>
                                            <th class="text-center">Bình luận</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center">STT</th>
                                            <th class="text-center">Sách</th>
                                            <th class="text-center">Khách hàng</th>
                                            <th class="text-center">Đánh giá</th>
                                            <th class="text-center">Bình luận</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            // Truy vấn sách & người dùng để phục vụ form lọc
                                            $books_q = mysqli_query($conn, "SELECT book_id, name FROM books ORDER BY name");
                                            $users_q = mysqli_query($conn, "SELECT user_id, name FROM users ORDER BY name");
                                            // Xây dựng điều kiện lọc
                                            $conditions = [];
                                            if (!empty($_GET['book_id'])) {
                                            $book_id = (int) $_GET['book_id'];
                                            $conditions[] = "r.book_id = $book_id";
                                            }
                                            if (!empty($_GET['user_id'])) {
                                            $user_id = (int) $_GET['user_id'];
                                            $conditions[] = "r.user_id = $user_id";
                                            }
                                            if (!empty($_GET['rating'])) {
                                            $rating = (int) $_GET['rating'];
                                            $conditions[] = "r.rating = $rating";
                                            }
                                            if (!empty($_GET['status'])) {
                                            $status = mysqli_real_escape_string($conn, $_GET['status']);
                                            $conditions[] = "r.status = '$status'";
                                            }

                                            $where = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
                                            //PHÂN TRANG
                                            // Tổng số đánh giá
                                            $count_sql = "
                                            SELECT COUNT(*) AS total
                                            FROM review r
                                            JOIN users u ON r.user_id = u.user_id
                                            JOIN books b ON r.book_id = b.book_id
                                            $where
                                            ";
                                            $count_result = mysqli_fetch_assoc(mysqli_query($conn, $count_sql));
                                            $total_reviews = intval($count_result['total']);

                                            // Số lượng hiển thị mỗi trang
                                            $limit = 10;

                                            // Tổng số trang
                                            $total_pages = ceil($total_reviews / $limit);

                                            // Trang hiện tại
                                            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
                                            $page = max(1, min($page, $total_pages));

                                            // Vị trí bắt đầu truy vấn
                                            $offset = ($page - 1) * $limit;
                                            // Truy vấn đánh giá với điều kiện lọc
                                            $sql_str = "
                                            SELECT r.review_id, r.rating, r.comment, r.status,
                                                    u.name AS user_name, b.name AS book_name
                                            FROM review r
                                            JOIN users u ON r.user_id = u.user_id
                                            JOIN books b ON r.book_id = b.book_id
                                            $where
                                            ORDER BY r.review_id DESC
                                            LIMIT $limit OFFSET $offset
                                            ";

                                            $result = mysqli_query($conn, $sql_str);
                                            $stt = 1;
                                            while($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $stt++ ?></td>
                                            <td><?= $row['book_name'] ?></td>
                                            <td><?= $row['user_name'] ?></td>
                                            <td class="text-center"><?= $row['rating'] ?> ⭐</td>
                                            <td><?= $row['comment'] ?></td>
                                            <td class="text-center">
                                                <form method="post" action="update_review.php" style="display:inline;">
                                                    <input type="hidden" name="review_id" value="<?= $row['review_id'] ?>">
                                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block w-auto">
                                                        <option value="Chờ duyệt" <?= $row['status']=='Chờ duyệt' ? 'selected' : '' ?>>Chờ duyệt</option>
                                                        <option value="Đã duyệt" <?= $row['status']=='Đã duyệt' ? 'selected' : '' ?>>Đã duyệt</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php 
                                        }
                                        ?>               
                                    </tbody>
                                </table>
                                <!--PHÂN TRANG -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center mt-4">
                                        <!-- Mũi tên trang trước -->
                                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
                                            aria-label="Trang trước">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                        </li>

                                        <!-- Số trang -->
                                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                        <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                            <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>">
                                            <?= $p ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <!-- Mũi tên trang sau -->
                                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
                                            aria-label="Trang sau">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
</div>

<?php
require("includes/footer.php");
?>