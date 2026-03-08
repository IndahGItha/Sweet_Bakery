<?php
// Sweet Bakery - Kelola Pesanan
session_start();
require_once '../../includes/koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Update status pesanan
if (isset($_POST['update_status'])) {
    $id_pesanan = intval($_POST['id_pesanan']);
    $status_pesanan = escape($_POST['status_pesanan']);
    $status_pembayaran = escape($_POST['status_pembayaran']);
    
    query("UPDATE pesanan SET status_pesanan = '$status_pesanan', status_pembayaran = '$status_pembayaran' WHERE id_pesanan = $id_pesanan");
    header('Location: pesanan.php');
    exit;
}

// Filter status
$filter = isset($_GET['filter']) ? escape($_GET['filter']) : '';
$sql = "SELECT p.*, IFNULL(pl.nama_pelanggan, p.nama_pelanggan) as nama FROM pesanan p LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan";

if ($filter) {
    $sql .= " WHERE p.status_pesanan = '$filter'";
}

$sql .= " ORDER BY p.created_at DESC";
$pesanan = query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Sweet Bakery</title>
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
                <li><a href="pesanan.php" class="active"><span class="icon">📋</span> Kelola Pesanan</a></li>
                <li><a href="pelanggan.php"><span class="icon">👥</span> Data Pelanggan</a></li>
                <li><a href="../../index.php"><span class="icon">🏠</span> Lihat Website</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
                <h2>📋 Kelola Pesanan</h2>
                <div>
                    <a href="pesanan.php" class="btn btn-sm <?= $filter == '' ? 'btn-primary' : 'btn-outline' ?>">Semua</a>
                    <a href="pesanan.php?filter=menunggu" class="btn btn-sm <?= $filter == 'menunggu' ? 'btn-primary' : 'btn-outline' ?>">Menunggu</a>
                    <a href="pesanan.php?filter=diproses" class="btn btn-sm <?= $filter == 'diproses' ? 'btn-primary' : 'btn-outline' ?>">Diproses</a>
                    <a href="pesanan.php?filter=dikirim" class="btn btn-sm <?= $filter == 'dikirim' ? 'btn-primary' : 'btn-outline' ?>">Dikirim</a>
                    <a href="pesanan.php?filter=selesai" class="btn btn-sm <?= $filter == 'selesai' ? 'btn-primary' : 'btn-outline' ?>">Selesai</a>
                </div>
            </div>
            
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status Pesanan</th>
                            <th>Status Bayar</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($p = $pesanan->fetch_assoc()): ?>
                        <tr>
                            <td><strong>SWB-<?= $p['id_pesanan'] ?></strong></td>
                            <td><?= $p['nama'] ?></td>
                            <td><?= formatRupiah($p['total_harga']) ?></td>
                            <td>
                                <?php 
                                $status_class = '';
                                $status_text = '';
                                switch($p['status_pesanan']) {
                                    case 'menunggu':
                                        $status_class = 'status-waiting';
                                        $status_text = 'Menunggu';
                                        break;
                                    case 'diproses':
                                        $status_class = 'status-process';
                                        $status_text = 'Diproses';
                                        break;
                                    case 'dikirim':
                                        $status_class = 'status-process';
                                        $status_text = 'Dikirim';
                                        break;
                                    case 'selesai':
                                        $status_class = 'status-done';
                                        $status_text = 'Selesai';
                                        break;
                                    case 'dibatalkan':
                                        $status_class = 'status-cancel';
                                        $status_text = 'Dibatalkan';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                            </td>
                            <td>
                                <?php 
                                $bayar_class = '';
                                $bayar_text = '';
                                switch($p['status_pembayaran']) {
                                    case 'menunggu':
                                        $bayar_class = 'status-waiting';
                                        $bayar_text = 'Menunggu';
                                        break;
                                    case 'terverifikasi':
                                        $bayar_class = 'status-done';
                                        $bayar_text = 'Terverifikasi';
                                        break;
                                    case 'ditolak':
                                        $bayar_class = 'status-cancel';
                                        $bayar_text = 'Ditolak';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?= $bayar_class ?>"><?= $bayar_text ?></span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                            <td>
                                <a href="detail_pesanan.php?id=<?= $p['id_pesanan'] ?>" class="btn btn-sm btn-primary">Detail</a>
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
