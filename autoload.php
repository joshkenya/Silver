<?php
/**
 * Composer Autoloader (if using Composer)
 * This is optional but recommended for larger projects
 */

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

// Otherwise use manual class loading
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/src/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
