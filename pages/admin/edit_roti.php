<?php
// Sweet Bakery - Edit Roti
session_start();
require_once '../../includes/koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data roti
$roti = query("SELECT * FROM roti WHERE id_roti = $id");
if ($roti->num_rows == 0) {
    header('Location: roti.php');
    exit;
}
$data = $roti->fetch_assoc();

$error = '';
$success = '';

// Ambil data kategori
$kategori = query("SELECT * FROM kategori ORDER BY nama_kategori");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = escape($_POST['nama_roti']);
    $id_kategori = intval($_POST['id_kategori']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $deskripsi = escape($_POST['deskripsi']);
    $status = escape($_POST['status']);
    
    // Upload gambar baru jika ada
    $gambar_sql = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $upload = uploadGambar($_FILES['gambar'], '../../uploads/');
        if ($upload) {
            // Hapus gambar lama
            if ($data['gambar'] && file_exists('../../uploads/' . $data['gambar'])) {
                unlink('../../uploads/' . $data['gambar']);
            }
            $gambar_sql = ", gambar = '$upload'";
        }
    }
    
    $sql = "UPDATE roti SET 
            nama_roti = '$nama', 
            id_kategori = $id_kategori, 
            harga = $harga, 
            stok = $stok, 
            deskripsi = '$deskripsi', 
            status = '$status'
            $gambar_sql 
            WHERE id_roti = $id";
    
    if (query($sql)) {
        $success = 'Roti berhasil diupdate!';
        // Refresh data
        $roti = query("SELECT * FROM roti WHERE id_roti = $id");
        $data = $roti->fetch_assoc();
    } else {
        $error = 'Terjadi kesalahan. Silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Roti - Sweet Bakery</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header admin-header">
        <div class="header-content">
            <a href="../../index.php" class="logo">
                <span class="logo-icon"></span>
                <div class="logo-text">
                    <h1>Sweet Bakery</h1>
                    <span>Admin Panel</span>
                </div>
            </a>
            <nav class="nav-menu">
                <span style="color: white;">👤 <?= $_SESSION['nama_admin'] ?></span>
                <a href="logout.php" class="nav-link" style="color: white;">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Admin Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><span class="icon">📊</span> Dashboard</a></li>
                <li><a href="roti.php" class="active"><span class="icon">🍞</span> Kelola Roti</a></li>
                <li><a href="pesanan.php"><span class="icon">📋</span> Kelola Pesanan</a></li>
                <li><a href="pelanggan.php"><span class="icon">👥</span> Data Pelanggan</a></li>
                <li><a href="../../index.php"><span class="icon">🏠</span> Lihat Website</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2>✏️ Edit Roti</h2>
                <a href="roti.php" class="btn btn-outline">← Kembali</a>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <span>⚠️</span> <?= $error ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                <span>✅</span> <?= $success ?>
            </div>
            <?php endif; ?>
            
            <div class="checkout-form">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_roti">Nama Roti *</label>
                        <input type="text" id="nama_roti" name="nama_roti" class="form-control" value="<?= $data['nama_roti'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_kategori">Kategori</label>
                        <select id="id_kategori" name="id_kategori" class="form-control">
                            <option value="">Pilih Kategori</option>
                            <?php 
                            $kategori = query("SELECT * FROM kategori ORDER BY nama_kategori");
                            while($k = $kategori->fetch_assoc()): 
                            ?>
                            <option value="<?= $k['id_kategori'] ?>" <?= $data['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label for="harga">Harga *</label>
                            <input type="number" id="harga" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stok">Stok *</label>
                            <input type="number" id="stok" name="stok" class="form-control" value="<?= $data['stok'] ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"><?= $data['deskripsi'] ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Saat Ini</label>
                        <div style="width: 150px; height: 150px; background: var(--light-yellow); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin-bottom: 10px;">
                            <?php if ($data['gambar'] && file_exists('../../uploads/' . $data['gambar'])): ?>
                            <img src="../../uploads/<?= $data['gambar'] ?>" alt="<?= $data['nama_roti'] ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                            <?php else: ?>
                            <span></span>
                            <?php endif; ?>
                        </div>
                        <label for="gambar">Ganti Gambar (Opsional)</label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" data-preview="preview-gambar">
                        <img id="preview-gambar" style="display: none; max-width: 200px; margin-top: 15px; border-radius: 10px;">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="tersedia" <?= $data['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                            <option value="habis" <?= $data['status'] == 'habis' ? 'selected' : '' ?>>Habis</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-success">💾 Update Roti</button>
                    <a href="roti.php" class="btn btn-outline">Batal</a>
                </form>
            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
