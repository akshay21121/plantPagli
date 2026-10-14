<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Category extends Model {
    public function getAll(bool $onlyActive = true): array {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                " . ($onlyActive ? "WHERE c.is_active = 1 " : "") . "
                GROUP BY c.id
                ORDER BY c.sort_order ASC, c.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getFeatured(): array {
        return $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) as product_count 
             FROM categories c
             LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
             WHERE c.is_active = 1 AND c.is_featured = 1
             GROUP BY c.id
             ORDER BY c.sort_order ASC"
        );
    }

    public function findById(int $id): ?array {
        return $this->db->fetch("SELECT * FROM categories WHERE id = ? LIMIT 1", [$id]);
    }

    public function findBySlug(string $slug): ?array {
        return $this->db->fetch("SELECT * FROM categories WHERE slug = ? AND is_active = 1 LIMIT 1", [$slug]);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO categories (parent_id, name, slug, description, image, icon, sort_order, is_active, is_featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            !empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            trim($data['name']),
            slugify($data['slug'] ?? $data['name']),
            $data['description'] ?? '',
            $data['image'] ?? null,
            $data['icon'] ?? 'leaf',
            (int) ($data['sort_order'] ?? 0),
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            isset($data['is_featured']) ? (int) $data['is_featured'] : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE categories SET parent_id = ?, name = ?, slug = ?, description = ?, image = ?, icon = ?, sort_order = ?, is_active = ?, is_featured = ? WHERE id = ?";
        return $this->db->execute($sql, [
            !empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            trim($data['name']),
            slugify($data['slug'] ?? $data['name']),
            $data['description'] ?? '',
            $data['image'] ?? null,
            $data['icon'] ?? 'leaf',
            (int) ($data['sort_order'] ?? 0),
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            isset($data['is_featured']) ? (int) $data['is_featured'] : 0,
            $id
        ]);
    }

    public function delete(int $id): bool {
        // Check if products exist in category
        $count = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE category_id = ?", [$id]);
        if ($count > 0) {
            return false;
        }
        return $this->db->execute("DELETE FROM categories WHERE id = ?", [$id]);
    }
}
