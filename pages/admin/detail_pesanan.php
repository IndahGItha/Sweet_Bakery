<?php
// Sweet Bakery - Detail Pesanan Admin
session_start();
require_once '../../includes/koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$id_pesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Update status
if (isset($_POST['update_status'])) {
    $status_pesanan = escape($_POST['status_pesanan']);
    $status_pembayaran = escape($_POST['status_pembayaran']);
    
    query("UPDATE pesanan SET status_pesanan = '$status_pesanan', status_pembayaran = '$status_pembayaran' WHERE id_pesanan = $id_pesanan");
    header('Location: detail_pesanan.php?id=' . $id_pesanan);
    exit;
}

// Ambil data pesanan
$pesanan = query("
    SELECT p.*, c.nama_pelanggan AS pelanggan_nama, c.no_telepon AS pelanggan_telepon 
    FROM pesanan p
    LEFT JOIN pelanggan c ON p.id_pelanggan = c.id_pelanggan
    WHERE p.id_pesanan = $id_pesanan
");

if ($pesanan->num_rows == 0) {
    header('Location: pesanan.php');
    exit;
}

$data_pesanan = $pesanan->fetch_assoc();

// Ambil detail pesanan
$detail = query("SELECT d.*, r.nama_roti, r.gambar FROM detail_pesanan d JOIN roti r ON d.id_roti = r.id_roti WHERE d.id_pesanan = $id_pesanan");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Sweet Bakery</title>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2>📋 Detail Pesanan</h2>
                <a href="pesanan.php" class="btn btn-outline">← Kembali</a>
            </div>
            
            <div class="checkout-form">
                <h3>📍 Informasi Pelanggan</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Nama</p>
                        <p style="font-weight: 500;"><?= $data_pesanan['nama_pelanggan'] ?? $data_pesanan['pelanggan_nama'] ?></p>
                    </div>
                    <div>
                        <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">No. WhatsApp</p>
                        <p style="font-weight: 500;"><?= $data_pesanan['no_telepon'] ?? $data_pesanan['pelanggan_telepon'] ?></p>
                    </div>
                    <div>
                        <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Tanggal Pesan</p>
                        <p style="font-weight: 500;"><?= date('d/m/Y H:i', strtotime($data_pesanan['created_at'])) ?></p>
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Alamat Pengiriman</p>
                    <p style="font-weight: 500;"><?= nl2br($data_pesanan['alamat_pengiriman']) ?></p>
                </div>
                <?php if ($data_pesanan['catatan']): ?>
                <div style="margin-top: 15px;">
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Catatan</p>
                    <p style="font-weight: 500;"><?= $data_pesanan['catatan'] ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="checkout-form" style="margin-top: 25px;">
                <h3>📦 Item Pesanan</h3>
                
                <div style="margin-bottom: 20px;">
                    <?php while($d = $detail->fetch_assoc()): ?>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px dashed var(--light-gray);">
                        <div style="width: 60px; height: 60px; background: var(--light-yellow); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <?php if ($d['gambar'] && file_exists('../../uploads/' . $d['gambar'])): ?>
                            <img src="../../uploads/<?= $d['gambar'] ?>" alt="<?= $d['nama_roti'] ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                            <?php else: ?>
                            <span></span>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1;">
                            <p style="font-weight: 600; margin-bottom: 5px;"><?= $d['nama_roti'] ?></p>
                            <p style="font-size: 0.85rem; color: var(--gray);"><?= $d['jumlah'] ?> x <?= formatRupiah($d['harga_satuan']) ?></p>
                        </div>
                        <div style="font-weight: 600;">
                            <?= formatRupiah($d['subtotal']) ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="summary-row total">
                    <span>Total Bayar</span>
                    <span><?= formatRupiah($data_pesanan['total_harga']) ?></span>
                </div>
            </div>
            
            <div class="checkout-form" style="margin-top: 25px;">
                <h3>📊 Update Status</h3>
                
                <form method="POST" action="">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="status_pesanan">Status Pesanan</label>
                            <select id="status_pesanan" name="status_pesanan" class="form-control">
                                <option value="menunggu" <?= $data_pesanan['status_pesanan'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="diproses" <?= $data_pesanan['status_pesanan'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="dikirim" <?= $data_pesanan['status_pesanan'] == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                <option value="selesai" <?= $data_pesanan['status_pesanan'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="dibatalkan" <?= $data_pesanan['status_pesanan'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status_pembayaran">Status Pembayaran</label>
                            <select id="status_pembayaran" name="status_pembayaran" class="form-control">
                                <option value="menunggu" <?= $data_pesanan['status_pembayaran'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="terverifikasi" <?= $data_pesanan['status_pembayaran'] == 'terverifikasi' ? 'selected' : '' ?>>Terverifikasi</option>
                                <option value="ditolak" <?= $data_pesanan['status_pembayaran'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" name="update_status" class="btn btn-success">💾 Update Status</button>
                </form>
            </div>
            
            <?php if ($data_pesanan['bukti_pembayaran']): ?>
            <div class="checkout-form" style="margin-top: 25px;">
                <h3>📷 Bukti Pembayaran</h3>
                <img src="../../uploads/pembayaran/<?= $data_pesanan['bukti_pembayaran'] ?>" alt="Bukti Pembayaran" style="max-width: 400px; border-radius: 10px;">
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
