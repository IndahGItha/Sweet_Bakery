<?php
/**
 * Sweet Bakery - Global Helper Functions
 * 
 * Fungsi-fungsi helper global
 * 
 * @package SweetBakery
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

use SweetBakery\Utils\Helper;

/**
 * Format angka ke Rupiah
 * 
 * @param float $angka
 * @return string
 */
function formatRupiah(float $angka): string
{
    return Helper::formatRupiah($angka);
}

/**
 * Format tanggal
 * 
 * @param string $tanggal
 * @param bool $withTime
 * @return string
 */
function formatTanggal(string $tanggal, bool $withTime = false): string
{
    return Helper::formatTanggal($tanggal, $withTime);
}

/**
 * Generate kode pesanan
 * 
 * @return string
 */
function generateKodePesanan(): string
{
    return Helper::generateKodePesanan();
}

/**
 * Redirect
 * 
 * @param string $url
 * @return void
 */
function redirect(string $url): void
{
    Helper::redirect($url);
}

/**
 * Set flash message
 * 
 * @param string $type
 * @param string $message
 * @return void
 */
function setFlash(string $type, string $message): void
{
    Helper::setFlash($type, $message);
}

/**
 * Get flash message
 * 
 * @return array|null
 */
function getFlash(): ?array
{
    return Helper::getFlash();
}

/**
 * Cek request POST
 * 
 * @return bool
 */
function isPost(): bool
{
    return Helper::isPost();
}

/**
 * Cek request GET
 * 
 * @return bool
 */
function isGet(): bool
{
    return Helper::isGet();
}

/**
 * Get input
 * 
 * @param string $key
 * @param mixed $default
 * @param string $method
 * @return mixed
 */
function input(string $key, $default = null, string $method = 'POST')
{
    return Helper::input($key, $default, $method);
}

/**
 * Escape string
 * 
 * @param string $string
 * @return string
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate string
 * 
 * @param string $string
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate(string $string, int $length, string $suffix = '...'): string
{
    return Helper::truncate($string, $length, $suffix);
}

/**
 * Get base URL
 * 
 * @return string
 */
function baseUrl(): string
{
    return Helper::baseUrl();
}

/**
 * Asset URL
 * 
 * @param string $path
 * @return string
 */
function asset(string $path): string
{
    return baseUrl() . '/assets/' . ltrim($path, '/');
}

/**
 * Upload URL
 * 
 * @param string $filename
 * @return string
 */
function uploadUrl(string $filename): string
{
    return baseUrl() . '/uploads/' . ltrim($filename, '/');
}

/**
 * Debug and die
 * 
 * @param mixed $var
 * @return void
 */
function dd($var): void
{
    Helper::dd($var);
}

/**
 * Old input value
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function old(string $key, $default = ''): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $value = $_SESSION['old_input'][$key] ?? $default;
    unset($_SESSION['old_input'][$key]);

    return $value;
}

/**
 * Set old input
 * 
 * @param array $data
 * @return void
 */
function setOldInput(array $data): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['old_input'] = $data;
}

/**
 * CSRF token
 * 
 * @return string
 */
function csrfToken(): string
{
    return Helper::generateCsrfToken();
}

/**
 * CSRF field
 * 
 * @return string
 */
function csrfField(): string
{
    $token = csrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF
 * 
 * @param string $token
 * @return bool
 */
function verifyCsrf(string $token): bool
{
    return Helper::verifyCsrfToken($token);
}
