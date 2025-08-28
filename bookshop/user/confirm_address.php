<?php
session_start();
$address = trim($_POST['shipping_address'] ?? '');

if ($address !== '') {
  $_SESSION['shipping_address'] = $address;
}

header("Location: cart.php");
exit;