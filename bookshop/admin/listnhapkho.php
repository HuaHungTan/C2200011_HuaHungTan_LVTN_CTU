<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('includes/header.php');
require('../database/conn.php');

// Bộ lọc dữ liệu
$month = $_GET['month'] ?? '';
$year  = $_GET['year'] ?? '';
$type  = $_GET['type'] ?? '';
$page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Tạo điều kiện lọc SQL
$where = [];
if ($month !== '') {
  $where[] = "MONTH(ab.added_date) = " . intval($month);
}
if ($year !== '') {
  $where[] = "YEAR(ab.added_date) = " . intval($year);
}
if ($type !== '') {
  $where[] = "ab.type = '" . mysqli_real_escape_string($conn, $type) . "'";
}

// Truy vấn tổng số bản ghi
$count_sql = "SELECT COUNT(*) AS total
              FROM added_book ab
              JOIN books b ON ab.book_id = b.book_id
              JOIN users u ON ab.added_by = u.user_id";
if (count($where)) {
  $count_sql .= " WHERE " . implode(" AND ", $where);
}
$count_result = mysqli_query($conn, $count_sql);
$total = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total / $limit);

// Truy vấn bản ghi thực tế
$sql_str = "SELECT ab.*, b.name AS book_name, u.name AS added_by_name
            FROM added_book ab
            JOIN books b ON ab.book_id = b.book_id
            JOIN users u ON ab.added_by = u.user_id";
if (count($where)) {
  $sql_str .= " WHERE " . implode(" AND ", $where);
}
$sql_str .= " ORDER BY ab.added_id desc LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql_str);

// Năm hiện tại để tạo dropdown
$currentYear = date('Y');
?>

<!-- CSS tùy biến -->
<style>
  h3 a {
    text-decoration: none;
    color: #007bff;
  }
  form .form-label {
    margin-bottom: 6px;
  }
  form select, form button {
    min-width: 150px;
  }
  .table th, .table td {
    vertical-align: middle;
  }
  .badge {
    font-size: 90%;
    padding: 6px 10px;
  }
  .btn-outline-secondary:hover {
    background-color: #f0f0f0;
  }
</style>

<div>
  <h3><a href="index.php">Bảng điều khiển</a> > <a href="listnhapkho.php">Lịch sử nhập sách</a></h3>
  <br>

  <!-- Form lọc -->
  <form method="GET" class="mb-4 d-flex gap-4 align-items-end flex-wrap">
    <div class="d-flex flex-column ml-3">
      <label for="month" class="form-label fw-bold">Tháng</label>
      <select name="month" id="month" class="form-select w-auto">
        <option value="">--Chọn tháng--</option>
        <?php for ($m = 1; $m <= 12; $m++): ?>
          <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>>Tháng <?= $m ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="d-flex flex-column ml-3">
      <label for="year" class="form-label fw-bold">Năm</label>
      <select name="year" id="year" class="form-select w-auto">
        <option value="">--Chọn năm--</option>
        <?php for ($y = $currentYear; $y >= $currentYear - 10; $y--): ?>
          <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>Năm <?= $y ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="d-flex flex-column ml-3">
      <label for="type" class="form-label fw-bold">Loại nhập</label>
      <select name="type" id="type" class="form-select w-auto">
        <option value="">--Loại nhập--</option>
        <option value="Thêm sách mới" <?= ($type === 'Thêm sách mới') ? 'selected' : '' ?>>Thêm sách mới</option>
        <option value="Nhập thêm sách có sẵn" <?= ($type === 'Nhập thêm sách có sẵn') ? 'selected' : '' ?>>Nhập thêm sách có sẵn</option>
      </select>
    </div>

    <div class="d-flex flex-column ml-3">
      <label class="form-label invisible">Thao tác</label>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lọc</button>
        <a href="listnhapkho.php" class="btn btn-outline-secondary ml-2">Xóa lọc</a>
      </div>
    </div>
  </form>

  <!-- Bảng dữ liệu -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">LỊCH SỬ NHẬP SÁCH</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-dark fw-normal" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr class="text-center">
              <th>Mã đơn nhập hàng</th>
              <th>Mã sách</th>
              <th>Tên sách</th>
              <th>Số lượng</th>
              <th>Giá nhập</th>
              <th>Ngày nhập</th>
              <th>Người nhập</th>
              <th>Phân loại</th>
              <th>Quản lý</th>
            </tr>
          </thead>
          <tfoot>
            <tr class="text-center">
              <th>Mã đơn nhập hàng</th>
              <th>Mã sách</th>
              <th>Tên sách</th>
              <th>Số lượng</th>
              <th>Giá nhập</th>
              <th>Ngày nhập</th>
              <th>Người nhập</th>
              <th>Phân loại</th>
              <th>Quản lý</th>
            </tr>
          </tfoot>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $row['added_id'] ?></td>
                <td><?= $row['book_id'] ?></td>
                <td><?= $row['book_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= number_format($row['price_in'], 0, ',', '.') ?>₫</td>
                <td><?= $row['added_date'] ?></td>
                <td><?= $row['added_by_name'] ?></td>
                <td>
                  <?php if ($row['type'] === 'Thêm sách mới'): ?>
                    <span class="badge bg-success"><?= $row['type'] ?></span>
                  <?php else: ?>
                    <span class="badge bg-info text-dark"><?= $row['type'] ?></span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <i class="fas fa-lock text-secondary me-2" title="Readonly"></i>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- Thanh phân trang -->
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

<?php require("includes/footer.php"); ?>