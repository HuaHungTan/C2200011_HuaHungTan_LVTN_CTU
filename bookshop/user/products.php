<?php
session_start();
include("includes/header.php");
require('../database/conn.php');

// ✅ Hàm bỏ dấu tiếng Việt
function remove_accents($str) {
  $accents = [
    'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
    'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
    'ì','í','ị','ỉ','ĩ',
    'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
    'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
    'ỳ','ý','ỵ','ỷ','ỹ',
    'đ',
    'À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
    'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
    'Ì','Í','Ị','Ỉ','Ĩ',
    'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
    'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
    'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
    'Đ'
  ];
  $replacements = [
    'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
    'e','e','e','e','e','e','e','e','e','e','e',
    'i','i','i','i','i',
    'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
    'u','u','u','u','u','u','u','u','u','u','u',
    'y','y','y','y','y',
    'd',
    'A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
    'E','E','E','E','E','E','E','E','E','E','E',
    'I','I','I','I','I',
    'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
    'U','U','U','U','U','U','U','U','U','U','U',
    'Y','Y','Y','Y','Y',
    'D'
  ];
  return strtolower(str_replace($accents, $replacements, $str));
}

// ⏱ Phân trang
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 18;
$offset = ($page - 1) * $limit;

// 🔎 Nhận bộ lọc
$category_id = intval($_GET['category'] ?? 0);
$author_id = intval($_GET['author'] ?? 0);
$price_group = intval($_GET['price_group'] ?? 0);
$discount = intval($_GET['discount'] ?? 0);
$keyword = trim($_GET['keyword'] ?? '');
$keyword = preg_replace('/[.,!?]+$/u', '', $keyword);


// ➕ Nếu truyền từ index.php
if (isset($_GET['category_id']) && $_GET['category_id'] > 0) {
  $category_id = intval($_GET['category_id']);
}

// 🧠 Tạo điều kiện lọc ban đầu
$where = "b.is_deleted = 0";
if ($category_id) $where .= " AND b.category_id = $category_id";
if ($author_id) $where .= " AND b.author_id = $author_id";
if ($price_group == 1) $where .= " AND b.price_out < 100000";
elseif ($price_group == 2) $where .= " AND b.price_out BETWEEN 100000 AND 300000";
elseif ($price_group == 3) $where .= " AND b.price_out > 300000";
if ($discount == 1) $where .= " AND b.price_discount IS NOT NULL AND b.price_discount > 0";
elseif ($discount == 2) $where .= " AND (b.price_discount IS NULL OR b.price_discount = 0)";

// 📦 Truy vấn toàn bộ sách phù hợp filter (chưa lọc từ khóa)
$sql_books = "SELECT b.book_id, b.name, b.price_out, b.price_discount, bi.img_url
              FROM books b
              JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
JOIN categories c ON b.category_id = c.category_id
              JOIN authors a ON b.author_id = a.author_id
              JOIN publishers p ON b.publisher_id = p.publisher_id
              WHERE $where AND c.is_deleted = 0
                          AND a.is_deleted = 0
                          AND p.is_deleted = 0
              ORDER BY b.book_id DESC
            ";
$books_raw = mysqli_query($conn, $sql_books);

// 🔍 Lọc theo từ khóa không dấu nếu có
$books = [];
$keyword_ascii = remove_accents($keyword);
while ($row = mysqli_fetch_assoc($books_raw)) {
  $name_ascii = remove_accents($row['name']);
  if (!$keyword || strpos($name_ascii, $keyword_ascii) !== false) {
    $books[] = $row;
  }
}

// 🔢 Phân trang thủ công trên mảng $books
$total_books = count($books);
$total_pages = ceil($total_books / $limit);
$books = array_slice($books, $offset, $limit);

// 📚 Lấy dữ liệu thể loại và tác giả
$categories = mysqli_query($conn, "SELECT category_id, name FROM categories WHERE is_deleted = 0");
$authors = mysqli_query($conn, "SELECT author_id, name FROM authors WHERE is_deleted = 0");
?>

<!-- 🔧 Bộ lọc -->
<div class="container my-4">
  <h4 class="fw-bold text-primary mb-3">🔎 Bộ lọc tìm kiếm</h4>
  <form method="GET" class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Thể loại</label>
      <select name="category" class="form-select">
        <option value="0">Tất cả</option>
        <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
          <option value="<?= $row['category_id'] ?>" <?= ($category_id == $row['category_id']) ? 'selected' : '' ?>>
            <?= $row['name'] ?>
          </option>
        <?php } ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Tác giả</label>
      <select name="author" class="form-select">
        <option value="0">Tất cả</option>
        <?php while ($row = mysqli_fetch_assoc($authors)) { ?>
          <option value="<?= $row['author_id'] ?>" <?= ($author_id == $row['author_id']) ? 'selected' : '' ?>>
            <?= $row['name'] ?>
          </option>
        <?php } ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Giá</label>
      <select name="price_group" class="form-select">
        <option value="0">Tất cả</option>
        <option value="1" <?= ($price_group == 1) ? 'selected' : '' ?>>Dưới 100.000₫</option>
        <option value="2" <?= ($price_group == 2) ? 'selected' : '' ?>>100.000₫ – 300.000₫</option>
        <option value="3" <?= ($price_group == 3) ? 'selected' : '' ?>>Trên 300.000₫</option>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Khuyến mại</label>
      <select name="discount" class="form-select">
        <option value="0">Tất cả</option>
        <option value="1" <?= ($discount == 1) ? 'selected' : '' ?>>Có</option>
