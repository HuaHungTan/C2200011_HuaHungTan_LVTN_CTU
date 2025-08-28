<?php
require_once('../database/conn.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo.jpg">
    <title>Yêu Sách - Mang tri thức đến gần bạn hơn — nơi kết nối những tâm hồn yêu đọc sách</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>

    <!-- Font Awesome CDN (nếu dùng icon FA) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Các file CSS riêng và plugin (nếu có) -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/author.css">
</head>
<body style="background-color: #f0fff0">
    <header class="py-2 shadow-sm" style="background-color: cyan;">
    <div class="container-fluid d-flex align-items-center justify-content-between">
    <!-- Logo + Tên web -->
    <a href="index.php" class="text-decoration-none">
        <div class="d-flex align-items-center gap-2">
            <img src="../assets/logo.jpg" alt="Logo" style="height:40px;">
            <h4 class="mb-0 fw-bold" style="color: hotpink;">Yêu</h4>
            <h4 class="mb-0 fw-bold text-primary">Sách</h4>
        </div>
    </a>
    <!-- Thanh tìm kiếm -->
    
    <form class="d-flex align-items-center" style="max-width: 600px;" method="GET" action="products.php">
      <div class="input-group">
        <input type="search" name="keyword" class="form-control border-start-0" placeholder="Tìm kiếm sách..." aria-label="Search" id="search-input">
        <span class="input-group-text bg-white border-end-0">
          <i class="fa fa-search text-secondary"></i>
        </span>
      </div>
      <button class="btn btn-danger ms-3 text-white" type="submit">Tìm</button>
       <!-- Thêm nút tìm kiếm bằng giọng nói -->
      <button id="start-voice-search" class="btn btn-primary"type="button">🎤</button>
    </form>
    
   
    

    <!-- Dữ liệu cho liên hệ và giỏ hàng -->
    <?php
        $cart_count = 0;
        $showDot = false;

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $admin_id = 1;

            $msgUnread = $conn->query("SELECT 1 FROM messages
                                          WHERE sender_id = $admin_id AND receiver_id = $user_id AND is_read = 0 LIMIT 1");
$showDot = ($msgUnread && $msgUnread->num_rows > 0);

          // Truy vấn giỏ hàng
          $cartIdQuery = $conn->query("SELECT cart_id FROM carts WHERE user_id = $user_id LIMIT 1");
          if ($cartIdQuery && $cartRow = $cartIdQuery->fetch_assoc()) {
            $cart_id = $cartRow['cart_id'];
            $countQuery = $conn->query("SELECT COUNT(*) AS count FROM cart_details WHERE cart_id = $cart_id");
            if ($countQuery && $countRow = $countQuery->fetch_assoc()) {
              $cart_count = $countRow['count'] ?? 0;
            }
          }
        }
    ?>
    <!-- Menu điều hướng -->
    <nav class="d-flex align-items-center gap-4">
      <a href="index.php" class="text-danger text-decoration-none fw-bold" >TRANG CHỦ</a>
      <a href="products.php" class="text-danger text-decoration-none fw-bold" id="navElement">TẤT CẢ SÁCH</a>
      <a href="authors.php" class="text-danger text-decoration-none fw-bold" id="navElement">TÁC GIẢ</a>
      <a href="about.php" class="text-danger text-decoration-none fw-bold" id="navElement">VỀ CHÚNG TÔI</a>
      <a href="contact.php" class="text-danger text-decoration-none fw-bold position-relative" id="navElement">HỖ TRỢ
        <?php if ($showDot): ?>
          <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
            <span class="visually-hidden">Tin chưa đọc</span>
          </span>
        <?php endif; ?>
      </a>
    </nav>

    <!-- Icon chức năng -->
    <div class="d-flex align-items-center gap-3 ms-4">
      <?php
      $heart_link = isset($_SESSION['user_id']) 
        ? "userprofile.php?id={$_SESSION['user_id']}#favorites" 
        : "login.php";
      ?>

      <a href="<?= $heart_link ?>" class="text-danger" title="Yêu thích">
        <i class="fa fa-heart fa-lg"></i>
      </a>
      <!-- Icon giỏ hàng với số lượng -->
      <a href="cart.php" class="text-dark position-relative" title="Giỏ hàng">
        <i class="fa fa-shopping-cart fa-lg"></i>
        <?php if ($cart_count > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $cart_count ?>
          </span>
        <?php endif; ?>
      </a>

      <div class="text-end">
        <?php if (isset($_SESSION['user_id'])) { ?>
          <a href="userprofile.php?id=<?=$_SESSION['user_id']?>">
            <img src="../<?= $_SESSION['avt'] ?: 'data_image/avatar/default.jpg' ?>"
              alt="Ảnh đại diện"
              style="width:40px; height:40px; object-fit:cover; border-radius:50%; border:1px solid #ccc;">
           <span class="ms-2 fw-bold"><?= $_SESSION['name'] ?></span>
          </a>
          
          <a href="logout.php" class="btn btn-sm btn-outline-danger ms-2">Đăng xuất</a>
        <?php } else { ?>
<a href="login.php" class="btn btn-sm btn-outline-primary">👤 Đăng nhập</a>
        <?php } ?>
      </div>

    </div>
  </div>
</header>

<script>
    // Web Speech API - Tìm kiếm bằng giọng nói
    const searchInput = document.getElementById('search-input');
    const startVoiceSearchButton = document.getElementById('start-voice-search');

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
        //searchProducts(query);
        // Chuyển hướng đến trang sản phẩm với từ khóa tìm kiếm
        window.location.href = 'products.php?keyword=' + encodeURIComponent(query);

    };
    
    // Hàm tìm kiếm sản phẩm
    // function searchProducts(query) {
    //     // Gửi yêu cầu tìm kiếm tới backend PHP
    //     fetch('index.php', {
    //         method: 'GET',
    //         headers: {
    //             'Content-Type': 'application/x-www-form-urlencoded',
    //         },
    //         body: 'keyword=' + encodeURIComponent(query)
    //     })
    //     .then(response => response.text())
    //     .then(data => {
    //         document.getElementById('search-results').innerHTML = data;
    //     })
    //     .catch(error => {
    //         document.getElementById('search-results').innerHTML = 'Lỗi khi tìm kiếm!';
    //     });
    // }
</script>
</body>
</html>
