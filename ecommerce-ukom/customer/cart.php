<?php
session_start(); // Memulai session untuk menyimpan data user (login, cart, dll)
include "../config/database.php"; // Menghubungkan ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    $_SESSION['error'] = "Please log in first !"; // Simpan pesan error
    header("Location: index.php"); // Redirect ke halaman login
    exit(); // Hentikan eksekusi
}

// Ambil data cart dari session (jika tidak ada, default array kosong)
$cart = $_SESSION['cart'] ?? [];
$total = 0; // Variabel total (belum digunakan di sini)

// Hitung total item dalam cart
$total_item = 0;
foreach($cart as $qty){
    $total_item += $qty; // Menjumlahkan semua quantity produk
}
?>

    
<!DOCTYPE html>
<html>
<head>
<title>Cart - BuyBuy</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Import Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Import Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Jersey+20&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Simonetta:ital,wght@0,400;0,900;1,400;1,900&display=swap" rel="stylesheet">

<style>
/* Styling dasar body */
body{ background:#ffff; }

/* Navbar */
.navbar {
    background:#FFA4A4;
}

/* Logo */
.navbar-brand {
    font-weight: 700;
    color: white !important;
    font-size: 35px;
    font-family: Simonetta;
}

/* Icon navbar */
.nav-icon {
    color:white;
    font-size:20px;
    margin-left:20px;
    position:relative;
    text-decoration:none;
}

.nav-icon:hover {
    color:#fff;
    opacity:0.8;
}

/* Badge jumlah cart */
.cart-badge {
    position:absolute;
    top:-5px;
    right:-10px;
    background:white;
    color: #FFA4A4;
    font-size:12px;
    border-radius:50%;
}

/* Wrapper tab */
.tabs-wrapper{
    position:relative;
    margin-top:40px;
    margin-bottom:40px;
}

/* Tombol back */
.back-btn{
    position:absolute;
    left:0;
    top:0;
    font-size:22px;
}

.back-btn a{
    color:black;
    text-decoration:none;
}

/* Tab navigasi */
.cart-tabs{
    display:flex;
    justify-content:center;
    gap:70px;
    font-weight:500;
}

.cart-tabs a{
    text-decoration:none;
    color:#444;
    position:relative;
    padding-bottom:5px;
}

/* Tab aktif */
.cart-tabs a.active{
    color:black;
}

.cart-tabs a.active::after{
    content:"";
    position:absolute;
    left:0;
    bottom:0;
    width:100%;
    height:2px;
    background:black;
}

/* Card produk */
.cart-card{
    background:white;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

/* Tombol quantity */
.qty-btn{
    border:none;
    background:#FFA4A4;
    color:white;
    width:30px;
    height:30px;
    border-radius:50%;
    gap: 20px;
}

/* Tombol delete */
.delete-btn{
    background:#ff6b6b;
    color:white;
    border:none;
    width:35px;
    height:35px;
    border-radius:50%;
}

/* Gambar produk */
.product-img{
    width:100px;
    height:100px;
    object-fit:cover;
    border-radius:10px;
}
</style>
</head>

<body>

<?php 
// Ambil ulang cart dari session
$cart = $_SESSION['cart'] ?? [];

// Hitung total item menggunakan array_sum
$cart_count = array_sum($cart);
?>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-5" style="background:#FFA4A4;">
    <a class="navbar-brand text-white fw-bold" href="index.php">BuyBuy</a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Icon cart -->
        <a href="cart.php" class="text-white fs-4 position-relative me-3">
            <i class="bi bi-cart"></i>
        </a>

        <!-- Logout -->
        <a href="../auth/logout.php" class="text-white fs-4">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</nav>

<div class="container mt-5">

    <!-- BACK + TABS -->
    <div class="tabs-wrapper">

        <!-- BACK BUTTON -->
        <div class="back-btn">
            <a href="index.php">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>

        <!-- TABS -->
        <div class="cart-tabs">
            <!-- Menentukan tab aktif berdasarkan nama file -->
            <a href="cart.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">Cart</a>
            <a href="checkout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'checkout.php' ? 'active' : '' ?>">Checkout</a>
            <a href="history.php" class="<?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">History Transaction</a>
        </div>

    </div>

    <!-- Jika cart kosong -->
    <?php if(empty($cart)): ?>
        <div class="alert alert-warning">Cart is empty : ( </div>
    <?php else: ?>

    <!-- Loop semua produk dalam cart -->
    <?php foreach($cart as $id => $qty): 

        // Ambil data produk dari database berdasarkan id
        $query = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
        $product = mysqli_fetch_assoc($query);

        // Jika produk tidak ditemukan, hapus dari cart
        if(!$product){
            unset($_SESSION['cart'][$id]);
            continue;
        }

        // Ambil stock produk
        $stock = $product['stock'];

        // Cek apakah stok habis
        $isOut = $stock <= 0;
    ?>

    <div class="cart-card">

        <!-- LEFT (Gambar + info produk) -->
        <div class="d-flex align-items-center gap-3">
            <img src="../assets/uploads/<?= $product['image']; ?>" class="product-img">

            <div>
                <strong><?= $product['name']; ?></strong><br>
                <!-- Format harga -->
                Rp <?= number_format($product['price'],0,',','.'); ?>
            </div>
        </div>

        <!-- RIGHT (Aksi) -->
        <div class="d-flex align-items-center gap-3">

            <!-- FORM UPDATE QUANTITY -->
            <form method="POST" action="../process/update-cart.php" class="d-flex align-items-center gap-2">
                <input type="hidden" name="product_id" value="<?= $id; ?>">

                <!-- Tombol kurangi -->
                <button name="decrease" class="qty-btn"
                    <?= $qty <= 1 ? 'disabled' : '' ?>>
                    -
                </button>

                <!-- Jumlah -->
                <span><?= $qty; ?></span>

                <!-- Tombol tambah -->
                <button name="increase" class="qty-btn"
                    <?= $qty >= $stock ? 'disabled' : '' ?>>
                    +
                </button>
            </form>

            <!-- Jika stok habis -->
            <?php if($isOut): ?>
                <span class="badge bg-danger">Out of Stock</span>
            <?php endif; ?>

            <!-- FORM DELETE -->
            <form method="POST" action="../process/remove-cart.php">
                <input type="hidden" name="product_id" value="<?= $id; ?>">
                <button class="delete-btn">
                    <i class="bi bi-trash"></i>
                </button>
            </form>

        </div>

    </div>

    <?php endforeach; ?>

    <!-- TOTAL ITEM + CHECKOUT BUTTON -->
    <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-white rounded shadow-sm">
        <h5 class="mb-0">Total Item: <?= $total_item; ?></h5>

        <!-- Tombol checkout -->
        <a href="checkout.php" class="btn btn-dark px-4">
            Proceed to Checkout
        </a>
    </div>

    <?php endif; ?>

</div>

</body>
</html>
