<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php"); // Redirect ke login jika belum
    exit();
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query); // Simpan hasil query ke array

// Jika tombol update profile ditekan
if(isset($_POST['update_profile'])){

    // Ambil dan amankan input
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    // Query update data user
    $update = mysqli_query($conn, "UPDATE users SET 
        full_name = '$full_name',
        address = '$address'
        WHERE id = '$user_id'
    ");

    // Jika berhasil update
    if($update){
        echo "<script>alert('Profile updated successfully!'); window.location='index.php';</script>";
        exit();
    } else {
        // Jika gagal
        echo "<script>alert('Failed to update profile!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile - BuyBuy</title> <!-- Judul halaman -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Simonetta:wght@400;900&display=swap" rel="stylesheet">

<style>
/* Background */
body{
    background:#fff;
}

/* Navbar */
.navbar{ background:#FFA4A4; }

/* Brand */
.navbar-brand {
    font-weight: 700;
    color: white !important;
    font-size: 35px;
    font-family: Simonetta;
}

/* Card profile */
.profile-card{
    background:white;
    border-radius:20px;
    padding:35px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* Tombol simpan */
.save-btn{
    background:#FFA4A4;
    border:none;
    color:white;
    padding:10px 30px;
    border-radius:10px;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-5">
    <a class="navbar-brand" href="index.php">BuyBuy</a>
</nav>

<!-- Container utama -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <!-- Card profile -->
            <div class="profile-card">
                <h3 class="mb-4">My Profile</h3>

                <!-- Form update profile -->
                <form method="POST">

                    <!-- Username (readonly) -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" readonly>
                    </div>

                    <!-- Full name -->
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="4" required><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <!-- Tombol submit -->
                    <button type="submit" name="update_profile" class="save-btn">
                        Save Changes
                    </button>

                    <!-- Tombol kembali -->
                    <a href="index.php" class="btn btn-outline-secondary ms-2">
                        Back
                    </a>

                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>
