<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

require('../database/conn.php');
require('includes/header.php');

// Truy vấn tổng quan
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE is_deleted = 0"));
$books = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM books WHERE is_deleted = 0"));
$orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE is_deleted = 0"));
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(final_amount) AS total FROM orders WHERE status = 'Hoàn thành' AND is_deleted = 0"));
$discounts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM discount WHERE is_deleted = 0 AND CURDATE() BETWEEN start_date AND end_date"));
$reviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM review WHERE status = 'Đã duyệt'"));
$reviews_all = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM review"));
// 📚 Tổng số sách trong kho (tính tổng quantity của tất cả sách)
$inventory = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total FROM books"));

// 🛒 Tổng số lượng sách đã bán (tính tổng quantity từ order_details của đơn không bị hủy)
$sold_books = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT SUM(od.quantity) AS total
  FROM order_details od
  JOIN orders o ON od.order_id = o.order_id
  WHERE o.status = 'Hoàn thành'
"));
// sách được đặt
$pending_books = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT SUM(od.quantity) AS total
  FROM order_details od
  JOIN orders o ON od.order_id = o.order_id
  WHERE o.status != 'Đã hủy' AND o.status != 'Hoàn thành'
"));

// Biểu đồ doanh thu theo tháng
$currentYear = date('Y');
$years = range($currentYear - 4, $currentYear); // Hiển thị 5 năm gần nhất
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$revenue_monthly = mysqli_query($conn,
    "SELECT MONTH(order_date) AS month, SUM(final_amount) AS total
     FROM orders
     WHERE status = 'Hoàn thành' AND YEAR(order_date) = $year AND is_deleted = 0
     GROUP BY MONTH(order_date)"
);
$monthly_data = array_fill(1, 12, 0);
while ($row = mysqli_fetch_assoc($revenue_monthly)) {
    $monthly_data[$row['month']] = $row['total'];
}

// Bảng top sách bán chạy
$best_sellers = mysqli_query($conn,
    "SELECT 
  b.book_id,
  b.name AS book_name,
  a.name AS author_name,
  bi.img_url,
  SUM(od.quantity) AS total_sold
FROM order_details od
JOIN books b ON od.book_id = b.book_id
JOIN authors a ON b.author_id = a.author_id
LEFT JOIN book_images bi ON b.book_id = bi.book_id AND bi.is_primary = 1
JOIN orders o ON od.order_id = o.order_id
WHERE o.status = 'Hoàn thành' AND o.is_deleted = 0
GROUP BY b.book_id, b.name, a.name, bi.img_url
ORDER BY total_sold DESC
LIMIT 5;"
);

// Biểu đồ tròn trạng thái đơn hàng
$status_data = mysqli_query($conn,
    "SELECT status, COUNT(*) AS total
     FROM orders
     WHERE is_deleted = 0
     GROUP BY status"
);
$labels = [];
$totals = [];
while ($row = mysqli_fetch_assoc($status_data)) {
    $labels[] = $row['status'];
    $totals[] = $row['total'];
}

