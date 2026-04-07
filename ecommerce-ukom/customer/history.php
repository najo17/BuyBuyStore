<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php"); // Redirect ke login
    exit(); // Hentikan eksekusi
}

// Ambil id customer dari session
$customer_id = $_SESSION['user_id'];

/* AMBIL HISTORY TRANSAKSI CUSTOMER */
$transactions = mysqli_query($conn, "
    SELECT 
        t.*, 
        -- Menggabungkan semua produk dalam satu transaksi jadi 1 string
        GROUP_CONCAT(
            CONCAT(
                COALESCE(s.product_name, 'Produk'), -- Nama produk (default 'Produk' jika null)
                ' (x', COALESCE(s.quantity, 0), ')~~', -- Qty produk
                COALESCE(s.subtotal, 0) -- Subtotal produk
            ) SEPARATOR '##' -- Pemisah antar produk
        ) AS products
    FROM transactions t
    LEFT JOIN sales s ON t.id = s.transaction_id -- Join ke tabel sales
    WHERE t.customer_id = $customer_id -- Filter berdasarkan user
    GROUP BY t.id -- Group per transaksi
    ORDER BY t.id DESC -- Urutkan dari terbaru
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>History - BuyBuy</title>

<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jersey+20&family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Simonetta:wght@400;900&display=swap" rel="stylesheet">

    <style>
        /* Styling body */
        body{ 
            background:#fff; 
            padding-bottom:200px; /* Space untuk footer/modal */
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar */
        .navbar{ 
            background:#FFA4A4; 
        }

        /* Logo */
        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 35px;
            font-family: Simonetta;
        }

        /* Icon navbar */
        .nav-icon{
            color:white !important;
            font-size:22px;
            margin-left:20px;
        }

        /* Wrapper tabs */
        .tabs-wrapper{
            position:relative;
            margin-top:40px;
            margin-bottom:40px;
        }

        /* Tombol back */
        .back-btn{
            position:absolute;
            left:0;
            top:0;
            font-size:22px;
        }

        .back-btn a{
            color:black;
            text-decoration:none;
        }

        /* Tabs */
        .cart-tabs{
            display:flex;
            justify-content:center;
            gap:70px;
            font-weight:500;
        }

        .cart-tabs a{
            text-decoration:none;
            color:#444;
            position:relative;
            padding-bottom:5px;
        }

        /* Tab aktif */
        .cart-tabs a.active{
            color:black;
        }

        .cart-tabs a.active::after{
            content:"";
            position:absolute;
            left:0;
            bottom:0;
            width:100%;
            height:2px;
            background:black;
        }

        /* Header tabel */
        .table thead th {
            background: #FFA4A4 !important;
            color: white !important;
            text-align: center;
            vertical-align: middle;
        }

        /* Isi tabel */
        .table td {
            text-align: center;
            vertical-align: middle;
        }

        /* List produk dalam tabel */
        .product-list {
            text-align: left;
            font-size: 14px;
            line-height: 1.8;
        }

        /* Tombol action (view) */
        .action-box {
            width: 43px;
            height: 43px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            transition: 0.2s;
        }

        .action-box:hover {
            transform: scale(1.05);
        }

        .action-view { 
            background-color: #63C78A; 
        }

        /* Box struk */
        .receipt-box {
            background: #fff;
        }

        /* Garis putus-putus struk */
        .receipt-line {
            border-bottom: 1px dashed #ccc;
            margin: 8px 0;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-5">
    <a class="navbar-brand" href="index.php">BuyBuy</a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Cart -->
        <a href="cart.php" class="nav-icon">
            <i class="bi bi-cart"></i>
        </a>

        <!-- Logout -->
        <a href="../auth/logout.php" class="nav-icon">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</nav>

<div class="container mt-5">

    <div class="tabs-wrapper">

        <!-- Back -->
        <div class="back-btn">
            <a href="index.php">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>

        <!-- Tabs -->
        <div class="cart-tabs">
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
            <a href="history.php" class="active">History Transaction</a>
        </div>

    </div>

    <!-- Jika tidak ada transaksi -->
    <?php if(mysqli_num_rows($transactions) == 0): ?>
        <div class="alert alert-warning text-center">No Transactions Yet</div>
    <?php else: ?>

    <!-- Tabel transaksi -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Products</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <!-- Loop semua transaksi -->
            <?php while($row = mysqli_fetch_assoc($transactions)) : ?>
                <tr>

                    <!-- ID transaksi -->
                    <td><?= $row['id'] ?></td>

                    <!-- List produk -->
                    <td class="product-list">
                        <?php
                        if(!empty($row['products'])){
                            // Pisahkan produk berdasarkan ##
                            $items = explode('##', $row['products']);

                            foreach($items as $item){
                                // Pisahkan nama+qty dan subtotal
                                $parts = explode('~~', $item);
                                $nameQty = $parts[0];

                                // Tampilkan
                                echo "• " . htmlspecialchars($nameQty) . "<br>";
                            }
                        } else {
                            echo '<span class="text-muted">No products</span>';
                        }
                        ?>
                    </td>

                    <!-- Tanggal -->
                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>

                    <!-- Total -->
                    <td>Rp <?= number_format($row['total']) ?></td>

                    <!-- Metode pembayaran -->
                    <td><?= ucfirst($row['payment_method']) ?></td>

                    <!-- Status -->
                    <td>
                        <?php
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

                    <!-- Tombol lihat detail -->
                    <td>
                        <div class="action-box action-view"
                            data-bs-toggle="modal"
                            data-bs-target="#receiptModal"

                            
                            data-id="<?= htmlspecialchars($row['id']) ?>"
                            data-customer="<?= htmlspecialchars($row['customer_name']) ?>"
                            data-total="<?= htmlspecialchars($row['total']) ?>"
                            data-payment="<?= htmlspecialchars($row['payment_method']) ?>"
                            data-status="<?= htmlspecialchars($row['status']) ?>"
                            data-proof="<?= htmlspecialchars($row['payment_proof']) ?>"
                            data-date="<?= htmlspecialchars($row['created_at']) ?>"
                            data-address="<?= htmlspecialchars($row['shipping_address']) ?>"
                            data-products="<?= htmlspecialchars($row['products']) ?>">

                            <i class="bi bi-eye"></i>
                        </div>
                    </td>

                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>
</div>

<!-- MODAL DETAIL TRANSAKSI -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header modal -->
            <div class="modal-header text-white"
                style="background:linear-gradient(135deg,#FFA4A4,#FF7E7E);">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-receipt-cutoff me-2"></i>Transaction Receipt
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body modal -->
            <div class="modal-body p-4">
                <div class="receipt-box p-4 rounded-4 border">

                    <!-- Judul -->
                    <h6 class="text-center fw-bold mb-3">BuyBuy Store</h6>
                    <hr>

                    <!-- Data transaksi -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>ID</span><span id="r-id"></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Customer</span><span id="r-customer"></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Date</span><span id="r-date"></span>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3 mt-2">
                        <span class="fw-semibold">Address</span>
                        <div id="r-address" class="small text-muted mt-1"></div>
                    </div>

                    <hr>

                    <!-- Produk -->
                    <div>
                        <strong>Products:</strong>
                        <div id="r-products" class="mt-2 small"></div>
                    </div>

                    <hr>

                    <!-- Payment & Status -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Payment</span><span id="r-payment"></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Status</span><span id="r-status"></span>
                    </div>

                    <!-- Total -->
                    <div class="d-flex justify-content-between mt-3 fs-5 fw-bold">
                        <span>Total</span>
                        <span>Rp <span id="r-total"></span></span>
                    </div>

                    <!-- Bukti pembayaran -->
                    <div id="proofArea" class="text-center mt-4"></div>

                    <p class="text-center small mt-4 text-muted">
                        Thank you for shopping
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Ambil modal
var receiptModal = document.getElementById('receiptModal');

// Event saat modal dibuka
receiptModal.addEventListener('show.bs.modal', function (event) {

    var button = event.relatedTarget; // Tombol yang diklik

    // Isi data ke modal
    document.getElementById('r-id').innerText = button.getAttribute('data-id');
    document.getElementById('r-customer').innerText = button.getAttribute('data-customer');

    // Format total ke rupiah
    document.getElementById('r-total').innerText =
        new Intl.NumberFormat('id-ID').format(button.getAttribute('data-total'));

    // Payment
    document.getElementById('r-payment').innerText =
        button.getAttribute('data-payment').toUpperCase();

    // Format tanggal
    document.getElementById('r-date').innerText =
        new Date(button.getAttribute('data-date')).toLocaleDateString('id-ID');

    // Address
    document.getElementById('r-address').innerText =
        button.getAttribute('data-address') || '-';

    // PRODUCTS LIST
    let products = button.getAttribute('data-products');
    let productArea = document.getElementById('r-products');

    if(products){
        let list = products.split('##');

        productArea.innerHTML = list.map(item => {
            let parts = item.split('~~');
            let nameQty = parts[0];
            let subtotal = parts[1] ? parts[1] : 0;

            return `
            <div class="d-flex justify-content-between border-bottom py-1">
                <span>${nameQty}</span>
                <span>Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</span>
            </div>
            `;
        }).join('');
    } else {
        productArea.innerHTML = `<span class="text-muted">No products</span>`;
    }

    // STATUS BADGE
    let status = button.getAttribute('data-status').toLowerCase();
    let badge = '';

    if(status === 'approved'){
        badge = '<span class="badge bg-success">Approved</span>';
    } else if(status === 'pending'){
        badge = '<span class="badge bg-warning text-dark">Pending</span>';
    } else {
        badge = '<span class="badge bg-danger">Cancelled</span>';
    }

    document.getElementById('r-status').innerHTML = badge;

    // PAYMENT PROOF
    let proof = button.getAttribute('data-proof');
    let proofArea = document.getElementById('proofArea');

    if(proof && proof !== ""){
        proofArea.innerHTML = `
        <p class="fw-bold">Payment Proof</p>
        <img src="../assets/payment_proof/${proof}" 
             class="img-fluid rounded border shadow-sm">
        `;
    } else {
        proofArea.innerHTML = `<span class="badge bg-info">No Proof (COD)</span>`;
    }
});
</script>

</body>
</html>
