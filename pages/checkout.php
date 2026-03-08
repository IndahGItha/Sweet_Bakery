<?php
// Sweet Bakery - Checkout
session_start();
require_once '../includes/koneksi.php';

// Cek keranjang
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    header('Location: keranjang.php');
    exit;
}

// Ambil data pelanggan jika login
$nama_pelanggan = '';
$no_telepon = '';
$alamat = '';

if (isset($_SESSION['pelanggan_id'])) {
    $id_pelanggan = $_SESSION['pelanggan_id'];
    $result = query("SELECT * FROM pelanggan WHERE id_pelanggan = $id_pelanggan");
    if ($result && $result->num_rows > 0) {
        $pelanggan = $result->fetch_assoc();
        $nama_pelanggan = $pelanggan['nama_pelanggan'];
        $no_telepon = $pelanggan['no_telepon'];
        $alamat = $pelanggan['alamat'];
    }
}

// Hitung total
$total = 0;
$keranjang_items = [];

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

// Proses checkout
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $nama = escape($_POST['nama_pelanggan']);
    $telepon = escape($_POST['no_telepon']);
    $alamat_pengiriman = escape($_POST['alamat']);
    $catatan = escape($_POST['catatan']);
    
    if (empty($nama) || empty($telepon) || empty($alamat_pengiriman)) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Cek upload bukti pembayaran
        $bukti_pembayaran = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
            $upload = uploadGambar($_FILES['bukti_pembayaran'], '../uploads/pembayaran/');
            if ($upload) {
                $bukti_pembayaran = $upload;
            }
        }
        
        // Insert pesanan
        $id_pelanggan_db = isset($_SESSION['id_pelanggan']) ? $_SESSION['id_pelanggan'] : 'NULL';
        $sql_pesanan = "INSERT INTO pesanan (id_pelanggan, nama_pelanggan, no_telepon, alamat_pengiriman, total_harga, catatan, bukti_pembayaran) 
                        VALUES ($id_pelanggan_db, '$nama', '$telepon', '$alamat_pengiriman', $total, '$catatan', '$bukti_pembayaran')";
        
        if (query($sql_pesanan)) {
            $id_pesanan = $conn->insert_id;
            
            // Insert detail pesanan dan update stok
            foreach ($keranjang_items as $item) {
                $id_roti = $item['roti']['id_roti'];
                $jumlah = $item['jumlah'];
                $harga = $item['roti']['harga'];
                $subtotal = $item['subtotal'];
                
                query("INSERT INTO detail_pesanan (id_pesanan, id_roti, jumlah, harga_satuan, subtotal) 
                       VALUES ($id_pesanan, $id_roti, $jumlah, $harga, $subtotal)");
                
                // Update stok
                query("UPDATE roti SET stok = stok - $jumlah WHERE id_roti = $id_roti");
            }
            
            // Kosongkan keranjang
            unset($_SESSION['keranjang']);
            
            $success = 'Pesanan berhasil dibuat! Kode pesanan: SWB-' . $id_pesanan;
        } else {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sweet Bakery</title>
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
            </nav>
        </div>
    </header>

    <!-- Checkout Section -->
    <section class="checkout-section" style="margin-top: 30px;">
        <div class="section-header">
            <h2>📋 Checkout</h2>
            <p>Lengkapi data pesanan Anda</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <span>⚠️</span> <?= $error ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <span>✅</span> <?= $success ?>
            akan segera diproses. Kami akan menghubungi Anda melalui WhatsApp untuk konfirmasi lebih lanjut.
        </div>
        <div style="text-align: center; margin-top: 30px;">
            <a href="belanja.php" class="btn btn-primary btn-lg">Lanjut Belanja</a>
        </div>
        <?php else: ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="checkout-form">
                <h3>📍 Informasi Pengiriman</h3>
                
                <div class="form-group">
                    <label for="nama_pelanggan">Nama Lengkap *</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control" value="<?= $nama_pelanggan ?>" placeholder="Masukkan nama lengkap" required>
                </div>
                
                <div class="form-group">
                    <label for="no_telepon">No. WhatsApp *</label>
                    <input type="tel" id="no_telepon" name="no_telepon" class="form-control" value="<?= $no_telepon ?>" placeholder="Contoh: 08123456789" required>
                </div>
                
                <div class="form-group">
                    <label for="alamat">Alamat Pengiriman Lengkap *</label>
                    <textarea id="alamat" name="alamat" class="form-control" rows="4" placeholder="Masukkan alamat lengkap (jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota)" required><?= $alamat ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="catatan">Catatan (Opsional)</label>
                    <textarea id="catatan" name="catatan" class="form-control" rows="2" placeholder="Catatan khusus untuk pesanan"></textarea>
                </div>
            </div>
            
            <div class="checkout-form" style="margin-top: 25px;">
                <h3>💳 Pembayaran</h3>
                
                <div class="payment-method">

                    <h4>Scan QR Code</h4>
                    <p>Silakan scan QR code berikut untuk melakukan pembayaran</p>
                    
                     <div style="background: white; padding: 20px; border-radius: 15px; margin: 20px 0; text-align: center;">
                            <div style="width: 300px; height: 300px; margin: 0 auto; border-radius: 15px; overflow: hidden; border: 1px solid #ccc;">
                                <img src="/sweet-bakery/uploads/qrcode.png" alt="QR Code" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <p style="margin-top: 15px; font-weight: 600; color: var(--dark-brown);">QR Code Pembayaran</p>
                            <p style="font-size: 0.85rem; color: var(--gray);">Total: <?= formatRupiah($total) ?></p>
                        </div>
                    </div>
                    <div class="upload-area" onclick="document.getElementById('bukti_pembayaran').click()">
                        <div class="icon">📤</div>
                        <p id="upload-label"><strong>Klik untuk upload bukti pembayaran</strong></p>
                        <p style="font-size: 0.85rem; color: var(--gray);">Format: JPG, PNG (Max 2MB)</p>
                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" style="display: none;" data-preview="preview-bukti">
                        <img id="preview-bukti" style="display: none; max-width: 100%; margin-top: 15px; border-radius: 10px;">
                    </div>
                    <img id="preview-bukti" style="display: none; max-width: 100%; margin-top: 15px; border-radius: 10px;">
                </div>
            </div>
            
            <div class="checkout-form" style="margin-top: 25px;">
                <h3>📝 Ringkasan Pesanan</h3>
                
                <div style="margin-bottom: 20px;">
                    <?php foreach ($keranjang_items as $item): ?>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--light-gray);">
                        <span><?= $item['roti']['nama_roti'] ?> (<?= $item['jumlah'] ?>x)</span>
                        <span><?= formatRupiah($item['subtotal']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-row total">
                    <span>Total Bayar</span>
                    <span><?= formatRupiah($total) ?></span>
                </div>
                
                <button type="submit" name="checkout" id="btn-checkout" class="btn btn-customer btn-lg" style="width: 100%; margin-top: 25px;">
                    ✅ Konfirmasi Pesanan
                </button>
                
                <a href="keranjang.php" class="btn btn-outline" style="width: 100%; margin-top: 10px;">
                    ← Kembali ke Keranjang
                </a>
            </div>
        </form>
        
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