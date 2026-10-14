<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use App\Services\RazorpayService;
use Exception;

class CheckoutController extends Controller {
    public function index(): void {
        $cart = new Cart();
        $summary = $cart->getSummary();

        if (empty($summary['items'])) {
            $this->redirect('/cart', 'error', 'Your shopping cart is empty.');
            return;
        }

        $user = Auth::user();
        $savedAddresses = [];
        if ($user) {
            $userModel = new User();
            $savedAddresses = $userModel->getAddresses((int)$user['id']);
        }

        $settingModel = new Setting();
        $razorpayKey = $settingModel->get('razorpay_key_id', config('razorpay.key_id'));
        $codEnabled = (bool) $settingModel->get('cod_enabled', 1);

        $this->render('checkout/index', [
            'title' => 'Secure Checkout - PlantPuglu',
            'cart' => $summary,
            'user' => $user,
            'savedAddresses' => $savedAddresses,
            'razorpayKey' => $razorpayKey,
            'codEnabled' => $codEnabled,
        ]);
    }

    public function placeOrder(): void {
        $this->validateCSRF();

        $cart = new Cart();
        $summary = $cart->getSummary();
        if (empty($summary['items'])) {
            $this->redirect('/cart', 'error', 'Your shopping cart is empty.');
            return;
        }

        $rules = [
            'customer_name' => 'required|min:2|max:150',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|phone',
            'shipping_address' => 'required|min:5|max:255',
            'shipping_city' => 'required|min:2|max:100',
            'shipping_state' => 'required|min:2|max:100',
            'shipping_pincode' => 'required|pincode',
            'payment_method' => 'required|in:cod,razorpay',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            Session::flash('error', $validator->first('customer_name') ?? $validator->first('shipping_pincode') ?? 'Please correct the highlighted form errors.');
            Session::set('_checkout_errors', $validator->errors());
            $this->redirect('/checkout');
            return;
        }

        $paymentMethod = $_POST['payment_method'];
        $customerData = [
            'user_id' => Auth::id(),
            'customer_name' => trim($_POST['customer_name']),
            'customer_email' => trim(strtolower($_POST['customer_email'])),
            'customer_phone' => trim($_POST['customer_phone']),
            'shipping_address' => trim($_POST['shipping_address']),
            'shipping_landmark' => trim($_POST['shipping_landmark'] ?? ''),
            'shipping_city' => trim($_POST['shipping_city']),
            'shipping_state' => trim($_POST['shipping_state']),
            'shipping_pincode' => trim($_POST['shipping_pincode']),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        // If user is logged in and requested to save address
        if (Auth::check() && !empty($_POST['save_address'])) {
            $userModel = new User();
            $userModel->addAddress(Auth::id(), [
                'label' => 'Saved Address',
                'full_name' => $customerData['customer_name'],
                'phone' => $customerData['customer_phone'],
                'address_line1' => $customerData['shipping_address'],
                'landmark' => $customerData['shipping_landmark'],
                'city' => $customerData['shipping_city'],
                'state' => $customerData['shipping_state'],
                'pincode' => $customerData['shipping_pincode'],
            ]);
        }

        $orderModel = new Order();

        try {
            $created = $orderModel->createFromCart($cart, $customerData, $paymentMethod);
            $orderId = $created['order_id'];
            $orderNumber = $created['order_number'];

            if ($paymentMethod === 'cod') {
                // Cash on Delivery confirmed immediately
                $cart->clear();
                $this->redirect("/checkout/success/{$orderNumber}", 'success', 'Order placed successfully with Cash on Delivery!');
                return;
            }

            // Razorpay flow: initiate order on Razorpay API
            $razorpayService = new RazorpayService();
            $rzpOrder = $razorpayService->createOrder((float)$created['total_amount'], $orderNumber);

            // Update payment record with razorpay order id
            $db = \Core\Database::getInstance();
            $db->execute(
                "UPDATE payments SET razorpay_order_id = ? WHERE order_id = ?",
                [$rzpOrder['id'], $orderId]
            );

            // Render Razorpay checkout view
            $this->render('checkout/razorpay_pay', [
                'title' => 'Complete Payment - PlantPuglu',
                'order' => $orderModel->findById($orderId),
                'rzpOrder' => $rzpOrder,
                'razorpayKey' => $razorpayService->getKeyId(),
                'customerData' => $customerData,
            ]);

        } catch (Exception $e) {
            $this->redirect('/checkout', 'error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function verifyPayment(): void {
        $this->validateCSRF();

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $razorpayOrderId = trim($_POST['razorpay_order_id'] ?? '');
        $razorpayPaymentId = trim($_POST['razorpay_payment_id'] ?? '');
        $razorpaySignature = trim($_POST['razorpay_signature'] ?? '');

        if ($orderId <= 0 || empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($razorpaySignature)) {
            $this->redirect('/checkout', 'error', 'Payment verification failed: missing payment confirmation parameters.');
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->findById($orderId);
        if (!$order) {
            $this->redirect('/cart', 'error', 'Order not found.');
            return;
        }

        // Prevent duplicate processing if already paid
        if ($order['payment_status'] === 'paid') {
            $this->redirect("/checkout/success/{$order['order_number']}");
            return;
        }

        $razorpayService = new RazorpayService();
        $isValid = $razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature);

        if (!$isValid) {
            $orderModel->updatePaymentStatus($orderId, 'failed');
            $this->redirect('/checkout', 'error', 'Payment verification signature failed. If your money was debited, it will be refunded within 5-7 working days.');
            return;
        }

        // Mark as paid securely
        $orderModel->markAsPaid($orderId, [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature,
        ]);

        // Clear user's cart now that payment is confirmed
        $cart = new Cart();
        $cart->clear();

        $this->redirect("/checkout/success/{$order['order_number']}", 'success', 'Payment verified successfully! Thank you for your purchase.');
    }

    public function success(string $orderNumber): void {
        $orderModel = new Order();
        $order = $orderModel->findByOrderNumber($orderNumber);

        if (!$order) {
            $this->redirect('/', 'error', 'Order not found.');
            return;
        }

        $settingModel = new Setting();
        $supportPhone = $settingModel->get('store_phone', '+91 98765 43210');
        $supportEmail = $settingModel->get('store_email', 'care@plantpagli.com');

        $this->render('checkout/success', [
            'title' => "Order Confirmed #{$order['order_number']} - PlantPuglu",
            'order' => $order,
            'supportPhone' => $supportPhone,
            'supportEmail' => $supportEmail,
        ]);
    }
}
