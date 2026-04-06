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
// Mengecek apakah tombol add_product ditekan
if (isset($_POST['add_product'])) {

        // Mengambil input nama produk lalu diamankan dari karakter berbahaya
        $name        = mysqli_real_escape_string($conn, $_POST['name']);

        // Mengambil input deskripsi produk lalu diamankan
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Mengambil input kategori produk lalu diamankan
        $category    = mysqli_real_escape_string($conn, $_POST['category']);

        // Mengambil input harga produk lalu diamankan
        $price       = mysqli_real_escape_string($conn, $_POST['price']);

        // Mengambil input stok produk lalu diamankan
        $stock       = mysqli_real_escape_string($conn, $_POST['stock']);
      

    // Mengambil nama file gambar yang diupload
    $imageName = $_FILES['image']['name'];

    // Mengambil lokasi file sementara gambar
    $tmpName   = $_FILES['image']['tmp_name'];

    // Memindahkan file gambar ke folder uploads
    move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

    // Menyimpan data produk baru ke tabel products
    mysqli_query($conn, "INSERT INTO products (name, description, category, price, stock, image)
                         VALUES ('$name','$description', '$category','$price','$stock','$imageName')");

    // Setelah berhasil tambah produk, redirect kembali ke halaman product management
    header("Location: product-management.php");

    // Menghentikan eksekusi script
    exit;
}


// ================= UPDATE PRODUCT =================
// Mengecek apakah tombol update_product ditekan
if (isset($_POST['update_product'])) {

    // Mengambil ID produk yang akan diupdate lalu diamankan
    $id          = mysqli_real_escape_string($conn, $_POST['id']);

    // Mengambil input nama produk lalu diamankan
    $name        = mysqli_real_escape_string($conn, $_POST['name']);

    // Mengambil input deskripsi produk lalu diamankan
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Mengambil input kategori produk lalu diamankan
    $category    = mysqli_real_escape_string($conn, $_POST['category']);

    // Mengambil input harga produk lalu diamankan
    $price       = mysqli_real_escape_string($conn, $_POST['price']);

    // Mengambil input stok produk lalu diamankan
    $stock       = mysqli_real_escape_string($conn, $_POST['stock']);

    // Mengecek apakah user mengupload gambar baru
    if (!empty($_FILES['image']['name'])) {

        // Mengambil nama file gambar baru
        $imageName = $_FILES['image']['name'];

        // Mengambil lokasi file sementara gambar baru
        $tmpName   = $_FILES['image']['tmp_name'];

        // Memindahkan file gambar baru ke folder uploads
        move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);

        // Mengupdate semua data produk termasuk gambar
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

        // Jika tidak upload gambar baru, update data produk tanpa mengubah gambar
        mysqli_query($conn, "UPDATE products SET 
            name='$name',
            description='$description',
            category='$category',
            price='$price',
            stock='$stock'
            WHERE id='$id'
        ");
    }

    // Setelah berhasil update produk, redirect kembali ke halaman product management
    header("Location: product-management.php");

    // Menghentikan eksekusi script
    exit;
}

// ================= DELETE PRODUCT =================
// Mengecek apakah ada parameter delete pada URL
if (isset($_GET['delete'])) {

    // Mengambil ID produk yang akan dihapus
    $id = $_GET['delete'];

    // Menghapus produk dari database berdasarkan ID
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");

    // Setelah berhasil hapus produk, redirect kembali ke halaman product management
    header("Location: product-management.php");

    // Menghentikan eksekusi script
    exit;
}


// ================= AMBIL DATA =================
// Mengambil semua data produk dari tabel products, urut dari ID terbesar ke terkecil
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<!-- Menentukan encoding karakter -->
<meta charset="UTF-8">

<!-- Judul halaman -->
<title>Product Management</title>

<!-- Favicon / icon website -->
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
    font-family: Poppins; background:#f4f4f4; 
    overflow: hidden;
}

/* Styling area konten utama */
.content { padding:40px; flex:1; }

