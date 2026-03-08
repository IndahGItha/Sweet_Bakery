<?php
/**
 * Sweet Bakery - Admin Logout
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use SweetBakery\Models\Admin;
use SweetBakery\Utils\Helper;

session_start();

$admin = new Admin();
$admin->logout(); // hapus session admin

// Hapus cookie remember (jika ada)
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/');
}

// Set flash (opsional)
Helper::setFlash('success', 'Admin telah logout.');

// Redirect ke **beranda**, bukan login admin
Helper::redirect('../../index.php');