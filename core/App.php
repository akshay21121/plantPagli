<?php
declare(strict_types=1);

namespace Core;

class App {
    private Router $router;

    public function __construct() {
        $this->registerAutoloader();
        require_once dirname(__DIR__) . '/core/Helper.php';
        Session::start();
        $this->router = new Router();
    }

    public static function run(): void {
        $app = new self();
        $app->init();
    }

    private function init(): void {
        // Register routes
        $router = $this->router;
        require dirname(__DIR__) . '/config/routes.php';

        // Dispatch request
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $this->router->dispatch($uri, $method);
        } catch (\Throwable $e) {
            $config = require dirname(__DIR__) . '/config/config.php';
            if ($config['app']['debug']) {
                http_response_code(500);
                echo "<h1>PlantPuglu System Error</h1>";
                echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                http_response_code(500);
                try {
                    echo View::render('errors/500', ['title' => 'Server Error'], 'layouts/main');
                } catch (\Throwable $t) {
                    echo "<h1>500 Internal Server Error</h1><p>Something went wrong on our end. Please try again shortly.</p>";
                }
            }
        }
    }

    private function registerAutoloader(): void {
        spl_autoload_register(function ($class) {
            $baseDir = dirname(__DIR__) . '/';

            // Mapping prefixes
            $prefixes = [
                'Core\\' => 'core/',
                'App\\' => 'app/',
            ];

            foreach ($prefixes as $prefix => $dir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    continue;
                }

                $relativeClass = substr($class, $len);
                $file = $baseDir . $dir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }
}
