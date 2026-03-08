<?php
/**
 * Sweet Bakery - Pelanggan Model
 * 
 * Model untuk Pelanggan
 * Menerapkan Inheritance dari AbstractUser
 * 
 * @package SweetBakery\Models
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Models;

/**
 * Class Pelanggan
 * 
 * Model untuk mengelola data pelanggan
 * Extends AbstractUser untuk inheritance
 */
class Pelanggan extends AbstractUser
{
    /**
     * @var string Nama tabel database
     */
    protected string $tableName = 'pelanggan';

    /**
     * @var string Primary key
     */
    protected string $primaryKey = 'id_pelanggan';

    /**
     * @var string Session key
     */
    protected string $sessionKey = 'pelanggan';

    /**
     * @var string Role
     */
    protected string $role = 'pelanggan';

    /**
     * @var array Field yang dapat diisi
     */
    protected array $fillable = [
        'nama_pelanggan',
        'email',
        'password',
        'no_telepon',
        'alamat'
    ];

    /**
     * Login pelanggan dengan email dan password
     * 
     * @param string $email Email pelanggan
     * @param string $password Password pelanggan
     * @return bool True jika login berhasil
     */
    public function login(string $email, string $password): bool
    {
        $this->clearErrors();

        // Validasi input
        if (!$this->validateRequired($email, 'Email')) {
            return false;
        }

        if (!$this->validateEmail($email)) {
            return false;
        }

        if (!$this->validateRequired($password, 'Password')) {
            return false;
        }

        // Cari pelanggan berdasarkan email
        $sql = "SELECT * FROM {$this->tableName} WHERE email = '{$this->escape($email)}' LIMIT 1";
        $result = $this->query($sql);

        if ($result && $result->num_rows > 0) {
            $pelanggan = $result->fetch_assoc();

            // Verifikasi password
            if ($this->verifyPassword($password, $pelanggan['password'])) {
                // Set session
                $this->setUserSession([
                    'id' => $pelanggan['id_pelanggan'],
                    'nama' => $pelanggan['nama_pelanggan'],
                    'email' => $pelanggan['email'],
                    'no_telepon' => $pelanggan['no_telepon'],
                    'alamat' => $pelanggan['alamat'],
                    'role' => $this->role
                ]);

                $this->logInfo("Pelanggan {$email} logged in successfully");
                return true;
            } else {
                $this->validationErrors[] = 'Password salah';
                $this->logWarning("Failed login attempt for pelanggan: {$email} - Wrong password");
            }
        } else {
            $this->validationErrors[] = 'Email tidak ditemukan';
            $this->logWarning("Failed login attempt - Email not found: {$email}");
        }

        return false;
    }

    /**
     * Polymorphism - Override method register
     * Register pelanggan baru dengan validasi tambahan
     * 
     * @param array $data
     * @return int|false
     */
    public function register(array $data): int|false
    {
        $this->clearErrors();

        // Validasi nama
        if (!$this->validateRequired($data['nama_pelanggan'] ?? '', 'Nama')) {
            return false;
        }

        if (!$this->validateLength($data['nama_pelanggan'], 3, 100, 'Nama')) {
            return false;
        }

        // Validasi email
        if (!$this->validateRequired($data['email'] ?? '', 'Email')) {
            return false;
        }

        if (!$this->validateEmail($data['email'])) {
            return false;
        }

        // Validasi password
        if (!$this->validateRequired($data['password'] ?? '', 'Password')) {
            return false;
        }

        if (!$this->validateLength($data['password'], 6, 255, 'Password')) {
            return false;
        }

        // Validasi nomor telepon (opsional)
        if (!empty($data['no_telepon'])) {
            if (!$this->validatePhone($data['no_telepon'])) {
                return false;
            }
        }

        // Cek email sudah terdaftar
        if ($this->findByEmail($data['email'])) {
            $this->validationErrors[] = 'Email sudah terdaftar';
            return false;
        }

        // Hash password
        $data['password'] = $this->hashPassword($data['password']);

        $id = $this->create($data);

        if ($id) {
            $this->logInfo("New pelanggan registered: {$data['email']}");
        }

        return $id;
    }

    /**
     * Polymorphism - Override method findBy
     * Mencari pelanggan dengan kriteria khusus
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
        $sql = "SELECT id_pelanggan, nama_pelanggan, email, no_telepon, alamat, created_at 
                FROM {$this->tableName} {$where}";

        $result = $this->query($sql);
        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Overloading - Update profile pelanggan
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $id, array $data): bool
    {
        // Hanya boleh update field tertentu
        $allowedFields = ['nama_pelanggan', 'no_telepon', 'alamat'];
        $data = array_intersect_key($data, array_flip($allowedFields));

        return $this->update($id, $data);
    }

    /**
     * Update password
     * 
     * @param int $id
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(int $id, string $oldPassword, string $newPassword): bool
    {
        // Ambil data user
        $user = $this->read($id);

        if (!$user) {
            $this->validationErrors[] = 'User tidak ditemukan';
            return false;
        }

        // Verifikasi password lama
        if (!$this->verifyPassword($oldPassword, $user['password'])) {
            $this->validationErrors[] = 'Password lama tidak sesuai';
            return false;
        }

        // Validasi password baru
        if (!$this->validateLength($newPassword, 6, 255, 'Password baru')) {
            return false;
        }

        // Update password
        return $this->update($id, [
            'password' => $this->hashPassword($newPassword)
        ]);
    }

    /**
     * Get order history
     * 
     * @param int $pelangganId
     * @return array
     */
    public function getOrderHistory(int $pelangganId): array
    {
        $sql = "SELECT * FROM pesanan 
                WHERE id_pelanggan = {$pelangganId} 
                ORDER BY created_at DESC";

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
     * Get order statistics
     * 
     * @param int $pelangganId
     * @return array
     */
    public function getOrderStats(int $pelangganId): array
    {
        $stats = [];

        // Total pesanan
        $sql = "SELECT COUNT(*) as total FROM pesanan WHERE id_pelanggan = {$pelangganId}";
        $result = $this->query($sql);
        $stats['total_pesanan'] = $result->fetch_assoc()['total'] ?? 0;

        // Total belanja
        $sql = "SELECT SUM(total_harga) as total FROM pesanan WHERE id_pelanggan = {$pelangganId}";
        $result = $this->query($sql);
        $stats['total_belanja'] = $result->fetch_assoc()['total'] ?? 0;

        return $stats;
    }
}
