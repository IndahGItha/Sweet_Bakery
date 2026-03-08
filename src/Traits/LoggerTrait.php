<?php
/**
 * Sweet Bakery - Logger Trait
 * 
 * Trait untuk logging aktivitas
 * 
 * @package SweetBakery\Traits
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Traits;

/**
 * Trait LoggerTrait
 * 
 * Menyediakan fungsionalitas logging yang dapat digunakan
 * oleh berbagai class dalam aplikasi
 */
trait LoggerTrait
{
    /**
     * @var string Lokasi file log
     */
    protected string $logFile = __DIR__ . '/../../logs/app.log';

    /**
     * @var array Log levels
     */
    protected array $logLevels = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3
    ];

    /**
     * Menulis log
     * 
     * @param string $message Pesan log
     * @param string $level Level log (DEBUG, INFO, WARNING, ERROR)
     * @return void
     */
    protected function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;

        // Pastikan folder logs ada
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log debug
     * 
     * @param string $message
     * @return void
     */
    protected function logDebug(string $message): void
    {
        $this->log($message, 'DEBUG');
    }

    /**
     * Log info
     * 
     * @param string $message
     * @return void
     */
    protected function logInfo(string $message): void
    {
        $this->log($message, 'INFO');
    }

    /**
     * Log warning
     * 
     * @param string $message
     * @return void
     */
    protected function logWarning(string $message): void
    {
        $this->log($message, 'WARNING');
    }

    /**
     * Log error
     * 
     * @param string $message
     * @return void
     */
    protected function logError(string $message): void
    {
        $this->log($message, 'ERROR');
    }
}
