<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Wishlist extends Model {
    public function getByUser(int $userId): array {
        $sql = "SELECT w.*, p.name, p.slug, p.price, p.compare_price, p.is_in_stock, p.average_rating,
                       c.name as category_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM wishlists w
                JOIN products p ON w.product_id = p.id
                JOIN categories c ON p.category_id = c.id
                WHERE w.user_id = ?
                ORDER BY w.id DESC";

        return $this->db->fetchAll($sql, [$userId]);
    }

    public function has(int $userId, int $productId): bool {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        ) > 0;
    }

    public function toggle(int $userId, int $productId): array {
        if ($this->has($userId, $productId)) {
            $this->db->execute("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
            return ['added' => false, 'message' => 'Removed from your wishlist.'];
        } else {
            $this->db->execute("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)", [$userId, $productId]);
            return ['added' => true, 'message' => 'Added to your wishlist!'];
        }
    }

    public function remove(int $userId, int $productId): bool {
        return $this->db->execute("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
    }
}
