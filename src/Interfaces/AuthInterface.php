<?php
/**
 * Sweet Bakery - Authentication Interface
 * 
 * Interface untuk sistem autentikasi
 * 
 * @package SweetBakery\Interfaces
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Interfaces;

/**
 * Interface AuthInterface
 * 
 * Mendefinisikan kontrak untuk sistem autentikasi
 * digunakan oleh class User, Admin, dan Pelanggan
 */
interface AuthInterface
{
    /**
     * Melakukan proses login
     * 
     * @param string $identifier Username atau email
     * @param string $password Password
     * @return bool True jika login berhasil
     */
    public function login(string $identifier, string $password): bool;

    /**
     * Melakukan proses logout
     * 
     * @return void
     */
    public function logout(): void;

    /**
     * Mengecek apakah user sudah login
     * 
     * @return bool True jika sudah login
     */
    public function isLoggedIn(): bool;

    /**
     * Mendapatkan data user yang sedang login
     * 
     * @return array|null Data user atau null
     */
    public function getCurrentUser(): ?array;

    /**
     * Mengecek permission/role user
     * 
     * @param string $role Role yang dicek
     * @return bool True jika memiliki role
     */
    public function hasRole(string $role): bool;
}
