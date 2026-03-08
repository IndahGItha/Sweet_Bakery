<?php
/**
 * Sweet Bakery - Register Pelanggan
 * 
 * Halaman registrasi untuk pelanggan baru
 * Menggunakan namespace SweetBakery\Models
 * 
 * @package SweetBakery\Pages
 * @author Sweet Bakery Team
 * @version 2.0.0
 */

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use SweetBakery\Models\Pelanggan;
use SweetBakery\Utils\Helper;

// Initialize model
$pelanggan = new Pelanggan();

// Redirect jika sudah login
if ($pelanggan->isLoggedIn()) {
    Helper::redirect('belanja.php');
}

$error = '';
$success = '';

// Proses registrasi
if (Helper::isPost()) {
    $data = [
        'nama_pelanggan' => Helper::input('nama_pelanggan', '', 'POST'),
        'email' => Helper::input('email', '', 'POST'),
        'password' => Helper::input('password', '', 'POST'),
        'no_telepon' => Helper::input('no_telepon', '', 'POST'),
        'alamat' => Helper::input('alamat', '', 'POST')
    ];

    $konfirmasiPassword = Helper::input('konfirmasi_password', '', 'POST');

    // Validasi konfirmasi password
    if ($data['password'] !== $konfirmasiPassword) {
        $error = 'Password dan konfirmasi password tidak cocok!';
        setOldInput($data);
    } else {
        $id = $pelanggan->register($data);

        if ($id) {
            $success = 'Akun berhasil dibuat! Silakan login.';
        } else {
            $errors = $pelanggan->getValidationErrors();
            $error = !empty($errors) ? implode(', ', $errors) : 'Registrasi gagal. Silakan coba lagi.';
            setOldInput($data);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sweet Bakery</title>
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
            </nav>
        </div>
    </header>

    <!-- Register Form -->
    <div class="form-container" style="margin-top: 40px; max-width: 500px;">
        <div class="form-header">
            <div class="icon">📝</div>
            <h2>Buat Akun Baru</h2>
            <p style="color: var(--gray);">Daftar untuk mulai berbelanja</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <span>⚠️</span> <?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <span>✅</span> <?= e($success) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?= csrfField() ?>
            
            <div class="form-group">
                <label for="nama_pelanggan">Nama Lengkap *</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control" 
                       placeholder="Masukkan nama lengkap" 
                       value="<?= e(old('nama_pelanggan')) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="Masukkan email" 
                       value="<?= e(old('email')) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="no_telepon">No. Whatsapp</label>
                <input type="tel" id="no_telepon" name="no_telepon" class="form-control" 
                       placeholder="Contoh: 08123456789" 
                       value="<?= e(old('no_telepon')) ?>">
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" 
                          placeholder="Masukkan alamat lengkap"><?= e(old('alamat')) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Minimal 6 karakter" required>
            </div>
            
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password *</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control" 
                       placeholder="Ulangi password" required>
            </div>
            
            <button type="submit" class="btn btn-customer" style="width: 100%;">
                Daftar Sekarang
            </button>
        </form>
        
        <div class="form-footer">
            <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" style="margin-top: 60px;">
        <div class="footer-bottom">
            <p>&copy; 2026 Sweet Bakery. Freshly Baked Every Day</p>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
