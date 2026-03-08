<?php
/**
 * Sweet Bakery - Logout
 * 
 * Halaman logout untuk pelanggan
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

// Logout
$pelanggan->logout();


// Set flash message
Helper::setFlash('success', 'Anda telah logout. Sampai jumpa!');

// Redirect ke halaman utama
Helper::redirect('../index.php');
