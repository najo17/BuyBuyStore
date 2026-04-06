<?php
// Menghubungkan file autentikasi untuk mengecek session/login user
require_once '../auth/auth.php';

// Menghubungkan file koneksi database
require_once '../config/database.php';

// Mengecek apakah user yang login bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke halaman login admin
    header("Location: ../auth/login-admin.php");

    // Menghentikan eksekusi script setelah redirect
    exit;
}

/* ================= STOCK ================= */
// Menjalankan query untuk mengambil data stok produk
$stock = mysqli_query($conn, "
    SELECT id, name, category, stock, price 
    FROM products 
    ORDER BY id DESC
");

/* ================= SALES ================= */
// Menjalankan query untuk mengambil data penjualan
$sales = mysqli_query($conn, "
    SELECT s.id, s.transaction_id, p.name AS product_name, 
           s.quantity, s.subtotal, s.created_at
    FROM sales s
    JOIN products p ON s.product_id = p.id
    ORDER BY s.id DESC
");

/* ================= TRANSACTION ================= */
// Menjalankan query untuk mengambil data transaksi
$transaction = mysqli_query($conn, "
    SELECT id, customer_name, total,
           payment_method, status,
           payment_proof, created_at
    FROM transactions
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<!-- Menentukan encoding karakter -->
<meta charset="UTF-8">

<!-- Judul halaman di tab browser -->
<title>Generate Report</title>

<!-- Icon website / favicon -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* Styling body halaman */
body {
    font-family: Poppins, sans-serif;
    background: #f4f4f4;
}

/* Styling area konten utama */
.content {
    padding: 40px;
    flex: 1;
}

/* Styling judul halaman */
.page-title {
    color: #FFA4A4;
    font-weight: 600;
}

/* Styling tab custom */
.custom-tabs .nav-link {
    background: #ffffff;
    border: none;
    color: #FFA4A4;
    font-weight: 500;
    border-radius: 10px 10px 0 0;
    margin-right: 5px;
}

/* Styling tab yang sedang aktif */
.custom-tabs .nav-link.active {
    background: #FFA4A4;
    color: white;
}

/* Styling header tabel */
.table thead th {
    background: #FFA4A4 !important;
    color: white !important;
    text-align: center;
}

/* Styling isi tabel */
.table td {
    text-align: center;
    vertical-align: middle;
}

/* Styling kotak action */
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

/* Warna action view */
.action-view { background-color: #63C78A; }

/* Garis putus-putus seperti struk */
.receipt-line {
    border-bottom: 1px dashed #ccc;
    margin: 8px 0;
}
</style>
</head>

<body>

<!-- Container utama halaman -->
<div class="d-flex min-vh-100">

<!-- Menampilkan sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Konten utama -->
<div class="content">

<!-- Judul halaman -->
<h4 class="page-title mb-4">Generate Report</h4>

<!-- Navigasi tab -->
<ul class="nav custom-tabs mb-4">
    <li class="nav-item">
        <!-- Tombol tab Stock -->
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#stock">Stock</button>
    </li>
    <li class="nav-item">
        <!-- Tombol tab Sales -->
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sales">Sales</button>
    </li>
    <li class="nav-item">
        <!-- Tombol tab Transaction -->
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transaction">Transaction</button>
    </li>
</ul>

<!-- Isi tab -->
<div class="tab-content">

<!-- ================= STOCK ================= -->
<!-- Tab untuk menampilkan data stok -->
<div class="tab-pane fade show active" id="stock">
<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Stock</th>
<th>Price</th>
</tr>
</thead>
<tbody>

<!-- Mengambil data stock satu per satu -->
<?php while($row = mysqli_fetch_assoc($stock)) : ?>
<tr>
<!-- Menampilkan ID produk -->
<td><?= $row['id'] ?></td>

<!-- Menampilkan nama produk -->
<td><?= $row['name'] ?></td>

<!-- Menampilkan kategori produk -->
<td><?= $row['category'] ?></td>

<!-- Menampilkan jumlah stok -->
<td><?= $row['stock'] ?></td>

<!-- Menampilkan harga dengan format rupiah -->
<td>Rp <?= number_format($row['price']) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<!-- ================= SALES ================= -->
<!-- Tab untuk menampilkan data penjualan -->
<div class="tab-pane fade" id="sales">
<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Product</th>
<th>Date</th>
<th>Qty</th>
<th>Subtotal</th>
</tr>
</thead>
<tbody>

<!-- Mengambil data sales satu per satu -->
<?php while($row = mysqli_fetch_assoc($sales)) : ?>
<tr>
<!-- Menampilkan ID penjualan -->
<td><?= $row['id'] ?></td>

<!-- Menampilkan nama produk -->
<td><?= $row['product_name'] ?></td>

<!-- Menampilkan tanggal penjualan -->
<td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>

<!-- Menampilkan jumlah barang yang terjual -->
<td><?= $row['quantity'] ?></td>

<!-- Menampilkan subtotal penjualan -->
<td>Rp <?= number_format($row['subtotal']) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<!-- ================= TRANSACTION ================= -->
<!-- Tab untuk menampilkan data transaksi -->
<div class="tab-pane fade" id="transaction">
<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Date</th>
<th>Total</th>
<th>Payment</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<!-- Mengambil data transaksi satu per satu -->
<?php while($row = mysqli_fetch_assoc($transaction)) : ?>
<tr>
<!-- Menampilkan ID transaksi -->
<td><?= $row['id'] ?></td>

<!-- Menampilkan nama customer -->
<td><?= $row['customer_name'] ?></td>

<!-- Menampilkan tanggal transaksi -->
<td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>

<!-- Menampilkan total transaksi -->
<td>Rp <?= number_format($row['total']) ?></td>

<!-- Menampilkan metode pembayaran dengan huruf awal kapital -->
<td><?= ucfirst($row['payment_method']) ?></td>

<td>
<?php
// Mengubah status transaksi menjadi huruf kecil agar konsisten saat dicek
$status = strtolower($row['status']);

// Jika status approved, tampilkan badge hijau
if($status == 'approved'){
    echo '<span class="badge bg-success">Approved</span>';

// Jika status pending, tampilkan badge kuning
} elseif($status == 'pending'){
    echo '<span class="badge bg-warning text-dark">Pending</span>';

// Selain itu tampilkan badge merah (cancelled)
} else {
    echo '<span class="badge bg-danger">Cancelled</span>';
}
?>
</td>

<td>
<!-- Tombol action untuk membuka modal detail transaksi -->
<div class="action-box action-view"
data-bs-toggle="modal"
data-bs-target="#receiptModal"
data-id="<?= $row['id'] ?>"
data-customer="<?= $row['customer_name'] ?>"
data-total="<?= $row['total'] ?>"
data-payment="<?= $row['payment_method'] ?>"
data-status="<?= $row['status'] ?>"
data-proof="<?= $row['payment_proof'] ?>"
data-date="<?= $row['created_at'] ?>">
<i class="bi bi-eye"></i>
</div>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

</div>
</div>
</div>

<!-- ================= TRANSACTION MODAL (NEW STYLE) ================= -->
<!-- Modal untuk menampilkan detail / receipt transaksi -->
<div class="modal fade" id="receiptModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

<!-- HEADER -->
<!-- Bagian header modal -->
<div class="modal-header text-white"
     style="background:linear-gradient(135deg,#FFA4A4,#FF7E7E);">
<h5 class="modal-title fw-semibold">
<i class="bi bi-receipt-cutoff me-2"></i>Transaction Receipt
</h5>

<!-- Tombol close modal -->
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- BODY -->
<!-- Isi modal -->
<div class="modal-body p-4 bg-light">

<!-- Kotak struk / receipt -->
<div class="receipt-box bg-white p-4 rounded-4 shadow-sm border"
     style="max-width:400px;margin:auto;">

<!-- Nama toko -->
<h6 class="text-center fw-bold mb-3">BuyBuy Store</h6>
<hr>

<!-- Bagian ID transaksi -->
<div class="d-flex justify-content-between mb-2">
<span>ID</span>
<span id="r-id" class="fw-semibold"></span>
</div>

<!-- Bagian customer -->
<div class="d-flex justify-content-between mb-2">
<span>Customer</span>
<span id="r-customer"></span>
</div>

<!-- Bagian tanggal -->
<div class="d-flex justify-content-between mb-2">
<span>Date</span>
<span id="r-date"></span>
</div>

<hr>

<!-- Bagian payment -->
<div class="d-flex justify-content-between mb-2">
<span>Payment</span>
<span id="r-payment" class="text-uppercase small"></span>
</div>

<!-- Bagian status -->
<div class="d-flex justify-content-between mb-2">
<span>Status</span>
<span id="r-status"></span>
</div>

<!-- Bagian total -->
<div class="d-flex justify-content-between mt-3 fs-5 fw-bold">
<span>Total</span>
<span>Rp <span id="r-total"></span></span>
</div>

<hr>

<!-- Area untuk menampilkan bukti pembayaran -->
<div id="proofArea" class="text-center mt-3"></div>

<!-- Teks penutup -->
<p class="text-center small mt-4 text-muted">
Thank you for shopping with us
</p>

</div>

</div>

</div>
</div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Mengambil elemen modal receipt
var receiptModal = document.getElementById('receiptModal');

// Menjalankan fungsi saat modal dibuka
receiptModal.addEventListener('show.bs.modal', function (event) {

// Mengambil elemen tombol / action yang diklik
var button = event.relatedTarget;

    // Mengisi ID transaksi ke modal
    document.getElementById('r-id').innerText = button.getAttribute('data-id');

    // Mengisi nama customer ke modal
    document.getElementById('r-customer').innerText = button.getAttribute('data-customer');

    // Mengisi total transaksi dengan format angka Indonesia
    document.getElementById('r-total').innerText =
        new Intl.NumberFormat('id-ID').format(button.getAttribute('data-total'));

    // Mengisi metode pembayaran
    document.getElementById('r-payment').innerText =
        button.getAttribute('data-payment');

    // Mengisi tanggal transaksi dengan format lokal Indonesia
    document.getElementById('r-date').innerText =
        new Date(button.getAttribute('data-date')).toLocaleDateString('id-ID');

    // STATUS BADGE
    // Mengambil status transaksi lalu dijadikan huruf kecil
    let status = button.getAttribute('data-status').toLowerCase();

    // Variabel untuk menyimpan badge status
    let badge = '';

    // Jika status approved, buat badge hijau
    if(status === 'approved'){
        badge = '<span class="badge bg-success px-3 py-1">Approved</span>';

    // Jika status pending, buat badge kuning
    } else if(status === 'pending'){
        badge = '<span class="badge bg-warning text-dark px-3 py-1">Pending</span>';

    // Selain itu, buat badge merah
    } else {
        badge = '<span class="badge bg-danger px-3 py-1">Cancelled</span>';
    }

    // Menampilkan badge status ke modal
    document.getElementById('r-status').innerHTML = badge;

    // PAYMENT PROOF
    // Mengambil nama file bukti pembayaran
    let proof = button.getAttribute('data-proof');

    // Mengambil area tempat bukti pembayaran ditampilkan
    let proofArea = document.getElementById('proofArea');

    // Jika ada file bukti pembayaran
    if(proof && proof !== ""){
        // Tampilkan gambar bukti pembayaran
        proofArea.innerHTML = `
            <p class="fw-bold small">Payment Proof</p>
            <img src="../assets/payment_proof/${proof}" 
                 class="img-fluid rounded shadow-sm border">
        `;

    // Jika tidak ada bukti pembayaran, anggap COD
    } else {
        proofArea.innerHTML = `<span class="badge bg-info px-3 py-1">No Proof (COD)</span>`;
    }
});
</script>


</body>
</html>