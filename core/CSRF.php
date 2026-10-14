<?php
declare(strict_types=1);

namespace Core;

class CSRF {
    private const TOKEN_KEY = '_csrf_token';

    public static function generate(): string {
        Session::start();
        $token = Session::get(self::TOKEN_KEY);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::TOKEN_KEY, $token);
        }
        return $token;
    }

    public static function token(): string {
        return self::generate();
    }

    public static function field(): string {
        $token = self::generate();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function meta(): string {
        $token = self::generate();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): bool {
        Session::start();
        $stored = Session::get(self::TOKEN_KEY);
        if (!$stored || !$token) {
            return false;
        }
        return hash_equals($stored, $token);
    }
}
