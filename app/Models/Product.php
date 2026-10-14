<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Product extends Model {
    public function getFiltered(array $filters = [], int $limit = 12, int $offset = 0): array {
        $conditions = ["p.is_active = 1"];
        $params = [];

        if (!empty($filters['category'])) {
            $conditions[] = "(c.slug = ? OR c.id = ?)";
            $params[] = $filters['category'];
            $params[] = is_numeric($filters['category']) ? (int)$filters['category'] : 0;
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(p.name LIKE ? OR p.short_description LIKE ? OR p.sku LIKE ?)";
            $searchTerm = '%' . trim($filters['search']) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $conditions[] = "p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $conditions[] = "p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['featured'])) {
            $conditions[] = "p.is_featured = 1";
        }

        if (!empty($filters['bestseller'])) {
            $conditions[] = "p.is_bestseller = 1";
        }

        if (!empty($filters['in_stock'])) {
            $conditions[] = "p.is_in_stock = 1 AND p.stock_quantity > 0";
        }

        $orderBy = "p.id DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = "p.price ASC";
                    break;
                case 'price_desc':
                    $orderBy = "p.price DESC";
                    break;
                case 'rating':
                    $orderBy = "p.average_rating DESC, p.reviews_count DESC";
                    break;
                case 'popular':
                    $orderBy = "p.is_bestseller DESC, p.reviews_count DESC";
                    break;
                case 'newest':
                default:
                    $orderBy = "p.id DESC";
                    break;
            }
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT {$limit} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }

    public function countFiltered(array $filters = []): int {
        $conditions = ["p.is_active = 1"];
        $params = [];

        if (!empty($filters['category'])) {
            $conditions[] = "(c.slug = ? OR c.id = ?)";
            $params[] = $filters['category'];
            $params[] = is_numeric($filters['category']) ? (int)$filters['category'] : 0;
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(p.name LIKE ? OR p.short_description LIKE ? OR p.sku LIKE ?)";
            $searchTerm = '%' . trim($filters['search']) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $conditions[] = "p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $conditions[] = "p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['featured'])) {
            $conditions[] = "p.is_featured = 1";
        }

        if (!empty($filters['bestseller'])) {
            $conditions[] = "p.is_bestseller = 1";
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE {$whereClause}";
        return (int) $this->db->fetchColumn($sql, $params);
    }

    public function getFeatured(int $limit = 8): array {
        return $this->getFiltered(['featured' => 1], $limit, 0);
    }

    public function getBestsellers(int $limit = 8): array {
        return $this->getFiltered(['bestseller' => 1], $limit, 0);
    }

    public function getByCategory(string|int $category, int $limit = 8): array {
        return $this->getFiltered(['category' => $category], $limit, 0);
    }

    public function findBySlug(string $slug): ?array {
        $product = $this->db->fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.slug = ? AND p.is_active = 1 LIMIT 1",
            [$slug]
        );

        if (!$product) {
            return null;
        }

        $product['images'] = $this->getImages((int)$product['id']);
        $product['variants'] = $this->getVariants((int)$product['id']);
        $product['primary_image'] = $product['images'][0]['image_path'] ?? null;

        return $product;
    }

    public function findById(int $id): ?array {
        $product = $this->db->fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.id = ? LIMIT 1",
            [$id]
        );

        if ($product) {
            $product['images'] = $this->getImages((int)$product['id']);
            $product['variants'] = $this->getVariants((int)$product['id']);
            $product['primary_image'] = $product['images'][0]['image_path'] ?? null;
        }

        return $product;
    }

    public function getImages(int $productId): array {
        return $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC, id ASC",
            [$productId]
        );
    }

    public function getVariants(int $productId): array {
        return $this->db->fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? ORDER BY price ASC",
            [$productId]
        );
    }

    public function getRelated(int $categoryId, int $currentProductId, int $limit = 4): array {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
             LIMIT {$limit}",
            [$categoryId, $currentProductId]
        );
    }

    public function decrementStock(int $productId, int $quantity, ?int $variantId = null): bool {
        if ($variantId) {
            $this->db->execute(
                "UPDATE product_variants SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ? AND product_id = ?",
                [$quantity, $variantId, $productId]
            );
        }

        return $this->db->execute(
            "UPDATE products 
             SET stock_quantity = GREATEST(0, stock_quantity - ?),
                 is_in_stock = IF(stock_quantity - ? <= 0, 0, 1)
             WHERE id = ?",
            [$quantity, $quantity, $productId]
        );
    }

    public function updateRatingStats(int $productId): void {
        $stats = $this->db->fetch(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as count 
             FROM product_reviews 
             WHERE product_id = ? AND status = 'approved'",
            [$productId]
        );

        $avg = round((float)($stats['avg_rating'] ?? 5.0), 2);
        $count = (int)($stats['count'] ?? 0);

        $this->db->execute(
            "UPDATE products SET average_rating = ?, reviews_count = ? WHERE id = ?",
            [$avg, $count, $productId]
        );
    }

    public function getAllForAdmin(string $search = '', ?int $categoryId = null, int $limit = 20, int $offset = 0): array {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        if ($categoryId) {
            $where[] = "p.category_id = ?";
            $params[] = $categoryId;
        }

        $sql = "SELECT p.*, c.name as category_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.id DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }

    public function countForAdmin(string $search = '', ?int $categoryId = null): int {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        if ($categoryId) {
            $where[] = "p.category_id = ?";
            $params[] = $categoryId;
        }

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products p WHERE " . implode(' AND ', $where),
            $params
        );
    }

    public function create(array $data): int {
        $sql = "INSERT INTO products (
            category_id, name, slug, sku, short_description, description, price, compare_price, cost_price,
            stock_quantity, is_in_stock, plant_height, pot_size, sunlight, watering, maintenance, pet_friendly,
            care_instructions, shipping_info, is_featured, is_bestseller, is_active, meta_title, meta_description
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $slug = slugify($data['slug'] ?? $data['name']);
        // Ensure slug uniqueness
        $existing = $this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE slug = ?", [$slug]);
        if ($existing > 0) {
            $slug .= '-' . rand(100, 999);
        }

        $this->db->execute($sql, [
            (int) $data['category_id'],
            trim($data['name']),
            $slug,
            trim($data['sku']),
            $data['short_description'] ?? '',
            $data['description'] ?? '',
            (float) $data['price'],
            !empty($data['compare_price']) ? (float) $data['compare_price'] : null,
            !empty($data['cost_price']) ? (float) $data['cost_price'] : null,
            (int) ($data['stock_quantity'] ?? 10),
            (int) ($data['stock_quantity'] ?? 10) > 0 ? 1 : 0,
            $data['plant_height'] ?? '',
            $data['pot_size'] ?? '',
            $data['sunlight'] ?? '',
            $data['watering'] ?? '',
            $data['maintenance'] ?? 'Low',
            isset($data['pet_friendly']) ? (int) $data['pet_friendly'] : 0,
            $data['care_instructions'] ?? '',
            $data['shipping_info'] ?? '',
            isset($data['is_featured']) ? (int) $data['is_featured'] : 0,
            isset($data['is_bestseller']) ? (int) $data['is_bestseller'] : 0,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $data['meta_title'] ?? $data['name'],
            $data['meta_description'] ?? substr($data['short_description'] ?? '', 0, 160)
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE products SET
            category_id = ?, name = ?, sku = ?, short_description = ?, description = ?, price = ?, compare_price = ?,
            stock_quantity = ?, is_in_stock = ?, plant_height = ?, pot_size = ?, sunlight = ?, watering = ?,
            maintenance = ?, pet_friendly = ?, care_instructions = ?, shipping_info = ?, is_featured = ?,
            is_bestseller = ?, is_active = ?, meta_title = ?, meta_description = ?
            WHERE id = ?";

        return $this->db->execute($sql, [
            (int) $data['category_id'],
            trim($data['name']),
            trim($data['sku']),
            $data['short_description'] ?? '',
            $data['description'] ?? '',
            (float) $data['price'],
            !empty($data['compare_price']) ? (float) $data['compare_price'] : null,
            (int) ($data['stock_quantity'] ?? 0),
            (int) ($data['stock_quantity'] ?? 0) > 0 ? 1 : 0,
            $data['plant_height'] ?? '',
            $data['pot_size'] ?? '',
            $data['sunlight'] ?? '',
            $data['watering'] ?? '',
            $data['maintenance'] ?? 'Low',
            isset($data['pet_friendly']) ? (int) $data['pet_friendly'] : 0,
            $data['care_instructions'] ?? '',
            $data['shipping_info'] ?? '',
            isset($data['is_featured']) ? (int) $data['is_featured'] : 0,
            isset($data['is_bestseller']) ? (int) $data['is_bestseller'] : 0,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $data['meta_title'] ?? '',
            $data['meta_description'] ?? '',
            $id
        ]);
    }

    public function delete(int $id): bool {
        return $this->db->execute("DELETE FROM products WHERE id = ?", [$id]);
    }

    public function addImage(int $productId, string $imagePath, bool $isPrimary = false): int {
        $count = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM product_images WHERE product_id = ?", [$productId]);
        if ($count === 0) {
            $isPrimary = true;
        } elseif ($isPrimary) {
            $this->db->execute("UPDATE product_images SET is_primary = 0 WHERE product_id = ?", [$productId]);
        }
        $sql = "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)";
        $this->db->execute($sql, [$productId, $imagePath, $isPrimary ? 1 : 0]);
        return (int) $this->db->lastInsertId();
    }

    public function deleteImage(int $imageId, int $productId): bool {
        $img = $this->db->fetch("SELECT * FROM product_images WHERE id = ? AND product_id = ?", [$imageId, $productId]);
        if (!$img) {
            return false;
        }

        $deleted = $this->db->execute("DELETE FROM product_images WHERE id = ? AND product_id = ?", [$imageId, $productId]);

        // Attempt physical file cleanup if file exists
        if (!empty($img['image_path'])) {
            $filePath = dirname(__DIR__, 2) . '/public/uploads/products/' . $img['image_path'];
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }

        // If deleted image was primary, assign primary to another existing image for this product
        if (!empty($img['is_primary'])) {
            $nextImg = $this->db->fetch("SELECT id FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1", [$productId]);
            if ($nextImg) {
                $this->db->execute("UPDATE product_images SET is_primary = 1 WHERE id = ?", [(int)$nextImg['id']]);
            }
        }

        return $deleted;
    }

    public function setPrimaryImage(int $imageId, int $productId): bool {
        $this->db->execute("UPDATE product_images SET is_primary = 0 WHERE product_id = ?", [$productId]);
        return $this->db->execute("UPDATE product_images SET is_primary = 1 WHERE id = ? AND product_id = ?", [$imageId, $productId]);
    }

    public function addVariant(int $productId, string $name, float $price, int $stock = 10, ?string $sku = null): int {
        $sql = "INSERT INTO product_variants (product_id, name, sku, price, stock_quantity) VALUES (?, ?, ?, ?, ?)";
        $this->db->execute($sql, [$productId, $name, $sku, $price, $stock]);
        return (int) $this->db->lastInsertId();
    }
}
