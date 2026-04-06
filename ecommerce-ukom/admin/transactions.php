<?php
// Memanggil file autentikasi untuk memastikan user sudah login
require_once '../auth/auth.php';

// Memanggil file koneksi database
require_once '../config/database.php';

// Mengecek apakah role user bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, redirect ke halaman login admin
    header("Location: ../auth/login-admin.php");
    // Menghentikan eksekusi script
    exit;
}

// Mengambil semua data transaksi dari tabel transactions
// Diurutkan dari ID terbesar ke terkecil (data terbaru tampil di atas)
$transactions = mysqli_query($conn, "
    SELECT * FROM transactions
    ORDER BY id DESC
");

/* ===============================
   AMBIL DATA SALES (DETAIL PRODUK)
================================ */

// Membuat array kosong untuk menyimpan detail produk dari tiap transaksi
$salesData = [];

// Mengambil data penjualan dari tabel sales
// Berisi transaction_id, nama produk, jumlah, dan subtotal
$salesQuery = mysqli_query($conn, "
    SELECT transaction_id, product_name, quantity, subtotal
    FROM sales
");

// Melakukan perulangan untuk setiap data sales
while($sale = mysqli_fetch_assoc($salesQuery)){
    // Mengelompokkan produk berdasarkan transaction_id
    // Jadi setiap transaksi punya daftar produk masing-masing
    $salesData[$sale['transaction_id']][] = $sale;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Transaction Management</title>

<!-- Icon logo pada tab browser -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Import Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Import Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Import font Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* Styling body utama */
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f4;
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
}

/* Warna header tabel */
.table thead th {
    background: #FFA4A4 !important;
    color: white !important;
    text-align: center;
}

/* Isi tabel rata tengah */
.table td {
    text-align: center;
    vertical-align: middle;
}

/* Tombol receipt */
.btn-receipt {
    background: #63C78A;
    color: white;
    border: none;
}

/* Tombol bukti pembayaran */
.btn-proof {
    background: #63C78A;
    color: white;
    border: none;
}

/* Membuat sudut modal lebih bulat */
.modal-content{
    border-radius:20px;
}

/* Box isi receipt */
.receipt-box{
    background:#fff;
}

/* Garis putus-putus pada receipt */
.receipt-box hr{
    border-top:1px dashed #ccc;
}

/* Baris item produk di receipt */
.receipt-item{
    display:flex;
    justify-content:space-between;
    margin-bottom:6px;
    font-size:14px;
}

/* Header modal dengan warna gradasi */
.gradient-header{
    background:linear-gradient(135deg,#FFA4A4,#FF7E7E);
}
#r-address{
    line-height:1.6;
    word-break:break-word;
}
</style>
</head>

<body>

<!-- Container utama halaman -->
<div class="d-flex min-vh-100">

<!-- Memanggil sidebar admin -->
<?php include 'sidebar.php'; ?>

<!-- Konten utama -->
<div class="content">

<!-- Judul halaman -->
<h4 class="page-title mb-4">Transaction Management</h4>

<!-- Tabel data transaksi -->
<table class="table table-bordered align-middle">
<thead>
<tr>
    <th>Customer</th>
    <th>Total</th>
    <th>Payment</th>
    <th>Status</th>
    <th>Proof</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<!-- Melakukan perulangan untuk menampilkan semua transaksi -->
<?php while($row = mysqli_fetch_assoc($transactions)) : ?>
<tr>

<!-- Menampilkan nama customer -->
<td><?= $row['customer_name'] ?></td>

<!-- Menampilkan total transaksi dengan format Rupiah -->
<td>Rp <?= number_format($row['total']) ?></td>

<!-- Menampilkan metode pembayaran dengan huruf awal kapital -->
<td><?= ucfirst($row['payment_method']) ?></td>

<td>
<?php
// Mengubah status menjadi huruf kecil agar mudah dicek
$status = strtolower($row['status']);

// Menampilkan badge sesuai status transaksi
if($status == 'approved'){
    echo '<span class="badge bg-success">Approved</span>';
} elseif($status == 'pending'){
    echo '<span class="badge bg-warning text-dark">Pending</span>';
} else {
    echo '<span class="badge bg-danger">Cancelled</span>';
}
?>
</td>

<!-- PROOF BUTTON -->
<td>
<?php if($row['payment_method'] == 'transfer' && !empty($row['payment_proof'])): ?>
    <!-- Jika pembayaran transfer dan ada bukti pembayaran -->
    <button class="btn btn-proof btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#proofModal"
        data-img="../assets/payment_proof/<?= $row['payment_proof'] ?>">
        View
    </button>
<?php else: ?>
    <!-- Jika COD atau tidak ada bukti -->
    <span class="badge bg-secondary">No Proof (Cod)</span>
<?php endif; ?>
</td>

<!-- RECEIPT BUTTON -->
<td>
<!-- Tombol untuk membuka modal receipt -->
<button class="btn btn-receipt btn-sm"
    data-bs-toggle="modal"
    data-bs-target="#receiptModal"
    data-id="<?= $row['id'] ?>"
    data-customer="<?= htmlspecialchars($row['customer_name']) ?>"
    data-total="<?= $row['total'] ?>"
    data-payment="<?= $row['payment_method'] ?>"
    data-status="<?= $row['status'] ?>"
    data-date="<?= $row['created_at'] ?>"
    data-address="<?= htmlspecialchars($row['shipping_address']) ?>"
    data-products='<?= json_encode($salesData[$row["id"]] ?? []) ?>'
    data-proof="<?= $row['payment_proof'] ?>">
    Receipt
</button>
</td>

</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>
</div>


<!-- ================= PROOF MODAL ================= -->
<div class="modal fade" id="proofModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<!-- Header modal bukti pembayaran -->
<div class="modal-header bg-success text-white">
    <h5 class="modal-title">Proof of Payment</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- Isi modal berupa gambar bukti pembayaran -->
<div class="modal-body text-center">
    <img id="proofImage" src="" class="img-fluid rounded">
</div>

</div>
</div>
</div>


<!-- ================= RECEIPT MODAL ================= -->
<div class="modal fade" id="receiptModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content shadow-lg border-0">

<!-- Header modal receipt -->
<div class="modal-header gradient-header text-white">
    <h5 class="modal-title fw-semibold">
        <i class="bi bi-receipt-cutoff me-2"></i>
        Transaction Receipt
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- Isi body modal -->
<div class="modal-body p-4">

<!-- Box receipt -->
<div class="receipt-box p-4 rounded-4 border">

<!-- Nama toko -->
<h6 class="text-center fw-bold mb-3">BUYBUY STORE</h6>
<hr>

<!-- ID transaksi -->
<div class="d-flex justify-content-between mb-2">
    <span>ID</span>
    <span id="r-id"></span>
</div>

<!-- Nama customer -->
<div class="d-flex justify-content-between mb-2">
    <span>Customer</span>
    <span id="r-customer"></span>
</div>

<div class="d-flex justify-content-between mb-2">
    <span>Date</span>
    <span id="r-date"></span>
</div>

<div class="mb-3 mt-2">
    <span class="fw-semibold">Address</span>
    <div id="r-address" class="small text-muted mt-1"></div>
</div>

<hr>

<strong>Products:</strong>
<div id="r-products" class="mt-2 small"></div>

<hr>

<!-- Metode pembayaran -->
<div class="d-flex justify-content-between mb-2">
    <span>Payment</span>
    <span id="r-payment"></span>
</div>

<!-- Status transaksi -->
<div class="d-flex justify-content-between mb-2">
    <span>Status</span>
    <span id="r-status"></span>
</div>

<!-- Total transaksi -->
<div class="d-flex justify-content-between mt-3 fs-5 fw-bold">
    <span>Total</span>
    <span>Rp <span id="r-total"></span></span>
</div>

<!-- Area bukti pembayaran -->
<div id="proofArea" class="text-center mt-4"></div>

<!-- Pesan penutup -->
<p class="text-center small mt-4 text-muted">
Thank you for shopping 
</p>

</div>

</div>
</div>
</div>
</div>

<!-- Import Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ================= PROOF IMAGE =================

// Mengambil elemen modal bukti pembayaran
var proofModal = document.getElementById('proofModal');

// Saat modal proof dibuka
proofModal.addEventListener('show.bs.modal', function (event) {
    // Mengambil tombol yang diklik
    var button = event.relatedTarget;

    // Mengambil path gambar dari atribut data-img
    var img = button.getAttribute('data-img');

    // Menampilkan gambar ke dalam modal
    document.getElementById('proofImage').src = img;
});

// ================= RECEIPT DATA =================

// Mengambil elemen modal receipt
var receiptModal = document.getElementById('receiptModal');

// Saat modal receipt dibuka
receiptModal.addEventListener('show.bs.modal', function (event) {

    // Mengambil tombol yang diklik
    var button = event.relatedTarget;

    // Menampilkan ID transaksi
    document.getElementById('r-id').innerText =
        button.getAttribute('data-id');

    // Menampilkan nama customer
    document.getElementById('r-customer').innerText =
        button.getAttribute('data-customer');

    // Menampilkan tanggal transaksi dalam format Indonesia
    document.getElementById('r-date').innerText =
        new Date(button.getAttribute('data-date'))
        .toLocaleDateString('id-ID');
    
    document.getElementById('r-address').innerText =
    button.getAttribute('data-address') || '-';

    // Menampilkan total transaksi dengan format angka Indonesia
    document.getElementById('r-total').innerText =
        new Intl.NumberFormat('id-ID')
        .format(button.getAttribute('data-total'));

    // Menampilkan metode pembayaran dalam huruf kapital
    document.getElementById('r-payment').innerText =
        button.getAttribute('data-payment').toUpperCase();

    // ================= STATUS =================

    // Mengambil status transaksi
    let status = button.getAttribute('data-status').toLowerCase();

    // Variabel untuk menyimpan badge status
    let badge = '';

    // Menentukan badge berdasarkan status
    if(status === 'approved'){
        badge = '<span class="badge bg-success">Approved</span>';
    } else if(status === 'pending'){
        badge = '<span class="badge bg-warning text-dark">Pending</span>';
    } else {
        badge = '<span class="badge bg-danger">Cancelled</span>';
    }

    // Menampilkan badge status ke modal
    document.getElementById('r-status').innerHTML = badge;

    // ================= PRODUCTS =================

    // Mengambil data produk dari atribut data-products lalu mengubah JSON menjadi array
    let products = JSON.parse(
        button.getAttribute('data-products') || "[]"
    );

    // Variabel untuk menampung HTML daftar produk
    let productHTML = '';

    // Perulangan setiap produk dalam transaksi
    products.forEach(item => {
        // Menambahkan setiap produk ke tampilan receipt
        productHTML += `
            <div class="receipt-item">
                <span>${item.product_name} (x${item.quantity})</span>
                <span>Rp ${new Intl.NumberFormat('id-ID')
                    .format(item.subtotal)}</span>
            </div>
        `;
    });

    // Menampilkan daftar produk ke modal
    // Jika kosong, tampilkan "No product"
    document.getElementById('r-products').innerHTML =
        productHTML || '<span class="text-muted">No product</span>';

    // ================= PROOF =================

    // Mengambil nama file bukti pembayaran
    let proof = button.getAttribute('data-proof');

    // Mengambil area proof di modal
    let proofArea = document.getElementById('proofArea');

    // Jika ada bukti pembayaran
    if(proof && proof !== ""){
        // Tampilkan gambar bukti pembayaran
        proofArea.innerHTML = `
            <p class="fw-bold">Payment Proof</p>
            <img src="../assets/payment_proof/${proof}"
                 class="img-fluid rounded border shadow-sm">
        `;
    } else {
        // Jika tidak ada bukti, kosongkan area
        proofArea.innerHTML = '';
    }
});
</script>

</body>
</html>