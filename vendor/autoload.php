<?php
/**
 * Sweet Bakery - Autoloader
 * 
 * Simple autoloader untuk development
 * Dalam production, gunakan composer autoload
 * 
 * @package SweetBakery
 * @author Sweet Bakery Team
 */

// Load environment
require_once __DIR__ . '/../src/Utils/EnvLoader.php';
\SweetBakery\Utils\EnvLoader::load();

// Autoloader function
spl_autoload_register(function ($class) {
    // Project namespace prefix
    $prefix = 'SweetBakery\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/../src/';

    // Check if class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Move to next autoloader
        return;
    }

    // Get relative class name
    $relative_class = substr($class, $len);

    // Replace namespace prefix with base directory, replace namespace
    // separators with directory separators in the relative class name,
    // append with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Helper functions
require_once __DIR__ . '/../src/Utils/functions.php';
