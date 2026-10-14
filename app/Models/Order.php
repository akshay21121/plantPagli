<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;
use Exception;

class Order extends Model {
    public function createFromCart(Cart $cart, array $customerData, string $paymentMethod): array {
        $summary = $cart->getSummary();
        if (empty($summary['items'])) {
            throw new Exception("Your shopping cart is empty.");
        }

        $this->db->beginTransaction();

        try {
            // Generate clean order number
            $year = date('Y');
            $randomDigits = str_pad((string) mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $orderNumber = "PP-{$year}-{$randomDigits}";

            // Ensure uniqueness
            while ($this->db->fetchColumn("SELECT COUNT(*) FROM orders WHERE order_number = ?", [$orderNumber]) > 0) {
                $orderNumber = "PP-{$year}-" . mt_rand(1000, 9999);
            }

            $sql = "INSERT INTO orders (
                order_number, user_id, customer_name, customer_email, customer_phone,
                shipping_address, shipping_landmark, shipping_city, shipping_state, shipping_pincode,
                subtotal, discount_amount, shipping_fee, total_amount, payment_method,
                payment_status, order_status, notes, coupon_code
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $this->db->execute($sql, [
                $orderNumber,
                $customerData['user_id'] ?? null,
                $customerData['customer_name'],
                $customerData['customer_email'],
                $customerData['customer_phone'],
                $customerData['shipping_address'],
                $customerData['shipping_landmark'] ?? '',
                $customerData['shipping_city'],
                $customerData['shipping_state'],
                $customerData['shipping_pincode'],
                $summary['subtotal'],
                $summary['discount_amount'],
                $summary['shipping_fee'],
                $summary['grand_total'],
                $paymentMethod,
                $paymentMethod === 'cod' ? 'pending' : 'pending',
                'pending',
                $customerData['notes'] ?? '',
                $summary['coupon_code']
            ]);

            $orderId = (int) $this->db->lastInsertId();

            // Insert order items & reduce stock
            $productModel = new Product();
            $itemSql = "INSERT INTO order_items (order_id, product_id, product_name, product_sku, variant_name, price, quantity, subtotal)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            foreach ($summary['items'] as $item) {
                $itemSubtotal = (float)$item['price'] * (int)$item['quantity'];

                $this->db->execute($itemSql, [
                    $orderId,
                    $item['product_id'],
                    $item['product_name'],
                    $item['sku'] ?? null,
                    $item['variant_name'] ?? null,
                    $item['price'],
                    $item['quantity'],
                    $itemSubtotal
                ]);

                // Decrement inventory safely
                $productModel->decrementStock((int)$item['product_id'], (int)$item['quantity'], !empty($item['variant_id']) ? (int)$item['variant_id'] : null);
            }

            // Record Coupon Redemption
            if (!empty($summary['coupon_code'])) {
                $coupon = $this->db->fetch("SELECT id FROM coupons WHERE code = ? LIMIT 1", [$summary['coupon_code']]);
                if ($coupon) {
                    $this->db->execute(
                        "INSERT INTO coupon_redemptions (coupon_id, order_id, user_id, discount_amount) VALUES (?, ?, ?, ?)",
                        [$coupon['id'], $orderId, $customerData['user_id'] ?? null, $summary['discount_amount']]
                    );
                    $this->db->execute("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?", [$coupon['id']]);
                }
            }

            // Create initial Payment entry
            $this->db->execute(
                "INSERT INTO payments (order_id, payment_method, amount, currency, status) VALUES (?, ?, ?, 'INR', 'initiated')",
                [$orderId, $paymentMethod, $summary['grand_total']]
            );

            $this->db->commit();

            return [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total_amount' => $summary['grand_total'],
                'subtotal' => $summary['subtotal'],
                'shipping_fee' => $summary['shipping_fee'],
                'discount_amount' => $summary['discount_amount'],
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findById(int $id): ?array {
        $order = $this->db->fetch("SELECT * FROM orders WHERE id = ? LIMIT 1", [$id]);
        if ($order) {
            $order['items'] = $this->getOrderItems((int)$order['id']);
            $order['payments'] = $this->getPayments((int)$order['id']);
        }
        return $order;
    }

    public function findByOrderNumber(string $orderNumber): ?array {
        $order = $this->db->fetch("SELECT * FROM orders WHERE order_number = ? LIMIT 1", [trim($orderNumber)]);
        if ($order) {
            $order['items'] = $this->getOrderItems((int)$order['id']);
            $order['payments'] = $this->getPayments((int)$order['id']);
        }
        return $order;
    }

    public function getOrderItems(int $orderId): array {
        return $this->db->fetchAll(
            "SELECT oi.*, p.slug as product_slug,
                    (SELECT image_path FROM product_images WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) as product_image
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?
             ORDER BY oi.id ASC",
            [$orderId]
        );
    }

    public function getPayments(int $orderId): array {
        return $this->db->fetchAll("SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC", [$orderId]);
    }

    public function getCustomerOrders(int $userId): array {
        $orders = $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC",
            [$userId]
        );

        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems((int)$order['id']);
        }

        return $orders;
    }

    public function updateOrderStatus(int $id, string $status, ?string $trackingNumber = null, ?string $courierName = null): bool {
        $sql = "UPDATE orders SET order_status = ?, tracking_number = COALESCE(?, tracking_number), courier_name = COALESCE(?, courier_name) WHERE id = ?";
        return $this->db->execute($sql, [$status, $trackingNumber, $courierName, $id]);
    }

    public function updatePaymentStatus(int $id, string $status): bool {
        return $this->db->execute("UPDATE orders SET payment_status = ? WHERE id = ?", [$status, $id]);
    }

    public function markAsPaid(int $orderId, array $paymentDetails): bool {
        $this->db->beginTransaction();
        try {
            $this->db->execute(
                "UPDATE orders SET payment_status = 'paid', order_status = IF(order_status = 'pending', 'confirmed', order_status) WHERE id = ?",
                [$orderId]
            );

            $this->db->execute(
                "UPDATE payments SET 
                    status = 'captured',
                    razorpay_order_id = ?,
                    razorpay_payment_id = ?,
                    razorpay_signature = ?,
                    transaction_id = ?,
                    payload = ?
                 WHERE order_id = ?",
                [
                    $paymentDetails['razorpay_order_id'] ?? null,
                    $paymentDetails['razorpay_payment_id'] ?? null,
                    $paymentDetails['razorpay_signature'] ?? null,
                    $paymentDetails['razorpay_payment_id'] ?? null,
                    json_encode($paymentDetails),
                    $orderId
                ]
            );

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAllForAdmin(array $filters = [], int $limit = 20, int $offset = 0): array {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "o.order_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $where[] = "o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ? OR o.customer_phone LIKE ?)";
            $term = '%' . trim($filters['search']) . '%';
            $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term;
        }

        $sql = "SELECT o.*, COUNT(oi.id) as total_items_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY o.id
                ORDER BY o.id DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }

    public function countForAdmin(array $filters = []): int {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "order_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $where[] = "payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(order_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?)";
            $term = '%' . trim($filters['search']) . '%';
            $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term;
        }

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM orders WHERE " . implode(' AND ', $where),
            $params
        );
    }

    public function getDashboardStats(): array {
        $totalSales = (float) $this->db->fetchColumn("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'");
        $totalOrders = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM orders");
        $pendingOrders = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'");
        $totalCustomers = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'customer'");
        $totalProducts = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE is_active = 1");
        $lowStockCount = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE stock_quantity <= 5 AND is_active = 1");

        $recentOrders = $this->db->fetchAll("SELECT * FROM orders ORDER BY id DESC LIMIT 5");

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'recent_orders' => $recentOrders,
        ];
    }
}
