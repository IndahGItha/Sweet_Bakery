<?php
/**
 * Sweet Bakery - Admin Login
 * 
 * Halaman login untuk administrator
 * Menggunakan namespace SweetBakery\Models
 * 
 * @package SweetBakery\Pages\Admin
 * @author Sweet Bakery Team
 * @version 2.0.0
 */

// Load autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use SweetBakery\Models\Admin;
use SweetBakery\Utils\Helper;

// Initialize model
$admin = new Admin();

// Redirect jika sudah login
if ($admin->isLoggedIn()) {
    Helper::redirect('dashboard.php');
}

$error = '';

// Proses login
if (Helper::isPost()) {
    $username = Helper::input('username', '', 'POST');
    $password = Helper::input('password', '', 'POST');

    if ($admin->login($username, $password)) {
        Helper::setFlash('success', 'Selamat datang, Admin!');
        Helper::redirect('dashboard.php');
    } else {
        $errors = $admin->getValidationErrors();
        $error = !empty($errors) ? implode(', ', $errors) : 'Login gagal. Silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sweet Bakery</title>
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
        </div>
    </header>

    <!-- Login Form -->
    <div class="form-container" style="margin-top: 80px;">
        <div class="form-header">
            <div class="icon">🔐</div>
            <h2>Login Admin</h2>
            <p style="color: var(--gray);">Masuk ke panel administrasi</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <span>⚠️</span> <?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?= csrfField() ?>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       placeholder="Masukkan username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn btn-admin" style="width: 100%;">
                Masuk
            </button>
        </form>
        
        <div class="form-footer">
            <p><a href="../../index.php">← Kembali ke Beranda</a></p>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: var(--light-yellow); border-radius: 10px; font-size: 0.85rem;">
            <p style="margin-bottom: 5px;"><strong>Default Login:</strong></p>
            <p>Username: <code>admin</code></p>
            <p>Password: <code>888</code></p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" style="margin-top: 80px;">
        <div class="footer-bottom">
            <p>&copy; 2026 Sweet Bakery. Admin Panel</p>
        </div>
    </footer>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
