<?php
declare(strict_types=1);

/**
 * PlantPuglu CLI Administrator Creation Script
 *
 * Usage:
 *   php bin/create_admin.php
 *   or:
 *   php bin/create_admin.php "Full Name" "email@example.com" "YourPassword123"
 */

require_once dirname(__DIR__) . '/core/Helper.php';
require_once dirname(__DIR__) . '/core/Database.php';

use Core\Database;

echo "\n======================================================\n";
echo "   PlantPuglu E-Commerce: Administrator Setup CLI     \n";
echo "======================================================\n\n";

$name = $argv[1] ?? null;
$email = $argv[2] ?? null;
$password = $argv[3] ?? null;

if (!$name) {
    echo "Enter Administrator Full Name: ";
    $name = trim((string) fgets(STDIN));
}

if (!$email) {
    echo "Enter Administrator Email Address: ";
    $email = trim((string) fgets(STDIN));
}

if (!$password) {
    echo "Enter Administrator Secure Password (min 8 chars): ";
    $password = trim((string) fgets(STDIN));
}

if (empty($name) || empty($email) || empty($password)) {
    echo "\n[ERROR] Name, Email, and Password are all required!\n\n";
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "\n[ERROR] Invalid email address format: {$email}\n\n";
    exit(1);
}

if (strlen($password) < 8) {
    echo "\n[ERROR] Password must be at least 8 characters long for security.\n\n";
    exit(1);
}

try {
    $db = Database::getInstance();

    // Check if user already exists
    $existing = $db->fetch("SELECT id, role FROM users WHERE email = ? LIMIT 1", [strtolower($email)]);

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if ($existing) {
        $db->execute(
            "UPDATE users SET name = ?, password = ?, role = 'admin', status = 'active' WHERE id = ?",
            [$name, $hash, $existing['id']]
        );
        echo "\n[SUCCESS] Existing user '{$email}' was promoted to Administrator and password updated!\n\n";
    } else {
        $db->execute(
            "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')",
            [$name, strtolower($email), $hash]
        );
        echo "\n[SUCCESS] Administrator account created successfully for '{$email}'!\n";
        echo "You can now log in at: /login\n\n";
    }
} catch (Exception $e) {
    echo "\n[DATABASE ERROR] " . $e->getMessage() . "\n\n";
    exit(1);
}
