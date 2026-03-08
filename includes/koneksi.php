<?php
// Koneksi Database Sweet Bakery
// Freshly Baked Every Day

$host = "localhost";
$username = "root";
$password = "";
$database = "sweetbakery_v2";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Fungsi helper
function query($sql) {
    global $conn;
    return $conn->query($sql);
}

function escape($string) {
    global $conn;
    return $conn->real_escape_string($string);
}

function generateKodePesanan() {
    return 'SWB-' . date('Ymd') . '-' . rand(1000, 9999);
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function uploadGambar($file, $folder = 'uploads/') {
    $namaFile = $file['name'];
    $ukuranFile = $file['size'];
    $error = $file['error'];
    $tmpName = $file['tmp_name'];
    
    // Cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
        return false;
    }
    
    // Cek ekstensi file
    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    
    if (!in_array($ekstensiFile, $ekstensiValid)) {
        return false;
    }
    
    // Cek ukuran file (max 2MB)
    if ($ukuranFile > 2000000) {
        return false;
    }
    
    // Generate nama file baru
    $namaFileBaru = uniqid() . '.' . $ekstensiFile;
    
    // Upload file
    move_uploaded_file($tmpName, $folder . $namaFileBaru);
    
    return $namaFileBaru;
}
?>
