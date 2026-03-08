<?php
/**
 * Sweet Bakery - Login Pelanggan
 * 
 * Halaman login untuk pelanggan
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

// Proses login
if (Helper::isPost()) {
    $email = Helper::input('email', '', 'POST');
    $password = Helper::input('password', '', 'POST');

    // Array untuk menyimpan input lama
    $oldInput = ['email' => $email];

    if ($pelanggan->login($email, $password)) {
        Helper::setFlash('success', 'Login berhasil! Selamat datang kembali.');
        Helper::redirect('belanja.php');
    } else {
        $errors = $pelanggan->getValidationErrors();
        $error = !empty($errors) ? implode(', ', $errors) : 'Login gagal. Silakan coba lagi.';
        setOldInput($oldInput);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sweet Bakery</title>
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

    <!-- Login Form -->
    <div class="form-container" style="margin-top: 80px;">
        <div class="form-header">
            <div class="icon">👤</div>
            <h2>Masuk ke Akun Anda</h2>
            <p style="color: var(--gray);">Login untuk melanjutkan belanja</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <span>⚠️</span> <?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?= csrfField() ?>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="Masukkan email Anda" 
                       value="<?= e(old('email')) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Masukkan password Anda" required>
            </div>
            
            <button type="submit" class="btn btn-customer" style="width: 100%;">
                Masuk
            </button>
        </form>
        
        <div class="form-footer">
            <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
            <p style="margin-top: 10px;"><a href="belanja.php">Belanja tanpa login</a></p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" style="margin-top: 80px;">
        <div class="footer-bottom">
            <p>&copy; 2026 Sweet Bakery. Freshly Baked Every Day</p>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
