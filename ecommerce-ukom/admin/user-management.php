<?php
// Memanggil file autentikasi agar halaman hanya bisa diakses user yang sudah login
require_once '../auth/auth.php';

// Memanggil file koneksi database
require_once '../config/database.php';

// Mengecek apakah user yang login bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke halaman login admin
    header("Location: ../auth/login-admin.php");
    // Menghentikan proses script
    exit;
}

// ================= TAMBAH OFFICER =================

// Mengecek apakah tombol add_officer ditekan
if (isset($_POST['add_officer'])) {

    // Mengambil input username lalu membersihkan spasi di awal/akhir dan mengamankan dari SQL Injection
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));

    // Mengambil input email lalu membersihkan spasi dan mengamankan input
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Mengambil password asli dari form
    $plain_password = trim($_POST['password']);

    // Mengubah password menjadi format md5 untuk disimpan sebagai password login
    $password = md5($plain_password);

    // Menyimpan data officer baru ke tabel users
    mysqli_query($conn, "INSERT INTO users (username, email, password, plain_password, role)
                         VALUES ('$username', '$email', '$password', '$plain_password', 'officer')");

    // Setelah berhasil, redirect kembali ke halaman user-management
    header("Location: user-management.php");
    exit;
}

// ================= UPDATE OFFICER =================

// Mengecek apakah tombol update_officer ditekan
if (isset($_POST['update_officer'])) {

    // Mengambil ID officer yang akan diupdate
    $id       = $_POST['id'];

    // Mengambil username baru dan mengamankannya
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));

    // Mengambil email baru dan mengamankannya
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Mengambil password baru dari form
    $plain_password = trim($_POST['password']);

    // Mengubah password menjadi md5
    $password = md5($plain_password);

    // Mengupdate data officer di database
    mysqli_query($conn, "UPDATE users 
                         SET username='$username', 
                             email='$email', 
                             password='$password',
                             plain_password='$plain_password'
                         WHERE id='$id'");

    // Setelah update berhasil, redirect kembali ke halaman user-management
    header("Location: user-management.php");
    exit;
}

// ================= DELETE OFFICER =================

// Mengecek apakah tombol delete_officer ditekan
if (isset($_POST['delete_officer'])) {

    // Mengambil ID officer yang akan dihapus
    $id = $_POST['id'];

    // Menghapus data officer berdasarkan ID
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");

    // Setelah berhasil dihapus, redirect kembali ke halaman user-management
    header("Location: user-management.php");
    exit;
}

// ================= AMBIL DATA USERS =================

