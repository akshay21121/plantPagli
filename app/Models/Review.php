<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Review extends Model {
    public function getByProduct(int $productId, bool $approvedOnly = true): array {
        $sql = "SELECT pr.*, u.name as user_name 
                FROM product_reviews pr
                LEFT JOIN users u ON pr.user_id = u.id
                WHERE pr.product_id = ? " . ($approvedOnly ? "AND pr.status = 'approved'" : "") . "
                ORDER BY pr.id DESC";
        return $this->db->fetchAll($sql, [$productId]);
    }

    public function add(array $data): int {
        $sql = "INSERT INTO product_reviews (product_id, user_id, customer_name, customer_email, rating, title, comment, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'approved')";

        $this->db->execute($sql, [
            (int) $data['product_id'],
            $data['user_id'] ?? null,
            trim($data['customer_name']),
            trim(strtolower($data['customer_email'])),
            (int) min(5, max(1, $data['rating'])),
            $data['title'] ?? '',
            trim($data['comment'])
        ]);

        $id = (int) $this->db->lastInsertId();

        // Recalculate product rating
        $productModel = new Product();
        $productModel->updateRatingStats((int) $data['product_id']);

        return $id;
    }

    public function getAllForAdmin(int $limit = 30, int $offset = 0): array {
        $sql = "SELECT pr.*, p.name as product_name, p.slug as product_slug
                FROM product_reviews pr
                JOIN products p ON pr.product_id = p.id
                ORDER BY pr.id DESC
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }

    public function updateStatus(int $id, string $status): bool {
        $review = $this->db->fetch("SELECT product_id FROM product_reviews WHERE id = ?", [$id]);
        $success = $this->db->execute("UPDATE product_reviews SET status = ? WHERE id = ?", [$status, $id]);
        if ($success && $review) {
            $productModel = new Product();
            $productModel->updateRatingStats((int) $review['product_id']);
        }
        return $success;
    }

    public function delete(int $id): bool {
        $review = $this->db->fetch("SELECT product_id FROM product_reviews WHERE id = ?", [$id]);
        $success = $this->db->execute("DELETE FROM product_reviews WHERE id = ?", [$id]);
        if ($success && $review) {
            $productModel = new Product();
            $productModel->updateRatingStats((int) $review['product_id']);
        }
        return $success;
    }
}