<option value="2" <?= ($discount == 2) ? 'selected' : '' ?>>Không</option>
      </select>
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-primary">🔍 Lọc</button>
      <a href="products.php" class="btn btn-outline-secondary">🧹 Xóa bộ lọc</a>
    </div>
  </form>
</div>

<!-- 📚 Kết quả -->
<div class="container my-4">
  <?php if ($keyword): ?>
    <h4 class="fw-bold text-primary mb-3">🔍 Kết quả tìm kiếm: "<?= htmlspecialchars($keyword) ?>" (<?= $total_books ?> sách)</h4>
  <?php else: ?>
    <h4 class="fw-bold text-danger mb-3">📚 Tất cả sách (<?= $total_books ?>)</h4>
  <?php endif; ?>

  <div class="row row-cols-1 row-cols-md-6 g-4">
    <?php foreach ($books as $row) { ?>
      <div class="col">
        <a href="productdetails.php?id=<?= $row['book_id'] ?>" style="text-decoration: none; color: inherit;">
          <div class="card h-100 text-center">
            <img src="../<?= $row['img_url'] ?>" alt="<?= $row['name'] ?>" class="card-img-top"
                 style="height:200px; object-fit:cover; border-radius:4px;">
            <div class="card-body">
              <h6 class="card-title text-truncate"><?= $row['name'] ?></h6>
              <?php
                $out = $row['price_out'];
                $discount = $row['price_discount'];
                if ($discount && $discount < $out) {
                  $percent = round((($out - $discount) / $out) * 100);
                  echo "<p class='text-danger fw-bold mb-1'>" . number_format($discount) . "₫ 
                        <span class='badge bg-warning text-dark'>-{$percent}%</span></p>
                        <p class='text-muted small text-decoration-line-through'>" . number_format($out) . "₫</p>";
                } else {
                  echo "<p class='text-danger fw-bold mb-1'>" . number_format($out) . "₫</p>";
                }
              ?>
              <button class="btn btn-sm btn-outline-primary">Xem chi tiết</button>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>

    <?php if (count($books) == 0): ?>
      <div class="col-12 text-center text-muted">Không tìm thấy sách phù hợp.</div>
    <?php endif; ?>
  </div>

  <!-- 🔢 Phân trang -->
  <?php if ($total_pages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">

      <!-- ◀️ Trang trước -->
      <li class="page-item <?= ($page <= 1 ? 'disabled' : '') ?>">
        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" title="Trang trước">&lt;</a>
      </li>

      <!-- ⏮ Trang đầu -->
      <li class="page-item <?= ($page == 1 ? 'disabled' : '') ?>">
        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" title="Trang đầu">&laquo;</a>
      </li>

      <!-- Các số trang -->
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
<li class="page-item <?= ($i == $page ? 'active' : '') ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>

      <!-- ⏭ Trang cuối -->
      <li class="page-item <?= ($page == $total_pages ? 'disabled' : '') ?>">
        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" title="Trang cuối">&raquo;</a>
      </li>

      <!-- ▶️ Trang sau -->
      <li class="page-item <?= ($page >= $total_pages ? 'disabled' : '') ?>">
        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" title="Trang sau">&gt;</a>
      </li>

    </ul>
  </nav>
  <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>

<!-- Tính năng giọng nói -->
<script>
  // Khởi tạo Web Speech API (SpeechRecognition)
  const searchInput = document.getElementById('search-input');
  const startVoiceSearchButton = document.getElementById('start-voice-search');
  const searchResultsDiv = document.getElementById('search-results');

  const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
  recognition.lang = 'vi-VN'; // Ngôn ngữ Tiếng Việt
  recognition.continuous = false;
  recognition.interimResults = false;

  // Bắt đầu nhận diện giọng nói khi nhấn nút
  startVoiceSearchButton.onclick = function() {
    recognition.start();
  };

  // Nhận diện kết quả từ giọng nói
  recognition.onresult = function(event) {
    const query = event.results[0][0].transcript;
    searchInput.value = query;
    searchProducts(query);
  };

  // Hàm tìm kiếm sản phẩm
  function searchProducts(query) {
    fetch('index.php', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'keyword=' + encodeURIComponent(query)
    })
    .then(response => response.text())
    .then(data => {
      searchResultsDiv.innerHTML = data;
    })
    .catch(error => {
      searchResultsDiv.innerHTML = 'Lỗi khi tìm kiếm!';
    });
  }
</script>

<!-- giữ lại keyword trên input tìm kiếm -->
<script>
  // Lấy tham số keyword từ URL
  const urlParams = new URLSearchParams(window.location.search);
  const keyword = urlParams.get('keyword');

  // Nếu có từ khóa, gán vào ô input
  if (keyword) {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
      searchInput.value = decodeURIComponent(keyword);
    }
  }
</script>