// Mengambil semua data user yang role-nya officer
$users = mysqli_query($conn, "SELECT * FROM users WHERE role='officer'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>

    <!-- Icon/logo tab browser -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <!-- BOOTSTRAP -->
    <!-- Menghubungkan Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICON -->
    <!-- Menghubungkan Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FONT -->
    <!-- Menghubungkan font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Styling body utama */
        body {
            font-family: Poppins, sans-serif;
            background: #f9f9f9;
            overflow: hidden;
        }

        /* Area konten utama */
        .content {
            padding: 40px;
            flex: 1;
        }

        /* Judul halaman */
        .page-title {
            color: #FFA4A4;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Tombol warna pink */
        .btn-pink {
            background: #FFA4A4;
            color: #fff;
            border-radius: 12px;
        }

        /* Efek hover tombol pink */
        .btn-pink:hover {
            background: #ff8e8e;
        }

        /* Membuat sudut tabel membulat */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        /* Header tabel */
        .table thead th {
            background-color: #FFA4A4 !important;
            color: #ffffff !important;
            text-align: center;
            vertical-align: middle;
        }

        /* Header modal */
        .modal-header {
            background: #FFA4A4;
            color: white;
        }

        /* Styling input form */
        .form-control {
            border-radius: 12px;
        }

        /* Tombol action kecil */
        .action-btn {
            width: 30px;
            height: 30px;
            padding: 0;
        }

        /* Box tombol action */
        .action-box {
            width: 43px;
            height: 43px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
        }

        /* Tombol edit warna hijau */
        .action-edit {
            background-color: #63C78A;
        }

        /* Tombol delete warna merah */
        .action-delete {
            background-color: #EB4C4C;
        }

        /* Ukuran icon di action box */
        .action-box i {
            font-size: 22px;
        }
    </style>
</head>

<body>
<div class="d-flex min-vh-100">

    <!-- Memanggil sidebar admin -->
    <?php include 'sidebar.php'; ?>

    <!-- Area konten utama -->
    <div class="content">
        <h4 class="page-title">User Data</h4>

        <!-- BUTTON -->
        <!-- Tombol untuk membuka modal tambah officer -->
        <button class="btn btn-pink mb-3" data-bs-toggle="modal" data-bs-target="#addOfficer">
            + Add Officer
        </button>

        <!-- TABLE -->
        <!-- Tabel daftar officer -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                <!-- Perulangan untuk menampilkan semua data officer -->
                <?php while ($row = mysqli_fetch_assoc($users)) : ?>
                    <tr>

                        <!-- Menampilkan ID officer -->
                        <td class="text-center"><?= $row['id'] ?></td>

                        <!-- Menampilkan username officer -->
                        <td><?= $row['username'] ?></td>

                        <!-- Menampilkan email officer -->
                        <td><?= $row['email'] ?></td>

                        <!-- Menampilkan password asli officer -->
                        <td><?= $row['plain_password'] ?></td>

                        <!-- Menampilkan tanggal dibuat -->
                        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>

                        <!-- Kolom action -->
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">

                                <!-- EDIT -->
                                <!-- Tombol untuk membuka modal edit officer -->
                                <div class="action-box action-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editOfficer"
                                    data-id="<?= $row['id'] ?>"
                                    data-username="<?= $row['username'] ?>"
                                    data-email="<?= $row['email'] ?>"
                                    data-password="<?= $row['plain_password'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </div>

                                <!-- DELETE -->
                                <!-- Tombol untuk membuka modal delete officer -->
                                <div class="action-box action-delete"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteOfficer"
                                    data-id="<?= $row['id'] ?>"
                                    data-username="<?= $row['username'] ?>">
                                    <i class="bi bi-trash"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD OFFICER ================= -->
<div class="modal fade" id="addOfficer" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header modal tambah officer -->
            <div class="modal-header">
                <h5 class="modal-title">Add Officer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form tambah officer -->
            <form method="POST">
                <div class="modal-body">

                    <!-- Input username -->
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <!-- Input email -->
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <!-- Input password -->
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>
                </div>

                <!-- Footer modal -->
                <div class="modal-footer">
                    <button type="submit" name="add_officer" class="btn btn-pink w-100">
                        Save Officer
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ================= MODAL EDIT OFFICER ================= -->
<div class="modal fade" id="editOfficer" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header modal edit officer -->
            <div class="modal-header">
                <h5 class="modal-title">Edit Officer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form edit officer -->
            <form method="POST">

                <!-- Input hidden untuk menyimpan ID officer -->
                <input type="hidden" name="id" id="edit-id">

                <div class="modal-body">

                    <!-- Input username -->
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>

                    <!-- Input email -->
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>

                    <!-- Input password -->
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="text" name="password" id="edit-password" class="form-control" required>
                    </div>
                </div>

                <!-- Footer modal -->
                <div class="modal-footer">
                    <button type="submit" name="update_officer" class="btn btn-pink w-100">
                        Update Officer
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ================= MODAL DELETE OFFICER ================= -->
<div class="modal fade" id="deleteOfficer" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header modal delete -->
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Officer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form delete officer -->
            <form method="POST">

                <!-- Input hidden untuk menyimpan ID officer yang akan dihapus -->
                <input type="hidden" name="id" id="delete-id">

                <div class="modal-body text-center">

                    <!-- Icon peringatan -->
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 48px;"></i>

                    <!-- Konfirmasi hapus -->
                    <p class="mt-3">
                        Are you sure you want to delete  
                        <strong id="delete-username"></strong>?
                    </p>

                    <!-- Pesan peringatan -->
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>

                <!-- Tombol aksi -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" name="delete_officer" class="btn btn-danger">
                        Yes, Delete
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Import Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ================= EDIT MODAL =================

// Mengambil elemen modal edit officer
const editModal = document.getElementById('editOfficer');

// Saat modal edit dibuka
editModal.addEventListener('show.bs.modal', function (event) {

    // Mengambil tombol yang diklik
    const button = event.relatedTarget;

    // Mengisi form edit secara otomatis sesuai data officer yang dipilih
    document.getElementById('edit-id').value = button.getAttribute('data-id');
    document.getElementById('edit-username').value = button.getAttribute('data-username');
    document.getElementById('edit-email').value = button.getAttribute('data-email');
    document.getElementById('edit-password').value = button.getAttribute('data-password');
});

// ================= DELETE MODAL =================

// Mengambil elemen modal delete officer
const deleteModal = document.getElementById('deleteOfficer');

// Saat modal delete dibuka
deleteModal.addEventListener('show.bs.modal', function (event) {

    // Mengambil tombol yang diklik
    const button = event.relatedTarget;

    // Mengisi ID officer ke input hidden
    document.getElementById('delete-id').value = button.getAttribute('data-id');

    // Menampilkan username officer yang akan dihapus
    document.getElementById('delete-username').innerText =
        button.getAttribute('data-username');
});
</script>

</body>
</html>