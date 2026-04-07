<?php
// Menghubungkan file autentikasi untuk mengecek login / session user
require_once '../auth/auth.php';

// Menghubungkan file koneksi database
require_once '../config/database.php';

// Mengecek apakah user yang login bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke halaman login admin
    header("Location: ../auth/login-admin.php");

    // Menghentikan eksekusi script
    exit;
}

// ================= TAMBAH PRODUCT =================
if (isset($_POST['add_product'])) {

    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $stock       = mysqli_real_escape_string($conn, $_POST['stock']);

    $imageName = $_FILES['image']['name'];
    $tmpName   = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

    mysqli_query($conn, "INSERT INTO products (name, description, category, price, stock, image)
                         VALUES ('$name','$description', '$category','$price','$stock','$imageName')");

    header("Location: product-management.php");
    exit;
}

// ================= UPDATE PRODUCT =================
if (isset($_POST['update_product'])) {

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $stock       = mysqli_real_escape_string($conn, $_POST['stock']);

    if (!empty($_FILES['image']['name'])) {

        $imageName = $_FILES['image']['name'];
        $tmpName   = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

        mysqli_query($conn, "UPDATE products SET 
            name='$name',
            description='$description',
            category='$category',
            price='$price',
            stock='$stock',
            image='$imageName'
            WHERE id='$id'
        ");

    } else {
        mysqli_query($conn, "UPDATE products SET 
            name='$name',
            description='$description',
            category='$category',
            price='$price',
            stock='$stock'
            WHERE id='$id'
        ");
    }

    header("Location: product-management.php");
    exit;
}

// ================= DELETE PRODUCT =================
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    header("Location: product-management.php");
    exit;
}

// ================= AMBIL DATA =================
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Product Management</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ================= GLOBAL ================= */
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f4;
    margin: 0;
    overflow: hidden; /* body tidak ikut scroll */
}

/* Wrapper utama */
.main-wrapper {
    display: flex;
    height: 100vh;
    overflow: hidden;
}

/* Content kanan */
.content {
    flex: 1;
    padding: 40px;
    height: 100vh;
    overflow-y: auto; /* hanya content yang scroll */
    overflow-x: hidden;
}

/* Judul halaman */
.page-title {
    color: #FFA4A4;
    font-weight: 600;
}

/* Button pink */
.btn-pink {
    background: #FFA4A4;
    color: #fff;
    border-radius: 12px;
    border: none;
    padding: 10px 18px;
    font-weight: 500;
}

.btn-pink:hover {
    background: #ff8e8e;
    color: white;
}

/* Card tabel */
.card {
    border-radius: 20px;
}

/* Custom select */
.select-wrapper {
    position: relative;
}

.custom-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    padding-right: 40px;
    border-radius: 12px;
}

.select-wrapper::after {
    content: "▼";
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #FFA4A4;
    font-size: 14px;
    transition: 0.3s;
    pointer-events: none;
}

/* Table */
.table {
    background: white;
    margin-bottom: 0;
}

