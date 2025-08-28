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

// Tổng NXB
$count_sql = "SELECT COUNT(*) AS total FROM publishers";
$total_rows = mysqli_fetch_assoc(mysqli_query($conn, $count_sql))['total'];
$total_pages = ceil($total_rows / $limit);

// Truy vấn danh sách
$sql_str = "SELECT * FROM publishers ORDER BY publisher_id LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql_str);
?>

<div>
  <h3><a href="index.php">Bảng điều khiển</a> > <a href="listnxb.php">Nhà xuất bản</a></h3>
  <br>
  <br>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ NHÀ XUẤT BẢN</h6>
       <a class="d-flex justify-content-end text-decoration-none" href="add_publisher.php"><button class="btn btn-success">➕Thêm nhà xuất bản mới</button></a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-dark fw-normal" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th class="text-center">Mã NXB</th>
              <th class="text-center">Logo</th>
              <th class="text-center">Tên NXB</th>
              <th class="text-center">Số sách</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Quản lý</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th class="text-center">Mã NXB</th>
              <th class="text-center">Logo</th>
              <th class="text-center">Tên NXB</th>
              <th class="text-center">Số sách</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Quản lý</th>
            </tr>
          </tfoot>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td class="text-center align-middle"><?= $row['publisher_id'] ?></td>
                <td class="text-center">
                  <img src="../<?= $row['logo_url'] ?>" alt="Logo" style="max-height: 80px; max-width: 80px;">
                </td>
                <td class="text-center align-middle"><?= $row['name'] ?></td>
                <td class="text-center align-middle"><?= $row['book_count'] ?></td>
                <td class="text-center align-middle">
                  <?php if ($row['is_deleted']) { ?>
                    <span class="badge bg-secondary">Đã ẩn</span>
                  <?php } else { ?>
                    <span class="badge bg-success">Hiển thị</span>
                  <?php } ?>
                </td>
                <td class="text-center align-middle">
                  <a href="update_publisher.php?id=<?= $row['publisher_id'] ?>">
                    <i class="fas fa-edit text-primary me-2" title="Chỉnh sửa"></i>
                  </a>
                  <?php if ($row['is_deleted']) { ?>
                    <a href="restore_publisher.php?id=<?= $row['publisher_id'] ?>"
                       onclick="return confirm('Khôi phục nhà xuất bản này?')">
                      <i class="fas fa-undo text-success me-2" title="Khôi phục"></i>
                    </a>
                  <?php } else { ?>
                    <a href="delete_publisher.php?id=<?= $row['publisher_id'] ?>"
                       onclick="return confirm('Xác nhận xóa nhà xuất bản này?')">
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
                  <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                    <?= $i ?>
                  </a>
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