<?php
session_start();
include "../config/database.php";

$id = $_POST['product_id'];

$query = mysqli_query($conn, "SELECT stock FROM products WHERE id=$id");
$product = mysqli_fetch_assoc($query);
$stock = $product['stock'];

if(!isset($_SESSION['cart'][$id])){
    header("Location: ../customer/cart.php");
    exit();
}

$currentQty = $_SESSION['cart'][$id];

// INCREASE
if(isset($_POST['increase'])){

    if($currentQty < $stock){
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['error'] = "Out of stock !";
    }

}

// DECREASE
if(isset($_POST['decrease'])){

    if($currentQty > 1){
        $_SESSION['cart'][$id]--;
    }

}

header("Location: ../customer/cart.php");
exit();