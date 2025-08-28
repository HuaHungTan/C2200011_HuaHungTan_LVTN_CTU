<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('includes/header.php');
require('../database/conn.php');
?>

<div>
  <h3><a href="index.php">Bảng điều khiển</a> > <a href="listhoantien.php">Danh sách hoàn tiền</a></h3>
  <br><br>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">DANH SÁCH HOÀN TIỀN</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">

        <?php
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $count_sql = "SELECT COUNT(*) AS total FROM refunds";
        $total_rows = mysqli_fetch_assoc(mysqli_query($conn, $count_sql))['total'];
        $total_pages = ceil($total_rows / $limit);

        $sql = "SELECT r.*, u.name AS user_name, o.status AS order_status 
                FROM refunds r 
                JOIN users u ON r.user_id = u.user_id 
                JOIN orders o ON r.order_id = o.order_id 
                ORDER BY r.refund_id desc
                LIMIT $limit OFFSET $offset";
        $result = mysqli_query($conn, $sql);
        ?>

        <table class="table table-bordered text-dark fw-normal" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th class="text-center">Mã hoàn tiền</th>
              <th class="text-center">Khách hàng</th>
              <th class="text-center">Đơn hàng</th>
              <th class="text-center">Số tiền</th>
              <th class="text-center">Lý do</th>
              <th class="text-center">Phương thức</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Ngày yêu cầu</th>
              <th class="text-center">Ngày cập nhật</th>
              <th class="text-center">Xác nhận</th>

            </tr>
          </thead>
          <tbody>
            <?php while ($r = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td class="text-center align-middle"><?= $r['refund_id'] ?></td>
                <td class="text-center align-middle"><?= htmlspecialchars($r['user_name']) ?></td>
                <td class="text-center align-middle">#<?= $r['order_id'] ?> (<?= $r['order_status'] ?>)</td>
                <td class="text-center align-middle"><?= number_format($r['amount']) ?>₫</td>
                <td class="text-center align-middle"><?= $r['refund_reason'] ?></td>
                <td class="text-center align-middle"><?= $r['refund_method'] ?></td>
                <td class="text-center align-middle"><?= $r['status'] ?></td>
                <td class="text-center align-middle"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                <td class="text-center align-middle"><?= date('d/m/Y H:i', strtotime($r['updated_at'])) ?></td>
                <td class="text-center align-middle">
                  <?php if ($r['status'] === 'Đang chờ'): ?>
                    <form method="post" action="update_refund.php" onsubmit="return confirm('Xác nhận đã hoàn tiền cho đơn hàng này?')">
                      <input type="hidden" name="refund_id" value="<?= $r['refund_id'] ?>">
                      <input type="hidden" name="new_status" value="Đã hoàn">
                      <button type="submit" class="btn btn-success btn-sm px-3">✔ Hoàn tiền</button>
                    </form>
                  <?php else: ?>
                    <span class="text-success fw-bold">Đã hoàn</span>
                  <?php endif; ?>
                </td>
                                
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
          <nav class="mt-4">
            <ul class="pagination justify-content-center">
              <li class="page-item <?= ($page == 1 ? 'disabled' : '') ?>">
                <a class="page-link" href="?page=1">&laquo;</a>
              </li>
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?= ($page == $total_pages ? 'disabled' : '') ?>">
                <a class="page-link" href="?page=<?= $total_pages ?>">&raquo;</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<?php require("includes/footer.php"); ?>