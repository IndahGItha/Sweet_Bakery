<?php
/**
 * Sweet Bakery - Home Page
 * 
 * Halaman utama aplikasi Sweet Bakery
 * Menggunakan namespace SweetBakery
 * 
 * @package SweetBakery
 * @author Sweet Bakery Team
 * @version 2.0.0
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Use namespace
use SweetBakery\Models\Admin;
use SweetBakery\Models\Pelanggan;
use SweetBakery\Utils\Helper;

// Initialize models untuk cek login
$admin = new Admin();
$pelanggan = new Pelanggan();

// Get flash message
$flash = Helper::getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Bakery - Freshly Baked Every Day</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'></text></svg>">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">

                <div class="logo-text">
                    <h1>Sweet Bakery</h1>
                    <span>Freshly Baked Every Day</span>
                </div>
            </a>
            <nav class="nav-menu">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="pages/belanja.php" class="nav-link">Belanja</a>
                <?php if ($pelanggan->isLoggedIn()): ?>
                    <a href="pages/keranjang.php" class="nav-link">🛒 Keranjang</a>
                    <a href="pages/riwayat.php" class="nav-link">Riwayat</a>
                    <a href="pages/logout.php" class="nav-link">Logout</a>
                <?php elseif ($admin->isLoggedIn()): ?>
                    <a href="pages/admin/dashboard.php" class="nav-link">Dashboard</a>
                    <a href="pages/admin/logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="pages/login.php" class="nav-link">Masuk</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Flash Message -->
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>" style="max-width: 800px; margin: 20px auto;">
        <span><?= $flash['type'] === 'success' ? '✅' : '⚠️' ?></span> <?= $flash['message'] ?>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
        <div class="hero-icon">
            <img src="uploads/Logo Sweet bakery.png" alt="Sweet Bakery Logo">
        </div>
            <h1>Selamat Datang di Sweet Bakery</h1>
            <p class="slogan">"Freshly Baked Every Day"</p>
            <p style="color: var(--gray); margin-bottom: 30px;">
                Nikmati roti dan kue segar setiap hari, dibuat dengan cinta dan bahan berkualitas
            </p>
            
            <div class="hero-buttons">
                <!-- Beli Langsung / Guest -->
                <a href="pages/belanja.php" class="hero-btn guest">
                    <span class="icon">🛒</span>
                    <span class="label">Beli Langsung</span>
                    <small>Tanpa Login</small>
                </a>
                
                <!-- Login Pelanggan -->
                <a href="pages/login.php" class="hero-btn login">
                    <span class="icon">👤</span>
                    <span class="label">Masuk</span>
                    <small>Login Pelanggan</small>
                </a>
                
                <!-- Login Admin -->
                <a href="pages/admin/login.php" class="hero-btn admin">
                    <span class="icon">🔐</span>
                    <span class="label">Admin</span>
                    <small>Login Admin</small>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="products-section" style="background: white; padding: 60px 20px;">
        <div class="section-header">
            <h2>Mengapa Memilih Sweet Bakery?</h2>
            <p>Kami berkomitmen memberikan yang terbaik untuk Anda</p>
        </div>
        
        <div class="products-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <div class="product-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 4rem; margin-bottom: 15px;">🌾</div>
                <h3 style="color: var(--dark-brown); margin-bottom: 10px;">Bahan Berkualitas</h3>
                <p style="color: var(--gray);">Menggunakan bahan pilihan terbaik untuk hasil sempurna</p>
            </div>
            
            <div class="product-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 4rem; margin-bottom: 15px;">⏰</div>
                <h3 style="color: var(--dark-brown); margin-bottom: 10px;">Fresh Setiap Hari</h3>
                <p style="color: var(--gray);">Roti dan kue selalu baru, dipanggang setiap pagi</p>
            </div>
            
            <div class="product-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 4rem; margin-bottom: 15px;">🚚</div>
                <h3 style="color: var(--dark-brown); margin-bottom: 10px;">Pengantaran Cepat</h3>
                <p style="color: var(--gray);">Pesanan diantar tepat waktu ke alamat Anda</p>
            </div>
            
            <div class="product-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 4rem; margin-bottom: 15px;">💳</div>
                <h3 style="color: var(--dark-brown); margin-bottom: 10px;">Pembayaran Mudah</h3>
                <p style="color: var(--gray);">Bayar dengan scan QR, cepat dan praktis</p>
            </div>
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
