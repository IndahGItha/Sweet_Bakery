<?php
/**
 * Sweet Bakery - Environment Loader
 * 
 * Class untuk load environment variables dari .env file
 * 
 * @package SweetBakery\Utils
 * @author Sweet Bakery Team
 * @version 1.0.0
 */

namespace SweetBakery\Utils;

/**
 * Class EnvLoader
 * 
 * Load environment variables dari file .env
 */
class EnvLoader
{
    /**
     * Load .env file
     * 
     * @param string $path Path ke folder yang berisi .env
     * @return void
     */
    public static function load(string $path = __DIR__ . '/../../'): void
    {
        $envFile = $path . '.env';

        if (!file_exists($envFile)) {
            // Copy dari .env.example jika .env tidak ada
            $exampleFile = $path . '.env.example';
            if (file_exists($exampleFile)) {
                copy($exampleFile, $envFile);
            }
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse line
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes
                if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                    $value = substr($value, 1, -1);
                } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
                    $value = substr($value, 1, -1);
                }

                // Set environment variable
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                    putenv("{$key}={$value}");
                }
            }
        }
    }

    /**
     * Get environment variable
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Set environment variable
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}