/* Styling judul halaman */
.page-title { color:#FFA4A4; font-weight:600; }

/* Styling tombol pink */
.btn-pink { background:#FFA4A4; color:#fff; border-radius:12px; }

/* Efek hover tombol pink */
.btn-pink:hover { background:#ff8e8e; }

/* Wrapper untuk select custom */
.select-wrapper {
    position: relative;
}

/* Styling select dropdown */
.custom-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    padding-right: 40px;
    border-radius: 12px;
}

/* Menambahkan icon panah custom pada select */
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

/* Styling header tabel */
.table thead th {
    background:#FFA4A4 !important;
    color:white !important;
    text-align:center;
}

/* Styling kotak action */
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

/* Warna tombol edit */
.action-edit { background:#63C78A; }

/* Warna tombol delete */
.action-delete { background:#EB4C4C; }

/* Ukuran icon dalam tombol action */
.action-box i { font-size:20px; }

/* Styling gambar produk */
.product-img {
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
}
</style>
</head>

<body>
<!-- Container utama -->
<div class="d-flex min-vh-100">

<!-- Menampilkan sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Area konten utama -->
<div class="content">

<!-- Judul halaman -->
<h4 class="page-title mb-3">Product Data</h4>

<!-- Tombol untuk membuka modal tambah produk -->
<button class="btn btn-pink mb-3" data-bs-toggle="modal" data-bs-target="#addProduct"> + Add Product
</button>

<!-- Tabel responsive agar tetap rapi di layar kecil -->
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

<!-- Mengambil data produk satu per satu -->
<?php while($row = mysqli_fetch_assoc($products)) : ?>
<tr>
<!-- Menampilkan ID produk -->
<td class="text-center"><?= $row['id'] ?></td>

<!-- Menampilkan nama produk -->
<td><?= $row['name'] ?></td>

<!-- Menampilkan kategori produk -->
<td><?= $row['category'] ?></td>

<!-- Menampilkan harga produk dengan format rupiah -->
<td>Rp <?= number_format($row['price']) ?></td>

<!-- Menampilkan stok produk -->
<td><?= $row['stock'] ?></td>

<!-- Menampilkan deskripsi produk maksimal 50 karakter -->
<td>
<?= substr($row['description'],0,50); ?>...
</td>

<!-- Menampilkan gambar produk -->
<td class="text-center">
<img src="../assets/uploads/<?= $row['image'] ?>" class="product-img">
</td>

<td class="text-center">
<div class="d-flex justify-content-center gap-2">

<!-- Tombol edit produk -->
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

<!-- Tombol delete produk -->
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


<!-- ================= MODAL ADD ================= -->
<!-- Modal untuk menambahkan produk baru -->
<div class="modal fade" id="addProduct">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<!-- Header modal add -->
<div class="modal-header" style="background:#FFA4A4;color:white;">
<h5>Add Product</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- Form tambah produk -->
<form method="POST" enctype="multipart/form-data">
<div class="modal-body">

<!-- Input nama produk -->
<input type="text" name="name" class="form-control mb-3" placeholder="Product Name" required>

<!-- Dropdown kategori -->
<div class="select-wrapper mb-3">
    <select name="category" class="form-control custom-select" required>
        <option value="">-- Select Category --</option>
        <option value="Boy">Boy</option>
        <option value="Girl">Girl</option>
    </select>
</div>

<!-- Input harga -->
<input type="number" name="price" class="form-control mb-3" placeholder="Price" required>

<!-- Input stok -->
<input type="number" name="stock" class="form-control mb-3" placeholder="Stock" required>

<!-- Input upload gambar -->
<input type="file" name="image" class="form-control mb-3" required>

<!-- Input deskripsi -->
<textarea name="description"
class="form-control"
placeholder="Product Description"
rows="3"
required></textarea>


</div>

<div class="modal-footer">
<!-- Tombol simpan produk -->
<button type="submit" name="add_product" class="btn btn-pink w-100">
Save Product
</button>
</div>
</form>

</div>
</div>
</div>


<!-- ================= MODAL EDIT ================= -->
<!-- Modal untuk mengedit produk -->
<div class="modal fade" id="editProduct">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Header modal edit -->
      <div class="modal-header" style="background:#FFA4A4;color:white;">
        <h5>Edit Product</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Form edit produk -->
      <form method="POST" enctype="multipart/form-data">
        <!-- Input hidden untuk menyimpan ID produk -->
        <input type="hidden" name="id" id="edit-id">

        <div class="modal-body">

          <!-- Input nama produk -->
          <input type="text" name="name" id="edit-name" class="form-control mb-3" required>

          <!-- Dropdown kategori -->
          <select name="category" id="edit-category" class="form-control mb-3" required>
            <option value="Boy">Boy</option>
            <option value="Girl">Girl</option>
          </select>

          <!-- Input harga -->
          <input type="number" name="price" id="edit-price" class="form-control mb-3" required>

          <!-- Input stok -->
          <input type="number" name="stock" id="edit-stock" class="form-control mb-3" required>

          <!-- Input upload gambar baru -->
          <input type="file" name="image" class="form-control mb-2">

          <!-- Keterangan bahwa gambar boleh dikosongkan -->
          <small class="text-muted d-block mb-3">
            Leave blank if you don't want to change the image
          </small>

          <!-- Input deskripsi -->
          <textarea name="description"
            id="edit-description"
            class="form-control"
            rows="3"
            required></textarea>

        </div>

        <div class="modal-footer">
          <!-- Tombol update produk -->
          <button type="submit" name="update_product" class="btn btn-pink w-100">
            Update Product
          </button>
        </div>
      </form>

    </div>
  </div>
</div>


<!-- DELETE PRODUCT MODAL -->
<!-- Modal konfirmasi hapus produk -->
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



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// ===== EDIT MODAL =====
// Mengambil elemen modal edit
const editModal = document.getElementById('editProduct');

// Menjalankan fungsi saat modal edit dibuka
editModal.addEventListener('show.bs.modal', function (event) {

  // Mengambil tombol yang diklik
  const button = event.relatedTarget;

  // Mengisi form edit dengan data produk yang dipilih
  document.getElementById('edit-id').value = button.getAttribute('data-id');
  document.getElementById('edit-name').value = button.getAttribute('data-name');
  document.getElementById('edit-category').value = button.getAttribute('data-category');
  document.getElementById('edit-price').value = button.getAttribute('data-price');
  document.getElementById('edit-stock').value = button.getAttribute('data-stock');
  document.getElementById('edit-description').value =
      button.getAttribute('data-description');
});


// ===== DELETE MODAL =====
// Mengambil elemen modal delete
const deleteModal = document.getElementById('deleteProduct');

// Menjalankan fungsi saat modal delete dibuka
deleteModal.addEventListener('show.bs.modal', function (event) {
    // Mengambil tombol yang diklik
    const button = event.relatedTarget;

    // Mengambil ID produk
    const productId = button.getAttribute('data-id');

    // Mengambil nama produk
    const productName = button.getAttribute('data-name');

    // Menampilkan nama produk pada modal konfirmasi
    document.getElementById('delete-product-name').innerText = productName;

    // Mengatur link tombol delete agar mengarah ke produk yang dipilih
    document.getElementById('confirmDeleteProduct').href =
        'product-management.php?delete=' + productId;
});

</script>

</body>
</html>