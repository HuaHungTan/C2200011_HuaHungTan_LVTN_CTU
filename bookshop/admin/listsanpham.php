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
<?

?>
<div>
    <h3 class="mb-3"><a href="index.php">Bảng điều khiển</a>><a href="listsanpham.php">Tất cả sách</a></h3>
    
    <form method="get" class="row g-3 mb-4">

    
    <div class="">
        <!-- Thể loại -->
        <label class="form-label">Thể loại:</label>
        <select name="category_id" class="form-select">
        <option value="">Tất cả</option>
        <?php
        $cats = mysqli_query($conn, "SELECT category_id, name FROM categories WHERE is_deleted = 0");
        while ($cat = mysqli_fetch_assoc($cats)) {
            $selected = ($_GET['category_id'] ?? '') == $cat['category_id'] ? 'selected' : '';
            echo "<option value='{$cat['category_id']}' $selected>{$cat['name']}</option>";
        }
        ?>
        </select>
        <!-- Tác giả -->
        <label class="form-label">Tác giả:</label>
        <select name="author_id" class="form-select">
        <option value="">Tất cả</option>
        <?php
        $authors = mysqli_query($conn, "SELECT author_id, name FROM authors WHERE is_deleted = 0");
        while ($au = mysqli_fetch_assoc($authors)) {
            $selected = ($_GET['author_id'] ?? '') == $au['author_id'] ? 'selected' : '';
            echo "<option value='{$au['author_id']}' $selected>{$au['name']}</option>";
        }
        ?>
        </select>
        <!-- Mức giá -->
        <label class="form-label">Giá:</label>
        <select name="price_range" class="form-select">
        <option value="">Tất cả</option>
        <option value="lt100" <?= ($_GET['price_range'] ?? '') == 'lt100' ? 'selected' : '' ?>>Dưới 100K</option>
        <option value="btw100_300" <?= ($_GET['price_range'] ?? '') == 'btw100_300' ? 'selected' : '' ?>>100K – 300K</option>
        <option value="gt300" <?= ($_GET['price_range'] ?? '') == 'gt300' ? 'selected' : '' ?>>Trên 300K</option>
        </select>
        <!-- Khuyến mại -->
        <label class="form-label">Khuyến mại:</label>
        <select name="has_discount" class="form-select">
        <option value="">Tất cả</option>
        <option value="1" <?= ($_GET['has_discount'] ?? '') == '1' ? 'selected' : '' ?>>Có</option>
        <option value="0" <?= ($_GET['has_discount'] ?? '') == '0' ? 'selected' : '' ?>>Không</option>
        </select>
        <!-- Trạng thái -->
        <label class="form-label">Trạng thái:</label>
        <select name="is_deleted" class="form-select">
        <option value="">Tất cả</option>
        <option value="0" <?= ($_GET['is_deleted'] ?? '') == '0' ? 'selected' : '' ?>>Hiển thị</option>
        <option value="1" <?= ($_GET['is_deleted'] ?? '') == '1' ? 'selected' : '' ?>>Đã ẩn</option>
        </select>
        <!-- Nút lọc -->
        <button type="submit" class="btn btn-primary px-4">🔍 Lọc sách</button>
        <a href="listsanpham.php" class="btn btn-secondary ms-2">❌ Xóa lọc</a>
    </div>
    </form>

    <br>
    <br>
    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">TẤT CẢ SÁCH</h6>
                            <a class="d-flex justify-content-end text-decoration-none" href="add_book.php"><button class="btn btn-success">➕Thêm sách mới</button></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-dark fw-normal" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Mã sách</th>
                                            <th class="text-center">Bìa sách</th>
                                            <th class="text-center">Tên sách</th>
                                            <th class="text-center">Mô tả</th>
                                            <th class="text-center">Giá nhập</th>
                                            <th class="text-center">Giá bán</th>
                                            <th class="text-center">Khuyến mại</th>
                                            <th class="text-center">Giá khuyến mại</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-center">Danh mục</th>                                            
                                            <th class="text-center">Tác giả</th>
                                            <th class="text-center">Nhà xuất bản</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center">Mã sách</th>
                                            <th class="text-center">Bìa sách</th>
                                            <th class="text-center">Tên sách</th>
                                            <th class="text-center">Mô tả</th>
                                            <th class="text-center">Giá nhập</th>
                                            <th class="text-center">Giá bán</th>
                                            <th class="text-center">Khuyến mại</th>
                                            <th class="text-center">Giá khuyến mại</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-center">Danh mục</th>
                                            <th class="text-center">Tác giả</th>
                                            <th class="text-center">Nhà xuất bản</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th class="text-center">Quản lý</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                        // điều kiện lọc
                                        $where = "WHERE 1=1";

                                        if (!empty($_GET['category_id'])) {
                                        $where .= " AND b.category_id = " . intval($_GET['category_id']);
                                        }

                                        if (!empty($_GET['author_id'])) {
                                        $where .= " AND b.author_id = " . intval($_GET['author_id']);
                                        }

                                        if (!empty($_GET['price_range'])) {
                                        switch ($_GET['price_range']) {
                                            case 'lt100':
                                            $where .= " AND b.price_out < 100000";
                                            break;
                                            case 'btw100_300':
                                            $where .= " AND b.price_out BETWEEN 100000 AND 300000";
                                            break;
                                            case 'gt300':
                                            $where .= " AND b.price_out > 300000";
                                            break;
                                        }
                                        }

                                        if (isset($_GET['has_discount']) && $_GET['has_discount'] !== '') {
                                        if ($_GET['has_discount'] == '1') {
                                            $where .= " AND d.discount_percent IS NOT NULL AND NOW() <= d.end_date";
                                        } else {
                                            $where .= " AND (d.discount_percent IS NULL OR NOW() > d.end_date)";
                                        }
                                        }

                                        if (isset($_GET['is_deleted']) && $_GET['is_deleted'] !== '') {
                                        $where .= " AND b.is_deleted = " . intval($_GET['is_deleted']);
                                        }
                                        //phân trang
                                        $page = max(1, intval($_GET['page'] ?? 1));
                                        $limit = 10;
                                        $offset = ($page - 1) * $limit;

                                        $total_sql = "SELECT COUNT(*) AS total
                                                        FROM books b
                                                        LEFT JOIN discount d ON b.book_id = d.book_id
                                                        $where
                                                    ";
                                        $total_books = mysqli_fetch_assoc(mysqli_query($conn, $total_sql))['total'];
                                        $total_pages = ceil($total_books / $limit);

                                        // $sql_str = "SELECT 
                                        //                 b.*, 
                                        //                 c.name AS category,
                                        //                 a.name AS author_name,
                                        //                 p.name AS publisher_name,
                                        //                 bi.img_url,
                                        //                 d.discount_percent,
                                        //                 d.end_date
                                        //                 FROM books b
                                        //                 JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
                                        //                 INNER JOIN categories c ON b.category_id = c.category_id
                                        //                 INNER JOIN authors a ON b.author_id = a.author_id
                                        //                 INNER JOIN publishers p ON b.publisher_id = p.publisher_id
                                        //                 LEFT JOIN discount d ON b.book_id = d.book_id AND NOW() <= d.end_date
                                        //                 ORDER BY b.book_id
                                        //                 LIMIT $limit OFFSET $offset";    
                                        $sql_str = "SELECT b.*, c.name AS category, a.name AS author_name, p.name AS publisher_name, bi.img_url,
                                                        d.discount_percent, d.end_date
                                                        FROM books b
                                                        JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
                                                        JOIN categories c ON b.category_id = c.category_id
                                                        JOIN authors a ON b.author_id = a.author_id
                                                        JOIN publishers p ON b.publisher_id = p.publisher_id
                                                        LEFT JOIN discount d ON b.book_id = d.book_id
                                                        $where
                                                        ORDER BY b.book_id
                                                        LIMIT $limit OFFSET $offset
                                                    ";   

                                        $result = mysqli_query($conn, $sql_str);

                                        if (!$result) {
                                            die("Lỗi SQL: " . mysqli_error($conn)); // Kiểm tra lỗi nếu có
                                        }

                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;"><?= $row['book_id'] ?></td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <img src="../<?= $row['img_url'] ?>" alt="Book Image" style="max-height: 80px; max-width: 80px;">
                                                </td>
                                                <td style="vertical-align: middle;"><?= $row['name'] ?></td>
                                                <td style="vertical-align: middle;"><?= $row['description'] ?></td>
                                                <td style="vertical-align: middle;"><?= number_format($row['price_in'], 0, ',', '.') ?>₫</td>
                                                <td style="vertical-align: middle;"><?= number_format($row['price_out'], 0, ',', '.') ?>₫</td>
                                                <td style="vertical-align: middle;" class="text-success">
                                                    <?php if ($row['discount_percent']) { ?>
                                                        <?= $row['discount_percent'] ?>%
                                                    <?php } else { ?>
                                                        <span class="text-muted">Không</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-danger" style="vertical-align: middle;">
                                                    <?php if ($row['discount_percent']) {
                                                        $discounted = $row['price_out'] * (1 - $row['discount_percent'] / 100);
                                                        echo number_format($discounted, 0, ',', '.') . "₫";
                                                    } else {
                                                        echo "<span class='text-muted'>—</span>";
                                                    } ?>
                                                </td>
                                                <td style="vertical-align: middle;"><?= $row['quantity'] ?></td>
                                                <td style="vertical-align: middle;"><?= $row['category'] ?></td>
                                                <td style="vertical-align: middle;"><?= $row['author_name'] ?></td>
                                                <td style="vertical-align: middle;"><?= $row['publisher_name'] ?></td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?php if ($row['is_deleted']) { ?>
                                                        <span class="badge bg-secondary">Đã ẩn</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-success">Hiển thị</span>
                                                    <?php } ?>
                                                </td>
                                        
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <a href="add_oldbook.php?id=<?= $row['book_id'] ?>">
                                                        <i class="fas fa-plus-circle text-success me-2" title="Nhập thêm sách"></i>
                                                    </a><br>
                                                    <a href="update_book.php?id=<?= $row['book_id'] ?>">
                                                        <i class="fas fa-edit text-primary me-3" title="Chỉnh sửa"></i>
                                                    </a><br>
                                                    <?php if ($row['is_deleted']) { ?>
                                                        <a href="restore_book.php?id=<?= $row['book_id'] ?>" 
                                                            onclick="return confirm('Khôi phục sách này?')">
                                                            <i class="fas fa-undo text-success me-3" title="Khôi phục"></i>
                                                        </a>
                                                        <?php } else { ?>
                                                        <a href="delete_book.php?id=<?= $row['book_id'] ?>" 
                                                            onclick="return confirm('Xác nhận xóa sách này?')">
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
                                <!-- Phân trang -->
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