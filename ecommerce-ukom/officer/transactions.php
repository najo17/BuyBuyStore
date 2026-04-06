<?php
require_once '../auth/auth.php'; // Cek autentikasi (biasanya sudah handle session)
require_once '../config/database.php'; // Koneksi database

// Cek role apakah officer
if ($_SESSION['role'] !== 'officer') {
    header("Location: ../auth/login-officer.php"); // Redirect jika bukan officer
    exit;
}

/* ===============================
   APPROVE PEMBAYARAN TRANSFER
================================ */
if (isset($_POST['approve_payment'])) {

    // Ambil id transaksi dari form
    $id = $_POST['transaction_id'];

    // Update status jadi approved hanya untuk transfer
    mysqli_query($conn, "UPDATE transactions 
                         SET status='approved'
                         WHERE id='$id' 
                         AND payment_method='transfer'");

    // Refresh halaman
    header("Location: transactions.php");
    exit;
}

/* ===============================
   CANCEL PEMBAYARAN TRANSFER
================================ */
if (isset($_POST['cancel_payment'])) {

    // Ambil id transaksi
    $id = $_POST['transaction_id'];

    // Update status jadi cancelled
    mysqli_query($conn, "UPDATE transactions 
                         SET status='cancelled'
                         WHERE id='$id' 
                         AND payment_method='transfer'");

    header("Location: transactions.php");
    exit;
}

/* ===============================
   AMBIL DATA TRANSAKSI
================================ */

// Ambil semua transaksi
$transactions = mysqli_query($conn, "
    SELECT * FROM transactions
    ORDER BY id DESC
");

/* ===============================
   AMBIL DATA SALES (DETAIL PRODUK)
================================ */

$salesData = []; // Array untuk menyimpan produk per transaksi

// Ambil semua data sales
$salesQuery = mysqli_query($conn, "
    SELECT transaction_id, product_name, quantity, subtotal
    FROM sales
");

// Loop data sales dan kelompokkan berdasarkan transaction_id
while($sale = mysqli_fetch_assoc($salesQuery)){
    $salesData[$sale['transaction_id']][] = $sale;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Transaction Management - Officer</title>

<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* Styling body */
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f4;
}

/* Container utama */
.content {
    padding: 40px;
    flex: 1;
}

/* Judul halaman */
.page-title {
    color: #FFA4A4;
    font-weight: 600;
}

/* Header tabel */
.table thead th {
    background: #FFA4A4 !important;
    color: white !important;
    text-align: center;
}

/* Isi tabel */
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

/* Tombol proof */
.btn-proof {
    background: #63C78A;
    color: white;
    border: none;
}

/* Tombol approve */
.btn-approve {
    background: #63C78A;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 13px;
}

/* Hover approve */
.btn-approve:hover {
    background: #52b477;
    color: white;
}

/* Tombol cancel */
.btn-cancel {
    background: #EB4C4C;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 13px;
}

/* Hover cancel */
.btn-cancel:hover {
    background: #d63d3d;
    color: white;
}

/* Modal styling */
.modal-content{
    border-radius:20px;
}

/* Receipt box */
.receipt-box{
    background:#fff;
}

/* Garis putus-putus */
.receipt-box hr{
    border-top:1px dashed #ccc;
}

/* Item produk di receipt */
.receipt-item{
    display:flex;
    justify-content:space-between;
    margin-bottom:6px;
    font-size:14px;
}

/* Header gradient */
.gradient-header{
    background:linear-gradient(135deg,#FFA4A4,#FF7E7E);
}

/* Address styling */
#r-address{
    line-height:1.6;
    word-break:break-word;
}
</style>
</head>

<body>

<div class="d-flex min-vh-100">

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<div class="content">

<h4 class="page-title mb-4">Transaction Management</h4>

<!-- TABEL TRANSAKSI -->
<table class="table table-bordered align-middle">
<thead>
<tr>
    <th>Customer</th>
    <th>Total</th>
    <th>Payment</th>
    <th>Status</th>
    <th>Proof</th>
    <th>Receipt</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($transactions)) : ?>
<tr>

<!-- Nama customer -->
<td><?= $row['customer_name'] ?></td>

<!-- Total pembayaran -->
<td>Rp <?= number_format($row['total']) ?></td>

<!-- Metode pembayaran -->
<td><?= ucfirst($row['payment_method']) ?></td>

<td>
<?php
$status = strtolower($row['status']); // status transaksi
$payment = strtolower($row['payment_method']); // metode pembayaran
?>

<!-- Jika transfer dan masih pending -->
<?php if($payment == 'transfer' && $status == 'pending'): ?>
    <div class="dropdown">
        <button class="btn btn-warning btn-sm dropdown-toggle text-dark fw-medium"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
            Pending
        </button>

        <!-- Dropdown aksi -->
        <ul class="dropdown-menu">
            <li>
                <!-- Approve -->
                <form method="POST" class="m-0">
                    <input type="hidden" name="transaction_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="approve_payment" class="dropdown-item text-success">
                        Approved
                    </button>
                </form>
            </li>
            <li>
                <!-- Cancel -->
                <form method="POST" class="m-0">
                    <input type="hidden" name="transaction_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="cancel_payment" class="dropdown-item text-danger">
                        Cancelled
                    </button>
                </form>
            </li>
        </ul>
    </div>

<!-- Jika sudah approved -->
<?php elseif($status == 'approved'): ?>
    <span class="badge bg-success">Approved</span>

<!-- Jika cancelled -->
<?php elseif($status == 'cancelled'): ?>
    <span class="badge bg-danger">Cancelled</span>

<!-- Status lain -->
<?php else: ?>
    <span class="badge bg-secondary"><?= ucfirst($row['status']) ?></span>
<?php endif; ?>
</td>

<!-- PROOF BUTTON -->
<td>
<?php if($row['payment_method'] == 'transfer' && !empty($row['payment_proof'])): ?>
    <button class="btn btn-proof btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#proofModal"
        data-img="../assets/payment_proof/<?= $row['payment_proof'] ?>">
        View
    </button>
<?php else: ?>
    <span class="badge bg-secondary">No Proof</span>
<?php endif; ?>
</td>

<!-- RECEIPT BUTTON -->
<td>
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

<!-- LOOP END -->
<td>
<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

<!-- ================= PROOF MODAL ================= -->
<div class="modal fade" id="proofModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-success text-white">
    <h5 class="modal-title">Proof of Payment</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- Menampilkan gambar bukti pembayaran -->
<div class="modal-body text-center">
    <img id="proofImage" src="" class="img-fluid rounded">
</div>

</div>
</div>
</div>

<!-- ================= RECEIPT MODAL ================= -->
<!-- Modal untuk detail transaksi -->
<div class="modal fade" id="receiptModal" tabindex="-1">
...
