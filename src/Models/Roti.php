<?php
/**
 * Sweet Bakery - Roti Model
 * 
 * Model untuk produk roti
 * 
 * @package SweetBakery\Models
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Models;

use SweetBakery\Traits\ValidatorTrait;

/**
 * Class Roti
 * 
 * Model untuk mengelola data roti/produk
 */
class Roti extends AbstractModel
{
    use ValidatorTrait;

    /**
     * @var string Nama tabel database
     */
    protected string $tableName = 'roti';

    /**
     * @var string Primary key
     */
    protected string $primaryKey = 'id_roti';

    /**
     * @var array Field yang dapat diisi
     */
    protected array $fillable = [
        'nama_roti',
        'id_kategori',
        'harga',
        'stok',
        'deskripsi',
        'gambar',
        'status'
    ];

    /**
     * Status tersedia
     */
    public const STATUS_TERSEDIA = 'tersedia';

    /**
     * Status habis
     */
    public const STATUS_HABIS = 'habis';

    /**
     * Polymorphism - Override method create
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data): int|false
    {
        $this->clearErrors();

        // Validasi
        if (!$this->validateRotiData($data)) {
            return false;
        }

        return parent::create($data);
    }

    /**
     * Polymorphism - Override method update
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->clearErrors();

        // Validasi
        if (!$this->validateRotiData($data, false)) {
            return false;
        }

        return parent::update($id, $data);
    }

    /**
     * Validasi data roti
     * 
     * @param array $data
     * @param bool $isCreate
     * @return bool
     */
    protected function validateRotiData(array $data, bool $isCreate = true): bool
    {
        if ($isCreate || isset($data['nama_roti'])) {
            if (!$this->validateRequired($data['nama_roti'] ?? '', 'Nama roti')) {
                return false;
            }
            if (!$this->validateLength($data['nama_roti'], 3, 100, 'Nama roti')) {
                return false;
            }
        }

        if ($isCreate || isset($data['harga'])) {
            if (!$this->validateRequired($data['harga'] ?? '', 'Harga')) {
                return false;
            }
            if (!$this->validateNumber($data['harga'], 0, 999999999, 'Harga')) {
                return false;
            }
        }

        if ($isCreate || isset($data['stok'])) {
            if (!$this->validateRequired($data['stok'] ?? '', 'Stok')) {
                return false;
            }
            if (!$this->validateNumber($data['stok'], 0, 999999, 'Stok')) {
                return false;
            }
        }

        return true;
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
        $select = 'r.*';

        // Join dengan kategori jika perlu
        if (isset($criteria['with_kategori']) && $criteria['with_kategori']) {
            $join = 'LEFT JOIN kategori k ON r.id_kategori = k.id_kategori';
            $select = 'r.*, k.nama_kategori';
            unset($criteria['with_kategori']);
        }

        $conditions = [];
        foreach ($criteria as $key => $value) {
            $key = $this->escape($key);
            if (is_string($value)) {
                $value = "'" . $this->escape($value) . "'";
            }
            $conditions[] = "r.{$key} = {$value}";
        }

        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT {$select} FROM {$this->tableName} r {$join} {$where}";

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
     * Get roti dengan kategori
     * 
     * @return array
     */
    public function getWithKategori(): array
    {
        return $this->findBy(['with_kategori' => true]);
    }

    /**
     * Get roti tersedia
     * 
     * @return array
     */
    public function getAvailable(): array
    {
        $sql = "SELECT r.*, k.nama_kategori 
                FROM {$this->tableName} r 
                LEFT JOIN kategori k ON r.id_kategori = k.id_kategori 
                WHERE r.status = 'tersedia' AND r.stok > 0 
                ORDER BY r.id_roti DESC";

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
     * Get roti by kategori
     * 
     * @param int $kategoriId
     * @return array
     */
    public function getByKategori(int $kategoriId): array
    {
        $sql = "SELECT r.*, k.nama_kategori 
                FROM {$this->tableName} r 
                LEFT JOIN kategori k ON r.id_kategori = k.id_kategori 
                WHERE r.id_kategori = {$kategoriId} AND r.status = 'tersedia' AND r.stok > 0 
                ORDER BY r.id_roti DESC";

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
     * Search roti
     * 
     * @param string $keyword
     * @return array
     */
    public function search(string $keyword): array
    {
        $keyword = $this->escape($keyword);
        $sql = "SELECT r.*, k.nama_kategori 
                FROM {$this->tableName} r 
                LEFT JOIN kategori k ON r.id_kategori = k.id_kategori 
                WHERE (r.nama_roti LIKE '%{$keyword}%' OR r.deskripsi LIKE '%{$keyword}%') 
                AND r.status = 'tersedia' 
                ORDER BY r.id_roti DESC";

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
     * Kurangi stok
     * 
     * @param int $id
     * @param int $jumlah
     * @return bool
     */
    public function kurangiStok(int $id, int $jumlah): bool
    {
        $sql = "UPDATE {$this->tableName} 
                SET stok = stok - {$jumlah} 
                WHERE {$this->primaryKey} = {$id} AND stok >= {$jumlah}";

        return $this->query($sql) === true;
    }

    /**
     * Tambah stok
     * 
     * @param int $id
     * @param int $jumlah
     * @return bool
     */
    public function tambahStok(int $id, int $jumlah): bool
    {
        $sql = "UPDATE {$this->tableName} 
                SET stok = stok + {$jumlah} 
                WHERE {$this->primaryKey} = {$id}";

        return $this->query($sql) === true;
    }

    /**
     * Get low stock items
     * 
     * @param int $threshold
     * @return array
     */
    public function getLowStock(int $threshold = 5): array
    {
        $sql = "SELECT r.*, k.nama_kategori 
                FROM {$this->tableName} r 
                LEFT JOIN kategori k ON r.id_kategori = k.id_kategori 
                WHERE r.stok <= {$threshold} AND r.status = 'tersedia' 
                ORDER BY r.stok ASC";

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
     * Overloading - Method dengan parameter berbeda
     * Get roti dengan filter berbeda
     * 
     * @param mixed ...$args
     * @return array
     */
    public function getFiltered(...$args): array
    {
        if (count($args) === 1 && is_string($args[0])) {
            // Filter by status
            return $this->findBy(['status' => $args[0]]);
        } elseif (count($args) === 1 && is_int($args[0])) {
            // Filter by kategori
            return $this->getByKategori($args[0]);
        } elseif (count($args) === 2) {
            // Filter by status dan kategori
            return $this->findBy([
                'status' => $args[0],
                'id_kategori' => $args[1]
            ]);
        }

        return $this->getAll();
    }
}
