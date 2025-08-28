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
    <h3><a href="index.php">Bảng điều khiển</a>><a href="listkhuyenmai.php">Khuyến mại</a></h3>
    <br>
    <br>
    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ KHUYẾN MẠI</h6>
                            <a class="d-flex justify-content-end text-decoration-none" href="add_discount.php"><button class="btn btn-success">➕Thêm khuyến mại</button></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-dark fw-normal" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Mã khuyến mại</th>
                                            <th class="text-center">Hình ảnh</th>
                                            <th class="text-center">Tên sách</th>
                                            <th class="text-center">Mức khuyến mại</th>
                                            <th class="text-center">Ngày bắt đầu</th>
                                            <th class="text-center">Ngày kết thúc</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center">Mã khuyến mại</th>
                                            <th class="text-center">Hình ảnh</th>
                                            <th class="text-center">Tên sách</th>
                                            <th class="text-center">Mức khuyến mại</th>
                                            <th class="text-center">Ngày bắt đầu</th>
                                            <th class="text-center">Ngày kết thúc</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                        //phân trang
                                        $limit = 10;
                                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                        $start = ($page - 1) * $limit;
                                        $count_sql = "SELECT COUNT(*) AS total FROM discount WHERE is_deleted = 0"; // hoặc bỏ điều kiện nếu muốn hiển thị cả đã ẩn
                                        $count_result = mysqli_query($conn, $count_sql);
                                        $total_rows = mysqli_fetch_assoc($count_result)['total'];
                                        $total_pages = ceil($total_rows / $limit);
                                        //truy vấn
                                       $sql_str = "SELECT d.discount_id, d.discount_percent, d.start_date, d.end_date, d.is_deleted,
                                                        b.name AS book_name, bi.img_url
                                                    FROM discount d
                                                    JOIN books b ON d.book_id = b.book_id
                                                    LEFT JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
                                                    ORDER BY d.discount_id 
                                                    LIMIT $start, $limit
                                                ";
                                        $result = mysqli_query($conn, $sql_str);
                                        while($row= mysqli_fetch_assoc($result)){
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $row['discount_id'] ?></td>
                                            <td class="text-center">
                                            <img src="../<?= $row['img_url'] ?: 'data_image/book/default.jpg' ?>" 
                                                alt="ảnh bìa" style="max-height:80px; max-width:60px; object-fit:cover;">
                                            </td>
                                            <td class="align-middle"><?= $row['book_name'] ?></td>
                                            <td class="text-danger fw-bold text-center align-middle">
                                                <?= $row['discount_percent'] ?>%
                                            </td>
                                            <td class="align-middle"><?= date('d/m/Y', strtotime($row['start_date'])) ?></td>
                                            <td class="align-middle"><?= date('d/m/Y', strtotime($row['end_date'])) ?></td>
                                            <td class="text-center align-middle">
                                                <?php if ($row['is_deleted']) { ?>
                                                    <span class="badge bg-secondary">Vô hiệu hóa</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-success">Hoạt động</span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="update_discount.php?id=<?= $row['discount_id'] ?>" class="text-primary me-2" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <?php if (!$row['is_deleted']): ?>
                                                    <a href="delete_discount.php?id=<?= $row['discount_id'] ?>"
                                                    onclick="return confirm('Bạn có chắc muốn ẩn khuyến mại này?');"
                                                    class="text-danger" title="Ẩn khuyến mại">
                                                    <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="restore_discount.php?id=<?= $row['discount_id'] ?>"
                                                    onclick="return confirm('Bạn có chắc muốn khôi phục khuyến mại này?');"
                                                    class="text-success" title="Khôi phục">
                                                    <i class="fas fa-undo"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php 
                                        }
                                        ?>                     
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

<?php
require("includes/footer.php");
?>