<?php
declare(strict_types=1);

namespace Core;

abstract class Controller {
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void {
        echo View::render($view, $data, $layout);
    }

    protected function json(array $data, int $statusCode = 200): void {
        jsonResponse($data, $statusCode);
    }

    protected function redirect(string $path, ?string $flashType = null, ?string $flashMessage = null): void {
        redirect($path, $flashType, $flashMessage);
    }

    protected function validateCSRF(): void {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!CSRF::validate($token)) {
            http_response_code(419);
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'CSRF token mismatch. Please reload the page.'], 419);
            }
            die("Security Token Expired (419). Please refresh the page and try again.");
        }
    }

    protected function validate(array $rules, ?array $data = null): Validator {
        $input = $data ?? $_POST;
        setOldInput($input);
        return Validator::make($input, $rules);
    }

    protected function requireAuth(): void {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to access this section.');
            redirect('/login');
        }
    }

    protected function requireAdmin(): void {
        if (!Auth::check() || !Auth::isAdmin()) {
            Session::flash('error', 'Administrator privileges are required.');
            redirect('/login');
        }
    }

    protected function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
    }
}
