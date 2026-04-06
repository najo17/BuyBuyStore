<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id'])){
    $_SESSION['error'] = "Please login first!";
    header("Location: ../customer/index.php");
    exit();
}

$id = $_POST['product_id'];

$query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$product = mysqli_fetch_assoc($query);

if(!$product){
    $_SESSION['error'] = "Product not found!";
    header("Location: ../customer/index.php");
    exit();
}

if($product['stock'] <= 0){
    $_SESSION['error'] = "Product out of stock!";
    header("Location: ../customer/index.php");
    exit();
}

// cek kalau sudah ada di cart
$currentQty = $_SESSION['cart'][$id] ?? 0;

if($currentQty >= $product['stock']){
    $_SESSION['error'] = "Stock not enough!";
    header("Location: ../customer/index.php");
    exit();
}

// tambahkan ke cart
$_SESSION['cart'][$id] = $currentQty + 1;

$_SESSION['success'] = "Product added to cart!";
header("Location: ../customer/index.php");
exit();