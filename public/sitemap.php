<?php
declare(strict_types=1);

/**
 * PlantPuglu Dynamic XML Sitemap Generator
 */

require_once dirname(__DIR__) . '/core/Helper.php';
require_once dirname(__DIR__) . '/core/Database.php';

use Core\Database;

header("Content-Type: application/xml; charset=utf-8");

$baseUrl = rtrim(config('app.url', 'https://plantpagli.com'), '/');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= $baseUrl ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/products</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/contact</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/plant-care</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    <?php
    try {
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT slug, updated_at FROM categories WHERE is_active = 1");
        foreach ($categories as $cat) {
            $lastMod = date('Y-m-d', strtotime($cat['updated_at'] ?? 'now'));
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/products?category=" . htmlspecialchars($cat['slug']) . "</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>\n";
        }

        $products = $db->fetchAll("SELECT slug, updated_at FROM products WHERE is_active = 1");
        foreach ($products as $prod) {
            $lastMod = date('Y-m-d', strtotime($prod['updated_at'] ?? 'now'));
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/product/" . htmlspecialchars($prod['slug']) . "</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>\n";
        }
    } catch (\Throwable $e) {
        // Silently omit dynamic entries if DB is not connected
    }
    ?>
</urlset>
