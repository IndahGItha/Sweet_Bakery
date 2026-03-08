<?php
/**
 * Sweet Bakery - Abstract Model Class
 * 
 * Abstract class untuk semua model
 * Menerapkan konsep Abstraction dalam OOP
 * 
 * @package SweetBakery\Models
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Models;

use SweetBakery\Interfaces\CRUDInterface;
use SweetBakery\Utils\Database;
use SweetBakery\Traits\LoggerTrait;

/**
 * Class AbstractModel
 * 
 * Abstract class yang menjadi parent untuk semua model
 * Mengimplementasikan CRUDInterface
 */
abstract class AbstractModel implements CRUDInterface
{
    use LoggerTrait;

    /**
     * @var Database Instance database
     */
    protected Database $db;

    /**
     * @var string Nama tabel database
     */
    protected string $tableName;

    /**
     * @var string Primary key tabel
     */
    protected string $primaryKey = 'id';

    /**
     * @var array Field yang dapat diisi
     */
    protected array $fillable = [];

    /**
     * @var array Data model
     */
    protected array $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Set data model
     * 
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data model
     * 
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get value dari data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set value ke data
     * 
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Escape string untuk query SQL
     * 
     * @param string $string
     * @return string
     */
    protected function escape(string $string): string
    {
        return $this->db->escape($string);
    }

    /**
     * Execute query
     * 
     * @param string $sql
     * @return \mysqli_result|bool
     */
    protected function query(string $sql)
    {
        $this->logDebug("Executing query: {$sql}");
        return $this->db->query($sql);
    }

    /**
     * Get last insert ID
     * 
     * @return int
     */
    protected function getLastInsertId(): int
    {
        return $this->db->getLastInsertId();
    }

    /**
     * Build WHERE clause dari filters
     * 
     * @param array $filters
     * @return string
     */
    protected function buildWhereClause(array $filters): string
    {
        if (empty($filters)) {
            return '';
        }

        $conditions = [];
        foreach ($filters as $key => $value) {
            $key = $this->escape($key);
            if (is_string($value)) {
                $value = "'" . $this->escape($value) . "'";
            }
            $conditions[] = "{$key} = {$value}";
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }

    /**
     * Implementasi CRUD - Create
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data): int|false
    {
        // Filter hanya field yang boleh diisi
        $data = array_intersect_key($data, array_flip($this->fillable));

        if (empty($data)) {
            $this->logError('No data to insert');
            return false;
        }

        $fields = array_keys($data);
        $values = array_map(function ($value) {
            if (is_string($value)) {
                return "'" . $this->escape($value) . "'";
            }
            return $value;
        }, array_values($data));

        $sql = "INSERT INTO {$this->tableName} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $values) . ")";

        if ($this->query($sql)) {
            $insertId = $this->getLastInsertId();
            $this->logInfo("Created new record in {$this->tableName} with ID: {$insertId}");
            return $insertId;
        }

        return false;
    }

    /**
     * Implementasi CRUD - Read
     * 
     * @param int $id
     * @return array|null
     */
    public function read(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = {$id} LIMIT 1";
        $result = $this->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Implementasi CRUD - Update
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        // Filter hanya field yang boleh diisi
        $data = array_intersect_key($data, array_flip($this->fillable));

        if (empty($data)) {
            return false;
        }

        $sets = [];
        foreach ($data as $key => $value) {
            $key = $this->escape($key);
            if (is_string($value)) {
                $value = "'" . $this->escape($value) . "'";
            }
            $sets[] = "{$key} = {$value}";
        }

        $sql = "UPDATE {$this->tableName} SET " . implode(', ', $sets) . 
               " WHERE {$this->primaryKey} = {$id}";

        if ($this->query($sql)) {
            $this->logInfo("Updated record {$id} in {$this->tableName}");
            return true;
        }

        return false;
    }

    /**
     * Implementasi CRUD - Delete
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = {$id}";

        if ($this->query($sql)) {
            $this->logInfo("Deleted record {$id} from {$this->tableName}");
            return true;
        }

        return false;
    }

    /**
     * Implementasi CRUD - Get All
     * 
     * @param array $filters
     * @return array
     */
    public function getAll(array $filters = []): array
    {
        $where = $this->buildWhereClause($filters);
        $sql = "SELECT * FROM {$this->tableName} {$where}";

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
     * Implementasi CRUD - Count
     * 
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int
    {
        $where = $this->buildWhereClause($filters);
        $sql = "SELECT COUNT(*) as total FROM {$this->tableName} {$where}";

        $result = $this->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return (int) $row['total'];
        }

        return 0;
    }

    /**
     * Abstract method untuk find by criteria
     * Harus diimplementasikan oleh child class
     * 
     * @param array $criteria
     * @return array
     */
    abstract public function findBy(array $criteria): array;
}
