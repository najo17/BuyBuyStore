<?php
session_start();
require_once '../config/database.php';

$error = "";

if (isset($_POST['register'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); // sementara md5

    // Cek username
    $checkUsername = mysqli_query($conn, 
        "SELECT id FROM users WHERE username='$username' LIMIT 1"
    );

    // Cek email
    $checkEmail = mysqli_query($conn, 
        "SELECT id FROM users WHERE email='$email' LIMIT 1"
    );

    if (mysqli_num_rows($checkUsername) > 0) {

        $error = "Username sudah digunakan";

    } elseif (mysqli_num_rows($checkEmail) > 0) {

        $error = "Email sudah terdaftar";

    } else {

        mysqli_query($conn, "
            INSERT INTO users (username, email, password, role) 
            VALUES ('$username', '$email', '$password', 'customer')
        ");

        // Redirect langsung ke login
        header("Location: login-customer.php?registered=success");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Customer</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
}

.right-bar {
    width: 120px;
    background-color: #FFA4A4;
}

.form-control,
.btn-register {
    width: 518px;
    height: 86px;
    border-radius: 40px;
    font-size: 18px;
}

.form-control {
    border: 1px solid #ddd;
}

.btn-register {
    background-color: #FFA4A4;
    color: white;
    border: none;
}

.btn-register:hover {
    background-color: #FFBEBE;
}

.login-link {
    color: #FFA4A4;
    text-decoration: none;
    font-weight: 500;
}

.login-link:hover {
    text-decoration: underline;
}
</style>
</head>

<body class="d-flex vh-100">

<div class="flex-fill d-flex justify-content-center align-items-center">
    <div class="text-center">

        <h1 class="mb-4 fw-semibold" style="color:#FFA4A4;">
            Create Account
        </h1>

        <?php if (isset($_GET['registered'])): ?>
            <div class="text-success mb-3">
                Register berhasil! Silakan login.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="text-danger mb-3"><?= $error ?></div>
        <?php endif; ?>

        <!-- <?php if ($success): ?>
            <div class="text-success mb-3"><?= $success ?></div>
        <?php endif; ?> -->
            
        <form method="POST" class="d-flex flex-column align-items-center gap-4">

            <input 
                type="text" 
                name="username" 
                class="form-control px-4"
                placeholder="Username"
                required
            >

            <input 
                type="email" 
                name="email" 
                class="form-control px-4"
                placeholder="Email"
                required
            >

            <input 
                type="password" 
                name="password" 
                class="form-control px-4"
                placeholder="Password"
                required
            >

            <button type="submit" name="register" class="btn btn-register">
                Sign Up
            </button>

        </form>

        <p class="mt-4" style="color:#666;">
            Already have an account? 
            <a href="login-customer.php" class="login-link">Sign In</a>
        </p>

    </div>
</div>

<div class="right-bar"></div>

</body>
</html>