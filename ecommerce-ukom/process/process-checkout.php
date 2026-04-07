<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'];
$address = mysqli_real_escape_string($conn, trim($_POST['address']));
$cart = $_SESSION['cart'] ?? [];

/* ================= AMBIL DATA USER ================= */
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$customer_id'");
$user = mysqli_fetch_assoc($user_query);

$customer_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];

if(empty($cart)){
    header("Location: ../customer/cart.php");
    exit();
}

/* ================= VALIDASI ADDRESS ================= */
if(empty($address)){
    $_SESSION['error'] = "Please fill your address first!";
    header("Location: ../customer/profile.php");
    exit();
}

/* ================= VALIDASI PAYMENT PROOF ================= */
if($payment_method === 'transfer'){
    if(!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== 0){
        $_SESSION['error'] = "Please upload payment proof for transfer!";
        header("Location: ../customer/checkout.php");
        exit();
    }
}

/* ================= VALIDASI STOK DULU ================= */
foreach($cart as $id => $qty){

    $id = (int)$id;
    $qty = (int)$qty;

    $query = mysqli_query($conn,"SELECT stock FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($query);

    if(!$product || $qty > $product['stock']){
        $_SESSION['error'] = "Stock not enough!";
        header("Location: ../customer/cart.php");
        exit();
    }
}

/* ================= HITUNG TOTAL ================= */
$total = 0;

foreach($cart as $id => $qty){
    $id = (int)$id;
    $qty = (int)$qty;

    $query = mysqli_query($conn,"SELECT price FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($query);

    $total += $product['price'] * $qty;
}

/* ================= STATUS ================= */
$status = ($payment_method == 'transfer') ? 'pending' : 'approved';

/* ================= MULAI TRANSACTION DATABASE ================= */
mysqli_begin_transaction($conn);

try {

    /* SIMPAN KE TRANSACTIONS */
    mysqli_query($conn,"INSERT INTO transactions 
    (customer_id, customer_name, total, payment_method, status, shipping_address, created_at)
    VALUES
    ('$customer_id','$customer_name','$total','$payment_method','$status','$address', NOW())
    ");

    $transaction_id = mysqli_insert_id($conn);

    /* UPLOAD PAYMENT PROOF */
    if($payment_method === 'transfer' 
       && isset($_FILES['payment_proof']) 
       && $_FILES['payment_proof']['error'] === 0){

        $filename = time() . '_' . $_FILES['payment_proof']['name'];
        $target = '../assets/payment_proof/' . $filename;

        if(move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target)){
            mysqli_query($conn,"UPDATE transactions 
            SET payment_proof='$filename' 
            WHERE id=$transaction_id");
        }
    }

    /* ================= LOOP PRODUK ================= */
    foreach($cart as $id => $qty){

        $id = (int)$id;
        $qty = (int)$qty;

        $query = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
        $product = mysqli_fetch_assoc($query);

        $subtotal = $product['price'] * $qty;

        /* SIMPAN KE SALES */
        mysqli_query($conn,"INSERT INTO sales 
        (transaction_id, product_id, product_name, quantity, subtotal, created_at)
        VALUES
        ('$transaction_id','$id','{$product['name']}','$qty','$subtotal', NOW())
        ");

        /* KURANGI STOCK */
        mysqli_query($conn,"
            UPDATE products 
            SET stock = stock - $qty 
            WHERE id=$id
        ");
    }

    /* COMMIT */
    mysqli_commit($conn);

} catch (Exception $e) {

    mysqli_rollback($conn);
    $_SESSION['error'] = "Transaction failed!";
    header("Location: ../customer/cart.php");
    exit();
}

/* ================= KOSONGKAN CART ================= */
unset($_SESSION['cart']);

$_SESSION['success'] = "Order successful!";
header("Location: ../customer/history.php");
exit();