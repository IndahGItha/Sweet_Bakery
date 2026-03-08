<?php
/**
 * Sweet Bakery - Admin Model
 * 
 * Model untuk Admin
 * Menerapkan Inheritance dari AbstractUser
 * 
 * @package SweetBakery\Models
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Models;

/**
 * Class Admin
 * 
 * Model untuk mengelola data admin
 * Extends AbstractUser untuk inheritance
 */
class Admin extends AbstractUser
{
    /**
     * @var string Nama tabel database
     */
    protected string $tableName = 'admin';

    /**
     * @var string Primary key
     */
    protected string $primaryKey = 'id_admin';

    /**
     * @var string Session key
     */
    protected string $sessionKey = 'admin';

    /**
     * @var string Role
     */
    protected string $role = 'admin';

    /**
     * @var array Field yang dapat diisi
     */
    protected array $fillable = [
        'username',
        'password',
        'nama_admin'
    ];

    /**
     * Login admin dengan username dan password
     * 
     * @param string $username Username admin
     * @param string $password Password admin
     * @return bool True jika login berhasil
     */
    public function login(string $username, string $password): bool
    {
        $this->clearErrors();

        // Validasi input
        if (!$this->validateRequired($username, 'Username')) {
            return false;
        }

        if (!$this->validateRequired($password, 'Password')) {
            return false;
        }

        // Cari admin berdasarkan username
        $sql = "SELECT * FROM {$this->tableName} WHERE username = '{$this->escape($username)}' LIMIT 1";
        $result = $this->query($sql);

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // Verifikasi password
            if ($password == $admin['password']) {
                // Set session
                $this->setUserSession([
                    'id' => $admin['id_admin'],
                    'nama' => $admin['nama_admin'],
                    'email' => $admin['username'], // Admin pakai username
                    'role' => $this->role
                ]);

                $this->logInfo("Admin {$username} logged in successfully");
                return true;
            } else {
                $this->validationErrors[] = 'Password salah';
                $this->logWarning("Failed login attempt for admin: {$username} - Wrong password");
            }
        } else {
            $this->validationErrors[] = 'Username tidak ditemukan';
            $this->logWarning("Failed login attempt - Username not found: {$username}");
        }

        return false;
    }

    /**
     * Polymorphism - Override method findBy
     * Mencari admin berdasarkan kriteria khusus
     * 
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): array
    {
        $conditions = [];

        foreach ($criteria as $key => $value) {
            $key = $this->escape($key);
            $value = $this->escape($value);
            $conditions[] = "{$key} LIKE '%{$value}%'";
        }

        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT * FROM {$this->tableName} {$where}";

        $result = $this->query($sql);
        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Hapus password dari hasil
                unset($row['password']);
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Overloading - Method untuk update profile admin
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $id, array $data): bool
    {
        // Hanya boleh update field tertentu
        $allowedFields = ['nama_admin', 'password'];
        $data = array_intersect_key($data, array_flip($allowedFields));

        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }

        return $this->update($id, $data);
    }

    /**
     * Get dashboard statistics
     * 
     * @return array
     */
    public function getDashboardStats(): array
    {
        $stats = [];

        // Total roti
        $result = $this->query("SELECT COUNT(*) as total FROM roti");
        $stats['total_roti'] = $result->fetch_assoc()['total'] ?? 0;

        // Total pesanan
        $result = $this->query("SELECT COUNT(*) as total FROM pesanan");
        $stats['total_pesanan'] = $result->fetch_assoc()['total'] ?? 0;

        // Pesanan menunggu
        $result = $this->query("SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'menunggu'");
        $stats['pesanan_menunggu'] = $result->fetch_assoc()['total'] ?? 0;

        // Total pelanggan
        $result = $this->query("SELECT COUNT(*) as total FROM pelanggan");
        $stats['total_pelanggan'] = $result->fetch_assoc()['total'] ?? 0;

        // Total pendapatan
        $result = $this->query("SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan = 'selesai'");
        $stats['total_pendapatan'] = $result->fetch_assoc()['total'] ?? 0;

        return $stats;
    }

    /**
     * Get recent orders
     * 
     * @param int $limit
     * @return array
     */
    public function getRecentOrders(int $limit = 5): array
    {
        $sql = "SELECT p.*, IFNULL(pl.nama_pelanggan, p.nama_pelanggan) as nama 
                FROM pesanan p 
                LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                ORDER BY p.created_at DESC 
                LIMIT {$limit}";

        $result = $this->query($sql);
        $orders = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }

        return $orders;
    }

    /**
     * Get low stock items
     * 
     * @param int $threshold
     * @return array
     */
    public function getLowStockItems(int $threshold = 5): array
    {
        $sql = "SELECT * FROM roti WHERE stok <= {$threshold} AND status = 'tersedia' ORDER BY stok ASC";
        
        $result = $this->query($sql);
        $items = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }

        return $items;
    }
}
