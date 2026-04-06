<?php
session_start();
include 'config/database.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = mysqli_query($conn,"SELECT * FROM users WHERE username='$username' AND password='$password'");
$data = mysqli_fetch_assoc($query);

if($data){

    $_SESSION['user_id'] = $data['id'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    if($data['role'] === 'admin'){
        header("Location: admin/index.php");
    }
    elseif($data['role'] === 'officer'){
        header("Location: officer/index.php");
    }
    else{
        header("Location: customer/index.php");
    }
    exit;
}
else{
    header("Location: login-customer.php?error=1");
}