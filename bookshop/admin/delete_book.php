<?php
session_start();
require('../database/conn.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.php");
  exit;
}

$book_id = intval($_GET['id'] ?? 0);

if ($book_id > 0) {
  mysqli_query($conn, "UPDATE books SET is_deleted = 1 WHERE book_id = $book_id");
}

header("Location: listsanpham.php");
exit;