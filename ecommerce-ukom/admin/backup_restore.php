<?php
// Menghubungkan file auth untuk mengecek login / session user
require_once '../auth/auth.php';

// Menghubungkan file koneksi database
require_once '../config/database.php';

// Mengecek apakah role user bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke halaman login admin
    header("Location: ../auth/login-admin.php");

    // Menghentikan eksekusi script setelah redirect
    exit;
}

// Menentukan folder penyimpanan file backup
$backupDir = "../assets/backup/";

/* ================= BACKUP PROCESS ================= */
// Mengecek apakah tombol backup_now ditekan
if(isset($_POST['backup_now'])) {

    // Membuat nama file backup berdasarkan tanggal dan jam saat ini
    $filename = "backup_" . date("Y-m-d_H-i-s") . ".sql";

    // Menggabungkan folder backup dengan nama file
    $filepath = $backupDir . $filename;

    // Menyiapkan array kosong untuk menampung nama tabel
    $tables = array();

    // Menjalankan query untuk mengambil semua nama tabel di database
    $result = mysqli_query($conn,"SHOW TABLES");

    // Mengambil nama tabel satu per satu
    while($row = mysqli_fetch_row($result)){
        // Menyimpan nama tabel ke dalam array $tables
        $tables[] = $row[0];
    }

    // Variabel untuk menampung seluruh isi file SQL backup
    $return = '';

    // Melakukan perulangan untuk setiap tabel dalam database
    foreach($tables as $table){

        // Mengambil seluruh data dari tabel yang sedang diproses
        $result = mysqli_query($conn,"SELECT * FROM $table");

        // Menghitung jumlah kolom pada tabel
        $num_fields = mysqli_num_fields($result);

        // Menambahkan perintah SQL untuk menghapus tabel jika sudah ada
        $return .= "DROP TABLE IF EXISTS $table;";

        // Mengambil query pembuatan tabel (CREATE TABLE)
        $row2 = mysqli_fetch_row(mysqli_query($conn,"SHOW CREATE TABLE $table"));

        // Menambahkan struktur tabel ke isi backup
        $return .= "\n\n".$row2[1].";\n\n";

        // Mengambil data per baris dari tabel
        while($row = mysqli_fetch_row($result)){
            // Menambahkan awal query INSERT INTO
            $return .= "INSERT INTO $table VALUES(";

            // Melakukan perulangan setiap kolom dalam satu baris data
            for($j=0; $j<$num_fields; $j++){
                // Mengamankan karakter khusus seperti tanda kutip
                $row[$j] = addslashes($row[$j]);

                // Mengubah enter/newline menjadi format \n agar aman di SQL
                $row[$j] = str_replace("\n","\\n",$row[$j]);

                // Mengecek apakah data kolom tersedia
                if(isset($row[$j])){
                    // Jika ada, tambahkan nilainya ke query INSERT
                    $return .= '"'.$row[$j].'"';
                } else {
                    // Jika tidak ada, isi dengan string kosong
                    $return .= '""';
                }

                // Jika belum kolom terakhir, tambahkan koma
                if($j<($num_fields-1)){
                    $return.= ',';
                }
            }

            // Menutup query INSERT untuk satu baris data
            $return .= ");\n";
        }

        // Memberi jarak antar tabel dalam file SQL
        $return .= "\n\n\n";
    }

    // Menyimpan isi backup SQL ke file .sql
    file_put_contents($filepath,$return);

    // Setelah backup selesai, redirect kembali ke halaman ini
    header("Location: backup_restore.php");

    // Menghentikan eksekusi script
    exit;
}


/* ================= DELETE BACKUP ================= */
// Mengecek apakah ada parameter delete pada URL
if(isset($_GET['delete'])) {

    // Mengambil nama file saja agar lebih aman dari manipulasi path
    $file = basename($_GET['delete']);

    // Menggabungkan folder backup dengan nama file yang ingin dihapus
    $fullPath = $backupDir . $file;

    // Mengecek apakah file benar-benar ada
    if(file_exists($fullPath)){
        // Menghapus file backup
        unlink($fullPath);
    }

    // Setelah hapus selesai, redirect kembali ke halaman ini
    header("Location: backup_restore.php");

    // Menghentikan eksekusi script
    exit;
}


/* ================= RESTORE PROCESS ================= */
// Mengecek apakah tombol restore_now ditekan
if(isset($_POST['restore_now'])) {

    // Mengambil lokasi file sementara hasil upload
    $file = $_FILES['restore_file']['tmp_name'];

    // Mengecek apakah file upload tersedia
    if($file){
        // Membaca isi file SQL backup
        $sql = file_get_contents($file);

        // Menjalankan semua query SQL dalam file tersebut
        mysqli_multi_query($conn,$sql);

        // Menyelesaikan sisa query jika ada banyak statement SQL
        while(mysqli_next_result($conn)){;}
    }

    // Setelah restore selesai, redirect kembali ke halaman ini
    header("Location: backup_restore.php");

    // Menghentikan eksekusi script
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<!-- Menentukan encoding karakter agar mendukung UTF-8 -->
<meta charset="UTF-8">

<!-- Judul halaman yang tampil di tab browser -->
<title>Backup & Restore</title>

<!-- Menampilkan favicon / icon tab browser -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Menghubungkan Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Menghubungkan font Poppins dari Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<!-- Menghubungkan Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
/* Styling dasar untuk body halaman */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f9f9f9;
}

/* Styling area konten utama */
.content {
    padding: 40px;
    flex: 1;
}

