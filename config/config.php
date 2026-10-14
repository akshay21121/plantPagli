<?php
declare(strict_types=1);

/**
 * PlantPuglu Application Configuration Loader
 * Handles environment parsing, base URL determination, and security constants.
 */

// Function to safely fetch environment variables
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            if ($lower === 'true') return true;
            if ($lower === 'false') return false;
            if ($lower === 'null') return null;
            if ($lower === 'empty') return '';
        }

        return $value;
    }
}

// Simple .env parser to avoid strict dependency on vlucas/phpdotenv
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove wrapping quotes
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

// Auto-determine Base URL if not set or dynamic
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = str_replace('\\', '/', $scriptDir);
$detectedUrl = rtrim($protocol . $host . ($scriptDir === '/' ? '' : $scriptDir), '/');

return [
    'app' => [
        'name' => env('APP_NAME', 'Plantpagli'),
        'env' => env('APP_ENV', 'production'),
        'debug' => (bool) env('APP_DEBUG', false),
        'url' => rtrim(env('APP_URL', $detectedUrl), '/'),
        'secret' => env('APP_SECRET', 'PlantpagliDefaultSecretPleaseChangeInProduction'),
    ],
    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => (int) env('DB_PORT', 3306),
        'database' => env('DB_NAME', 'plantpagli'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASS', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
    ],
    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID', 'rzp_test_YourTestKeyId123'),
        'key_secret' => env('RAZORPAY_KEY_SECRET', 'YourTestKeySecret456789'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
    ],
    'session' => [
        'lifetime' => (int) env('SESSION_LIFETIME', 86400),
        'secure' => (bool) env('SESSION_SECURE_COOKIE', false),
        'cookie_name' => 'plantpuglu_sess',
    ],
    'paths' => [
        'root' => dirname(__DIR__),
        'public' => dirname(__DIR__) . '/public',
        'uploads' => dirname(__DIR__) . '/public/uploads',
        'views' => dirname(__DIR__) . '/views',
    ]
];
