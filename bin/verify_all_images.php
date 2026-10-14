<?php
require_once __DIR__ . '/../core/Helper.php';

$viewsDir = dirname(__DIR__) . '/views';
$publicDir = dirname(__DIR__) . '/public';

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$patterns = [
    '/(?:src|href|image)\s*=\s*["\']([^"\']+\.(?:png|jpg|jpeg|svg|webp|ico))["\']/i',
    '/asset\s*\(\s*["\']([^"\']+)["\']\s*\)/i',
    '/uploadUrl\s*\(\s*["\']([^"\']+)["\']\s*\)/i'
];

$foundAssets = [];
$foundUploads = [];
$foundRaw = [];

foreach ($files as $file) {
    if ($file->isDir() || $file->getExtension() !== 'php') continue;
    $content = file_get_contents($file->getPathname());
    
    // asset()
    if (preg_match_all('/asset\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', $content, $m)) {
        foreach ($m[1] as $p) $foundAssets[$p][] = $file->getFilename();
    }
    // uploadUrl()
    if (preg_match_all('/uploadUrl\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', $content, $m)) {
        foreach ($m[1] as $p) $foundUploads[$p][] = $file->getFilename();
    }
}

echo "=== ASSETS CHECK ===\n";
foreach ($foundAssets as $p => $usages) {
    $cleanPath = ltrim($p, '/');
    $fullPath = $publicDir . '/assets/' . $cleanPath;
    $status = file_exists($fullPath) ? "OK" : "BROKEN (File not found: assets/$cleanPath)";
    echo "[$status] asset('$p') used in: " . implode(', ', array_unique($usages)) . "\n";
}

echo "\n=== STATIC UPLOADS CHECK ===\n";
foreach ($foundUploads as $p => $usages) {
    $cleanPath = ltrim($p, '/');
    $fullPath = $publicDir . '/uploads/' . $cleanPath;
    $status = file_exists($fullPath) ? "OK" : "BROKEN (File not found: uploads/$cleanPath)";
    echo "[$status] uploadUrl('$p') used in: " . implode(', ', array_unique($usages)) . "\n";
}
