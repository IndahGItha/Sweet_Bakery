<?php
/**
 * Sweet Bakery - Database Utility
 * 
 * Singleton class untuk koneksi database
 * Menerapkan Design Pattern Singleton
 * 
 * @package SweetBakery\Utils
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Utils;



/**
 * Class Database
 * 
 * Singleton class untuk mengelola koneksi database
 * Hanya membuat satu instance koneksi selama aplikasi berjalan
 */
class Database
{
    /**
     * @var Database|null Instance singleton
     */
    private static ?Database $instance = null;

    /**
     * @var \mysqli Koneksi database
     */
    private \mysqli $connection;

    /**
     * @var bool Status transaction
     */
    private bool $inTransaction = false;

    /**
     * Private constructor (Singleton pattern)
     */
    private function __construct()
    {


        // Get database configuration
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $database = $_ENV['DB_NAME'] ?? 'sweetbakery_v2';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        // Create connection
        $this->connection = new \mysqli($host, $username, $password, $database);

        // Check connection
        if ($this->connection->connect_error) {
            throw new \Exception('Koneksi database gagal: ' . $this->connection->connect_error);
        }

        // Set charset
        $this->connection->set_charset($charset);
    }

    /**
     * Get singleton instance
     * 
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Prevent cloning (Singleton pattern)
     */
    private function __clone() {}

    /**
     * Prevent unserialization (Singleton pattern)
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * Get database connection
     * 
     * @return \mysqli
     */
    public function getConnection(): \mysqli
    {
        return $this->connection;
    }

    /**
     * Execute query
     * 
     * @param string $sql
     * @return \mysqli_result|bool
     */
    public function query(string $sql)
    {
        return $this->connection->query($sql);
    }

    /**
     * Escape string
     * 
     * @param string $string
     * @return string
     */
    public function escape(string $string): string
    {
        return $this->connection->real_escape_string($string);
    }

    /**
     * Get last insert ID
     * 
     * @return int
     */
    public function getLastInsertId(): int
    {
        return (int) $this->connection->insert_id;
    }

    /**
     * Get affected rows
     * 
     * @return int
     */
    public function getAffectedRows(): int
    {
        return $this->connection->affected_rows;
    }

    /**
     * Get error
     * 
     * @return string
     */
    public function getError(): string
    {
        return $this->connection->error;
    }

    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        if ($this->inTransaction) {
            return false;
        }

        $this->inTransaction = $this->connection->begin_transaction();
        return $this->inTransaction;
    }

    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit(): bool
    {
        if (!$this->inTransaction) {
            return false;
        }

        $result = $this->connection->commit();
        $this->inTransaction = false;
        return $result;
    }

    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollback(): bool
    {
        if (!$this->inTransaction) {
            return false;
        }

        $result = $this->connection->rollback();
        $this->inTransaction = false;
        return $result;
    }

    /**
     * Check if in transaction
     * 
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    /**
     * Close connection
     * 
     * @return void
     */
    public function close(): void
    {
        $this->connection->close();
        self::$instance = null;
    }

    /**
     * Prepare statement
     * 
     * @param string $sql
     * @return \mysqli_stmt|false
     */
    public function prepare(string $sql)
    {
        return $this->connection->prepare($sql);
    }

    /**
     * Execute prepared statement
     * 
     * @param string $sql
     * @param array $params
     * @return \mysqli_result|bool
     */
    public function execute(string $sql, array $params = [])
    {
        $stmt = $this->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($params)) {
            $types = '';
            $values = [];

            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
                $values[] = $param;
            }

            $stmt->bind_param($types, ...$values);
        }

        $stmt->execute();
        return $stmt->get_result();
    }
}
