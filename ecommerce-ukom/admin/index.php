<?php
// Menghubungkan file autentikasi untuk mengecek login / session user
require_once '../auth/auth.php';

// Menghubungkan file koneksi database
require_once '../config/database.php';

// Mengecek apakah role user yang login bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke halaman login admin
    header("Location: ../auth/login-admin.php");

    // Menghentikan eksekusi script setelah redirect
    exit;
}

// hitung jumlah produk
// Menjalankan query untuk menghitung total semua produk dalam tabel products
$produk = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");

// Mengambil hasil query lalu menyimpan jumlah total produk ke variabel
$total_produk = mysqli_fetch_assoc($produk)['total'];

// hitung jumlah petugas saja (role officer)
// Menjalankan query untuk menghitung jumlah user yang memiliki role officer
$officer = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='officer'");

// Mengambil hasil query lalu menyimpan jumlah total officer ke variabel
$total_officer = mysqli_fetch_assoc($officer)['total'];

// hitung jumlah transaksi
// Menjalankan query untuk menghitung total transaksi dalam tabel transactions
$transaksi = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transactions");

// Mengambil hasil query lalu menyimpan jumlah total transaksi ke variabel
$total_transaksi = mysqli_fetch_assoc($transaksi)['total'];
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <!-- Menentukan encoding karakter agar mendukung UTF-8 -->
        <meta charset="UTF-8">

        <!-- Judul halaman yang tampil di tab browser -->
        <title>Dashboard Admin</title>

        <!-- Favicon / icon website -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

        <!-- Bootstrap -->
        <!-- Menghubungkan Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font -->
        <!-- Menghubungkan font Poppins dari Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

        <style>
            /* Styling dasar untuk body */
            body {
                margin: 0;
                font-family: 'Poppins', sans-serif;
                background: #f9f9f9;
                overflow: hidden;
            }

            /* Styling area konten utama */
            .content {
                padding: 40px;
                flex: 1;
            }

            /* Styling judul dashboard */
            .content h2 {
                margin-bottom: 30px;
                font-weight: 600;
                font-size: 24px; /* ukuran asli */
                color: #333;
            }

            /* Container untuk card statistik */
            .card-container {
                display: flex;
                gap: 30px;
            }

            /* Styling setiap card statistik */
            .stat-card {
                flex: 1;
                background: #fff;
                border-radius: 20px;
                padding: 30px;
                box-shadow: 0 10px 20px rgba(0,0,0,0.05);
                border: none;
            }

            /* Styling judul kecil pada card */
            .stat-card h3 {
                font-size: 16px;
                font-weight: 500;
                color: #888;
                margin-bottom: 15px;
            }

            /* Styling angka statistik pada card */
            .stat-card p {
                font-size: 42px;
                font-weight: 600;
                color: #FFA4A4;
                margin: 0;
            }
        </style>
    </head>

    <body>
    <!-- Container utama dengan flexbox dan tinggi minimal 1 layar -->
    <div class="d-flex min-vh-100">

        <!-- SIDEBAR -->
        <!-- Menampilkan sidebar admin dari file terpisah -->
        <?php include 'sidebar.php'; ?>

        <!-- CONTENT -->
        <!-- Bagian konten utama dashboard -->
        <div class="content">
            <!-- Judul halaman dashboard -->
            <h2>Dashboard Overview</h2>

            <!-- Container card statistik -->
            <div class="card-container">

                <!-- Card total produk -->
                <div class="stat-card">
                    <h3>Total Products</h3>

                    <!-- Menampilkan jumlah total produk -->
                    <p><?= $total_produk ?></p>
                </div>

                <!-- Card total officer -->
                <div class="stat-card">
                    <h3>Total Officers</h3>

                    <!-- Menampilkan jumlah total officer -->
                    <p><?= $total_officer ?></p>
                </div>

                <!-- Card total transaksi -->
                <div class="stat-card">
                    <h3>Total Transactions</h3>

                    <!-- Menampilkan jumlah total transaksi -->
                    <p><?= $total_transaksi ?></p>
                </div>
            </div>
        </div>

    </div>
    </body>
    </html>