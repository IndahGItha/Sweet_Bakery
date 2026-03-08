<?php
// Sweet Bakery - Keranjang Belanja
session_start();
require_once '../includes/koneksi.php';

// Update keranjang
if (isset($_POST['update_keranjang'])) {
    foreach ($_POST['jumlah'] as $id_roti => $jumlah) {
        $jumlah = intval($jumlah);
        if ($jumlah > 0) {
            foreach ($_SESSION['keranjang'] as &$item) {
                if ($item['id_roti'] == $id_roti) {
                    $item['jumlah'] = $jumlah;
                    break;
                }
            }
        }
    }
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    foreach ($_SESSION['keranjang'] as $key => $item) {
        if ($item['id_roti'] == $id_hapus) {
            unset($_SESSION['keranjang'][$key]);
            break;
        }
    }
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
    header('Location: keranjang.php');
    exit;
}

// Hitung total
$total = 0;
$keranjang_items = [];

if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $item) {
        $id_roti = $item['id_roti'];
        $result = query("SELECT * FROM roti WHERE id_roti = $id_roti");
        if ($result->num_rows > 0) {
            $roti = $result->fetch_assoc();
            $subtotal = $roti['harga'] * $item['jumlah'];
            $total += $subtotal;
            $keranjang_items[] = [
                'roti' => $roti,
                'jumlah' => $item['jumlah'],
                'subtotal' => $subtotal
            ];
        }
    }
}

$jumlah_keranjang = count($keranjang_items);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Sweet Bakery</title>
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
                <?php if (isset($_SESSION['pelanggan'])): ?>
                <a href="riwayat.php" class="nav-link">Riwayat</a>
                <a href="logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                <a href="login.php" class="nav-link">Masuk</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Cart Section -->
    <section class="cart-section" style="margin-top: 30px;">
        <div class="section-header">
            <h2>🛒 Keranjang Belanja</h2>
            <p>Kelola pesanan Anda</p>
        </div>
        
        <?php if ($jumlah_keranjang > 0): ?>
        <form method="POST" action="">
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keranjang_items as $index => $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="cart-item-image">
                                        <?php if ($item['roti']['gambar'] && file_exists('../uploads/' . $item['roti']['gambar'])): ?>
                                        <img src="../uploads/<?= $item['roti']['gambar'] ?>" alt="<?= $item['roti']['nama_roti'] ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                        <?php else: ?>
                                        <span></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?= $item['roti']['nama_roti'] ?></strong>
                                        <p style="font-size: 0.85rem; color: var(--gray); margin: 0;">Stok: <?= $item['roti']['stok'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="item-price" data-price="<?= $item['roti']['harga'] ?>">
                                <?= formatRupiah($item['roti']['harga']) ?>
                            </td>
                            <td>
                                <div class="quantity-control">
                                    <button type="button" class="qty-minus">-</button>
                                    <span class="qty-value"><?= $item['jumlah'] ?></span>
                                    <input type="hidden" name="jumlah[<?= $item['roti']['id_roti'] ?>]" value="<?= $item['jumlah'] ?>">
                                    <button type="button" class="qty-plus" data-max="<?= $item['roti']['stok'] ?>">+</button>
                                </div>
                            </td>
                            <td class="item-subtotal">
                                <?= formatRupiah($item['subtotal']) ?>
                            </td>
                            <td>
                                <a href="keranjang.php?hapus=<?= $item['roti']['id_roti'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus item ini?')">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <a href="belanja.php" class="btn btn-outline">← Lanjut Belanja</a>
                <button type="submit" name="update_keranjang" class="btn btn-primary">🔄 Update Keranjang</button>
            </div>
        </form>
        
        <div class="cart-summary">
            <h3>Ringkasan Pesanan</h3>
            <div class="summary-row">
                <span>Total Item</span>
                <span><?= $jumlah_keranjang ?> item</span>
            </div>
            <div class="summary-row total">
                <span>Total Harga</span>
                <span class="cart-total-amount"><?= formatRupiah($total) ?></span>
            </div>
            <a href="checkout.php" class="btn btn-customer btn-lg" style="width: 100%; margin-top: 20px;">
                Lanjut ke Checkout →
            </a>
        </div>
        
        <?php else: ?>
        <div class="empty-state" style="background: white; border-radius: 20px; padding: 60px;">
            <div class="icon">🛒</div>
            <h3>Keranjang Kosong</h3>
            <p>Anda belum menambahkan produk ke keranjang</p>
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
