<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'officer') {
    header("Location: login-officer.php");
    exit;
}
?>