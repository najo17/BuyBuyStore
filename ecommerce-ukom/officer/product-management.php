<?php
// ======================== PHP BACKEND ========================

// Memanggil file autentikasi untuk memastikan user sudah login
require_once '../auth/auth.php';

// Memanggil file konfigurasi database
require_once '../config/database.php';

// Mengecek role user. Jika bukan 'officer', redirect ke halaman login officer
if ($_SESSION['role'] !== 'officer') {
    header("Location: ../auth/login-officer.php");
    exit;
}

// ================= TAMBAH PRODUCT =================
// Mengecek jika form Add Product disubmit
if (isset($_POST['add_product'])) {

    // Ambil data dari form dan escape agar aman dari SQL Injection
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $stock       = mysqli_real_escape_string($conn, $_POST['stock']);

    // Ambil file gambar dari form
    $imageName = $_FILES['image']['name'];
    $tmpName   = $_FILES['image']['tmp_name'];

    // Pindahkan gambar ke folder uploads
    move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

    // Masukkan data produk ke database
    mysqli_query($conn, "INSERT INTO products (name, description, category, price, stock, image)
                         VALUES ('$name','$description', '$category','$price','$stock','$imageName')");

    // Redirect ke halaman product-management
    header("Location: product-management.php");
    exit;
}

// ================= UPDATE PRODUCT =================
// Mengecek jika form Edit Product disubmit
if (isset($_POST['update_product'])) {

    // Ambil data dari form edit
    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $stock       = mysqli_real_escape_string($conn, $_POST['stock']);

    // Jika ada file gambar baru
    if (!empty($_FILES['image']['name'])) {

        $imageName = $_FILES['image']['name'];
        $tmpName   = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

        // Update data beserta gambar
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
        // Update data tanpa gambar
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
// Mengecek jika ada parameter delete di URL
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    header("Location: product-management.php");
    exit;
}

// ================= AMBIL DATA =================
// Mengambil semua data produk dari database
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Product Management</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ================= STYLE CSS ================= */
body { 
    font-family: Poppins; background:#f4f4f4; 
    overflow: hidden;
}

.content { padding:40px; flex:1; }

.page-title { color:#FFA4A4; font-weight:600; }

/* Button pink */
.btn-pink { background:#FFA4A4; color:#fff; border-radius:12px; }
.btn-pink:hover { background:#ff8e8e; }

/* Custom select */
.select-wrapper { position: relative; }
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

/* Table header */
.table thead th {
    background:#FFA4A4 !important;
    color:white !important;
    text-align:center;
}

/* Action buttons */
.action-box {
    width:43px;
    height:43px;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    cursor:pointer;
}

.action-edit { background:#63C78A; }
.action-delete { background:#EB4C4C; }

.action-box i { font-size:20px; }

/* Product image */
.product-img {
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
}
</style>
</head>

<body>
<div class="d-flex min-vh-100">

<?php include 'sidebar.php'; ?> <!-- Sidebar navigation -->

<div class="content">

<h4 class="page-title mb-3">Product Data</h4>

<!-- Button Add Product -->
<button class="btn btn-pink mb-3" data-bs-toggle="modal" data-bs-target="#addProduct"> + Add Product
</button>

<!-- Product Table -->
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

<!-- Looping data produk -->
<?php while($row = mysqli_fetch_assoc($products)) : ?>
<tr>
<td class="text-center"><?= $row['id'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['category'] ?></td>
<td>Rp <?= number_format($row['price']) ?></td>
<td><?= $row['stock'] ?></td>
<td><?= substr($row['description'],0,50); ?>...</td>
<td class="text-center">
<img src="../assets/uploads/<?= $row['image'] ?>" class="product-img">
</td>
<td class="text-center">
<div class="d-flex justify-content-center gap-2">

<!-- Edit button -->
<div class="action-box action-edit"
data-bs-toggle="modal"
data-bs-target="#editProduct"
data-id="<?= $row['id'] ?>"
data-name="<?= $row['name'] ?>"
data-category="<?= $row['category'] ?>"
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
     data-name="<?= $row['name'] ?>">
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

<!-- ================= MODAL ADD PRODUCT ================= -->
<div class="modal fade" id="addProduct">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header" style="background:#FFA4A4;color:white;">
<h5>Add Product</h5>
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
<div class="modal fade" id="editProduct">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header" style="background:#FFA4A4;color:white;">
        <h5>Edit Product</h5>
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

      <!-- HEADER -->
      <div class="modal-header border-0" style="background:#DC3545;">
        <h5 class="modal-title text-white fw-semibold">
          Delete Product
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body text-center py-5">

        <i class="bi bi-exclamation-triangle-fill text-danger"
           style="font-size:70px;"></i>

        <h5 class="mt-4">
          Are you sure you want to delete
          <strong id="delete-product-name"></strong>?
        </h5>

        <p class="text-muted mt-2">
          This action cannot be undone.
        </p>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button"
                class="btn btn-secondary px-4"
                data-bs-dismiss="modal">
          Cancel
        </button>

        <a href="#" id="confirmDeleteProduct"
           class="btn btn-danger px-4">
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

editModal.addEventListener('show.bs.modal', function (event) {

  const button = event.relatedTarget;

  // Set form input values dari data atribut tombol
  document.getElementById('edit-id').value = button.getAttribute('data-id');
  document.getElementById('edit-name').value = button.getAttribute('data-name');
  document.getElementById('edit-category').value = button.getAttribute('data-category');
  document.getElementById('edit-price').value = button.getAttribute('data-price');
  document.getElementById('edit-stock').value = button.getAttribute('data-stock');
  document.getElementById('edit-description').value =
      button.getAttribute('data-description');
});

// ================== DELETE MODAL SCRIPT ==================
const deleteModal = document.getElementById('deleteProduct');

deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    const productId = button.getAttribute('data-id');
    const productName = button.getAttribute('data-name');

    // Set nama produk di modal
    document.getElementById('delete-product-name').innerText = productName;

    // Set link confirm delete
    document.getElementById('confirmDeleteProduct').href =
        'product-management.php?delete=' + productId;
});
</script>

</body>
</html>