//biểu đồ rating sách
$grouped_rating_data = mysqli_query($conn, "
  SELECT 
    CASE 
      WHEN rating < 1 THEN '< 1'
      WHEN rating >= 1 AND rating < 2 THEN '1–2'
      WHEN rating >= 2 AND rating < 3 THEN '2–3'
      WHEN rating >= 3 AND rating < 4 THEN '3–4'
      WHEN rating >= 4 THEN '4–5'
    END AS rating_group,
    COUNT(*) AS total
  FROM books
  WHERE is_deleted = 0 AND rating IS NOT NULL
  GROUP BY rating_group
  ORDER BY rating_group
");

$group_labels = [];
$group_totals = [];
while ($row = mysqli_fetch_assoc($grouped_rating_data)) {
  $group_labels[] =   $row['rating_group'].'⭐ ';
  $group_totals[] = $row['total'];
}
?>

<div class="container mt-4">
  <h4 class="mb-4 text-primary">📈 Thống kê tổng quan</h4>
  <div class="row text-center">
    <!-- Tổng quan -->
    <div class="col-md-4 mb-3"><div class="card shadow"><div class="card-body"><h5>👥 Người dùng</h5><h3><?= $users['total'] ?></h3></div></div></div>
    <div class="col-md-4 mb-3"><div class="card shadow"><div class="card-body"><h5>📚 Số lượng đầu sách</h5><h3><?= $books['total'] ?></h3></div></div></div>
    <div class="col-md-4 mb-3"><div class="card shadow"><div class="card-body"><h5>🧾 Đơn hàng</h5><h3><?= $orders['total'] ?></h3></div></div></div>
    <div class="col-md-4 mb-3"><div class="card shadow"><div class="card-body"><h5>💰 Tổng doanh thu</h3><h3><?= number_format($revenue['total'] ?? 0, 0, ',', '.') ?>₫</h3></div></div></div>
    <div class="col-md-4 mb-3"><div class="card shadow"><div class="card-body"><h5>🎉 Khuyến mại có hiệu lực</h5><h3><?= $discounts['total'] ?></h3></div></div></div>
    <div class="col-md-4 mb-3"><div class="card shadow"><div class="card-body"><h5>⭐ Lượt đánh giá</h5><h3>Đã duyệt <?= $reviews['total'] ?>/<?= $reviews_all['total'] ?></h3></div></div></div>
    <div class="col-md-4 mb-3">
      <div class="card shadow">
        <div class="card-body">
          <h5>📦 Sách tồn kho</h5>
          <h3><?= $inventory['total']?? 0 ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card shadow">
        <div class="card-body">
          <h5>📥 Sách đang được đặt</h5>
          <h3><?= $pending_books['total'] ?? 0 ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card shadow">
        <div class="card-body">
          <h5>📤 Sách đã bán</h5>
          <h3><?= $sold_books['total']?? 0 ?></h3>
        </div>
      </div>
    </div>
  </div>
  
<hr class="my-4" style="border-top: 6px solid #000;">
  <!-- Biểu đồ doanh thu -->
  <div>
    <h4 class="mt-5 mb-3 text-success">📊 Biểu đồ doanh thu</h4>
    <form method="GET" class="mb-3">
      <label for="yearSelect" class="me-2">Chọn năm:</label>
      <select name="year" id="yearSelect" onchange="this.form.submit()">
        <?php foreach ($years as $y): ?>
          <option value="<?= $y ?>" <?= ($y == ($_GET['year'] ?? $currentYear)) ? 'selected' : '' ?>><?= $y ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <canvas id="revenueChart" height="100"></canvas>
<hr class="my-4" style="border-top: 6px solid #000;">

  <!-- Bảng sách bán chạy -->
  <h4 class="mt-5 mb-3 text-danger">🏆 Top sách bán chạy</h4>
  <table class="table table-striped table-bordered text-dark fw-normal">
    <thead>
      <tr>
        <th>Ảnh bìa</th>
        <th>Tên sách</th>
        <th>Tác giả</th>
        <th>Đã bán</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($best_sellers)): ?>
        <tr>
          <td class="text-center">
            <img src="../<?= $row['img_url'] ?: 'data_image/book/default.jpg' ?>" 
                alt="ảnh bìa" style="max-height:80px; max-width:60px; object-fit:cover;">
          </td>
          <td class="align-middle"><?= $row['book_name'] ?></td>
          <td class="align-middle"><?= $row['author_name'] ?></td>
          <td class="align-middle"><?= $row['total_sold'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<hr class="my-4" style="border-top: 6px solid #000;">

<h4 class="mt-4 mb-3 text-warning text-center">🥧 Thống kê đơn hàng & rating sách</h4>
<div class="row text-center">
  <!-- Biểu đồ trạng thái đơn hàng -->
  <div class="col-md-6 mb-3">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="text-primary mb-3">📦 Phân loại đơn hàng</h5>
        <canvas id="orderStatusChart" width="220" height="220"></canvas>
      </div>
    </div>
  </div>

  <!-- Biểu đồ rating sách -->
  <div class="col-md-6 mb-3">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="text-success mb-3">⭐ Phân bố rating sách</h5>
        <canvas id="bookRatingChart" width="220" height="220"></canvas>
      </div>
    </div>
  </div>
</div>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Biểu đồ doanh thu
  const ctx = document.getElementById('revenueChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Th1','Th2','Th3','Th4','Th5','Th6','Th7','Th8','Th9','Th10','Th11','Th12'],
      datasets: [{
        label: 'Doanh thu (₫)',
        data: <?= json_encode(array_values($monthly_data)) ?>,
        backgroundColor: '#28a745'
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // Biểu đồ trạng thái đơn hàng
  const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
  new Chart(statusCtx, {
    type: 'pie',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        data: <?= json_encode($totals) ?>,
        backgroundColor: ['#007bff', '#ffc107', '#28a745', '#17a2b8', '#dc3545']
      }]
    },
    options: {
      plugins: {
        legend: { position: 'left' }
      }
    }
  });
  // biểu đồ rating
 const bookRatingCtx = document.getElementById('bookRatingChart').getContext('2d');
  new Chart(bookRatingCtx, {
    type: 'pie',
    data: {
      labels: <?= json_encode($group_labels) ?>,
      
      datasets: [{
        data: <?= json_encode($group_totals) ?>,
        backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#17a2b8']
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'left'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return `${context.label}: ${context.parsed} sách`;
            }
          }
        }
      }
    }
  });
</script>

<?php require('includes/footer.php'); ?>