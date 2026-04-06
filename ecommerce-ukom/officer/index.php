<?php
// ======================== PHP BACKEND ========================

// Memanggil file autentikasi untuk memastikan user login
require_once '../auth/auth.php';

// Memanggil file koneksi database
require_once '../config/database.php';

// Mengecek role user, jika bukan 'officer', redirect ke halaman login officer
if ($_SESSION['role'] !== 'officer') {
    header("Location: ../auth/login-officer.php");
    exit;
}

// ======================== QUERY DATABASE ========================

// Hitung jumlah total produk di tabel 'products'
$produk = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
// Ambil hasil query sebagai array asosiatif dan simpan total produk
$total_produk = mysqli_fetch_assoc($produk)['total'];

// Hitung jumlah total transaksi di tabel 'transactions'
$transaksi = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transactions");
// Ambil hasil query sebagai array asosiatif dan simpan total transaksi
$total_transaksi = mysqli_fetch_assoc($transaksi)['total'];

// Hitung jumlah transaksi yang statusnya 'pending' (belum disetujui)
$belum_acc = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transactions WHERE status = 'pending'");
// Ambil hasil query sebagai array asosiatif dan simpan jumlah transaksi pending
$total_belum_acc = mysqli_fetch_assoc($belum_acc)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Petugas</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <!-- Bootstrap CSS untuk layout dan komponen -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- ======================== STYLE CSS ======================== -->
    <style>
        /* Style global body */
        body {
            margin: 0; /* Hilangkan margin default browser */
            font-family: 'Poppins', sans-serif; /* Gunakan font Poppins */
            background: #f9f9f9; /* Warna background abu muda */
            overflow: hidden; /* Hilangkan scroll jika konten meluber */
        }

        /* Container utama konten */
        .content {
            padding: 40px; /* Jarak dalam konten */
            flex: 1; /* Memenuhi ruang yang tersedia pada flex container */
        }

        /* Judul halaman */
        .content h2 {
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 24px;
            color: #333; /* Warna abu gelap */
        }

        /* Container untuk kartu statistik */
        .card-container {
            display: flex; /* Gunakan flexbox untuk layout horizontal */
            gap: 30px; /* Jarak antar kartu */
        }

        /* Kartu statistik */
        .stat-card {
            flex: 1; /* Membuat semua kartu memiliki lebar sama */
            background: #fff; /* Latar belakang putih */
            border-radius: 20px; /* Sudut membulat */
            padding: 30px; /* Jarak dalam kartu */
            box-shadow: 0 10px 20px rgba(0,0,0,0.05); /* Efek bayangan lembut */
            border: none; /* Hilangkan border default */
        }

        /* Judul di dalam kartu */
        .stat-card h3 {
            font-size: 16px;
            font-weight: 500;
            color: #888; /* Abu medium */
            margin-bottom: 15px;
        }

        /* Angka atau nilai statistik dalam kartu */
        .stat-card p {
            font-size: 42px;
            font-weight: 600;
            color: #FFA4A4; /* Warna pink/merah muda */
            margin: 0; /* Hilangkan margin default */
        }
    </style>
</head>

<body>
<div class="d-flex min-vh-100">
    <!-- ======================== SIDEBAR ======================== -->
    <?php include 'sidebar.php'; ?>
    <!-- Sidebar ini biasanya berisi menu navigasi -->

    <!-- ======================== MAIN CONTENT ======================== -->
    <div class="content">
        <h2>Dashboard Overview</h2>

        <!-- Container kartu statistik -->
        <div class="card-container">

            <!-- Kartu 1: Total Produk -->
            <div class="stat-card">
                <h3>Total Products</h3>
                <!-- Menampilkan jumlah total produk dari query PHP -->
                <p><?= $total_produk ?></p>
            </div>

            <!-- Kartu 2: Pending Payments -->
            <div class="stat-card">
                <h3>Pending Payments</h3>
                <!-- Menampilkan jumlah transaksi pending -->
                <p><?= $total_belum_acc ?></p>
            </div>

            <!-- Kartu 3: Total Transactions -->
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