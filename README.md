#  Sweet Bakery v2.0

**Freshly Baked Every Day**

Aplikasi website toko roti dan kue dengan PHP OOP, Namespace, dan External Libraries.

## ✨ Fitur

### Untuk Pelanggan:
- 🏠 **Home Page** - 4 opsi (Guest, Login, Register, Admin)
- 🛒 **Belanja** - Lihat daftar roti dengan filter kategori
- 🛍️ **Keranjang** - Kelola pesanan sebelum checkout
- 📋 **Checkout** - Input alamat + upload bukti pembayaran (scan QR)
- 📜 **Riwayat** - Lihat history pesanan

### Untuk Admin:
- 📊 **Dashboard** - Statistik penjualan real-time
- 🍞 **Kelola Roti** - CRUD roti, cek stok
- 📋 **Kelola Pesanan** - Update status & verifikasi pembayaran
- 👥 **Data Pelanggan** - Lihat data pelanggan terdaftar

## 🛠️ Teknologi

- **Backend:** PHP 7.4+ dengan OOP
- **Database:** MySQL
- **Namespace:** PSR-4 Autoloading
- **External Libraries:**
  - vlucas/phpdotenv (^5.5) - Environment config
  - phpmailer/phpmailer (^6.8) - Email notifications
  - dompdf/dompdf (^2.0) - PDF generation
  - phpunit/phpunit (^9.6) - Unit testing

## 📁 Struktur Folder

```
sweet-bakery/
├── src/                          # Source code (Namespace SweetBakery)
│   ├── Interfaces/               # Interface definitions
│   │   ├── CRUDInterface.php
│   │   ├── AuthInterface.php
│   │   └── PaymentInterface.php
│   ├── Models/                   # Model classes
│   │   ├── AbstractModel.php     # Abstract parent class
│   │   ├── AbstractUser.php      # Abstract user class
│   │   ├── Admin.php
│   │   ├── Pelanggan.php
│   │   ├── Roti.php
│   │   └── Pesanan.php
│   ├── Traits/                   # Reusable traits
│   │   ├── LoggerTrait.php
│   │   └── ValidatorTrait.php
│   └── Utils/                    # Utility classes
│       ├── Database.php          # Singleton database
│       ├── Helper.php
│       ├── EnvLoader.php
│       └── functions.php
├── pages/                        # Halaman web
│   ├── belanja.php
│   ├── keranjang.php
│   ├── checkout.php
│   ├── login.php
│   ├── register.php
│   ├── riwayat.php
│   └── admin/                    # Admin pages
│       ├── login.php
│       ├── dashboard.php
│       ├── roti.php
│       ├── pesanan.php
│       └── pelanggan.php
├── docs/                         # Dokumentasi
│   ├── RANCANGAN.md              # DFD, ERD, Use Case, Class Diagram
│   └── JAWABAN.md                # Jawaban kriteria penilaian
├── vendor/                       # Composer dependencies
├── composer.json                 # Composer config
├── .env.example                  # Environment template
├── database.sql                  # Database schema
└── index.php                     # Home page
```

## 🚀 Cara Install

### 1. Clone/Download Project
```bash
# Copy folder sweet-bakery ke htdocs (XAMPP)
```

### 2. Install Dependencies
```bash
cd sweet-bakery
composer install
```

### 3. Setup Environment
```bash
# Copy .env.example ke .env
cp .env.example .env

# Edit .env sesuai konfigurasi database Anda
```

### 4. Buat Database
```bash
# Buka phpMyAdmin
# Buat database: sweet_bakery
# Import file database.sql
```

### 5. Akses Website
```
http://localhost/sweet-bakery/
```

## 🔑 Default Login

### Admin:
- **Username:** `admin`
- **Password:** `888`

## 🎨 OOP Concepts Implemented

### 1. **Class & Object**
```php
$pelanggan = new Pelanggan();
$admin = new Admin();
```

### 2. **Inheritance**
```php
class Admin extends AbstractUser extends AbstractModel
```

### 3. **Polymorphism**
```php
// Method findBy() diimplementasikan berbeda di setiap model
$roti->findBy(['status' => 'tersedia']);
$pelanggan->findBy(['email' => 'test@example.com']);
```

### 4. **Interface**
```php
interface CRUDInterface {
    public function create(array $data): int|false;
    public function read(int $id): ?array;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
```

### 5. **Abstract Class**
```php
abstract class AbstractModel implements CRUDInterface {
    abstract public function findBy(array $criteria): array;
}
```

### 6. **Trait**
```php
trait LoggerTrait {
    protected function log(string $message, string $level = 'INFO'): void;
}
```

### 7. **Namespace**
```php
namespace SweetBakery\Models;
use SweetBakery\Interfaces\CRUDInterface;
```

### 8. **Visibility (Access Modifiers)**
```php
public string $publicProperty;
protected string $protectedProperty;
private string $privateProperty;
```

### 9. **Static Method**
```php
Database::getInstance();  // Singleton pattern
Helper::formatRupiah(10000);
```

### 10. **Overloading**
```php
public function getFiltered(...$args): array {
    // Different behavior based on arguments
}
```

## 📋 Kriteria Ujian Praktek

| Kriteria | Status | Lokasi |
|----------|--------|--------|
| a. Rancangan (DFD, Use Case) | ✅ | `docs/RANCANGAN.md` |
| b. Coding Guidelines | ✅ | PSR-4, PHPDoc |
| c. Interface I/O | ✅ | Form, Tampilan Web |
| d. Tipe Data & Control | ✅ | int, string, array, if-else, for, foreach |
| e. Prosedur/Fungsi/Method | ✅ | Class methods, Helper functions |
| f. Array | ✅ | Indexed, Associative, Multidimensional |
| g. Simpan/Baca Data | ✅ | CRUD, File Upload |
| h. OOP (Inheritance, Polymorphism, Interface) | ✅ | Full OOP implementation |
| i. Namespace/Package | ✅ | 4 Namespace |
| j. External Library | ✅ | 4 Libraries (Composer) |
| k. Basis Data | ✅ | MySQL, 6 Tabel |
| l. Dokumentasi | ✅ | PHPDoc, README |

**Total: 12/12 Kriteria ✅ TERPENUHI**

## 📄 Dokumentasi

- **[RANCANGAN.md](docs/RANCANGAN.md)** - DFD, ERD, Use Case, Class Diagram
- **[JAWABAN.md](docs/JAWABAN.md)** - Jawaban lengkap untuk setiap kriteria penilaian
- **[INSTALL.txt](INSTALL.txt)** - Panduan instalasi detail

## 📝 License

MIT License - Free to use for learning purposes.

---

**Sweet Bakery** - Freshly Baked Every Day 
