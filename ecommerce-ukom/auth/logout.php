<?php
session_start();

$role = $_SESSION['role'] ?? null;

$_SESSION = array();
session_destroy();

if ($role == 'admin') {
    header("Location: login-admin.php");
} 
elseif ($role == 'officer') {
    header("Location: login-officer.php");
} 
else {
    // customer
    header("Location: ../customer/index.php");
}
exit;
?>