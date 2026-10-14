<?php
declare(strict_types=1);

use Core\Session;

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed {
        static $configs = null;
        if ($configs === null) {
            $configs = require dirname(__DIR__) . '/config/config.php';
        }
        $parts = explode('.', $key);
        $value = $configs;
        foreach ($parts as $part) {
            if (is_array($value) && array_key_exists($part, $value)) {
                $value = $value[$part];
            } else {
                return $default;
            }
        }
        return $value;
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string {
        if ($value === null) return '';
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $path = ltrim($path, '/');
        
        // In CLI environment (cron / queue / tests), use configured base URL
        if (php_sapi_name() === 'cli' && empty($_SERVER['HTTP_HOST'])) {
            $baseUrl = rtrim((string)config('app.url', 'https://plantpagli.com'), '/');
            return $baseUrl . '/' . $path;
        }

        // Subdirectory detection (e.g. if installed in /plantpagli/ subfolder)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', $scriptDir);
        $baseDir = ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
        
        return ($baseDir === '' ? '/' : $baseDir . '/') . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('uploadUrl')) {
    function uploadUrl(string $path): string {
        if (empty($path)) {
            return asset('images/plant-placeholder.svg');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice(mixed $amount): string {
        $num = (float) $amount;
        // Indian Rupee formatting
        return '₹' . number_format($num, 2);
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text ?: 'n-a');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, ?string $flashType = null, ?string $flashMessage = null): void {
        if ($flashType && $flashMessage) {
            Session::flash($flashType, $flashMessage);
        }
        $target = str_starts_with($path, 'http') ? $path : url($path);
        header("Location: {$target}");
        exit;
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed {
        $old = Session::get('_old_input', []);
        return $old[$key] ?? $default;
    }
}

if (!function_exists('setOldInput')) {
    function setOldInput(array $data): void {
        // Exclude passwords
        unset($data['password'], $data['password_confirm'], $data['password_current'], $data['_csrf_token']);
        Session::set('_old_input', $data);
    }
}

if (!function_exists('clearOldInput')) {
    function clearOldInput(): void {
        Session::remove('_old_input');
    }
}
