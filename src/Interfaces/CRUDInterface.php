<?php
/**
 * Sweet Bakery - CRUD Interface
 * 
 * Interface untuk operasi CRUD (Create, Read, Update, Delete)
 * Menerapkan konsep Interface dalam OOP
 * 
 * @package SweetBakery\Interfaces
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Interfaces;

/**
 * Interface CRUDInterface
 * 
 * Mendefinisikan kontrak untuk semua operasi CRUD
 * pada model-model dalam aplikasi Sweet Bakery
 */
interface CRUDInterface
{
    /**
     * Membuat data baru
     * 
     * @param array $data Data yang akan disimpan
     * @return int|false ID dari data yang baru dibuat atau false jika gagal
     */
    public function create(array $data): int|false;

    /**
     * Membaca data berdasarkan ID
     * 
     * @param int $id ID data yang akan dibaca
     * @return array|null Data yang ditemukan atau null jika tidak ada
     */
    public function read(int $id): ?array;

    /**
     * Memperbarui data berdasarkan ID
     * 
     * @param int $id ID data yang akan diupdate
     * @param array $data Data baru untuk diupdate
     * @return bool True jika berhasil, false jika gagal
     */
    public function update(int $id, array $data): bool;

    /**
     * Menghapus data berdasarkan ID
     * 
     * @param int $id ID data yang akan dihapus
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete(int $id): bool;

    /**
     * Mendapatkan semua data
     * 
     * @param array $filters Filter opsional untuk query
     * @return array Array berisi semua data
     */
    public function getAll(array $filters = []): array;

    /**
     * Menghitung total data
     * 
     * @param array $filters Filter opsional
     * @return int Jumlah total data
     */
    public function count(array $filters = []): int;
}
