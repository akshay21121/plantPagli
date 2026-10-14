<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Banner extends Model {
    public function getByType(string $type = 'hero'): array {
        return $this->db->fetchAll(
            "SELECT * FROM banners WHERE type = ? AND is_active = 1 ORDER BY sort_order ASC, id DESC",
            [$type]
        );
    }

    public function getAnnouncement(): ?array {
        return $this->db->fetch(
            "SELECT * FROM banners WHERE type = 'announcement' AND is_active = 1 ORDER BY sort_order ASC LIMIT 1"
        );
    }

    public function getAllForAdmin(): array {
        return $this->db->fetchAll("SELECT * FROM banners ORDER BY type ASC, sort_order ASC, id DESC");
    }

    public function findById(int $id): ?array {
        return $this->db->fetch("SELECT * FROM banners WHERE id = ? LIMIT 1", [$id]);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO banners (title, subtitle, badge_text, button_text, button_link, image_path, type, sort_order, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->execute($sql, [
            trim($data['title']),
            $data['subtitle'] ?? null,
            $data['badge_text'] ?? null,
            $data['button_text'] ?? 'Shop Now',
            $data['button_link'] ?? '/products',
            $data['image_path'] ?? null,
            $data['type'] ?? 'hero',
            (int) ($data['sort_order'] ?? 0),
            isset($data['is_active']) ? (int) $data['is_active'] : 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE banners SET title = ?, subtitle = ?, badge_text = ?, button_text = ?, button_link = ?, image_path = COALESCE(?, image_path), type = ?, sort_order = ?, is_active = ? WHERE id = ?";

        return $this->db->execute($sql, [
            trim($data['title']),
            $data['subtitle'] ?? null,
            $data['badge_text'] ?? null,
            $data['button_text'] ?? 'Shop Now',
            $data['button_link'] ?? '/products',
            $data['image_path'] ?? null,
            $data['type'] ?? 'hero',
            (int) ($data['sort_order'] ?? 0),
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id
        ]);
    }

    public function delete(int $id): bool {
        return $this->db->execute("DELETE FROM banners WHERE id = ?", [$id]);
    }
}
