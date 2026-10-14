<?php
declare(strict_types=1);

/**
 * PlantPuglu Web Setup & Database Diagnostics Wizard
 * Useful for 1-click verification of PDO, MySQL, and Schema imports.
 */

$configFile = dirname(__DIR__) . '/config/config.php';
$config = file_exists($configFile) ? require $configFile : [];
$dbConfig = $config['db'] ?? [];

$message = null;
$messageType = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_sql') {
    try {
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Create DB if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `{$dbConfig['database']}`;");

        // Import database.sql
        $sqlFile = dirname(__DIR__) . '/database.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
            $message = "Database schema and all 24 sample nursery products imported successfully!";
            $messageType = "success";
        } else {
            $message = "Error: database.sql not found at {$sqlFile}";
            $messageType = "error";
        }
    } catch (Exception $e) {
        $message = "Setup Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Test connection
$connected = false;
$connError = null;
try {
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
    $testPdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $connected = true;
} catch (Exception $e) {
    $connError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlantPuglu Installation & Diagnostics</title>
    <style>
        :root { --primary: #1b4332; --accent: #2d6a4f; --cream: #fbf7ee; --text: #1a202c; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: var(--cream); color: var(--text); padding: 40px 20px; line-height: 1.6; }
        .setup-card { max-width: 650px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
        h1 { color: var(--primary); margin-top: 0; font-size: 26px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .badge-success { background: #d1e7dd; color: #0f5132; }
        .badge-danger { background: #f8d7da; color: #842029; }
        .alert { padding: 14px 18px; border-radius: 8px; margin: 20px 0; font-size: 14px; }
        .alert-success { background: #d1e7dd; color: #0f5132; }
        .alert-error { background: #f8d7da; color: #842029; }
        .btn { display: inline-block; background: var(--primary); color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn:hover { background: var(--accent); }
        .btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); }
        code { background: #eee; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
        ul { padding-left: 20px; }
    </style>
</head>
<body>
    <div class="setup-card">
        <h1>🌱 PlantPuglu Environment & Setup Diagnostics</h1>
        <p>This utility validates your PHP 8.2+ environment, MySQL database connection, and configuration settings.</p>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <h3>1. PHP Requirements</h3>
        <ul>
            <li>PHP Version: <code><?= PHP_VERSION ?></code> <?= version_compare(PHP_VERSION, '8.2.0', '>=') ? '<span class="badge badge-success">OK</span>' : '<span class="badge badge-danger">PHP 8.2+ recommended</span>' ?></li>
            <li>PDO MySQL: <?= extension_loaded('pdo_mysql') ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Missing</span>' ?></li>
            <li>cURL Extension: <?= extension_loaded('curl') ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Missing</span>' ?></li>
            <li>OpenSSL (Razorpay HMAC): <?= extension_loaded('openssl') ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Missing</span>' ?></li>
        </ul>

        <h3>2. Database Status</h3>
        <p>Target Database: <code><?= htmlspecialchars($dbConfig['database'] ?? 'plantpagli') ?></code> at <code><?= htmlspecialchars($dbConfig['host'] ?? '127.0.0.1') ?>:<?= htmlspecialchars((string)($dbConfig['port'] ?? 3306)) ?></code></p>
        
        <?php if ($connected): ?>
            <p><span class="badge badge-success">Connected Successfully</span> Your database is active and accepting connections.</p>
        <?php else: ?>
            <p><span class="badge badge-danger">Connection Failed</span></p>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($connError ?? '') ?><br>
                Please verify credentials in <code>.env</code>.
            </div>
        <?php endif; ?>

        <h3>3. Setup Actions</h3>
        <form method="POST" style="margin-bottom: 20px;">
            <input type="hidden" name="action" value="import_sql">
            <button type="submit" class="btn">Import / Reset database.sql Schema & Seed Data</button>
        </form>

        <div style="display: flex; gap: 12px; margin-top: 25px;">
            <a href="/" class="btn btn-outline">Visit Storefront &rarr;</a>
            <a href="/login" class="btn btn-outline">Admin / Customer Login &rarr;</a>
        </div>
    </div>
</body>
</html>
