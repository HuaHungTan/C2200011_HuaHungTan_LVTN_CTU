<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('includes/header.php');
require('../database/conn.php');

// Phân trang
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Tổng số tác giả
$count_sql = "SELECT COUNT(*) AS total FROM authors";
$total_rows = mysqli_fetch_assoc(mysqli_query($conn, $count_sql))['total'];
$total_pages = ceil($total_rows / $limit);

// Truy vấn danh sách
$sql_str = "SELECT * FROM authors ORDER BY author_id LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql_str);
?>

<div>
  <h3>
    <a href="index.php">Bảng điều khiển</a> > <a href="listtacgia.php">Tác giả</a>
  </h3>
  <br>
  <br>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ TÁC GIẢ</h6>
      <a class="d-flex justify-content-end text-decoration-none" href="add_author.php"><button class="btn btn-success">➕Thêm tác giả mới</button></a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-dark fw-normal" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th class="text-center">Mã</th>
              <th class="text-center">Hình ảnh</th>
              <th class="text-center">Tên tác giả</th>
              <th class="text-center">Số sách</th>
              <th class="text-center">Quốc tịch</th>
              <th class="text-center">Giới thiệu</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Quản lý</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th class="text-center">Mã</th>
              <th class="text-center">Hình ảnh</th>
              <th class="text-center">Tên tác giả</th>
              <th class="text-center">Số sách</th>
              <th class="text-center">Quốc tịch</th>
              <th class="text-center">Giới thiệu</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Quản lý</th>
            </tr>
          </tfoot>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td class="text-center align-middle"><?= $row['author_id'] ?></td>
                <td class="text-center">
                  <img src="../<?= $row['img_url'] ?>" alt="Ảnh" style="max-height:80px; max-width:80px; border-radius:4px;">
                </td>
                <td class="text-center align-middle"><?= $row['name'] ?></td>
                <td class="text-center align-middle"><?= $row['book_count'] ?></td>
                <td class="text-center align-middle"><?= $row['nationality'] ?></td>
                <td class="align-middle"><?= $row['bio'] ?></td>
                <td class="text-center align-middle">
                  <?php if ($row['is_deleted']) { ?>
                    <span class="badge bg-secondary">Đã ẩn</span>
                  <?php } else { ?>
                    <span class="badge bg-success">Hiển thị</span>
                  <?php } ?>
                </td>
                <td class="text-center align-middle">
                  <a href="update_author.php?id=<?= $row['author_id'] ?>">
                    <i class="fas fa-edit text-primary me-2" title="Chỉnh sửa"></i>
                  </a>
                  <?php if ($row['is_deleted']) { ?>
                    <a href="restore_author.php?id=<?= $row['author_id'] ?>" onclick="return confirm('Khôi phục tác giả này?')">
                      <i class="fas fa-undo text-success me-2" title="Khôi phục"></i>
                    </a>
                  <?php } else { ?>
                    <a href="delete_author.php?id=<?= $row['author_id'] ?>" onclick="return confirm('Xác nhận ẩn tác giả này?')">
                      <i class="fas fa-trash-alt text-danger me-2" title="Xóa"></i>
                    </a>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
          <nav class="mt-4">
            <ul class="pagination justify-content-center">
              <li class="page-item <?= ($page == 1 ? 'disabled' : '') ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">&laquo;</a>
              </li>
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
                  <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?= ($page == $total_pages ? 'disabled' : '') ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>">&raquo;</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<?php require("includes/footer.php"); ?>