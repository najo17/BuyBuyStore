<?php
// Memulai session agar data login customer bisa disimpan
session_start();

// Memanggil file koneksi database
require_once '../config/database.php';

// Variabel untuk menampung pesan error jika login gagal
$error = "";

// Mengecek apakah tombol login ditekan
if (isset($_POST['login'])) {

    // Mengambil input email dari form lalu mengamankannya dari karakter khusus SQL
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Mengambil password dari form lalu mengubahnya ke format MD5
    // Agar bisa dicocokkan dengan password yang tersimpan di database
    $password = md5($_POST['password']); // sementara md5

    // Query untuk mencari data user customer berdasarkan:
    // 1. email
    // 2. password
    // 3. role harus customer
    // LIMIT 1 berarti hanya ambil 1 data saja
    $query = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE email='$email' 
        AND password='$password'
        AND role='customer'
        LIMIT 1
    ");

    // Mengecek apakah data customer ditemukan
    if (mysqli_num_rows($query) > 0) {

        // Mengambil data customer dari hasil query
        $data = mysqli_fetch_assoc($query);

        // Menyimpan data customer ke dalam session
        // Session ini akan digunakan agar customer tetap login
        $_SESSION['user_id']  = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Jika login berhasil, customer diarahkan ke halaman utama customer
        header("Location: ../customer/index.php");

        // Menghentikan program agar tidak menjalankan kode di bawahnya
        exit;

    } else {
        // Jika email atau password salah, tampilkan pesan error
        $error = "Email atau password salah";
    }
}
?>  

<!DOCTYPE html>
<html lang="en">
<head>
<!-- Menentukan karakter encoding -->
<meta charset="UTF-8">

<!-- Judul halaman yang tampil di tab browser -->
<title>Login Customer</title>

<!-- Icon website / favicon -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Menghubungkan Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Menghubungkan font Poppins dari Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* Mengatur font utama untuk seluruh halaman */
body {
    font-family: 'Poppins', sans-serif;
}

/* Membuat bar pink di sebelah kiri halaman */
.left-bar {
    width: 120px;
    background-color: #FFA4A4;
}

/* Mengatur ukuran input form dan tombol login */
.form-control,
.btn-login {
    width: 518px;
    height: 86px;
    border-radius: 40px;
    font-size: 18px;
}

/* Mengatur border input */
.form-control {
    border: 1px solid #ddd;
}

/* Warna tombol login */
.btn-login {
    background-color: #FFA4A4;
    color: white;
    border: none;
}

/* Warna tombol saat mouse diarahkan */
.btn-login:hover {
    background-color: #FFBEBE;
}

/* Styling link Sign Up */
.signup-link {
    color: #FFA4A4;
    text-decoration: none;
    font-weight: 500;
}

/* Efek saat link Sign Up disentuh mouse */
.signup-link:hover {
    text-decoration: underline;
}
</style>
</head>

<!-- d-flex = layout flexbox -->
<!-- vh-100 = tinggi body penuh 1 layar -->
<body class="d-flex vh-100">

<!-- Bar pink di sisi kiri -->
<div class="left-bar"></div>

<!-- Konten utama login -->
<!-- flex-fill = mengisi sisa ruang -->
<!-- d-flex justify-content-center align-items-center = isi berada di tengah -->
<div class="flex-fill d-flex justify-content-center align-items-center">
    <div class="text-center">

        <!-- Judul halaman login -->
        <h1 class="mb-4">
            <span class="fw-semibold" style="color:#9CAFAA;">Sign In To </span>
            <span class="fw-semibold fst-italic" style="color:#FFA4A4;">BuyBuy</span>
        </h1>

        <!-- Menampilkan pesan error jika login gagal -->
        <?php if ($error): ?>
            <div class="text-danger mb-3"><?= $error ?></div>
        <?php endif; ?>

        <!-- Form login customer -->
        <!-- method POST digunakan agar data form dikirim secara aman -->
        <form method="POST" class="d-flex flex-column align-items-center gap-4">

            <!-- Input email -->
            <input 
                type="email" 
                name="email" 
                class="form-control px-4"
                placeholder="Email"
                required
            >

            <!-- Input password -->
            <input 
                type="password" 
                name="password" 
                class="form-control px-4"
                placeholder="Password"
                required
            >

            <!-- Tombol login -->
            <!-- name="login" dipakai untuk mendeteksi submit -->
            <button type="submit" name="login" class="btn btn-login">
                Sign In
            </button>

        </form>

        <!-- Link ke halaman register jika customer belum punya akun -->
        <p class="mt-4" style="color:#666;">
            Don’t have an account? 
            <a href="register-customer.php" class="signup-link">Sign Up</a>
        </p>

    </div>
</div>

</body>
</html>