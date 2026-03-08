-- Database Sweet Bakery
-- Freshly Baked Every Day

CREATE DATABASE IF NOT EXISTS sweetbakery_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sweetbakery_v2;

-- Tabel Admin
CREATE TABLE admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_admin VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pelanggan
CREATE TABLE pelanggan (
    id_pelanggan INT PRIMARY KEY AUTO_INCREMENT,
    nama_pelanggan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    no_telepon VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kategori Roti
CREATE TABLE kategori (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL
);

-- Tabel Roti
CREATE TABLE roti (
    id_roti INT PRIMARY KEY AUTO_INCREMENT,
    nama_roti VARCHAR(100) NOT NULL,
    id_kategori INT,
    harga INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255),
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL
);

-- Tabel Pesanan
CREATE TABLE pesanan (
    id_pesanan INT PRIMARY KEY AUTO_INCREMENT,
    id_pelanggan INT,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(20) NOT NULL,
    alamat_pengiriman TEXT NOT NULL,
    total_harga INT NOT NULL,
    status_pesanan ENUM('menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'menunggu',
    status_pembayaran ENUM('menunggu', 'terverifikasi', 'ditolak') DEFAULT 'menunggu',
    bukti_pembayaran VARCHAR(255),
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE SET NULL
);

-- Tabel Detail Pesanan
CREATE TABLE detail_pesanan (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_pesanan INT NOT NULL,
    id_roti INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_roti) REFERENCES roti(id_roti) ON DELETE CASCADE
);

-- Insert data admin default (password:888)
INSERT INTO admin (username, password, nama_admin) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Sweet Bakery');

-- Insert data kategori
INSERT INTO kategori (nama_kategori) VALUES 
('Roti Manis'),
('Roti Tawar'),
('Pastry'),
('Kue Kering'),
('Donat'),
('Croissant'),
('Cake'),
('Brownies');

-- Insert data roti sample
INSERT INTO roti (nama_roti, id_kategori, harga, stok, deskripsi, gambar, status) VALUES
('Roti Coklat Keju', 1, 8000, 50, 'Roti lembut dengan isian coklat lumer dan taburan keju', 'roti-coklat-keju.jpg', 'tersedia'),
('Roti Abon', 1, 10000, 40, 'Roti dengan taburan abon sapi premium', 'roti-abon.jpg', 'tersedia'),
('Roti Bluder', 1, 12000, 30, 'Roti bluder klasik dengan tekstur lembut', 'roti-bluder.jpg', 'tersedia'),
('Roti Tawar Kupas', 2, 15000, 25, 'Roti tawar tanpa kulit, lembut dan fluffy', 'roti-tawar.jpg', 'tersedia'),
('Croissant Butter', 6, 18000, 20, 'Croissant premium dengan mentega asli', 'croissant.jpg', 'tersedia'),
('Donat Glaze', 5, 7000, 60, 'Donat dengan glaze manis yang menggoda', 'donat-glaze.jpg', 'tersedia'),
('Donat Coklat', 5, 8000, 50, 'Donat dengan topping coklat leleh', 'donat-coklat.jpg', 'tersedia'),
('Kue Sus', 3, 10000, 35, 'Kue sus dengan vla vanilla creamy', 'kue-sus.jpg', 'tersedia'),
('Apple Pie', 3, 25000, 15, 'Pie apel dengan isian apel segar dan cinnamon', 'apple-pie.jpg', 'tersedia'),
('Brownies Kukus', 8, 20000, 20, 'Brownies kukus lembut dengan topping coklat', 'brownies.jpg', 'tersedia'),
('Cheese Cake', 7, 35000, 10, 'Cheesecake creamy dengan base biskuit', 'cheesecake.jpg', 'tersedia'),
('Kue Nastar', 4, 80000, 30, 'Kue nastar homemade dengan selai nanas', 'nastar.jpg', 'tersedia');
