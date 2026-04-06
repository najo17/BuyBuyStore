<?php
// Memanggil file autentikasi officer
require_once '../auth/auth.php';
// Memanggil file database untuk koneksi MySQL
require_once '../config/database.php';

// Pastikan user yang login adalah officer
if ($_SESSION['role'] !== 'officer') {
    // Jika bukan officer, redirect ke halaman login officer
    header("Location: ../auth/login-officer.php");
    exit;
}

/* ================= STOCK ================= */
// Mengambil data stok produk dari tabel products
$stock = mysqli_query($conn, "
    SELECT id, name, category, stock, price 
    FROM products 
    ORDER BY id DESC
");

/* ================= SALES ================= */
// Mengambil data penjualan dari tabel sales
// JOIN dengan tabel products untuk mengambil nama produk
$sales = mysqli_query($conn, "
    SELECT s.id, s.transaction_id, p.name AS product_name, 
           s.quantity, s.subtotal, s.created_at
    FROM sales s
    JOIN products p ON s.product_id = p.id
    ORDER BY s.id DESC
");

/* ================= TRANSACTION ================= */
// Mengambil data transaksi dari tabel transactions
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
<meta charset="UTF-8">
<title>Generate Report</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* Style umum halaman */
body {
    font-family: Poppins, sans-serif;
    background: #f4f4f4;
}
.content {
    padding: 40px;
    flex: 1;
}
.page-title {
    color: #FFA4A4;
    font-weight: 600;
}
/* Tab navigasi kustom */
.custom-tabs .nav-link {
    background: #ffffff;
    border: none;
    color: #FFA4A4;
    font-weight: 500;
    border-radius: 10px 10px 0 0;
    margin-right: 5px;
}
.custom-tabs .nav-link.active {
    background: #FFA4A4;
    color: white;
}
/* Tabel */
.table thead th {
    background: #FFA4A4 !important;
    color: white !important;
    text-align: center;
}
.table td {
    text-align: center;
    vertical-align: middle;
}
/* Tombol aksi view */
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
.action-view { background-color: #63C78A; }
/* Garis dashed di struk */
.receipt-line {
    border-bottom: 1px dashed #ccc;
    margin: 8px 0;
}
</style>
</head>

<body>

<div class="d-flex min-vh-100">

<!-- Sidebar officer -->
<?php include 'sidebar.php'; ?>

<div class="content">

<h4 class="page-title mb-4">Generate Report</h4>

<!-- Tab navigasi: Stock, Sales, Transaction -->
<ul class="nav custom-tabs mb-4">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#stock">Stock</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sales">Sales</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transaction">Transaction</button>
    </li>
</ul>

<div class="tab-content">

<!-- ================= STOCK ================= -->
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
        <?php while($row = mysqli_fetch_assoc($stock)) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><?= $row['stock'] ?></td>
                <td>Rp <?= number_format($row['price']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- ================= SALES ================= -->
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
        <?php while($row = mysqli_fetch_assoc($sales)) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>Rp <?= number_format($row['subtotal']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- ================= TRANSACTION ================= -->
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
        <?php while($row = mysqli_fetch_assoc($transaction)) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['customer_name'] ?></td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>Rp <?= number_format($row['total']) ?></td>
                <td><?= ucfirst($row['payment_method']) ?></td>
                <td>
                    <?php
                    // Menentukan badge status transaksi
                    $status = strtolower($row['status']);
                    if($status == 'approved'){
                        echo '<span class="badge bg-success">Approved</span>';
                    } elseif($status == 'pending'){
                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                    } else {
                        echo '<span class="badge bg-danger">Cancelled</span>';
                    }
                    ?>
                </td>
                <td>
                    <!-- Tombol view detail transaksi -->
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

<!-- ================= TRANSACTION MODAL ================= -->
<div class="modal fade" id="receiptModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

<!-- Header Modal -->
<div class="modal-header text-white"
     style="background:linear-gradient(135deg,#FFA4A4,#FF7E7E);">
<h5 class="modal-title fw-semibold">
<i class="bi bi-receipt-cutoff me-2"></i>Transaction Receipt
</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- Body Modal -->
<div class="modal-body p-4 bg-light">
<div class="receipt-box bg-white p-4 rounded-4 shadow-sm border"
     style="max-width:400px;margin:auto;">
<h6 class="text-center fw-bold mb-3">BuyBuy Store</h6>
<hr>

<!-- Info transaksi -->
<div class="d-flex justify-content-between mb-2">
    <span>ID</span>
    <span id="r-id" class="fw-semibold"></span>
</div>
<div class="d-flex justify-content-between mb-2">
    <span>Customer</span>
    <span id="r-customer"></span>
</div>
<div class="d-flex justify-content-between mb-2">
    <span>Date</span>
    <span id="r-date"></span>
</div>
<hr>
<div class="d-flex justify-content-between mb-2">
    <span>Payment</span>
    <span id="r-payment" class="text-uppercase small"></span>
</div>
<div class="d-flex justify-content-between mb-2">
    <span>Status</span>
    <span id="r-status"></span>
</div>
<div class="d-flex justify-content-between mt-3 fs-5 fw-bold">
    <span>Total</span>
    <span>Rp <span id="r-total"></span></span>
</div>
<hr>
<!-- Payment proof -->
<div id="proofArea" class="text-center mt-3"></div>

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
// Event untuk menampilkan detail transaksi di modal
var receiptModal = document.getElementById('receiptModal');

receiptModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;

    // Isi data transaksi ke modal
    document.getElementById('r-id').innerText = button.getAttribute('data-id');
    document.getElementById('r-customer').innerText = button.getAttribute('data-customer');
    document.getElementById('r-total').innerText =
        new Intl.NumberFormat('id-ID').format(button.getAttribute('data-total'));
    document.getElementById('r-payment').innerText =
        button.getAttribute('data-payment');
    document.getElementById('r-date').innerText =
        new Date(button.getAttribute('data-date')).toLocaleDateString('id-ID');

    // Status badge
    let status = button.getAttribute('data-status').toLowerCase();
    let badge = '';
    if(status === 'approved'){
        badge = '<span class="badge bg-success px-3 py-1">Approved</span>';
    } else if(status === 'pending'){
        badge = '<span class="badge bg-warning text-dark px-3 py-1">Pending</span>';
    } else {
        badge = '<span class="badge bg-danger px-3 py-1">Cancelled</span>';
    }
    document.getElementById('r-status').innerHTML = badge;

    // Payment proof
    let proof = button.getAttribute('data-proof');
    let proofArea = document.getElementById('proofArea');
    if(proof && proof !== ""){
        proofArea.innerHTML = `
            <p class="fw-bold small">Payment Proof</p>
            <img src="../assets/payment_proof/${proof}" 
                 class="img-fluid rounded shadow-sm border">
        `;
    } else {
        proofArea.innerHTML = `<span class="badge bg-info px-3 py-1">No Proof (COD)</span>`;
    }
});
</script>

</body>
</html>