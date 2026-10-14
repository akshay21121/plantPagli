<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Coupon extends Model {
    public function validateCode(string $code, float $cartSubtotal): array {
        $cleanCode = strtoupper(trim($code));
        $coupon = $this->db->fetch("SELECT * FROM coupons WHERE code = ? AND is_active = 1 LIMIT 1", [$cleanCode]);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid or expired promo code.'];
        }

        // Check date validity
        $now = date('Y-m-d H:i:s');
        if (!empty($coupon['starts_at']) && $coupon['starts_at'] > $now) {
            return ['valid' => false, 'message' => 'This promo code is not active yet.'];
        }
        if (!empty($coupon['expires_at']) && $coupon['expires_at'] < $now) {
            return ['valid' => false, 'message' => 'This promo code has expired.'];
        }

        // Check usage limit
        if ($coupon['usage_limit'] > 0 && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'This promo code has reached its maximum usage limit.'];
        }

        // Check minimum order value
        if ($cartSubtotal < (float)$coupon['min_order_amount']) {
            return [
                'valid' => false,
                'message' => 'This promo code requires a minimum order value of ' . formatPrice($coupon['min_order_amount']) . '.'
            ];
        }

        // Calculate discount
        $discount = 0.0;
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($cartSubtotal * (float)$coupon['discount_value']) / 100.0;
            if (!empty($coupon['max_discount_amount']) && $discount > (float)$coupon['max_discount_amount']) {
                $discount = (float)$coupon['max_discount_amount'];
            }
        } else {
            $discount = min($cartSubtotal, (float)$coupon['discount_value']);
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => round($discount, 2),
            'message' => 'Coupon code applied successfully!'
        ];
    }

    public function getAll(): array {
        return $this->db->fetchAll("SELECT * FROM coupons ORDER BY id DESC");
    }

    public function findById(int $id): ?array {
        return $this->db->fetch("SELECT * FROM coupons WHERE id = ? LIMIT 1", [$id]);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, expires_at, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->execute($sql, [
            strtoupper(trim($data['code'])),
            $data['description'] ?? '',
            $data['discount_type'] ?? 'percentage',
            (float) $data['discount_value'],
            (float) ($data['min_order_amount'] ?? 0),
            !empty($data['max_discount_amount']) ? (float) $data['max_discount_amount'] : null,
            (int) ($data['usage_limit'] ?? 1000),
            !empty($data['expires_at']) ? $data['expires_at'] : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE coupons SET code = ?, description = ?, discount_type = ?, discount_value = ?, min_order_amount = ?, max_discount_amount = ?, usage_limit = ?, expires_at = ?, is_active = ? WHERE id = ?";

        return $this->db->execute($sql, [
            strtoupper(trim($data['code'])),
            $data['description'] ?? '',
            $data['discount_type'] ?? 'percentage',
            (float) $data['discount_value'],
            (float) ($data['min_order_amount'] ?? 0),
            !empty($data['max_discount_amount']) ? (float) $data['max_discount_amount'] : null,
            (int) ($data['usage_limit'] ?? 1000),
            !empty($data['expires_at']) ? $data['expires_at'] : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id
        ]);
    }

    public function delete(int $id): bool {
        return $this->db->execute("DELETE FROM coupons WHERE id = ?", [$id]);
    }
}