/* Styling judul section */
.section-title {
    color: #FFA4A4;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Styling header tabel */
.table thead th {
    background: #FFA4A4 !important;
    color: white !important;
    text-align: center;
}

/* Styling tombol warna pink */
.btn-pink {
    background: #FFA4A4;
    color: white;
    border: none;
}

/* Efek hover tombol pink */
.btn-pink:hover {
    background: #ff8d8d;
}

/* Styling tombol download */
.btn-download {
    background-color: #61C38D;
    border: none;
    color: #fff;
    padding: 6px 16px;
    border-radius: 6px;
    text-decoration: none;
}

/* Styling tombol delete */
.btn-delete {
    background-color: #E54B4B;
    border: none;
    color: #fff;
    padding: 6px 16px;
    border-radius: 6px;
}

/* Styling container tombol aksi agar sejajar */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>
</head>

<body>

<!-- Container utama dengan flexbox dan tinggi minimal 1 layar -->
<div class="d-flex min-vh-100">

    <!-- Menampilkan sidebar dari file terpisah -->
    <?php include 'sidebar.php'; ?>

    <!-- Konten utama halaman -->
    <div class="content">

        <!-- BACKUP -->
        <!-- Judul section backup -->
        <h4 class="mb-4 section-title">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            Backup
        </h4>

        <!-- Form untuk tombol backup -->
        <form method="POST">
            <button name="backup_now" class="btn btn-pink mb-3">
                <i class="bi bi-database-fill-add me-1"></i> Backup Now
            </button>
        </form>

        <!-- Tabel daftar file backup -->
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Backup File</th>
                    <th>Date</th>
                    <th>Size</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

<?php
// Mengambil semua file .sql di folder backup
$files = glob($backupDir . "*.sql");

// Melakukan perulangan untuk setiap file backup
foreach($files as $file):

    // Mengambil nama file saja tanpa path
    $filename = basename($file);

    // Mengambil tanggal terakhir file dimodifikasi
    $date = date("d/m/Y H:i", filemtime($file));

    // Menghitung ukuran file dalam KB
    $size = round(filesize($file)/1024,2) . " KB";
?>

<tr>
    <!-- Menampilkan nama file backup -->
    <td><?= $filename ?></td>

    <!-- Menampilkan tanggal file -->
    <td><?= $date ?></td>

    <!-- Menampilkan ukuran file -->
    <td><?= $size ?></td>

    <td>
        <div class="action-buttons">
            <!-- Tombol untuk mendownload file backup -->
            <a href="download.php?file=<?= urlencode($filename) ?>" class="btn-download">
    <i class="bi bi-download me-1"></i> Download
</a>
            <!-- Tombol untuk membuka modal delete -->
            <button 
                class="btn-delete"
                data-bs-toggle="modal"
                data-bs-target="#deleteBackupModal"
                data-file="<?= $filename ?>">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </div>
    </td>
</tr>

<?php endforeach; ?>

            </tbody>
        </table>

        <!-- Garis pemisah antara backup dan restore -->
        <hr class="my-5">

        <!-- RESTORE -->
        <!-- Judul section restore -->
        <h4 class="mb-4 section-title">
            <i class="bi bi-arrow-clockwise"></i>
            Restore
        </h4>

        <!-- Form upload file SQL untuk restore -->
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <!-- Input file untuk memilih file backup -->
                <input type="file" name="restore_file" class="form-control" required>
            </div>

            <div class="col-md-3">
                <!-- Tombol untuk menjalankan restore -->
                <button name="restore_now" class="btn btn-pink w-100">
                    <i class="bi bi-upload me-1"></i> Restore Now
                </button>
            </div>
        </form>

    </div>
</div>


<!-- DELETE MODAL -->
<!-- Modal konfirmasi hapus backup -->
<div class="modal fade" id="deleteBackupModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 overflow-hidden">

      <!-- Header modal -->
      <div class="modal-header border-0" style="background:#E54B4B;">
        <h5 class="modal-title text-white fw-semibold">
          Delete Backup File
        </h5>

        <!-- Tombol close modal -->
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Isi modal -->
      <div class="modal-body text-center py-5">
        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:70px;"></i>

        <h5 class="mt-4">
          Are you sure you want to delete
          <strong id="delete-file-name"></strong>?
        </h5>

        <p class="text-muted mt-2">
          This action cannot be undone.
        </p>
      </div>

      <!-- Footer modal -->
      <div class="modal-footer border-0 justify-content-center pb-4">
        <!-- Tombol batal -->
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          Cancel
        </button>

        <!-- Tombol konfirmasi hapus -->
        <a href="#" id="confirmDeleteBackup"
           class="btn"
           style="background:#E54B4B; color:#fff;">
          Yes, Delete
        </a>
      </div>

    </div>
  </div>
</div>


<script>
// Mengambil elemen modal delete
var deleteModal = document.getElementById('deleteBackupModal');

// Menjalankan fungsi saat modal delete ditampilkan
deleteModal.addEventListener('show.bs.modal', function (event) {
    // Mengambil tombol yang diklik
    var button = event.relatedTarget;

    // Mengambil nama file dari atribut data-file
    var fileName = button.getAttribute('data-file');

    // Menampilkan nama file di dalam modal
    document.getElementById('delete-file-name').innerText = fileName;

    // Mengatur link tombol delete agar mengarah ke file yang dipilih
    document.getElementById('confirmDeleteBackup').href =
        "backup_restore.php?delete=" + fileName;
});
</script>

<!-- Menghubungkan Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>