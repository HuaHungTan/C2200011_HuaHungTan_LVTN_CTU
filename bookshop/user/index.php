<?php session_start(); ?>
<?php 
include("includes/header.php"); 
require('../database/conn.php');
?>

<main class="container-fluid py-4">

  <!-- Banner -->
  <div class="container-fluid">
    <div class="row">
      
      <!-- Danh mục sách (2 cột) -->
      <div class="col-md-2 mb-4">
        <div class="border rounded shadow-sm bg-white p-3 h-100">

          <h5 class="fw-bold text-white text-center mb-3" style="background-color: #0dcaf0; border-radius: 10px; padding: 10px;">
            📚 DANH MỤC SÁCH
          </h5>

          <div id="bookCategory">
            <ul class="list-unstyled">
              <?php 
                $sql_str = "SELECT * FROM categories where is_deleted=0 ORDER BY category_id";
                $result = mysqli_query($conn, $sql_str);
                while ($row = mysqli_fetch_assoc($result)) {
                  $name_upper = strtoupper($row['name']); // viết hoa toàn bộ
              ?>
                <li class="mb-2">
                  <a 
                    href="products.php?category_id=<?= $row['category_id'] ?>" 
                    class="d-block text-decoration-none px-2 py-1 fw-bold rounded text-dark hover-category"
                    style="text-transform: uppercase;"
                  >
                    📗 <?= $name_upper ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </div>

        </div>
      </div>

      <!-- Carousel Banner (10 cột) -->
      <div class="col-md-10">
        <div id="bannerCarousel" class="carousel slide w-100 mb-4" data-bs-ride="carousel" data-bs-interval="4000">
          <div class="carousel-inner rounded shadow-sm" style="height: 600px;">
            <div class="carousel-item active">
              <img src="../assets/banner1.jpg" class="d-block w-100 h-100" alt="Banner 1" style="object-fit: cover;">
            </div>
            <div class="carousel-item">
              <img src="../assets/banner2.jpg" class="d-block w-100 h-100" alt="Banner 2" style="object-fit: cover;">
            </div>
            <div class="carousel-item">
              <img src="../assets/banner3.jpg" class="d-block w-100 h-100" alt="Banner 3" style="object-fit: cover;">
            </div>
            <div class="carousel-item">
              <img src="../assets/banner4.jpg" class="d-block w-100 h-100" alt="Banner 4" style="object-fit: cover;">
            </div>
            <!-- Thêm ảnh nếu cần -->
          </div>

          <!-- Nút điều hướng -->
          <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>

    </div>
  </div>

<hr class="my-4" style="border-top: 6px solid #000;">

  <!-- Sách giảm giá -->
<div class="container-fluid my-4">
  <h4 class="fw-bold text-success mb-3">💸 SÁCH GIẢM GIÁ</h4>
  <div class="row justify-content-center g-4">
    <?php
    $discountedBooks = mysqli_query($conn, "
      SELECT b.book_id, b.name, b.price_out, b.price_discount, bi.img_url, d.discount_percent
      FROM books b
      JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
      LEFT JOIN discount d ON b.book_id = d.book_id 
                      AND d.is_deleted = 0 
                      AND d.start_date <= CURDATE() 
                      AND d.end_date >= CURDATE()
      WHERE b.price_discount IS NOT NULL AND b.is_deleted = 0
      ORDER BY d.discount_percent DESC, b.book_id DESC
      LIMIT 5
    ");
    while($row = mysqli_fetch_assoc($discountedBooks)) {
    ?>
      <div class="col-md-2">
        <a href="productdetails.php?id=<?= $row['book_id'] ?>" style="text-decoration: none; color: inherit;">
          <div class="card text-center h-100">
            <img src="../<?= $row['img_url'] ?>" class="card-img-top" alt="<?= $row['name'] ?>"
                 style="width:100%; height:400px; object-fit:cover; border-radius:4px;">
            <div class="card-body">
              <h6 class="card-title"><?= $row['name'] ?></h6>
              <p class="mb-1">
                <span class="text-muted" style="text-decoration: line-through;">
                  <?= number_format($row['price_out']) ?>₫
                </span>
                <span class="text-danger fw-bold ms-2" style="font-size: 1.1rem;">
                  <?= number_format($row['price_discount']) ?>₫
                </span>
                <span class="badge bg-success ms-2" style="font-size: 0.8rem;">
                  -<?= $row['discount_percent'] ?>%
                </span>
              </p>
              <button class="btn btn-sm btn-outline-primary">Xem chi tiết</button>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
  </div>
</div>

<hr class="my-4" style="border-top: 6px solid #000;">
  <!-- Sách bán chạy -->
  
  <div class="container-fluid my-4">
    <h4 class="fw-bold text-danger mb-3">📈 SÁCH BÁN CHẠY</h4>
    <div class="row justify-content-center g-4">
      <?php
      $bestSellers = mysqli_query($conn, "SELECT b.book_id, b.name, b.price_out, b.price_discount, b.sold, b.rating,
                                          bi.img_url, d.discount_percent
                                          FROM books b
                                          JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
                                          LEFT JOIN discount d ON b.book_id = d.book_id 
                                                                AND d.is_deleted = 0 
                                                                AND d.start_date <= CURDATE() 
                                                                AND d.end_date >= CURDATE()
                                          WHERE b.is_deleted = 0 AND b.quantity > 0
                                          ORDER BY b.sold DESC, b.rating DESC
                                          LIMIT 5
                                  ");
      while($row = mysqli_fetch_assoc($bestSellers)) {
        $hasDiscount = isset($row['price_discount']) && $row['price_discount'] > 0;
      ?>
        <div class="col-md-2">
          <a href="productdetails.php?id=<?= $row['book_id'] ?>" style="text-decoration: none; color: inherit;">
            <div class="card text-center h-100">
              <img src="../<?= $row['img_url'] ?>" class="card-img-top" alt="<?= $row['name'] ?>"
                  style="width:100%; height:400px; object-fit:cover; border-radius:4px;">
              <div class="card-body">
                <h6 class="card-title"><?= $row['name'] ?></h6>
                <p class="mb-0 text-muted">Đã bán: <?= $row['sold'] ?> cuốn</p>
                <p class="mb-1">
                  <?php if ($hasDiscount): ?>
                    <span class="text-muted" style="text-decoration: line-through;">
                      <?= number_format($row['price_out']) ?>₫
                    </span>
                    <span class="text-danger fw-bold ms-2" style="font-size: 1.1rem;">
                      <?= number_format($row['price_discount']) ?>₫
                    </span>
                    <span class="badge bg-success ms-2" style="font-size: 0.8rem;">
                      -<?= $row['discount_percent'] ?>%
                    </span>                    
                  <?php else: ?>
                    <span class="text-danger fw-bold" style="font-size: 1.1rem;">
                      <?= number_format($row['price_out']) ?>₫
                    </span>
                  <?php endif; ?>
                </p>
                <button class="btn btn-sm btn-outline-primary">Xem chi tiết</button>
              </div>
            </div>
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
<hr class="my-4" style="border-top: 6px solid #000;">
  <!-- Sách đề cử -->
  <div class="container-fluid my-4">
    <h4 class="fw-bold text-primary mb-3">⭐ SÁCH ĐỀ CỬ</h4>
    <div class="row justify-content-center g-4">

      <?php
        $recommend = mysqli_query($conn, "
          SELECT b.book_id, b.name, b.price_out, b.price_discount, bi.img_url, d.discount_percent
          FROM books b
          JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
          LEFT JOIN discount d ON b.book_id = d.book_id 
                      AND d.is_deleted = 0 
                      AND d.start_date <= CURDATE() 
                      AND d.end_date >= CURDATE()
          WHERE b.is_deleted = 0
          ORDER BY b.rating DESC
          LIMIT 5
        ");
        while($row = mysqli_fetch_assoc($recommend)) {
          $hasDiscount = isset($row['price_discount']) && $row['price_discount'] > 0;
      ?>
        <div class="col-md-2">
          <a href="productdetails.php?id=<?= $row['book_id'] ?>" style="text-decoration: none; color: inherit;">
            <div class="card text-center h-100">
              <img src="../<?= $row['img_url'] ?>" class="card-img-top" alt="<?= $row['name'] ?>"
                  style="width:100%; height:400px; object-fit:cover; border-radius:4px;">
              <div class="card-body">
                <h6 class="card-title"><?= $row['name'] ?></h6>
                <p class="mb-1">
                  <?php if ($hasDiscount): ?>
                    <span class="text-muted" style="text-decoration: line-through;">
                      <?= number_format($row['price_out']) ?>₫
                    </span>
                    <span class="text-danger fw-bold ms-2" style="font-size: 1.1rem;">
                      <?= number_format($row['price_discount']) ?>₫
                    </span>
                    <span class="badge bg-success ms-2" style="font-size: 0.8rem;">
                      -<?= $row['discount_percent'] ?>%
                    </span>
                  <?php else: ?>
                    <span class="text-danger fw-bold" style="font-size: 1.1rem;">
                      <?= number_format($row['price_out']) ?>₫
                    </span>
                  <?php endif; ?>
                </p>
                <button class="btn btn-sm btn-outline-primary">Xem chi tiết</button>
              </div>
            </div>
          </a>
        </div>
      <?php } ?>

    </div>
  </div>
<hr class="my-4" style="border-top: 6px solid #000;">
  <!-- Sách mới -->
  <div class="container-fluid my-4">
    <h4 class="fw-bold text-warning mb-3">🆕 SÁCH MỚI</h4>
    <div class="row justify-content-center g-4">

      <?php
        $newBooks = mysqli_query($conn, "
          SELECT b.book_id, b.name, b.price_out, b.price_discount, bi.img_url, d.discount_percent
          FROM books b
          JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
          LEFT JOIN discount d ON b.book_id = d.book_id 
                      AND d.is_deleted = 0 
                      AND d.start_date <= CURDATE() 
                      AND d.end_date >= CURDATE()
          WHERE b.is_deleted = 0
          ORDER BY b.book_id DESC
          LIMIT 5
        ");
        while($row = mysqli_fetch_assoc($newBooks)) {
          $hasDiscount = isset($row['price_discount']) && $row['price_discount'] > 0;
      ?>
        <div class="col-md-2">
          <a href="productdetails.php?id=<?= $row['book_id'] ?>" style="text-decoration: none; color: inherit;">
            <div class="card text-center h-100">
              <img src="../<?= $row['img_url'] ?>" class="card-img-top" alt="<?= $row['name'] ?>"
                  style="width:100%; height:400px; object-fit:cover; border-radius:4px;">
              <div class="card-body">
                <h6 class="card-title"><?= $row['name'] ?></h6>
                <p class="mb-1">
                  <?php if ($hasDiscount): ?>
                    <span class="text-muted" style="text-decoration: line-through;">
                      <?= number_format($row['price_out']) ?>₫
                    </span>
                    <span class="text-danger fw-bold ms-2" style="font-size: 1.1rem;">
                      <?= number_format($row['price_discount']) ?>₫
                    </span>
                    <span class="badge bg-success ms-2" style="font-size: 0.8rem;">
                      -<?= $row['discount_percent'] ?>%
                    </span>
                  <?php else: ?>
                    <span class="text-danger fw-bold" style="font-size: 1.1rem;">
                      <?= number_format($row['price_out']) ?>₫
                    </span>
                  <?php endif; ?>
                </p>
                <button class="btn btn-sm btn-outline-primary">Xem chi tiết</button>
              </div>
            </div>
          </a>
        </div>
      <?php } ?>

    </div>
  </div>
<hr class="my-4" style="border-top: 6px solid #000;">
<!-- Tác giả nổi bật -->
 <div class="container-fluid my-4">
  <h4 class="fw-bold text-dark mb-3">👨‍💼 TÁC GIẢ NỔI BẬT</h4>
  <div class="row justify-content-center g-4">
    <?php
$authors = mysqli_query($conn, "SELECT author_id, name, bio, img_url, book_count
                                FROM authors
                                ORDER BY book_count DESC
                                LIMIT 3;
                        ");
while($row = mysqli_fetch_assoc($authors)) {
?>
  <div class="col-md-3">
    <div class="border border-3 border-dark rounded p-3 text-center h-100"
          style="background-color: #f8f9fa;">
      <img src="../<?= $row['img_url'] ?>" alt="<?= $row['name'] ?>" 
           style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:2px solid #6c757d;">
      <h6 class="mt-3 fw-bold mb-1"><?= $row['name'] ?></h6>
      <p class="text-muted small"><?= $row['bio'] ?></p>
      <a href="author.php?id=<?= $row['author_id'] ?>" class="btn btn-sm btn-outline-dark">Xem sách</a>
    </div>
  </div>
<?php } ?>

  </div>
</div>
<!-- hết main -->
</main>
<?php include("includes/footer.php"); ?>