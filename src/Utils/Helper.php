<?php
/**
 * Sweet Bakery - Helper Utility
 * 
 * Class helper untuk fungsi-fungsi utilitas
 * 
 * @package SweetBakery\Utils
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Utils;

/**
 * Class Helper
 * 
 * Menyediakan fungsi-fungsi utilitas yang sering digunakan
 */
class Helper
{
    /**
     * Format angka ke format Rupiah
     * 
     * @param float $angka
     * @return string
     */
    public static function formatRupiah(float $angka): string
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    /**
     * Format tanggal ke format Indonesia
     * 
     * @param string $tanggal
     * @param bool $withTime
     * @return string
     */
    public static function formatTanggal(string $tanggal, bool $withTime = false): string
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $timestamp = strtotime($tanggal);
        $tgl = date('j', $timestamp);
        $bln = $bulan[(int) date('n', $timestamp)];
        $thn = date('Y', $timestamp);

        $result = "{$tgl} {$bln} {$thn}";

        if ($withTime) {
            $result .= ' ' . date('H:i', $timestamp);
        }

        return $result;
    }

    /**
     * Generate kode pesanan
     * 
     * @return string
     */
    public static function generateKodePesanan(): string
    {
        return 'SWB-' . date('Ymd') . '-' . rand(1000, 9999);
    }

    /**
     * Upload gambar
     * 
     * @param array $fileData Data dari $_FILES
     * @param string $destination Folder tujuan
     * @param int $maxSize Ukuran maksimal dalam bytes
     * @param array $allowedExt Ekstensi yang diizinkan
     * @return string|false Nama file atau false
     */
    public static function uploadGambar(
        array $fileData,
        string $destination,
        int $maxSize = 2097152,
        array $allowedExt = ['jpg', 'jpeg', 'png']
    ): string|false {
        // Cek error
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Cek ukuran
        if ($fileData['size'] > $maxSize) {
            return false;
        }

        // Cek ekstensi
        $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            return false;
        }

        // Generate nama file
        $filename = uniqid() . '.' . $ext;
        $filepath = rtrim($destination, '/') . '/' . $filename;

        // Pindahkan file
        if (move_uploaded_file($fileData['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }

    /**
     * Redirect ke URL
     * 
     * @param string $url
     * @return void
     */
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Set flash message
     * 
     * @param string $type success|error|warning
     * @param string $message
     * @return void
     */
    public static function setFlash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get flash message
     * 
     * @return array|null
     */
    public static function getFlash(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }

        return null;
    }

    /**
     * Cek apakah request adalah POST
     * 
     * @return bool
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Cek apakah request adalah GET
     * 
     * @return bool
     */
    public static function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Get input dengan sanitasi
     * 
     * @param string $key
     * @param mixed $default
     * @param string $method GET|POST
     * @return mixed
     */
    public static function input(string $key, $default = null, string $method = 'POST')
    {
        $source = $method === 'POST' ? $_POST : $_GET;

        if (!isset($source[$key])) {
            return $default;
        }

        $value = $source[$key];

        // Sanitasi
        if (is_string($value)) {
            $value = trim($value);
            $value = stripslashes($value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        return $value;
    }

    /**
     * Generate CSRF token
     * 
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     * 
     * @param string $token
     * @return bool
     */
    public static function verifyCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Truncate string
     * 
     * @param string $string
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate(string $string, int $length, string $suffix = '...'): string
    {
        if (strlen($string) <= $length) {
            return $string;
        }

        return substr($string, 0, $length) . $suffix;
    }

    /**
     * Slugify string
     * 
     * @param string $string
     * @return string
     */
    public static function slugify(string $string): string
    {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }

    /**
     * Get base URL
     * 
     * @return string
     */
    public static function baseUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script = dirname($_SERVER['SCRIPT_NAME']);

        return "{$protocol}://{$host}{$script}";
    }

    /**
     * Debug variable
     * 
     * @param mixed $var
     * @param bool $die
     * @return void
     */
    public static function dd($var, bool $die = true): void
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';

        if ($die) {
            exit;
        }
    }
}
