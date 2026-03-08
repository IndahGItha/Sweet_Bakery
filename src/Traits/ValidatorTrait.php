<?php
/**
 * Sweet Bakery - Validator Trait
 * 
 * Trait untuk validasi data
 * 
 * @package SweetBakery\Traits
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Traits;

/**
 * Trait ValidatorTrait
 * 
 * Menyediakan fungsionalitas validasi data
 */
trait ValidatorTrait
{
    /**
     * @var array Menyimpan error validasi
     */
    protected array $validationErrors = [];

    /**
     * Validasi email
     * 
     * @param string $email Email yang divalidasi
     * @return bool True jika valid
     */
    protected function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = 'Format email tidak valid';
            return false;
        }
        return true;
    }

    /**
     * Validasi panjang string
     * 
     * @param string $string String yang divalidasi
     * @param int $min Panjang minimum
     * @param int $max Panjang maksimum
     * @param string $fieldName Nama field untuk pesan error
     * @return bool True jika valid
     */
    protected function validateLength(string $string, int $min, int $max, string $fieldName): bool
    {
        $length = strlen($string);
        if ($length < $min || $length > $max) {
            $this->validationErrors[] = "{$fieldName} harus antara {$min} dan {$max} karakter";
            return false;
        }
        return true;
    }

    /**
     * Validasi angka
     * 
     * @param mixed $value Nilai yang divalidasi
     * @param float $min Nilai minimum
     * @param float $max Nilai maksimum
     * @param string $fieldName Nama field
     * @return bool True jika valid
     */
    protected function validateNumber($value, float $min, float $max, string $fieldName): bool
    {
        if (!is_numeric($value) || $value < $min || $value > $max) {
            $this->validationErrors[] = "{$fieldName} harus antara {$min} dan {$max}";
            return false;
        }
        return true;
    }

    /**
     * Validasi required
     * 
     * @param mixed $value Nilai yang divalidasi
     * @param string $fieldName Nama field
     * @return bool True jika valid
     */
    protected function validateRequired($value, string $fieldName): bool
    {
        if (empty($value) && $value !== '0') {
            $this->validationErrors[] = "{$fieldName} wajib diisi";
            return false;
        }
        return true;
    }

    /**
     * Validasi nomor telepon
     * 
     * @param string $phone Nomor telepon
     * @return bool True jika valid
     */
    protected function validatePhone(string $phone): bool
    {
        // Format: minimal 10 digit, hanya angka dan + di awal
        if (!preg_match('/^[+]?[0-9]{10,15}$/', $phone)) {
            $this->validationErrors[] = 'Format nomor telepon tidak valid';
            return false;
        }
        return true;
    }

    /**
     * Mendapatkan error validasi
     * 
     * @return array Array pesan error
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * Cek apakah ada error
     * 
     * @return bool True jika ada error
     */
    public function hasErrors(): bool
    {
        return !empty($this->validationErrors);
    }

    /**
     * Clear errors
     * 
     * @return void
     */
    public function clearErrors(): void
    {
        $this->validationErrors = [];
    }
}
