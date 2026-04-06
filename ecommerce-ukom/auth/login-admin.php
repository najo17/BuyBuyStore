<?php
// Memulai session agar data login admin bisa disimpan dan digunakan di halaman lain
session_start();

// Memanggil file koneksi database
require_once '../config/database.php';

// Variabel untuk menampung pesan error jika login gagal
$error = "";

// Mengecek apakah tombol login sudah ditekan
if (isset($_POST['login'])) {

    // Mengambil input username dari form lalu mengamankannya dari karakter khusus SQL
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Mengambil input password lalu mengubahnya menjadi format MD5
    // Digunakan agar password dicocokkan dengan data password di database
    $password = md5($_POST['password']); // sementara md5 (nanti bisa upgrade ke password_hash)

    // Menjalankan query untuk mencari user yang:
    // 1. username-nya sesuai
    // 2. password-nya sesuai
    // 3. rolenya admin
    // 4. hanya mengambil 1 data saja
    $query = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE username='$username' 
        AND password='$password'
        AND role='admin'
        LIMIT 1
    ");

    // Mengecek apakah data admin ditemukan
    if (mysqli_num_rows($query) > 0) {

        // Mengambil data admin dari hasil query dalam bentuk array associative
        $data = mysqli_fetch_assoc($query);

        // Menyimpan data admin ke dalam session
        // Session ini nantinya dipakai agar admin tetap dianggap login
        $_SESSION['user_id']  = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Jika login berhasil, arahkan admin ke halaman dashboard admin
        header("Location: ../admin/index.php");

        // Menghentikan program agar tidak lanjut menampilkan kode di bawahnya
        exit;

    } else {
        // Jika username/password salah atau user bukan admin,
        // tampilkan pesan error
        $error = "Username atau password salah / bukan admin";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Menentukan karakter encoding agar teks terbaca dengan baik -->
    <meta charset="UTF-8">

    <!-- Judul halaman yang tampil di tab browser -->
    <title>Login Admin</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <!-- Menghubungkan Bootstrap 5 untuk mempermudah styling tampilan -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Menghubungkan Google Font Poppins agar tampilan lebih modern -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Mengatur font utama pada seluruh halaman */
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

        /* Warna utama tombol login */
        .btn-login {
            background-color: #FFA4A4;
            color: white;
            border: none;
        }

        /* Warna tombol saat mouse diarahkan ke tombol */
        .btn-login:hover {
            background-color: #FFBEBE;
        }
    </style>
</head>

<!-- d-flex = layout flexbox -->
<!-- vh-100 = tinggi body 100% layar -->
<body class="d-flex vh-100">

    <!-- Bagian kiri halaman berupa bar pink -->
    <div class="left-bar"></div>

    <!-- Bagian utama isi login -->
    <!-- flex-fill = memenuhi sisa ruang -->
    <!-- d-flex justify-content-center align-items-center = isi berada di tengah -->
    <div class="flex-fill d-flex justify-content-center align-items-center">
        <div class="text-center">

            <!-- Judul halaman login -->
            <h1 class="mb-4">
                <span class="fw-semibold" style="color:#9CAFAA;">Login for </span>
                <span class="fw-semibold fst-italic" style="color:#FFA4A4;">Admin</span>
            </h1>

            <!-- Jika ada pesan error, maka tampilkan -->
            <?php if ($error): ?>
                <div class="text-danger mb-3"><?= $error ?></div>
            <?php endif; ?>

            <!-- Form login admin -->
            <!-- method POST digunakan agar data dikirim secara aman dan tidak tampil di URL -->
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
                <!-- name="login" digunakan untuk mendeteksi apakah form dikirim -->
                <button type="submit" name="login" class="btn btn-login">
                    Sign In
                </button>
            </form>

        </div>
    </div>

</body>
</html>