<?php
declare(strict_types=1);

namespace Core;

class Auth {
    private const USER_SESSION_KEY = '_auth_user_id';
    private static ?array $cachedUser = null;

    public static function check(): bool {
        return self::id() !== null;
    }

    public static function id(): ?int {
        Session::start();
        $id = Session::get(self::USER_SESSION_KEY);
        return $id ? (int) $id : null;
    }

    public static function user(): ?array {
        $id = self::id();
        if (!$id) {
            return null;
        }

        if (self::$cachedUser !== null && self::$cachedUser['id'] === $id) {
            return self::$cachedUser;
        }

        $db = Database::getInstance();
        $user = $db->fetch("SELECT id, name, email, phone, role, status, created_at FROM users WHERE id = ? AND status = 'active' LIMIT 1", [$id]);
        self::$cachedUser = $user;
        return $user;
    }

    public static function isAdmin(): bool {
        $user = self::user();
        return $user !== null && $user['role'] === 'admin';
    }

    public static function login(array $user): void {
        Session::start();
        Session::regenerate();
        Session::set(self::USER_SESSION_KEY, (int) $user['id']);
        self::$cachedUser = $user;
    }

    public static function logout(): void {
        Session::start();
        Session::remove(self::USER_SESSION_KEY);
        self::$cachedUser = null;
        Session::destroy();
    }

    public static function attempt(string $email, string $password): bool {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE email = ? LIMIT 1", [trim(strtolower($email))]);

        if (!$user) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            // Rehash if algorithm cost has updated
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $db->execute("UPDATE users SET password = ? WHERE id = ?", [$newHash, $user['id']]);
            }
            self::login($user);
            return true;
        }

        return false;
    }
}
