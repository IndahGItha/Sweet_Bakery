<?php
/**
 * Sweet Bakery - Abstract User Class
 * 
 * Abstract class untuk User (Admin dan Pelanggan)
 * Menerapkan Inheritance dalam OOP
 * 
 * @package SweetBakery\Models
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Models;

use SweetBakery\Interfaces\AuthInterface;
use SweetBakery\Traits\ValidatorTrait;

/**
 * Class AbstractUser
 * 
 * Abstract class untuk semua tipe user
 * Diextends oleh Admin dan Pelanggan
 */
abstract class AbstractUser extends AbstractModel implements AuthInterface
{
    use ValidatorTrait;

    /**
     * @var string Session key untuk user
     */
    protected string $sessionKey;

    /**
     * @var string Role user
     */
    protected string $role;

    /**
     * @var array Data user yang sedang login
     */
    protected ?array $currentUser = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Start session jika belum
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Load current user dari session
        $this->loadCurrentUser();
    }

    /**
     * Login user
     * 
     * @param string $identifier Username atau email
     * @param string $password Password
     * @return bool True jika berhasil
     */
    abstract public function login(string $identifier, string $password): bool;

    /**
     * Logout user
     * 
     * @return void
     */
    public function logout(): void
    {
        // Hapus session
        if (isset($_SESSION[$this->sessionKey])) {
            unset($_SESSION[$this->sessionKey]);
        }

        // Hapus semua session data terkait
        $sessionVars = ['id', 'nama', 'email', 'role'];
        foreach ($sessionVars as $var) {
            $key = $this->sessionKey . '_' . $var;
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }

        $this->currentUser = null;
        $this->logInfo("User logged out from {$this->role}");
    }

    /**
     * Cek apakah user sudah login
     * 
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION[$this->sessionKey]) && $_SESSION[$this->sessionKey] === true;
    }

    /**
     * Get current user data
     * 
     * @return array|null
     */
    public function getCurrentUser(): ?array
    {
        return $this->currentUser;
    }

    /**
     * Cek apakah user memiliki role tertentu
     * 
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Load current user dari session
     * 
     * @return void
     */
    protected function loadCurrentUser(): void
    {
        if ($this->isLoggedIn()) {
            $this->currentUser = [
                'id' => $_SESSION[$this->sessionKey . '_id'] ?? null,
                'nama' => $_SESSION[$this->sessionKey . '_nama'] ?? null,
                'email' => $_SESSION[$this->sessionKey . '_email'] ?? null,
                'role' => $this->role
            ];
        }
    }

    /**
     * Set session untuk user yang login
     * 
     * @param array $userData
     * @return void
     */
    protected function setUserSession(array $userData): void
    {
        $_SESSION[$this->sessionKey] = true;
        $_SESSION[$this->sessionKey . '_id'] = $userData['id'];
        $_SESSION[$this->sessionKey . '_nama'] = $userData['nama'];
        $_SESSION[$this->sessionKey . '_email'] = $userData['email'];
        $_SESSION[$this->sessionKey . '_role'] = $this->role;

        $this->currentUser = $userData;
    }

    /**
     * Hash password
     * 
     * @param string $password
     * @return string
     */
    protected function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify password
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    protected function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Require login - redirect jika belum login
     * 
     * @param string $redirectUrl
     * @return void
     */
    public function requireLogin(string $redirectUrl = 'login.php'): void
    {
        if (!$this->isLoggedIn()) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Require guest - redirect jika sudah login
     * 
     * @param string $redirectUrl
     * @return void
     */
    public function requireGuest(string $redirectUrl = 'index.php'): void
    {
        if ($this->isLoggedIn()) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Find by criteria - implementasi abstract method
     * 
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): array
    {
        return $this->getAll($criteria);
    }

    /**
     * Find by email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE email = '{$this->escape($email)}' LIMIT 1";
        $result = $this->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Register new user
     * 
     * @param array $data
     * @return int|false
     */
    public function register(array $data): int|false
    {
        // Validasi
        $this->clearErrors();

        if (!$this->validateRequired($data['email'] ?? '', 'Email')) {
            return false;
        }

        if (!$this->validateEmail($data['email'])) {
            return false;
        }

        if (!$this->validateRequired($data['password'] ?? '', 'Password')) {
            return false;
        }

        if (!$this->validateLength($data['password'], 6, 255, 'Password')) {
            return false;
        }

        // Cek email sudah terdaftar
        if ($this->findByEmail($data['email'])) {
            $this->validationErrors[] = 'Email sudah terdaftar';
            return false;
        }

        // Hash password
        $data['password'] = $this->hashPassword($data['password']);

        return $this->create($data);
    }
}
