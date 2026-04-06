<?php
// Memulai session agar data login officer bisa disimpan dan digunakan di halaman lain
session_start();

// Memanggil file koneksi database
require_once '../config/database.php';

// Variabel untuk menampung pesan error jika login gagal
$error = "";

// Mengecek apakah tombol login sudah ditekan
if (isset($_POST['login'])) {

    // Mengambil input username dari form lalu mengamankannya dari karakter khusus SQL
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Mengambil password dari form lalu mengubahnya ke format MD5
    // Agar bisa dicocokkan dengan password yang tersimpan di database
    $password = md5($_POST['password']);

    // Query untuk mencari user dengan:
    // 1. username sesuai
    // 2. password sesuai
    // 3. role harus officer
    // LIMIT 1 berarti hanya mengambil satu data saja
    $query = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE username='$username' 
        AND password='$password'
        AND role='officer'
        LIMIT 1
    ");

    // Mengecek apakah data officer ditemukan
    if (mysqli_num_rows($query) > 0) {

        // Mengambil data officer dari hasil query
        $data = mysqli_fetch_assoc($query);

        // Menyimpan data officer ke dalam session
        // Session ini akan digunakan agar officer tetap login
        $_SESSION['user_id']  = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Jika login berhasil, arahkan officer ke dashboard officer
        header("Location: ../officer/index.php");

        // Menghentikan program agar tidak melanjutkan kode di bawahnya
        exit;

    } else {
        // Jika username/password salah atau role bukan officer,
        // maka tampilkan pesan error
        $error = "Username atau password salah / bukan petugas";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Menentukan karakter encoding -->
    <meta charset="UTF-8">

    <!-- Judul halaman yang tampil di tab browser -->
    <title>Login Officer</title>

    <!-- Icon website / favicon -->
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <!-- Menghubungkan Bootstrap 5 untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Menghubungkan font Poppins dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Mengatur font utama untuk seluruh halaman */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Membuat bar berwarna pink di sisi kiri halaman */
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
    </style>
</head>

<!-- d-flex = menggunakan flexbox -->
<!-- vh-100 = tinggi body penuh 1 layar -->
<body class="d-flex vh-100">

    <!-- Bagian kiri halaman berupa bar pink -->
    <div class="left-bar"></div>

    <!-- Konten utama login -->
    <!-- flex-fill = mengisi sisa ruang -->
    <!-- d-flex justify-content-center align-items-center = isi berada di tengah -->
    <div class="flex-fill d-flex justify-content-center align-items-center">
        <div class="text-center">

            <!-- Judul halaman login -->
            <h1 class="mb-4">
                <span class="fw-semibold" style="color:#9CAFAA;">Login for </span>
                <span class="fw-semibold fst-italic" style="color:#FFA4A4;">Officer</span>
            </h1>

            <!-- Menampilkan pesan error jika login gagal -->
            <?php if ($error): ?>
                <div class="text-danger mb-3"><?= $error ?></div>
            <?php endif; ?>

            <!-- Form login officer -->
            <!-- method POST digunakan agar data tidak tampil di URL -->
            <form method="POST" class="d-flex flex-column align-items-center gap-4">

                <!-- Input username -->
                <input 
                    type="text" 
                    name="username" 
                    class="form-control px-4"
                    placeholder="Username" 
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

                <!-- Tombol submit login -->
                <!-- name="login" dipakai untuk mendeteksi form dikirim -->
                <button type="submit" name="login" class="btn btn-login">
                    Sign In
                </button>
            </form>

        </div>
    </div>

</body>
</html>