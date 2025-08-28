<?php
require('../database/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['review_id']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);

  // ✅ Cập nhật trạng thái đánh giá
  mysqli_query($conn, "UPDATE review SET status = '$status' WHERE review_id = $id");

  // ✅ Luôn cập nhật lại rating sách sau mỗi thay đổi trạng thái
  $review = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT book_id FROM review WHERE review_id = $id
  "));
  if ($review) {
    $book_id = intval($review['book_id']);

    // Tính trung bình điểm rating từ các đánh giá đã duyệt
    $avg_result = mysqli_query($conn, "
      SELECT AVG(rating) AS avg_rating 
      FROM review 
      WHERE book_id = $book_id AND status = 'Đã duyệt'
    ");

    // Nếu không còn đánh giá nào được duyệt, để rating mặc định
    if ($avg = mysqli_fetch_assoc($avg_result)) {
      $new_rating = $avg['avg_rating'] !== null ? round($avg['avg_rating'], 1) : 3.0;
      mysqli_query($conn, "
        UPDATE books SET rating = $new_rating WHERE book_id = $book_id
      ");
    }
  }
}

header("Location: listdanhgia.php");
exit;