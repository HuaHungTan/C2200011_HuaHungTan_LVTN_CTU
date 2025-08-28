<?php
$conn = mysqli_connect('localhost',  'root', '', 'lvtn_book');

if (!$conn) {
    die("Kết nối thất bại: " .mysqli_connect_error());
}
?>