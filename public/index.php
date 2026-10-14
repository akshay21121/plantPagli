<?php
declare(strict_types=1);

/**
 * PlantPagli Gardening E-commerce
 * Single Entry Point
 */

// Route static files for PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
        return false;
    }
}

require_once dirname(__DIR__) . '/core/App.php';

use Core\App;

App::run();

