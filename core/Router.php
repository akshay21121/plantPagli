<?php
declare(strict_types=1);

namespace Core;

use Exception;

class Router {
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middleware = []): self {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): self {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, array|callable $handler, array $middleware = []): self {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function dispatch(string $uri, string $method): void {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');
        // Handle subfolder execution (e.g. if installed in /plantpagli/)
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptName = str_replace('\\', '/', $scriptName);
        if ($scriptName !== '/' && $scriptName !== '' && str_starts_with($uri, $scriptName)) {
            $uri = '/' . trim(substr($uri, strlen($scriptName)), '/');
        }

        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') {
                continue;
            }

            // Convert /product/{slug} to regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#i';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, fn($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY);

                // Execute middleware guards
                $this->runMiddleware($route['middleware']);

                $this->executeHandler($route['handler'], $params);
                return;
            }
        }

        // No route matched -> 404
        http_response_code(404);
        try {
            echo View::render('errors/404', ['title' => 'Page Not Found'], 'layouts/main');
        } catch (\Throwable $e) {
            echo "<h1>404 Not Found</h1><p>The requested page was not found on PlantPuglu.</p>";
        }
    }

    private function runMiddleware(array $middlewares): void {
        foreach ($middlewares as $mw) {
            if ($mw === 'auth') {
                if (!Auth::check()) {
                    Session::flash('error', 'Please log in to continue.');
                    redirect('/login');
                }
            } elseif ($mw === 'admin') {
                if (!Auth::check() || !Auth::isAdmin()) {
                    Session::flash('error', 'Administrator access required.');
                    redirect('/login');
                }
            } elseif ($mw === 'guest') {
                if (Auth::check()) {
                    redirect('/');
                }
            }
        }
    }

    private function executeHandler(array|callable $handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;
            if (!class_exists($class)) {
                throw new Exception("Controller class not found: {$class}");
            }
            $controller = new $class();
            if (!method_exists($controller, $action)) {
                throw new Exception("Action method '{$action}' not found in {$class}");
            }
            call_user_func_array([$controller, $action], $params);
            return;
        }

        throw new Exception("Invalid route handler definition.");
    }
}
