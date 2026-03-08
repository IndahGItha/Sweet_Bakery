<?php
/**
 * Sweet Bakery - Pesanan Model
 * 
 * Model untuk pesanan
 * Mengimplementasikan PaymentInterface
 * 
 * @package SweetBakery\Models
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Models;

use SweetBakery\Interfaces\PaymentInterface;
use SweetBakery\Traits\ValidatorTrait;

/**
 * Class Pesanan
 * 
 * Model untuk mengelola data pesanan
 * Mengimplementasikan PaymentInterface
 */
class Pesanan extends AbstractModel implements PaymentInterface
{
    use ValidatorTrait;

    /**
     * @var string Nama tabel database
     */
    protected string $tableName = 'pesanan';

    /**
     * @var string Primary key
     */
    protected string $primaryKey = 'id_pesanan';

    /**
     * @var array Field yang dapat diisi
     */
    protected array $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'no_telepon',
        'alamat_pengiriman',
        'total_harga',
        'status_pesanan',
        'status_pembayaran',
        'bukti_pembayaran',
        'catatan'
    ];

    /**
     * @var Roti Instance model roti
     */
    protected Roti $rotiModel;

    /**
     * @var string Folder upload bukti pembayaran
     */
    protected string $uploadPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->rotiModel = new Roti();
        $this->uploadPath = __DIR__ . '/../../uploads/pembayaran/';
    }

    /**
     * Polymorphism - Override method create
     * Create pesanan dengan detail
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data): int|false
    {
        $this->clearErrors();

        // Validasi data pesanan
        if (!$this->validatePesananData($data)) {
            return false;
        }

        // Start transaction
        $this->db->beginTransaction();

        try {
            // Insert pesanan
            $pesananId = parent::create($data);

            if (!$pesananId) {
                throw new \Exception('Gagal membuat pesanan');
            }

            // Insert detail pesanan jika ada
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->addDetail($pesananId, $item);

                    // Kurangi stok roti
                    $this->rotiModel->kurangiStok($item['id_roti'], $item['jumlah']);
                }
            }

            $this->db->commit();
            $this->logInfo("Pesanan created with ID: {$pesananId}");

            return $pesananId;
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->logError("Failed to create pesanan: " . $e->getMessage());
            $this->validationErrors[] = $e->getMessage();
            return false;
        }
    }

    /**
     * Validasi data pesanan
     * 
     * @param array $data
     * @return bool
     */
    protected function validatePesananData(array $data): bool
    {
        if (!$this->validateRequired($data['nama_pelanggan'] ?? '', 'Nama pelanggan')) {
            return false;
        }

        if (!$this->validateRequired($data['no_telepon'] ?? '', 'No telepon')) {
            return false;
        }

        if (!$this->validatePhone($data['no_telepon'])) {
            return false;
        }

        if (!$this->validateRequired($data['alamat_pengiriman'] ?? '', 'Alamat pengiriman')) {
            return false;
        }

        if (!$this->validateRequired($data['total_harga'] ?? '', 'Total harga')) {
            return false;
        }

        return true;
    }

    /**
     * Tambah detail pesanan
     * 
     * @param int $pesananId
     * @param array $item
     * @return bool
     */
    protected function addDetail(int $pesananId, array $item): bool
    {
        $idRoti = intval($item['id_roti']);
        $jumlah = intval($item['jumlah']);
        $harga = floatval($item['harga']);
        $subtotal = $harga * $jumlah;

        $sql = "INSERT INTO detail_pesanan (id_pesanan, id_roti, jumlah, harga_satuan, subtotal) 
                VALUES ({$pesananId}, {$idRoti}, {$jumlah}, {$harga}, {$subtotal})";

        return $this->query($sql) === true;
    }

    /**
     * Polymorphism - Override method read
     * Read pesanan dengan detail
     * 
     * @param int $id
     * @return array|null
     */
    public function read(int $id): ?array
    {
        // Get data pesanan
        $pesanan = parent::read($id);

        if (!$pesanan) {
            return null;
        }

        // Get detail pesanan
        $pesanan['items'] = $this->getDetail($id);

        return $pesanan;
    }

    /**
     * Get detail pesanan
     * 
     * @param int $pesananId
     * @return array
     */
    public function getDetail(int $pesananId): array
    {
        $sql = "SELECT d.*, r.nama_roti, r.gambar 
                FROM detail_pesanan d 
                JOIN roti r ON d.id_roti = r.id_roti 
                WHERE d.id_pesanan = {$pesananId}";

        $result = $this->query($sql);
        $items = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }

        return $items;
    }

    /**
     * Polymorphism - Override method findBy
     * 
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): array
    {
        $join = '';
        $select = 'p.*';

        // Join dengan pelanggan jika perlu
        if (isset($criteria['with_pelanggan']) && $criteria['with_pelanggan']) {
            $join = 'LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan';
            $select = 'p.*, pl.nama_pelanggan as nama_pelanggan_lengkap, pl.email';
            unset($criteria['with_pelanggan']);
        }

        $conditions = [];
        foreach ($criteria as $key => $value) {
            $key = $this->escape($key);
            if (is_string($value)) {
                $value = "'" . $this->escape($value) . "'";
            }
            $conditions[] = "p.{$key} = {$value}";
        }

        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT {$select} FROM {$this->tableName} p {$join} {$where} ORDER BY p.created_at DESC";

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
     * Implementasi PaymentInterface - Process Payment
     * 
     * @param int $orderId
     * @param float $amount
     * @param array $paymentData
     * @return bool
     */
    public function processPayment(int $orderId, float $amount, array $paymentData = []): bool
    {
        // Update status pembayaran
        return $this->update($orderId, [
            'status_pembayaran' => self::STATUS_PENDING
        ]);
    }

    /**
     * Implementasi PaymentInterface - Verify Payment
     * 
     * @param int $paymentId
     * @param string $status
     * @return bool
     */
    public function verifyPayment(int $paymentId, string $status): bool
    {
        if (!in_array($status, [self::STATUS_VERIFIED, self::STATUS_REJECTED])) {
            return false;
        }

        return $this->update($paymentId, [
            'status_pembayaran' => $status
        ]);
    }

    /**
     * Implementasi PaymentInterface - Get Payment Status
     * 
     * @param int $orderId
     * @return string
     */
    public function getPaymentStatus(int $orderId): string
    {
        $pesanan = $this->read($orderId);
        return $pesanan['status_pembayaran'] ?? self::STATUS_PENDING;
    }

    /**
     * Implementasi PaymentInterface - Upload Proof
     * 
     * @param array $fileData
     * @return string|false
     */
    public function uploadProof(array $fileData): string|false
    {
        // Validasi file
        if (!isset($fileData['tmp_name']) || empty($fileData['tmp_name'])) {
            $this->validationErrors[] = 'File tidak ditemukan';
            return false;
        }

        // Cek error upload
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            $this->validationErrors[] = 'Gagal upload file';
            return false;
        }

        // Cek ukuran file (max 2MB)
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($fileData['size'] > $maxSize) {
            $this->validationErrors[] = 'Ukuran file maksimal 2MB';
            return false;
        }

        // Cek ekstensi file
        $allowedExt = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $this->validationErrors[] = 'Format file harus JPG, JPEG, atau PNG';
            return false;
        }

        // Generate nama file unik
        $filename = uniqid('payment_') . '.' . $ext;
        $filepath = $this->uploadPath . $filename;

        // Pindahkan file
        if (move_uploaded_file($fileData['tmp_name'], $filepath)) {
            $this->logInfo("Payment proof uploaded: {$filename}");
            return $filename;
        }

        $this->validationErrors[] = 'Gagal menyimpan file';
        return false;
    }

    /**
     * Update status pesanan
     * 
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $allowedStatus = ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

        if (!in_array($status, $allowedStatus)) {
            $this->validationErrors[] = 'Status tidak valid';
            return false;
        }

        return $this->update($id, ['status_pesanan' => $status]);
    }

    /**
     * Get pesanan by pelanggan
     * 
     * @param int $pelangganId
     * @return array
     */
    public function getByPelanggan(int $pelangganId): array
    {
        return $this->findBy(['id_pelanggan' => $pelangganId]);
    }

    /**
     * Get pesanan dengan status tertentu
     * 
     * @param string $status
     * @return array
     */
    public function getByStatus(string $status): array
    {
        return $this->findBy(['status_pesanan' => $status]);
    }

    /**
     * Get recent orders
     * 
     * @param int $limit
     * @return array
     */
    public function getRecent(int $limit = 5): array
    {
        $sql = "SELECT p.*, pl.nama_pelanggan 
                FROM {$this->tableName} p 
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
     * Get total pendapatan
     * 
     * @return float
     */
    public function getTotalPendapatan(): float
    {
        $sql = "SELECT SUM(total_harga) as total FROM {$this->tableName} WHERE status_pesanan = 'selesai'";
        $result = $this->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            return floatval($row['total'] ?? 0);
        }

        return 0;
    }

    /**
     * Overloading - Get orders with different filters
     * 
     * @param mixed ...$args
     * @return array
     */
    public function getFiltered(...$args): array
    {
        if (count($args) === 1 && is_string($args[0])) {
            // Filter by status
            return $this->getByStatus($args[0]);
        } elseif (count($args) === 1 && is_int($args[0])) {
            // Filter by pelanggan
            return $this->getByPelanggan($args[0]);
        } elseif (count($args) === 2) {
            // Filter by status and pelanggan
            $sql = "SELECT * FROM {$this->tableName} 
                    WHERE status_pesanan = '{$this->escape($args[0])}' 
                    AND id_pelanggan = {$args[1]} 
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

        return $this->getAll();
    }
}
