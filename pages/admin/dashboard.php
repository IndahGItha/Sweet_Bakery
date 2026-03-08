<?php
/**
 * Sweet Bakery - Admin Dashboard
 * 
 * Halaman dashboard untuk administrator
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

// Require login
$admin->requireLogin('login.php');

// Get dashboard stats
$stats = $admin->getDashboardStats();

// Get recent orders
$recentOrders = $admin->getRecentOrders(5);

// Get low stock items
$lowStockItems = $admin->getLowStockItems(5);

// Get flash message
$flash = Helper::getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sweet Bakery</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header admin-header">
        <div class="header-content">
            <a href="logout.php" class="logo">
                <span class="logo-icon"></span>
                <div class="logo-text">
                    <h1>Sweet Bakery</h1>
                    <span>Admin Panel</span>
                </div>
            </a>
            <nav class="nav-menu">
                <span style="color: white;">👤 <?= e($admin->getCurrentUser()['nama'] ?? 'Admin') ?></span>
                <a href="logout.php" class="nav-link" style="color: white;">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Admin Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><span class="icon">📊</span> Dashboard</a></li>
                <li><a href="roti.php"><span class="icon">🍞</span> Kelola Roti</a></li>
                <li><a href="pesanan.php"><span class="icon">📋</span> Kelola Pesanan</a></li>
                <li><a href="pelanggan.php"><span class="icon">👥</span> Data Pelanggan</a></li>
                <li><a href="logout.php"><span class="icon">🏠</span> Lihat Website</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h2 style="margin-bottom: 25px;">📊 Dashboard</h2>
            
            <!-- Flash Message -->
            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>" style="margin-bottom: 20px;">
                <span><?= $flash['type'] === 'success' ? '✅' : '⚠️' ?></span> <?= $flash['message'] ?>
            </div>
            <?php endif; ?>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="icon">🍞</div>
                    <div class="stat-info">
                        <h3><?= $stats['total_roti'] ?? 0 ?></h3>
                        <p>Total Roti</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="icon">📋</div>
                    <div class="stat-info">
                        <h3><?= $stats['total_pesanan'] ?? 0 ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="icon">⏰</div>
                    <div class="stat-info">
                        <h3><?= $stats['pesanan_menunggu'] ?? 0 ?></h3>
                        <p>Pesanan Menunggu</p>
                    </div>
                </div>
                <div class="stat-card danger">
                    <div class="icon">👥</div>
                    <div class="stat-info">
                        <h3><?= $stats['total_pelanggan'] ?? 0 ?></h3>
                        <p>Total Pelanggan</p>
                    </div>
                </div>
            </div>
            
            <!-- Pesanan Terbaru -->
            <div style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 15px;">📋 Pesanan Terbaru</h3>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong>SWB-<?= $order['id_pesanan'] ?></strong></td>
                                <td><?= e($order['nama']) ?></td>
                                <td><?= formatRupiah($order['total_harga']) ?></td>
                                <td>
                                    <?php 
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($order['status_pesanan']) {
                                        case 'menunggu':
                                            $statusClass = 'status-waiting';
                                            $statusText = 'Menunggu';
                                            break;
                                        case 'diproses':
                                            $statusClass = 'status-process';
                                            $statusText = 'Diproses';
                                            break;
                                        case 'dikirim':
                                            $statusClass = 'status-process';
                                            $statusText = 'Dikirim';
                                            break;
                                        case 'selesai':
                                            $statusClass = 'status-done';
                                            $statusText = 'Selesai';
                                            break;
                                        case 'dibatalkan':
                                            $statusClass = 'status-cancel';
                                            $statusText = 'Dibatalkan';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td><?= formatTanggal($order['created_at'], true) ?></td>
                                <td>
                                    <a href="detail_pesanan.php?id=<?= $order['id_pesanan'] ?>" class="btn btn-sm btn-primary">Detail</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align: right; margin-top: 15px;">
                    <a href="pesanan.php" class="btn btn-outline btn-sm">Lihat Semua Pesanan →</a>
                </div>
            </div>
            
            <!-- Stok Menipis -->
            <div>
                <h3 style="margin-bottom: 15px;">⚠️ Stok Menipis</h3>
                <?php if (!empty($lowStockItems)): ?>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Roti</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockItems as $item): ?>
                            <tr>
                                <td><?= e($item['nama_roti']) ?></td>
                                <td><span style="color: var(--soft-red); font-weight: 600;"><?= $item['stok'] ?></span></td>
                                <td>
                                    <a href="edit_roti.php?id=<?= $item['id_roti'] ?>" class="btn btn-sm btn-primary">Tambah Stok</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div style="background: white; padding: 30px; border-radius: 15px; text-align: center;">
                    <p style="color: var(--gray);">✅ Semua stok roti aman</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
