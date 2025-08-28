<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}
?>

<?php
require('includes/header.php');
?>

<div>
    <h3><a href="index.php">Bảng điều khiển</a>><a href="listdanhmuc.php">Danh mục sách</a></h3>
    <br>
    <br>
    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ DANH MỤC SÁCH</h6>
                            <a class="d-flex justify-content-end text-decoration-none" href="add_category.php"><button class="btn btn-success">➕Thêm danh mục mới</button></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-dark fw-normal" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Mã danh mục</th>
                                            <th class="text-center">Tên danh mục</th>
                                            <th class="text-center">Số lượng đầu sách</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center">Mã danh mục</th>
                                            <th class="text-center">Tên danh mục</th>
                                            <th class="text-center">Số lượng đầu sách</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                        require('../database/conn.php');
                                        // Xác định trang hiện tại
                                        $page = max(1, intval($_GET['page'] ?? 1));
                                        $limit = 10;
                                        $offset = ($page - 1) * $limit;

                                        // Tổng số danh mục
                                        $count_sql = "SELECT COUNT(*) AS total FROM categories WHERE is_deleted = 0";
                                        $total_rows = mysqli_fetch_assoc(mysqli_query($conn, $count_sql))['total'];
                                        $total_pages = ceil($total_rows / $limit);


                                        $sql_str = "SELECT * FROM categories ORDER BY category_id LIMIT $limit OFFSET $offset";
                                        $result = mysqli_query($conn, $sql_str);
                                        while($row= mysqli_fetch_assoc($result)){
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?=$row['category_id']?></td>
                                            <td class="text-center align-middle"><?=$row['name']?></td>
                                            <td class="text-center align-middle"><?=$row['book_count']?></td>
                                            <td class="text-center align-middle">
                                                <?php if ($row['is_deleted']) { ?>
                                                    <span class="badge bg-secondary">Đã ẩn</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-success">Hiển thị</span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="update_category.php?id=<?= $row['category_id'] ?>">
                                                    <i class="fas fa-edit text-primary me-3" title="Chỉnh sửa"></i>
                                                </a>

                                                <?php if ($row['is_deleted']) { ?>
                                                    <a href="restore_category.php?id=<?= $row['category_id'] ?>"
                                                    onclick="return confirm('Khôi phục danh mục này?')">
                                                    <i class="fas fa-undo text-success me-3" title="Khôi phục"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="delete_category.php?id=<?= $row['category_id'] ?>"
                                                    onclick="return confirm('Xác nhận xóa danh mục này?')">
                                                    <i class="fas fa-trash-alt text-danger me-3" title="Xóa"></i>
                                                    </a>
                                                <?php } ?>
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
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">&laquo;</a>
                                        </li>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- ⏭ Trang cuối -->
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

<?php
require("includes/footer.php");
?>