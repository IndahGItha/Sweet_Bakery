<?php
// Sweet Bakery - Kelola Roti
session_start();
require_once '../../includes/koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Hapus roti
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    query("DELETE FROM roti WHERE id_roti = $id");
    header('Location: roti.php');
    exit;
}

// Ambil data roti
$roti = query("SELECT r.*, k.nama_kategori FROM roti r LEFT JOIN kategori k ON r.id_kategori = k.id_kategori ORDER BY r.id_roti DESC");

// Ambil data kategori
$kategori = query("SELECT * FROM kategori ORDER BY nama_kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Roti - Sweet Bakery</title>
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
                <h2>🍞 Kelola Roti</h2>
                <a href="tambah_roti.php" class="btn btn-success">+ Tambah Roti</a>
            </div>
            
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Roti</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $roti->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="width: 60px; height: 60px; background: var(--light-yellow); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                    <?php if ($r['gambar'] && file_exists('../../uploads/' . $r['gambar'])): ?>
                                    <img src="../../uploads/<?= $r['gambar'] ?>" alt="<?= $r['nama_roti'] ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                    <?php else: ?>
                                    <span></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><strong><?= $r['nama_roti'] ?></strong></td>
                            <td><?= $r['nama_kategori'] ?: '-' ?></td>
                            <td><?= formatRupiah($r['harga']) ?></td>
                            <td>
                                <span style="color: <?= $r['stok'] <= 5 ? 'var(--soft-red)' : 'inherit' ?>; font-weight: <?= $r['stok'] <= 5 ? '600' : 'normal' ?>;">
                                    <?= $r['stok'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= $r['status'] == 'tersedia' ? 'status-done' : 'status-cancel' ?>">
                                    <?= $r['status'] == 'tersedia' ? 'Tersedia' : 'Habis' ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_roti.php?id=<?= $r['id_roti'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                                <a href="roti.php?hapus=<?= $r['id_roti'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus roti ini?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
