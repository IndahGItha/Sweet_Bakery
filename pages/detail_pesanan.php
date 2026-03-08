<?php
// Sweet Bakery - Detail Pesanan
session_start();
require_once '../includes/koneksi.php';

// Cek login
if (!isset($_SESSION['pelanggan'])) {
    header('Location: login.php');
    exit;
}

$id_pesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_pelanggan = $_SESSION['id_pelanggan'];

// Ambil data pesanan
$pesanan = query("SELECT * FROM pesanan WHERE id_pesanan = $id_pesanan AND id_pelanggan = $id_pelanggan");

if ($pesanan->num_rows == 0) {
    header('Location: riwayat.php');
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="../index.php" class="logo">
                <span class="logo-icon"></span>
                <div class="logo-text">
                    <h1>Sweet Bakery</h1>
                    <span>Freshly Baked Every Day</span>
                </div>
            </a>
            <nav class="nav-menu">
                <a href="../index.php" class="nav-link">Beranda</a>
                <a href="belanja.php" class="nav-link">Belanja</a>
                <a href="riwayat.php" class="nav-link">Riwayat</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Detail Section -->
    <section class="checkout-section" style="margin-top: 30px;">
        <div class="section-header">
            <h2>📋 Detail Pesanan</h2>
            <p>Kode Pesanan: <strong>SWB-<?= $data_pesanan['id_pesanan'] ?></strong></p>
        </div>
        
        <div class="checkout-form">
            <h3>📍 Informasi Pengiriman</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Nama</p>
                    <p style="font-weight: 500;"><?= $data_pesanan['nama_pelanggan'] ?></p>
                </div>
                <div>
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">No. WhatsApp</p>
                    <p style="font-weight: 500;"><?= $data_pesanan['no_telepon'] ?></p>
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
                        <?php if ($d['gambar'] && file_exists('../uploads/' . $d['gambar'])): ?>
                        <img src="../uploads/<?= $d['gambar'] ?>" alt="<?= $d['nama_roti'] ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
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
            <h3>📊 Status Pesanan</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Status Pesanan</p>
                    <?php 
                    $status_class = '';
                    $status_text = '';
                    switch($data_pesanan['status_pesanan']) {
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
                </div>
                <div>
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 5px;">Status Pembayaran</p>
                    <?php 
                    $bayar_class = '';
                    $bayar_text = '';
                    switch($data_pesanan['status_pembayaran']) {
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
                </div>
            </div>
            
            <?php if ($data_pesanan['bukti_pembayaran']): ?>
            <div style="margin-top: 20px;">
                <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 10px;">Bukti Pembayaran</p>
                <img src="../uploads/pembayaran/<?= $data_pesanan['bukti_pembayaran'] ?>" alt="Bukti Pembayaran" style="max-width: 300px; border-radius: 10px;">
            </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 25px;">
            <a href="riwayat.php" class="btn btn-outline">← Kembali ke Riwayat</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Sweet Bakery</h4>
                <p>Freshly Baked Every Day</p>
                <p>Menyediakan roti dan kue segar berkualitas untuk keluarga Anda.</p>
            </div>
            <div class="footer-section">
                <h4>Kontak</h4>
                <p>📍 Jl. Roti Manis No. 123</p>
                <p>📞 (021) 1234-5678</p>
                <p>✉️ info@sweetbakery.com</p>
            </div>
            <div class="footer-section">
                <h4>Jam Buka</h4>
                <p>Senin - Jumat: 07:00 - 20:00</p>
                <p>Sabtu - Minggu: 08:00 - 21:00</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Sweet Bakery. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>