<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
require('includes/header.php');
require('../database/conn.php');
?>
<style>
/* Form lọc đơn hàng */
.filter-form {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 15px 10px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.filter-form .form-label {
    font-weight: 500;
    margin-bottom: 4px;
    font-size: 14px;
}

.filter-form .form-control,
.filter-form .form-select {
    font-size: 14px;
    padding: 6px 12px;
    height: 38px;
}

.filter-form .btn {
    height: 38px;
    font-size: 14px;
    padding: 6px 12px;
}

.filter-form .col-md-2 {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding-left: 5px;
    padding-right: 5px;
}
</style>
<div>
    <h3><a href="index.php">Bảng điều khiển</a> > <a href="listdonhang.php">Tất cả đơn hàng</a></h3>
    <br>
    <form method="GET" class="mb-3 row g-2 filter-form">
        <!-- Tên khách hàng -->
        <div class="col-md-2">
            <label class="form-label">Khách hàng</label>
            <input type="text" name="customer" class="form-control" placeholder="Tất cả" value="<?= $_GET['customer'] ?? '' ?>">
        </div>

        <!-- Giá trị sản phẩm -->
        <div class="col-md-2">
            <label class="form-label">Giá trị sản phẩm</label>
            <select name="price" class="form-select">
                <option value="">Tất cả</option>
                <option value="under500" <?= ($_GET['price'] ?? '') == 'under500' ? 'selected' : '' ?>>Dưới 500 000</option>
                <option value="above500" <?= ($_GET['price'] ?? '') == 'above500' ? 'selected' : '' ?>>Từ 500 000</option>
            </select>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="col-md-2">
            <label class="form-label">Phương thức thanh toán</label>
            <select name="payment" class="form-select">
                <option value="">Tất cả</option>
                <option value="COD" <?= ($_GET['payment'] ?? '') == 'COD' ? 'selected' : '' ?>>COD</option>
                <option value="Online" <?= ($_GET['payment'] ?? '') == 'Online' ? 'selected' : '' ?>>Online</option>
            </select>
        </div>

        <!-- Trạng thái đơn -->
        <div class="col-md-2">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="">Tất cả</option>
                <?php $statuses = ['Chờ duyệt', 'Đang giao', 'Đã thanh toán', 'Hoàn thành', 'Đã hủy'];?>
                <?php foreach ($statuses as $st): ?>
                    <option value="<?= $st ?>" <?= ($_GET['status'] ?? '') == $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nút lọc và xóa -->
        <div class="col-md-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50">Lọc</button>
                <a href="listdonhang.php" class="btn btn-secondary w-50 ml-2">Xóa lọc</a>
            </div>
        </div>
    </form>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ ĐƠN HÀNG</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-dark fw-normal" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Mã đơn</th>
                            <th class="text-center">Khách hàng</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">SĐT</th>
                            <th class="text-center">Giá trị sản phẩm</th>
                            <th class="text-center">Phí vận chuyển</th>
                            <th class="text-center">Tổng tiền</th>
                            <th class="text-center">Phương thức thanh toán</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                            <th class="text-center">Đặt lúc</th>
                            <th class="text-center">Cập nhật</th>
                            <th class="text-center">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $limit = 10;
                            $page = $_GET['page'] ?? 1;
                            $start = ($page - 1) * $limit;
                            $where = "WHERE o.is_deleted = 0";
                            if (!empty($_GET['customer'])) {
                                $name = mysqli_real_escape_string($conn, $_GET['customer']);
                                $where .= " AND u.name LIKE '%$name%'";
                            }
                            if (!empty($_GET['price'])) {
                                if ($_GET['price'] == 'under500') $where .= " AND o.total_price < 500000";
                                if ($_GET['price'] == 'above500') $where .= " AND o.total_price >= 500000";
                            }
                            if (!empty($_GET['payment'])) {
                                $pay = mysqli_real_escape_string($conn, $_GET['payment']);
                                $where .= " AND o.payment_method = '$pay'";
                            }
                            if (!empty($_GET['status'])) {
                                $stt = mysqli_real_escape_string($conn, $_GET['status']);
                                $where .= " AND o.status = '$stt'";
                            }
                            $sql = "
                                SELECT o.*, u.name, u.email, u.phone
                                FROM orders o
                                JOIN users u ON o.user_id = u.user_id
                                $where
                                ORDER BY o.order_id DESC
                                LIMIT $start, $limit
                            ";
                            $result = mysqli_query($conn, $sql);
                            //phân trang
                            // Sau khi query danh sách
                            $total_sql = "
                            SELECT COUNT(*) AS total
                            FROM orders o
                            JOIN users u ON o.user_id = u.user_id
                            $where
                            ";
                            $total_result = mysqli_query($conn, $total_sql);
                            $total_row = mysqli_fetch_assoc($total_result);
                            $total_pages = ceil($total_row['total'] / $limit);
                            while ($row = mysqli_fetch_assoc($result)) {
                            
                        ?>
                        <tr>
                            <td><?= $row['order_id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= number_format($row['total_price'], 0, ',', '.') ?>đ</td>
                            <td><?= number_format($row['shipping_fee'], 0, ',', '.') ?>đ</td>
                            <td><?= number_format($row['final_amount'], 0, ',', '.') ?>đ</td>

                            <!-- Bắt đầu form, bao gồm cả hai cột -->
                            <form method="post" action="update_order.php"
                                onsubmit="return confirm('Bạn chắc chắn muốn thay đổi đơn hàng?')">
                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">

        

                                <!-- Cột phương thức thanh toán -->
                                <td>
                                    <select name="payment_method" class="form-select form-select-sm">
                                        <?php  
                                        $methods = ['COD', 'Online'];
                                        foreach ($methods as $pm):  
                                        ?>
                                            <option value="<?= $pm ?>" <?= ($row['payment_method'] == $pm ? 'selected' : '') ?>><?= $pm ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <!-- Cột trạng thái -->
                                <td>
                                    <select name="status" class="form-select form-select-sm">
                                        <?php  
                                        $statuses = ['Chờ duyệt', 'Đang giao', 'Đã thanh toán', 'Hoàn thành', 'Đã hủy'];
                                        foreach ($statuses as $st):  
                                        ?>
                                            <option value="<?= $st ?>" <?= ($row['status'] == $st ? 'selected' : '') ?>><?= $st ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <!-- Nút cập nhật -->
                                <td>
                                    <button type="submit" class="btn btn-sm btn-success">✔</button>
                                </td>
                            </form>

                            <td><?= $row['order_date'] ?></td>
                            <td><?= $row['updated_date'] ?></td>
                            <td class="text-center">
                                <a href="orderdetails.php?order_id=<?= $row['order_id'] ?>" class="btn btn-sm btn-info">Xem</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                        <!-- ⏮ Trang đầu -->
                        <li class="page-item <?= ($page == 1 ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" title="Trang đầu">&laquo;</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- ⏭ Trang cuối -->
                        <li class="page-item <?= ($page == $total_pages ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" title="Trang cuối">&raquo;</a>
                        </li>
                        </ul>
                    </nav>
                <?php endif; ?>                                               
            </div>
        </div>
    </div>
</div>

<?php require("includes/footer.php"); ?>