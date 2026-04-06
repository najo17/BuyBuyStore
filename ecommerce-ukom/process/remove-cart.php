<?php
session_start();

$id = $_POST['product_id'];
unset($_SESSION['cart'][$id]);

header("Location: ../customer/cart.php");
exit();