<?php
// Sweet Bakery - Data Pelanggan
session_start();
require_once '../../includes/koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pelanggan
$pelanggan = query("SELECT p.*, 
                    COUNT(ps.id_pesanan) AS total_pesanan, 
                    IFNULL(SUM(ps.total_harga), 0) AS total_belanja
                    FROM pelanggan p 
                    LEFT JOIN pesanan ps ON p.id_pelanggan = ps.id_pelanggan 
                    GROUP BY p.id_pelanggan 
                    ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - Sweet Bakery</title>
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
                <li><a href="roti.php"><span class="icon">🍞</span> Kelola Roti</a></li>
                <li><a href="pesanan.php"><span class="icon">📋</span> Kelola Pesanan</a></li>
                <li><a href="pelanggan.php" class="active"><span class="icon">👥</span> Data Pelanggan</a></li>
                <li><a href="../../index.php"><span class="icon">🏠</span> Lihat Website</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2>👥 Data Pelanggan</h2>
            </div>
            
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Total Pesanan</th>
                            <th>Total Belanja</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($p = $pelanggan->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= $p['nama_pelanggan'] ?></strong></td>
                            <td><?= $p['email'] ?></td>
                            <td><?= $p['no_telepon'] ?: '-' ?></td>
                            <td><?= $p['total_pesanan'] ?> pesanan</td>
                            <td><?= $p['total_belanja'] ? formatRupiah($p['total_belanja']) : '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
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
