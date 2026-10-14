<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;
use Core\Session;
use Core\Auth;

class Cart extends Model {
    private int $cartId;

    public function __construct() {
        parent::__construct();
        $this->cartId = $this->resolveCartId();
    }

    public function getCartId(): int {
        return $this->cartId;
    }

    private function resolveCartId(): int {
        Session::start();
        $sessionId = session_id();
        $userId = Auth::id();

        // Check if cart already exists for this session or user
        if ($userId) {
            $cart = $this->db->fetch("SELECT id FROM carts WHERE user_id = ? LIMIT 1", [$userId]);
            if ($cart) {
                return (int) $cart['id'];
            }
        }

        $cart = $this->db->fetch("SELECT id FROM carts WHERE session_id = ? LIMIT 1", [$sessionId]);
        if ($cart) {
            // If user is logged in now, associate the session cart with user
            if ($userId) {
                $this->db->execute("UPDATE carts SET user_id = ? WHERE id = ?", [$userId, $cart['id']]);
            }
            return (int) $cart['id'];
        }

        // Create new cart
        $this->db->execute("INSERT INTO carts (user_id, session_id) VALUES (?, ?)", [$userId, $sessionId]);
        return (int) $this->db->lastInsertId();
    }

    public function getItems(): array {
        $sql = "SELECT ci.*, p.name as product_name, p.slug as product_slug, p.stock_quantity as product_stock,
                       pv.name as variant_name, pv.stock_quantity as variant_stock,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as product_image
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                LEFT JOIN product_variants pv ON ci.variant_id = pv.id
                WHERE ci.cart_id = ?
                ORDER BY ci.id DESC";

        return $this->db->fetchAll($sql, [$this->cartId]);
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): array {
        if ($quantity < 1) $quantity = 1;

        // Check product exists and has stock
        $product = $this->db->fetch("SELECT id, name, price, stock_quantity, is_active FROM products WHERE id = ? LIMIT 1", [$productId]);
        if (!$product || !$product['is_active']) {
            return ['success' => false, 'message' => 'Product is not available.'];
        }

        $price = (float) $product['price'];
        $availableStock = (int) $product['stock_quantity'];

        if ($variantId) {
            $variant = $this->db->fetch("SELECT id, price, stock_quantity FROM product_variants WHERE id = ? AND product_id = ? LIMIT 1", [$variantId, $productId]);
            if ($variant) {
                $price = (float) $variant['price'];
                $availableStock = (int) $variant['stock_quantity'];
            } else {
                $variantId = null;
            }
        }

        // Check existing cart item
        $existing = $this->db->fetch(
            "SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL)) LIMIT 1",
            [$this->cartId, $productId, $variantId, $variantId]
        );

        $newQty = $quantity;
        if ($existing) {
            $newQty += (int) $existing['quantity'];
        }

        if ($newQty > $availableStock) {
            return [
                'success' => false,
                'message' => "Only {$availableStock} units available in stock."
            ];
        }

        if ($existing) {
            $this->db->execute("UPDATE cart_items SET quantity = ?, price = ? WHERE id = ?", [$newQty, $price, $existing['id']]);
        } else {
            $this->db->execute(
                "INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, price) VALUES (?, ?, ?, ?, ?)",
                [$this->cartId, $productId, $variantId, $newQty, $price]
            );
        }

        return ['success' => true, 'message' => 'Added to cart successfully!'];
    }

    public function updateQuantity(int $cartItemId, int $quantity): array {
        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        $item = $this->db->fetch("SELECT * FROM cart_items WHERE id = ? AND cart_id = ? LIMIT 1", [$cartItemId, $this->cartId]);
        if (!$item) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }

        // Check stock
        $maxStock = (int) $this->db->fetchColumn("SELECT stock_quantity FROM products WHERE id = ?", [$item['product_id']]);
        if ($item['variant_id']) {
            $varStock = (int) $this->db->fetchColumn("SELECT stock_quantity FROM product_variants WHERE id = ?", [$item['variant_id']]);
            if ($varStock > 0) $maxStock = $varStock;
        }

        if ($quantity > $maxStock) {
            return ['success' => false, 'message' => "Only {$maxStock} units available."];
        }

        $this->db->execute("UPDATE cart_items SET quantity = ? WHERE id = ?", [$quantity, $cartItemId]);
        return ['success' => true, 'message' => 'Cart updated.'];
    }

    public function removeItem(int $cartItemId): array {
        $this->db->execute("DELETE FROM cart_items WHERE id = ? AND cart_id = ?", [$cartItemId, $this->cartId]);
        return ['success' => true, 'message' => 'Item removed from cart.'];
    }

    public function clear(): void {
        $this->db->execute("DELETE FROM cart_items WHERE cart_id = ?", [$this->cartId]);
        Session::remove('applied_coupon');
    }

    public function getSummary(): array {
        $items = $this->getItems();
        $subtotal = 0.0;
        $totalItems = 0;

        foreach ($items as $item) {
            $subtotal += ((float)$item['price'] * (int)$item['quantity']);
            $totalItems += (int)$item['quantity'];
        }

        // Settings
        $settingModel = new Setting();
        $shippingFee = (float) $settingModel->get('shipping_fee', '99.00');
        $freeShippingThreshold = (float) $settingModel->get('free_shipping_threshold', '999.00');

        // Coupon calculation
        $appliedCoupon = Session::get('applied_coupon');
        $discountAmount = 0.0;
        $couponCode = null;

        if ($appliedCoupon && $subtotal > 0) {
            $couponModel = new Coupon();
            $check = $couponModel->validateCode($appliedCoupon['code'], $subtotal);
            if ($check['valid']) {
                $discountAmount = (float) $check['discount'];
                $couponCode = $appliedCoupon['code'];
            } else {
                Session::remove('applied_coupon');
            }
        }

        // Shipping fee logic: free if subtotal >= threshold or subtotal is 0
        $effectiveShipping = 0.0;
        if ($subtotal > 0) {
            $effectiveShipping = ($subtotal >= $freeShippingThreshold) ? 0.0 : $shippingFee;
        }

        $grandTotal = max(0.0, ($subtotal - $discountAmount + $effectiveShipping));

        $freeShippingProgress = 0;
        $amountLeftForFreeShipping = 0;
        if ($freeShippingThreshold > 0 && $subtotal < $freeShippingThreshold) {
            $amountLeftForFreeShipping = $freeShippingThreshold - $subtotal;
            $freeShippingProgress = min(100, (int) round(($subtotal / $freeShippingThreshold) * 100));
        } elseif ($subtotal >= $freeShippingThreshold && $subtotal > 0) {
            $freeShippingProgress = 100;
        }

        return [
            'items' => $items,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'coupon_code' => $couponCode,
            'shipping_fee' => $effectiveShipping,
            'free_shipping_threshold' => $freeShippingThreshold,
            'amount_left_for_free_shipping' => $amountLeftForFreeShipping,
            'free_shipping_progress' => $freeShippingProgress,
            'grand_total' => $grandTotal,
        ];
    }
}
