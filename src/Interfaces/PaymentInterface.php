<?php
/**
 * Sweet Bakery - Payment Interface
 * 
 * Interface untuk sistem pembayaran
 * 
 * @package SweetBakery\Interfaces
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Interfaces;

/**
 * Interface PaymentInterface
 * 
 * Mendefinisikan kontrak untuk sistem pembayaran
 */
interface PaymentInterface
{
    /**
     * Status pembayaran menunggu
     */
    public const STATUS_PENDING = 'menunggu';

    /**
     * Status pembayaran terverifikasi
     */
    public const STATUS_VERIFIED = 'terverifikasi';

    /**
     * Status pembayaran ditolak
     */
    public const STATUS_REJECTED = 'ditolak';

    /**
     * Memproses pembayaran
     * 
     * @param int $orderId ID pesanan
     * @param float $amount Jumlah pembayaran
     * @param array $paymentData Data pembayaran tambahan
     * @return bool True jika berhasil
     */
    public function processPayment(int $orderId, float $amount, array $paymentData = []): bool;

    /**
     * Memverifikasi pembayaran
     * 
     * @param int $paymentId ID pembayaran
     * @param string $status Status verifikasi
     * @return bool True jika berhasil
     */
    public function verifyPayment(int $paymentId, string $status): bool;

    /**
     * Mendapatkan status pembayaran
     * 
     * @param int $orderId ID pesanan
     * @return string Status pembayaran
     */
    public function getPaymentStatus(int $orderId): string;

    /**
     * Mengupload bukti pembayaran
     * 
     * @param array $fileData Data file dari $_FILES
     * @return string|false Path file atau false
     */
    public function uploadProof(array $fileData): string|false;
}
