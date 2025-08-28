<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('../database/conn.php');

// ===== Xử lý lọc =====
$where = [];
if (!empty($_GET['role'])) {
  $role = mysqli_real_escape_string($conn, $_GET['role']);
  $where[] = "role = '$role'";
}
if (isset($_GET['is_deleted']) && $_GET['is_deleted'] !== '') {
  $is_deleted = intval($_GET['is_deleted']);
  $where[] = "is_deleted = $is_deleted";
}
$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ===== Phân trang =====
$page = intval($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Tổng số bản ghi
$count_sql = "SELECT COUNT(*) AS total FROM users $where_sql";
$count_result = mysqli_query($conn, $count_sql);
$total_row = mysqli_fetch_assoc($count_result)['total'];
$total_page = ceil($total_row / $limit);

// Truy vấn dữ liệu
$sql_str = "SELECT * FROM users $where_sql ORDER BY user_id LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql_str);
?>

<?php require('includes/header.php'); ?>

<div class="container mt-4">
  <h3><a href="index.php">Bảng điều khiển</a> > <a href="listnguoidung.php">Người dùng</a></h3>
  <br>

  <!-- Bộ lọc -->
<form method="GET" class="mb-4 d-flex flex-wrap gap-3 align-items-end">
  <div>
    <label class="form-label fw-semibold">Vai trò</label>
    <select name="role" class="form-select" style="min-width: 150px;">
      <option value="">Tất cả</option>
      <option value="customer" <?= ($_GET['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Người dùng</option>
      <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>
  </div>

  <div class="ml-2">
    <label class="form-label fw-semibold">Trạng thái</label>
    <select name="is_deleted" class="form-select" style="min-width: 150px;">
      <option value="">Tất cả</option>
      <option value="0" <?= ($_GET['is_deleted'] ?? '') === '0' ? 'selected' : '' ?>>Hoạt động</option>
      <option value="1" <?= ($_GET['is_deleted'] ?? '') === '1' ? 'selected' : '' ?>>Vô hiệu hóa</option>
    </select>
  </div>

  <div class="d-flex align-items-end gap-2">
    <button type="submit" class="btn btn-primary ml-2">Lọc</button>
    <a href="listnguoidung.php" class="btn btn-outline-secondary ml-2">
      <i class="fas fa-times"></i> Bỏ lọc
    </a>
  </div>
</form>
  <!-- Bảng dữ liệu -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ NGƯỜI DÙNG</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-dark" width="100%">
          <thead class="table-light">
            <tr>
              <th class="text-center">Mã</th>
              <th class="text-center">Avatar</th>
              <th class="text-center">Email</th>
              <th class="text-center">Họ tên</th>
              <th class="text-center">Địa chỉ</th>
              <th class="text-center">SĐT</th>
              <th class="text-center">Vai trò</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Quản lý</th>
            </tr>
          </thead>
          <tfoot class="table-light">
            <tr>
              <th class="text-center">Mã</th>
              <th class="text-center">Avatar</th>
              <th class="text-center">Email</th>
              <th class="text-center">Họ tên</th>
              <th class="text-center">Địa chỉ</th>
              <th class="text-center">SĐT</th>
              <th class="text-center">Vai trò</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Quản lý</th>
            </tr>
          </tfoot>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td class="text-center align-middle"><?= $row['user_id'] ?></td>
                <td class="text-center align-middle">
                  <?php if (!empty($row['avt'])) { ?>
                    <img src="../<?= $row['avt'] ?>" alt="avatar" style="max-height: 80px; max-width: 60px;">
                  <?php } ?>
                </td>
                <td class="text-center align-middle"><?= htmlspecialchars($row['email']) ?></td>
                <td class="text-center align-middle"><?= htmlspecialchars($row['name']) ?></td>
                <td class="text-center align-middle"><?= htmlspecialchars($row['address']) ?></td>
                <td class="text-center align-middle"><?= htmlspecialchars($row['phone']) ?></td>
                <td class="text-center align-middle"><?= $row['role'] ?></td>
                <td class="text-center align-middle">
                  <?php if ($row['is_deleted']) { ?>
                    <span class="badge bg-secondary">Vô hiệu hóa</span>
                  <?php } else { ?>
                    <span class="badge bg-success">Hoạt động</span>
                  <?php } ?>
                </td>
                <td class="text-center align-middle">
                  <?php if ($row['role'] !== 'admin') { ?>
                    <a href="update_user.php?id=<?= $row['user_id'] ?>">
                      <i class="fas fa-edit text-primary me-2" title="Chỉnh sửa"></i>
                    </a>
                    <?php if ($row['is_deleted']) { ?>
                      <a href="restore_user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Khôi phục người dùng này?')">
                        <i class="fas fa-undo text-success me-2" title="Khôi phục"></i>
                      </a>
                    <?php } else { ?>
                      <a href="delete_user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Xác nhận xóa người dùng này?')">
                        <i class="fas fa-trash-alt text-danger me-2" title="Xóa"></i>
                      </a>
                    <?php } ?>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Phân trang -->
      <?php if ($total_page > 1): ?>
        <nav class="mt-4">
          <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_page; $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php require("includes/footer.php"); ?>