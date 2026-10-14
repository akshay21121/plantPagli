<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use App\Models\Cart;
use App\Models\Coupon;

class CartController extends Controller {
    public function index(): void {
        $cart = new Cart();
        $summary = $cart->getSummary();

        $this->render('cart/index', [
            'title' => 'Shopping Cart - PlantPuglu',
            'cart' => $summary,
        ]);
    }

    public function add(): void {
        $this->validateCSRF();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $variantId = !empty($_POST['variant_id']) ? (int) $_POST['variant_id'] : null;

        if ($productId <= 0) {
            $this->respond(['success' => false, 'message' => 'Invalid product.'], 400);
            return;
        }

        $cart = new Cart();
        $result = $cart->addItem($productId, $quantity, $variantId);

        if ($result['success']) {
            $summary = $cart->getSummary();
            $this->respond([
                'success' => true,
                'message' => $result['message'],
                'total_items' => $summary['total_items'],
                'subtotal' => formatPrice($summary['subtotal']),
            ]);
        } else {
            $this->respond($result, 400);
        }
    }

    public function update(): void {
        $this->validateCSRF();

        $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($cartItemId <= 0) {
            $this->respond(['success' => false, 'message' => 'Invalid cart item.'], 400);
            return;
        }

        $cart = new Cart();
        $result = $cart->updateQuantity($cartItemId, $quantity);

        if ($result['success']) {
            $summary = $cart->getSummary();
            $this->respond([
                'success' => true,
                'message' => $result['message'],
                'total_items' => $summary['total_items'],
                'subtotal' => formatPrice($summary['subtotal']),
                'grand_total' => formatPrice($summary['grand_total']),
                'shipping_fee' => formatPrice($summary['shipping_fee']),
                'discount_amount' => formatPrice($summary['discount_amount']),
                'free_shipping_progress' => $summary['free_shipping_progress'],
                'amount_left_for_free_shipping' => formatPrice($summary['amount_left_for_free_shipping']),
            ]);
        } else {
            $this->respond($result, 400);
        }
    }

    public function remove(): void {
        $this->validateCSRF();

        $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);
        if ($cartItemId <= 0) {
            $this->respond(['success' => false, 'message' => 'Invalid cart item.'], 400);
            return;
        }

        $cart = new Cart();
        $result = $cart->removeItem($cartItemId);

        if ($this->isAjax()) {
            $summary = $cart->getSummary();
            $this->json([
                'success' => true,
                'message' => 'Item removed.',
                'total_items' => $summary['total_items'],
                'subtotal' => formatPrice($summary['subtotal']),
                'grand_total' => formatPrice($summary['grand_total']),
            ]);
        } else {
            redirect('/cart', 'success', 'Item removed from your cart.');
        }
    }

    public function applyCoupon(): void {
        $this->validateCSRF();

        $code = trim($_POST['coupon_code'] ?? '');
        if (empty($code)) {
            $this->respond(['success' => false, 'message' => 'Please enter a coupon code.'], 400);
            return;
        }

        $cart = new Cart();
        $summary = $cart->getSummary();

        $couponModel = new Coupon();
        $check = $couponModel->validateCode($code, $summary['subtotal']);

        if (!$check['valid']) {
            $this->respond(['success' => false, 'message' => $check['message']], 400);
            return;
        }

        Session::set('applied_coupon', [
            'code' => $check['coupon']['code'],
            'discount' => $check['discount'],
            'type' => $check['coupon']['discount_type']
        ]);

        if ($this->isAjax()) {
            $newSummary = $cart->getSummary();
            $this->json([
                'success' => true,
                'message' => "Coupon '{$code}' applied! You saved " . formatPrice($newSummary['discount_amount']),
                'discount_amount' => formatPrice($newSummary['discount_amount']),
                'grand_total' => formatPrice($newSummary['grand_total']),
                'coupon_code' => $code
            ]);
        } else {
            redirect('/cart', 'success', "Coupon applied successfully!");
        }
    }

    public function removeCoupon(): void {
        $this->validateCSRF();
        Session::remove('applied_coupon');

        if ($this->isAjax()) {
            $cart = new Cart();
            $summary = $cart->getSummary();
            $this->json([
                'success' => true,
                'message' => 'Coupon removed.',
                'grand_total' => formatPrice($summary['grand_total']),
                'discount_amount' => formatPrice(0)
            ]);
        } else {
            redirect('/cart', 'info', 'Coupon removed.');
        }
    }

    public function count(): void {
        $cart = new Cart();
        $summary = $cart->getSummary();
        $this->json([
            'total_items' => $summary['total_items'],
            'subtotal' => formatPrice($summary['subtotal']),
        ]);
    }

    private function respond(array $data, int $statusCode = 200): void {
        if ($this->isAjax()) {
            $this->json($data, $statusCode);
        } else {
            $flash = $data['success'] ? 'success' : 'error';
            redirect('/cart', $flash, $data['message']);
        }
    }
}
