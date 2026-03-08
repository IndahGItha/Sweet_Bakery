<?php
// Sweet Bakery - Riwayat Pesanan
session_start();
require_once '../includes/koneksi.php';

// Cek login
if (!isset($_SESSION['pelanggan_id'])) {
    header('Location: login.php');
    exit;
}

$id_pelanggan = $_SESSION['pelanggan_id'];

// Ambil riwayat pesanan
$sql = "SELECT * FROM pesanan WHERE id_pelanggan = $id_pelanggan ORDER BY created_at DESC";
$result = query($sql);

$pesanan = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pesanan[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Sweet Bakery</title>
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
                <a href="keranjang.php" class="nav-link">🛒 Keranjang</a>
                <a href="riwayat.php" class="nav-link">Riwayat</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Riwayat Section -->
    <section class="products-section" style="margin-top: 30px;">
        <div class="section-header">
            <h2>📋 Riwayat Pesanan</h2>
            <p>Lihat pesanan Anda sebelumnya</p>
        </div>

        <?php if (!empty($pesanan)): ?>
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Kode Pesanan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status Pesanan</th>
                        <th>Status Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pesanan as $p): ?>
                    <tr>
                        <td><strong>SWB-<?= $p['id_pesanan'] ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                        <td><?= formatRupiah($p['total_harga']) ?></td>
                        <td>
                            <?php 
                            $status_class = '';
                            $status_text = '';
                            switch($p['status_pesanan']) {
                                case 'menunggu': $status_class = 'status-waiting'; $status_text = 'Menunggu'; break;
                                case 'diproses': $status_class = 'status-process'; $status_text = 'Diproses'; break;
                                case 'dikirim': $status_class = 'status-process'; $status_text = 'Dikirim'; break;
                                case 'selesai': $status_class = 'status-done'; $status_text = 'Selesai'; break;
                                case 'dibatalkan': $status_class = 'status-cancel'; $status_text = 'Dibatalkan'; break;
                            }
                            ?>
                            <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                        </td>
                        <td>
                            <?php 
                            $bayar_class = '';
                            $bayar_text = '';
                            switch($p['status_pembayaran']) {
                                case 'menunggu': $bayar_class = 'status-waiting'; $bayar_text = 'Menunggu'; break;
                                case 'terverifikasi': $bayar_class = 'status-done'; $bayar_text = 'Terverifikasi'; break;
                                case 'ditolak': $bayar_class = 'status-cancel'; $bayar_text = 'Ditolak'; break;
                            }
                            ?>
                            <span class="status-badge <?= $bayar_class ?>"><?= $bayar_text ?></span>
                        </td>
                        <td>
                            <a href="detail_pesanan.php?id=<?= $p['id_pesanan'] ?>" class="btn btn-sm btn-primary">Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state" style="background: white; border-radius: 20px; padding: 60px; text-align: center;">
            <div class="icon" style="font-size: 50px;">📋</div>
            <h3>Belum Ada Pesanan</h3>
            <p>Anda belum memiliki riwayat pesanan</p>
            <a href="belanja.php" class="btn btn-primary" style="margin-top: 20px;">Mulai Belanja</a>
        </div>
        <?php endif; ?>
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
