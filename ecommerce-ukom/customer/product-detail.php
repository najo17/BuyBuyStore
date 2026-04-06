<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Ambil id produk dari URL
$id = $_GET['id'];

// Ambil data produk berdasarkan id
$product = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$data = mysqli_fetch_assoc($product); // Ambil hasil query jadi array
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"> <!-- Encoding -->
<title><?= $data['name']; ?> - BuyBuy</title> <!-- Judul halaman sesuai nama produk -->

<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* Styling body */
body{
    font-family:'Poppins', sans-serif;
    background:#ffff;
}

/* Card utama detail produk */
.detail-card{
    background:white;
    border-radius:25px;
    padding:40px;
    box-shadow:0 15px 30px rgba(0,0,0,0.08);
}

/* Container gambar */
.product-img{
    background:#f8f8f8;
    border-radius:20px;
    padding:20px;
    text-align:center;
}

/* Gambar produk */
.product-img img{
    max-width:100%;
    max-height:400px;
}

/* Harga */
.price{
    font-size:28px;
    font-weight:600;
    color:#FFA4A4;
}

/* Badge stock */
.stock-badge{
    font-size:14px;
}

/* Tombol add to cart */
.add-btn{
    background:#FFA4A4;
    color:white;
    border:none;
    padding:12px 30px;
    border-radius:30px;
    transition:0.3s;
}

/* Hover tombol */
.add-btn:hover{
    background:#ff8c8c;
    transform:scale(1.05);
}

/* Tombol back */
.back-btn{
    background:#9CAFAA;
    color:white;
    border:none;
    padding:8px 20px;
    border-radius:20px;
}
</style>
</head>

<body class="p-5">

<!-- Tombol kembali ke halaman utama -->
<a href="index.php" class="back-btn mb-4 d-inline-block">
    <i class="bi bi-arrow-left"></i> Back
</a>

<div class="container">
    <div class="detail-card">
        <div class="row align-items-center">

            <!-- IMAGE (KIRI) -->
            <div class="col-md-6">
                <div class="product-img">
                    <!-- Menampilkan gambar produk -->
                    <img src="../assets/uploads/<?= $data['image']; ?>">
                </div>
            </div>

            <!-- INFO PRODUK (KANAN) -->
            <div class="col-md-6">

                <!-- Nama produk -->
                <h2 class="mb-3"><?= $data['name']; ?></h2>

                <!-- Harga -->
                <div class="price mb-3">
                    Rp <?= number_format($data['price'],0,',','.') ?>
                </div>

                <!-- Status stok -->
                <?php if($data['stock'] > 0): ?>
                    <span class="badge bg-success stock-badge mb-3">
                        In Stock (<?= $data['stock']; ?>)
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger stock-badge mb-3">
                        Out Of Stock
                    </span>
                <?php endif; ?>

                <!-- Deskripsi produk -->
                <p class="mt-3 text-muted">
                    <?= $data['description'] ?? 'No description available.' ?>
                </p>

                <!-- Tombol add to cart hanya jika stok tersedia -->
                <?php if($data['stock'] > 0): ?>

                    <form method="POST" action="../process/add-to-cart.php">

                        <!-- Kirim id produk -->
                        <input type="hidden" name="product_id" value="<?= $data['id']; ?>">

                        <!-- Tombol submit -->
                        <button type="submit" class="add-btn mt-3">
                            <i class="bi bi-cart-plus me-2"></i>
                            Add To Cart
                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

</body>
</html>
