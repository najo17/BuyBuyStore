<?php
// ============================
// DOWNLOAD FILE BACKUP
// ============================

// Cek autentikasi login
require_once '../auth/auth.php';

// Koneksi database (opsional, sebenarnya tidak wajib untuk download file)
require_once '../config/database.php';

// Batasi hanya admin yang boleh download backup
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login-admin.php");
    exit;
}

// Folder penyimpanan backup
$backupDir = "../assets/backup/";

// Cek apakah parameter file ada
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("File not found.");
}

// Ambil nama file saja (aman dari manipulasi path)
$file = basename($_GET['file']);

// Gabungkan dengan folder backup
$filePath = $backupDir . $file;

// Cek apakah file benar-benar ada
if (!file_exists($filePath)) {
    die("File does not exist.");
}

// Cek ekstensi file agar hanya file .sql yang bisa didownload
$fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
if ($fileExtension !== 'sql') {
    die("Invalid file type.");
}

// Set header agar browser mendownload file
header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Bersihkan output buffer agar file tidak corrupt
ob_clean();
flush();

// Kirim isi file ke browser
readfile($filePath);
exit;
?>