<?php
/**
 * Sweet Bakery - Halaman Belanja
 * 
 * Halaman daftar roti untuk pelanggan
 * Menggunakan namespace SweetBakery\Models
 * 
 * @package SweetBakery\Pages
 * @author Sweet Bakery Team
 * @version 2.0.0
 */

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use SweetBakery\Models\Roti;
use SweetBakery\Models\Pelanggan;
use SweetBakery\Utils\Helper;

// Initialize models
$rotiModel = new Roti();
$pelanggan = new Pelanggan();

// Get flash message
$flash = Helper::getFlash();

// Filter kategori
$filterKategori = Helper::input('kategori', 0, 'GET');
$kategoriModel = new \SweetBakery\Models\Kategori();
$kategori = $kategoriModel->readAll();

// Ambil data roti
if ($filterKategori > 0) {
    $daftarRoti = $rotiModel->getByKategori($filterKategori);
} else {
    $daftarRoti = $rotiModel->getAvailable();
}

// Hitung jumlah item di keranjang
$jumlahKeranjang = 0;
if (isset($_SESSION['keranjang'])) {
    $jumlahKeranjang = count($_SESSION['keranjang']);
}

// Proses tambah ke keranjang
if (Helper::isPost() && isset($_POST['tambah_keranjang'])) {
    // Verify CSRF
    $csrfToken = Helper::input('csrf_token', '', 'POST');
    if (!verifyCsrf($csrfToken)) {
        Helper::setFlash('error', 'Invalid CSRF token');
        Helper::redirect('belanja.php');
    }

    $idRoti = intval(Helper::input('id_roti', 0, 'POST'));
    $jumlah = intval(Helper::input('jumlah', 1, 'POST'));

    // Cek stok
    $dataRoti = $rotiModel->read($idRoti);
    if ($dataRoti && $dataRoti['stok'] >= $jumlah) {
        // Inisialisasi keranjang jika belum ada
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        // Cek jika roti sudah ada di keranjang
        $found = false;
        foreach ($_SESSION['keranjang'] as &$item) {
            if ($item['id_roti'] == $idRoti) {
                $item['jumlah'] += $jumlah;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['keranjang'][] = [
                'id_roti' => $idRoti,
                'jumlah' => $jumlah
            ];
        }

        Helper::setFlash('success', 'Produk ditambahkan ke keranjang!');
    } else {
        Helper::setFlash('error', 'Stok tidak mencukupi!');
    }

    Helper::redirect('belanja.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belanja - Sweet Bakery</title>
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
                <a href="keranjang.php" class="nav-link">
                    🛒 Keranjang 
                    <?php if ($jumlahKeranjang > 0): ?>
                    <span style="background: var(--soft-red); color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem;"><?= $jumlahKeranjang ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($pelanggan->isLoggedIn()): ?>
                <a href="riwayat.php" class="nav-link">Riwayat</a>
                <a href="logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                <a href="login.php" class="nav-link">Masuk</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
<!-- Products Section -->
<section class="products-section" style="margin-top: 30px;">

    <div class="section-header">
        <h2>🍞 Daftar Roti & Kue</h2>
        <p>Pilih roti favorit Anda</p>
    </div>

    <!-- Filter Kategori -->
    <div class="kategori-filter" style="text-align:center; margin-bottom:30px;">
        <a href="belanja.php"
           class="btn btn-sm <?= $filterKategori == 0 ? 'btn-primary' : 'btn-outline' ?>"
           style="margin:5px;">
           Semua
        </a>

        <?php foreach ($kategori as $k): ?>
        <a href="belanja.php?kategori=<?= $k['id_kategori'] ?>"
           class="btn btn-sm <?= $filterKategori == $k['id_kategori'] ? 'btn-primary' : 'btn-outline' ?>"
           style="margin:5px;">
           <?= e($k['nama_kategori']) ?>
        </a>
        <?php endforeach; ?>
    </div>
        
        <!-- Flash Message -->
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>" style="max-width: 800px; margin: 0 auto 20px;">
            <span><?= $flash['type'] === 'success' ? '✅' : '⚠️' ?></span> <?= $flash['message'] ?>
        </div>
        <?php endif; ?>
        
        <div class="products-grid">
            <?php if (!empty($daftarRoti)): ?>
            <?php foreach ($daftarRoti as $r): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if (!empty($r['gambar']) && file_exists('../uploads/' . $r['gambar'])): ?>
                    <img src="../uploads/<?= e($r['gambar']) ?>" alt="<?= e($r['nama_roti']) ?>">
                    <?php else: ?>
                    <span style="font-size: 4rem;"></span>
                    <?php endif; ?>
                    <?php if ($r['stok'] <= 5): ?>
                    <span class="product-badge">Stok Terbatas</span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?= e($r['nama_roti']) ?></h3>
                    <p class="product-category"><?= e($r['nama_kategori'] ?? 'Roti') ?></p>
                    <p class="product-price"><?= formatRupiah($r['harga']) ?></p>
                    <p class="product-stock <?= $r['stok'] <= 5 ? 'low' : '' ?>">Stok: <?= $r['stok'] ?></p>
                    <p style="font-size: 0.85rem; color: var(--gray); margin-bottom: 15px;"><?= e(truncate($r['deskripsi'] ?? '', 60)) ?></p>
                    
                    <form method="POST" action="">
                        <?= csrfField() ?>
                        <input type="hidden" name="id_roti" value="<?= $r['id_roti'] ?>">
                        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="number" name="jumlah" value="1" min="1" max="<?= $r['stok'] ?>" class="form-control" style="width: 80px; text-align: center;">
                            <button type="submit" name="tambah_keranjang" class="btn btn-primary" style="flex: 1;">
                                🛒 Tambah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <div class="icon">😔</div>
                <h3>Tidak ada produk</h3>
                <p>Belum ada roti tersedia saat ini</p>
            </div>
            <?php endif; ?>
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
