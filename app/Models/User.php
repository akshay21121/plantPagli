<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class User extends Model {
    public function findById(int $id): ?array {
        return $this->db->fetch("SELECT * FROM users WHERE id = ? LIMIT 1", [$id]);
    }

    public function findByEmail(string $email): ?array {
        return $this->db->fetch("SELECT * FROM users WHERE email = ? LIMIT 1", [trim(strtolower($email))]);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'customer';
        $status = $data['status'] ?? 'active';

        $this->db->execute($sql, [
            trim($data['name']),
            trim(strtolower($data['email'])),
            trim($data['phone'] ?? ''),
            $hashedPassword,
            $role,
            $status
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = "`name` = ?";
            $params[] = trim($data['name']);
        }
        if (isset($data['phone'])) {
            $fields[] = "`phone` = ?";
            $params[] = trim($data['phone']);
        }
        if (isset($data['email'])) {
            $fields[] = "`email` = ?";
            $params[] = trim(strtolower($data['email']));
        }
        if (!empty($data['password'])) {
            $fields[] = "`password` = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->execute($sql, $params);
    }

    public function getAddresses(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM customer_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC",
            [$userId]
        );
    }

    public function getAddress(int $id, int $userId): ?array {
        return $this->db->fetch(
            "SELECT * FROM customer_addresses WHERE id = ? AND user_id = ? LIMIT 1",
            [$id, $userId]
        );
    }

    public function addAddress(int $userId, array $data): int {
        // If first address or marked default, reset other addresses
        $existingCount = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM customer_addresses WHERE user_id = ?", [$userId]);
        $isDefault = ($existingCount === 0 || !empty($data['is_default'])) ? 1 : 0;

        if ($isDefault === 1) {
            $this->db->execute("UPDATE customer_addresses SET is_default = 0 WHERE user_id = ?", [$userId]);
        }

        $sql = "INSERT INTO customer_addresses (user_id, label, full_name, phone, address_line1, address_line2, landmark, city, state, pincode, is_default)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->execute($sql, [
            $userId,
            $data['label'] ?? 'Home',
            $data['full_name'],
            $data['phone'],
            $data['address_line1'],
            $data['address_line2'] ?? '',
            $data['landmark'] ?? '',
            $data['city'],
            $data['state'],
            $data['pincode'],
            $isDefault
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateAddress(int $id, int $userId, array $data): bool {
        if (!empty($data['is_default'])) {
            $this->db->execute("UPDATE customer_addresses SET is_default = 0 WHERE user_id = ?", [$userId]);
        }

        $sql = "UPDATE customer_addresses SET label = ?, full_name = ?, phone = ?, address_line1 = ?, address_line2 = ?, landmark = ?, city = ?, state = ?, pincode = ?, is_default = ? WHERE id = ? AND user_id = ?";
        return $this->db->execute($sql, [
            $data['label'] ?? 'Home',
            $data['full_name'],
            $data['phone'],
            $data['address_line1'],
            $data['address_line2'] ?? '',
            $data['landmark'] ?? '',
            $data['city'],
            $data['state'],
            $data['pincode'],
            !empty($data['is_default']) ? 1 : 0,
            $id,
            $userId
        ]);
    }

    public function deleteAddress(int $id, int $userId): bool {
        return $this->db->execute("DELETE FROM customer_addresses WHERE id = ? AND user_id = ?", [$id, $userId]);
    }

    public function getAllCustomers(int $limit = 50, int $offset = 0): array {
        return $this->db->fetchAll(
            "SELECT u.id, u.name, u.email, u.phone, u.status, u.created_at,
                    COUNT(o.id) as total_orders,
                    COALESCE(SUM(o.total_amount), 0) as total_spent
             FROM users u
             LEFT JOIN orders o ON u.id = o.user_id AND o.payment_status = 'paid'
             WHERE u.role = 'customer'
             GROUP BY u.id
             ORDER BY u.id DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function countCustomers(): int {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'customer'");
    }
}