.table thead th {
    background: #FFA4A4 !important;
    color: white !important;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

/* Action buttons */
.action-box {
    width: 43px;
    height: 43px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    cursor: pointer;
    transition: 0.2s ease;
}

.action-edit {
    background: #63C78A;
}

.action-delete {
    background: #EB4C4C;
}

.action-edit:hover {
    background: #52b477;
}

.action-delete:hover {
    background: #d63d3d;
}

.action-box i {
    font-size: 20px;
}

/* Product image */
.product-img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

/* Modal */
.modal-content {
    border-radius: 20px;
}

/* Table responsive */
.table-responsive {
    overflow-x: auto;
}
</style>
</head>

<body>

<div class="main-wrapper">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Content -->
    <div class="content">

        <h4 class="page-title mb-4">Product Data</h4>

        <!-- Button Add Product -->
        <button type="button" class="btn btn-pink mb-4" data-bs-toggle="modal" data-bs-target="#addProduct">
            + Add Product
        </button>

        <!-- Product Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Description</th>
                                <th>Picture</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php while($row = mysqli_fetch_assoc($products)) : ?>
                            <tr>
                                <td class="text-center"><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td>Rp <?= number_format($row['price']) ?></td>
                                <td><?= htmlspecialchars($row['stock']) ?></td>
                                <td>
                                    <?= strlen($row['description']) > 50 
                                        ? htmlspecialchars(substr($row['description'], 0, 50)) . '...' 
                                        : htmlspecialchars($row['description']) ?>
                                </td>
                                <td class="text-center">
                                    <img src="../assets/uploads/<?= htmlspecialchars($row['image']) ?>" class="product-img" alt="Product Image">
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        <!-- Edit button -->
                                        <div class="action-box action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editProduct"
                                            data-id="<?= $row['id'] ?>"
                                            data-name="<?= htmlspecialchars($row['name']) ?>"
                                            data-category="<?= htmlspecialchars($row['category']) ?>"
                                            data-price="<?= $row['price'] ?>"
                                            data-stock="<?= $row['stock'] ?>"
                                            data-description="<?= htmlspecialchars($row['description']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </div>

                                        <!-- Delete button -->
                                        <div class="action-box action-delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteProduct"
                                            data-id="<?= $row['id'] ?>"
                                            data-name="<?= htmlspecialchars($row['name']) ?>">
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

    </div>
</div>

<!-- ================= MODAL ADD PRODUCT ================= -->
<div class="modal fade" id="addProduct" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header" style="background:#FFA4A4;color:white;">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">

                    <input type="text" name="name" class="form-control mb-3" placeholder="Product Name" required>

                    <div class="select-wrapper mb-3">
                        <select name="category" class="form-control custom-select" required>
                            <option value="">-- Select Category --</option>
                            <option value="Boy">Boy</option>
                            <option value="Girl">Girl</option>
                        </select>
                    </div>

                    <input type="number" name="price" class="form-control mb-3" placeholder="Price" required>
                    <input type="number" name="stock" class="form-control mb-3" placeholder="Stock" required>
                    <input type="file" name="image" class="form-control mb-3" required>

                    <textarea name="description"
                        class="form-control"
                        placeholder="Product Description"
                        rows="3"
                        required></textarea>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="add_product" class="btn btn-pink w-100">
                        Save Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ================= MODAL EDIT PRODUCT ================= -->
<div class="modal fade" id="editProduct" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header" style="background:#FFA4A4;color:white;">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">

                <div class="modal-body">

                    <input type="text" name="name" id="edit-name" class="form-control mb-3" required>

                    <select name="category" id="edit-category" class="form-control mb-3" required>
                        <option value="Boy">Boy</option>
                        <option value="Girl">Girl</option>
                    </select>

                    <input type="number" name="price" id="edit-price" class="form-control mb-3" required>
                    <input type="number" name="stock" id="edit-stock" class="form-control mb-3" required>
                    <input type="file" name="image" class="form-control mb-2">

                    <small class="text-muted d-block mb-3">
                        Leave blank if you don't want to change the image
                    </small>

                    <textarea name="description"
                        id="edit-description"
                        class="form-control"
                        rows="3"
                        required></textarea>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="update_product" class="btn btn-pink w-100">
                        Update Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ================= MODAL DELETE PRODUCT ================= -->
<div class="modal fade" id="deleteProduct" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden">

            <div class="modal-header border-0" style="background:#DC3545;">
                <h5 class="modal-title text-white fw-semibold">
                    Delete Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-5">

                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:70px;"></i>

                <h5 class="mt-4">
                    Are you sure you want to delete
                    <strong id="delete-product-name"></strong>?
                </h5>

                <p class="text-muted mt-2">
                    This action cannot be undone.
                </p>

            </div>

            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Cancel
                </button>

                <a href="#" id="confirmDeleteProduct" class="btn btn-danger px-4">
                    Yes, Delete
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ================== EDIT MODAL SCRIPT ==================
const editModal = document.getElementById('editProduct');

if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        document.getElementById('edit-id').value = button.getAttribute('data-id');
        document.getElementById('edit-name').value = button.getAttribute('data-name');
        document.getElementById('edit-category').value = button.getAttribute('data-category');
        document.getElementById('edit-price').value = button.getAttribute('data-price');
        document.getElementById('edit-stock').value = button.getAttribute('data-stock');
        document.getElementById('edit-description').value = button.getAttribute('data-description');
    });
}

// ================== DELETE MODAL SCRIPT ==================
const deleteModal = document.getElementById('deleteProduct');

if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const productId = button.getAttribute('data-id');
        const productName = button.getAttribute('data-name');

        document.getElementById('delete-product-name').innerText = productName;
        document.getElementById('confirmDeleteProduct').href = 'product-management.php?delete=' + productId;
    });
}
</script>

</body>
</html>