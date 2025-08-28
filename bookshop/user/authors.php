<?php
session_start();


include("includes/header.php");
require('../database/conn.php');

// Xác định trang hiện tại
$page = intval($_GET['page'] ?? 1);
$page = max($page, 1);

// Số tác giả mỗi trang
$limit = 9;
$offset = ($page - 1) * $limit;

// Tổng số tác giả
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM authors WHERE is_deleted = 0");
$total_data = mysqli_fetch_assoc($total_result);
$total_authors = intval($total_data['total']);
$total_pages = ceil($total_authors / $limit);

// Lấy tác giả theo trang
$authors = mysqli_query($conn, "
  SELECT author_id, name, bio, img_url, book_count
  FROM authors
  WHERE is_deleted = 0
  ORDER BY book_count DESC
  LIMIT $limit OFFSET $offset
");
?>

<div class="container my-5">
  <h4 class="fw-bold text-dark mb-4">👨‍💼 Danh sách tác giả</h4>
  <div class="row justify-content-center g-4">
    <?php while($row = mysqli_fetch_assoc($authors)) { ?>
      <div class="col-md-4">
        <div class="border rounded p-3 text-center h-100 shadow-sm" style="background-color: #f8f9fa;">
          <img src="../<?= $row['img_url'] ?>" alt="<?= $row['name'] ?>" 
               style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:2px solid #6c757d;">
          <h6 class="mt-3 fw-bold mb-1"><?= $row['name'] ?></h6>
          <p class="text-muted small"><?= $row['bio'] ?></p>
          <a href="author.php?id=<?= $row['author_id'] ?>" class="btn btn-sm btn-outline-dark w-100">📚 Xem sách</a>
        </div>
      </div>
    <?php } ?>
  </div>

  <!-- Phân trang -->
  <nav class="mt-5">
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page - 1 ?>">«</a>
        </li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page + 1 ?>">»</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

<?php include("includes/footer.php"); ?>