<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php"); // Redirect ke login
    exit(); // Hentikan program
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Ambil data cart dari session
$cart = $_SESSION['cart'] ?? [];

/* ===============================
   HITUNG TOTAL
================================ */
$total = 0; // Inisialisasi total harga

// Loop setiap item di cart
foreach($cart as $id => $qty){

    // Ambil harga produk berdasarkan id
    $query = mysqli_query($conn,"SELECT price FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($query);

    // Hitung subtotal (harga x qty)
    $subtotal = $product['price'] * $qty;

    // Tambahkan ke total
    $total += $subtotal;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Checkout - BuyBuy</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Jersey+20&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Simonetta:ital,wght@0,400;0,900;1,400;1,900&display=swap" rel="stylesheet">

<style>
/* Background body */
body{ 
    background:#ffff; 
    padding-bottom:200px; /* Memberi ruang untuk fixed footer */
}

/* Navbar */
.navbar{ background:#FFA4A4; }

/* Logo */
.navbar-brand {
    font-weight: 700;
    color: white !important;
    font-size: 35px;
    font-family: Simonetta;
}

/* Icon navbar */
.nav-icon{
    color:white !important;
    font-size:22px;
    margin-left:20px;
}

/* Wrapper tabs */
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

/* Tabs */
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

/* Card checkout */
.checkout-card{
    background:white;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

/* Gambar produk */
.product-img{
    width:100px;
    height:100px;
    object-fit:cover;
    border-radius:10px;
}

/* Footer checkout (fixed bawah) */
.checkout-bottom{
    position:fixed;
    bottom:0;
    left:0;
    width:100%;
    background:#FFA4A4;
    padding:25px 60px;
    color:white;
    z-index:1000;
}

/* Tombol metode pembayaran */
.method-btn{
    border:none;
    padding:8px 25px;
    border-radius:8px 8px 0 0;
    background:#f7b6b6;
    color:white;
    font-weight:500;
}

/* Metode aktif */
.method-active{
    background:white;
    color:#FFA4A4;
}

/* Tombol order */
.order-btn{
    background:white;
    color:#FFA4A4;
    border:none;
    padding:10px 60px;
    border-radius:8px;
    font-weight:500;
}

/* Box alamat */
.address-box{
    background:white;
    color:#333;
    border-radius:10px;
    padding:10px 14px;
    min-width:250px;
    max-width:300px;
    min-height:58px;
    font-size:14px;
    line-height:1.5;
}

/* Link edit alamat */
.edit-address-link{
    color:white;
    text-decoration:underline;
    font-size:14px;
    margin-top:5px;
    display:inline-block;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-5">
    <a class="navbar-brand" href="index.php">BuyBuy</a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Icon cart -->
        <a href="cart.php" class="nav-icon">
            <i class="bi bi-cart"></i>
        </a>

        <!-- Logout -->
        <a href="../auth/logout.php" class="nav-icon">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</nav>

<div class="container mt-5">

<div class="tabs-wrapper">

    <!-- Tombol back -->
    <div class="back-btn">
        <a href="index.php">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>

    <!-- Tabs -->
    <div class="cart-tabs">
        <a href="cart.php">Cart</a>
        <a href="checkout.php" class="active">Checkout</a>
        <a href="history.php">History Transaction</a>
    </div>

</div>

<!-- Jika cart kosong -->
<?php if(empty($cart)): ?>

<div class="alert alert-warning text-center">
    Cart Is Empty
</div>

<?php else: ?>

<!-- Loop semua item cart -->
<?php foreach($cart as $id => $qty): 

    // Ambil data produk lengkap
    $query = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($query);

    // Hitung subtotal per item
    $subtotal = $product['price'] * $qty;
?>

<div class="checkout-card">
    <div class="d-flex align-items-center gap-3">

        <!-- Gambar produk -->
        <img src="../assets/uploads/<?= $product['image']; ?>" class="product-img">

        <!-- Info produk -->
        <div>
            <strong><?= $product['name']; ?></strong><br>
            Qty: <?= $qty; ?>
        </div>
    </div>

    <!-- Subtotal -->
    <strong>
        Rp <?= number_format($subtotal,0,',','.'); ?>
    </strong>
</div>

<?php endforeach; ?>
<?php endif; ?>

</div>

<!-- FORM START -->
<form method="POST" action="../process/process-checkout.php" enctype="multipart/form-data">

<div class="checkout-bottom">

    <!-- Pilih metode pembayaran -->
    <div class="mb-3">
        <button type="button" id="btnTransfer" class="method-btn method-active">
            Transfer
        </button>

        <button type="button" id="btnCOD" class="method-btn">
            COD
        </button>

        <!-- Hidden input untuk kirim metode pembayaran -->
        <input type="hidden" name="payment_method" id="paymentMethod" value="transfer">
    </div>

    <!-- Info transfer -->
    <div id="transferSection" class="mb-3">
        <em>Transfer Here : 087864200621 ( Via Dana )</em>
    </div>

    <div class="d-flex align-items-center justify-content-between">

        <div class="d-flex gap-3 align-items-center">

            <!-- ADDRESS OTOMATIS -->
            <div>
                <div class="address-box">

    <!-- Jika alamat ada -->
    <?php if(!empty($user['address'])): ?>

        <strong><?= htmlspecialchars(!empty($user['full_name']) ? $user['full_name'] : $user['username']); ?></strong><br>

        <?= nl2br(htmlspecialchars($user['address'])); ?>

    <?php else: ?>

        <!-- Jika belum isi alamat -->
        <span class="text-danger">Fill Address First</span>

    <?php endif; ?>

</div>

                <!-- Link edit alamat -->
                <a href="profile.php" class="edit-address-link">
                    Edit Address
                </a>
            </div>

            <!-- Hidden alamat untuk dikirim ke server -->
            <input type="hidden" name="address" value="<?= htmlspecialchars($user['address'] ?? ''); ?>">

            <!-- Upload bukti pembayaran -->
            <div id="uploadSection">
                <input type="file"
                       name="payment_proof"
                       class="form-control"
                       style="width:250px;">
            </div>

        </div>

        <div class="d-flex align-items-center gap-4">

            <!-- Total harga -->
            <strong>
                Total : Rp <?= number_format($total,0,',','.'); ?>
            </strong>

            <!-- Jika alamat kosong -->
            <?php if(empty($user['address'])): ?>

                <a href="profile.php" class="order-btn text-decoration-none d-inline-flex align-items-center">
                    Fill Address First
                </a>

            <?php else: ?>

                <!-- Tombol order (trigger modal) -->
                <button type="button" 
                        class="order-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmModal">
                    Order
                </button>

            <?php endif; ?>

        </div>

    </div>

</div>

<!-- MODAL KONFIRMASI -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Confirm Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- Tampilkan total -->
        <p><strong>Total:</strong> Rp <?= number_format($total,0,',','.'); ?></p>

        <!-- Tampilkan metode pembayaran -->
        <p><strong>Payment Method:</strong> 
            <span id="modalPaymentMethod">Transfer</span>
        </p>

        <!-- Tampilkan alamat -->
        <p><strong>Address:</strong><br>
            <?= !empty($user['address']) ? nl2br(htmlspecialchars($user['address'])) : '-' ?>
        </p>

      </div>

      <div class="modal-footer">

        <!-- Cancel -->
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
        </button>

        <!-- Submit form -->
        <button type="submit" class="btn btn-danger">
            Confirm Order
        </button>

      </div>

    </div>
  </div>
</div>

</form>
<!-- FORM END -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Ambil elemen DOM
const btnTransfer = document.getElementById("btnTransfer");
const btnCOD = document.getElementById("btnCOD");
const transferSection = document.getElementById("transferSection");
const uploadSection = document.getElementById("uploadSection");
const paymentInput = document.getElementById("paymentMethod");
const modalPaymentText = document.getElementById("modalPaymentMethod");

// Saat tombol Transfer diklik
btnTransfer.addEventListener("click", function(){

    // Set aktif
    btnTransfer.classList.add("method-active");
    btnCOD.classList.remove("method-active");

    // Tampilkan section transfer
    transferSection.style.display = "block";
    uploadSection.style.display = "block";

    // Set value
    paymentInput.value = "transfer";
    modalPaymentText.innerText = "Transfer";
});

// Saat tombol COD diklik
btnCOD.addEventListener("click", function(){

    // Set aktif
    btnCOD.classList.add("method-active");
    btnTransfer.classList.remove("method-active");

    // Sembunyikan section transfer
    transferSection.style.display = "none";
    uploadSection.style.display = "none";

    // Set value
    paymentInput.value = "cod";
    modalPaymentText.innerText = "COD";
});
</script>

</body>
</html>
