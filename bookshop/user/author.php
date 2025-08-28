<?php session_start(); ?>
<?php 
include("includes/header.php"); 
require('../database/conn.php');
?>

<?php
$author_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 8;
$offset = ($page - 1) * $limit;

// ✅ Thông tin tác giả
$author_query = mysqli_query($conn, "SELECT * FROM authors WHERE author_id = $author_id AND is_deleted = 0");
$author_data = mysqli_fetch_assoc($author_query);

if (!$author_data) {
  echo "<div class='container my-4 text-center'>❌ Không tìm thấy tác giả.</div>";
  include 'footer.php';
  exit;
}

// ✅ Tổng sách của tác giả
$count = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) AS total FROM books WHERE author_id = $author_id AND is_deleted = 0
"))['total'];
$total_pages = ceil($count / $limit);

// ✅ Danh sách sách + khuyến mãi nếu có
$sql_books = "
  SELECT b.book_id, b.name, b.price_out, b.price_discount, bi.img_url,
         d.discount_percent
  FROM books b
  JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
  LEFT JOIN discount d ON b.book_id = d.book_id AND d.is_deleted = 0
                        AND CURDATE() BETWEEN d.start_date AND d.end_date
  WHERE b.author_id = $author_id AND b.is_deleted = 0
  ORDER BY b.book_id DESC
  LIMIT $limit OFFSET $offset
";
$books = mysqli_query($conn, $sql_books);
?>

<div class="container-fluid my-4">
  <h4 class="fw-bold text-primary mb-3">📘 Thông tin tác giả</h4>
  <div class="row">
    <div class="col-md-3 text-center">
      <img src="../<?= $author_data['img_url'] ?>" alt="<?= $author_data['name'] ?>"
           style="width:160px; height:160px; object-fit:cover; border-radius:50%; border:3px solid #0d6efd;">
    </div>
    <div class="col-md-9">
      <h5 class="fw-bold"><?= $author_data['name'] ?></h5>
      <p><strong>Quốc tịch:</strong> <?= $author_data['nationality'] ?></p>
      <p class="text-muted"><strong>Mô tả:</strong> <?= $author_data['bio'] ?></p>
      <p><strong>Tổng số sách:</strong> <?= $count ?></p>
    </div>
  </div>
</div>

<div class="container-fluid my-4">
  <h4 class="fw-bold text-danger mb-3">📚 Sách của <?= $author_data['name'] ?> (<?= $count ?>)</h4>
  <div class="row g-4">
    <?php while ($row = mysqli_fetch_assoc($books)) { 
      $out = floatval($row['price_out']);
      $discount = floatval($row['price_discount']);
      $percent = floatval($row['discount_percent']);
    ?>
      <div class="col-md-3">
        <a href="productdetails.php?id=<?= $row['book_id'] ?>" style="text-decoration: none; color: inherit;">
          <div class="card h-100 text-center">
            <img src="../<?= $row['img_url'] ?>" alt="<?= $row['name'] ?>" class="card-img-top"
                 style="height:280px; object-fit:cover; border-radius:4px;">
            <div class="card-body">
              <h6 class="card-title"><?= $row['name'] ?></h6>
              <?php if ($discount > 0 && $discount < $out): ?>
                <p class="text-danger fw-bold mb-1">
                  <?= number_format($discount) ?>₫
                  <?php if ($percent): ?>
                    <span class="badge bg-success ms-1">-<?= $percent ?>%</span>
                  <?php endif; ?>
                </p>
                <p class="text-muted small text-decoration-line-through"><?= number_format($out) ?>₫</p>
              <?php else: ?>
                <p class="text-danger fw-bold mb-1"><?= number_format($out) ?>₫</p>
              <?php endif; ?>
              <button class="btn btn-sm btn-outline-primary">Xem chi tiết</button>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
    <?php if (mysqli_num_rows($books) == 0): ?>
      <div class="col-12 text-center text-muted">Hiện tại chưa có sách nào được hiển thị.</div>
    <?php endif; ?>
  </div>

  <!-- 🔢 Phân trang -->
  <?php if ($total_pages > 1): ?>
    <nav class="mt-4">
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?id=<?= $author_id ?>&page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>