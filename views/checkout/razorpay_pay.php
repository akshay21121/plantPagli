<?php
/**
 * PlantPuglu Razorpay Payment Initiation & Modal Trigger
 * @var array $order
 * @var array $rzpOrder
 * @var string $razorpayKey
 * @var array $customerData
 */
?>

<div class="container" style="max-width: 680px; margin: 50px auto;">
    <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-md); text-align: center;">
        <div style="font-size: 44px; margin-bottom: 12px;">🌱</div>
        <h1 style="font-size: 26px; margin-bottom: 8px;">Order #<?= e($order['order_number']) ?> Created</h1>
        <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 24px;">Please complete your online payment of <strong><?= formatPrice($order['total_amount']) ?></strong> to confirm your plant shipment.</p>

        <div style="background: var(--cream-bg); border-radius: var(--radius-md); padding: 20px; text-align: left; margin-bottom: 30px; font-size: 14px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span>Customer:</span>
                <strong><?= e($order['customer_name']) ?></strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span>Mobile Number:</span>
                <strong><?= e($order['customer_phone']) ?></strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span>Total Amount:</span>
                <strong style="color:var(--primary-dark); font-size:16px;"><?= formatPrice($order['total_amount']) ?></strong>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>Delivery To:</span>
                <span><?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> (<?= e($order['shipping_pincode']) ?>)</span>
            </div>
        </div>

        <!-- Real Razorpay Trigger Button -->
        <button id="rzp-button" class="btn btn-primary btn-block" style="padding: 16px; font-size: 17px; margin-bottom: 14px;">
            🔒 Pay <?= formatPrice($order['total_amount']) ?> with Razorpay
        </button>

        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 24px;">
            Supported: UPI (GPay, PhonePe, Paytm), Credit & Debit Cards, NetBanking, Wallets.
        </div>

        <!-- Hidden form for backend HMAC signature verification post -->
        <form id="rzp-verify-form" action="<?= url('/checkout/verify-payment') ?>" method="POST" style="display:none;">
            <?= \Core\CSRF::field() ?>
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <input type="hidden" name="razorpay_order_id" id="form-rzp-order-id" value="">
            <input type="hidden" name="razorpay_payment_id" id="form-rzp-payment-id" value="">
            <input type="hidden" name="razorpay_signature" id="form-rzp-signature" value="">
        </form>

        <?php if (!empty($rzpOrder['is_mock']) || str_starts_with($razorpayKey, 'rzp_test_')): ?>
            <!-- Test Mode Simulation Helper for Local Development / Review -->
            <div style="margin-top: 30px; border-top: 1px dashed var(--cream-border); padding-top: 20px;">
                <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 10px;">
                    <strong>[Developer Test Mode Detected]:</strong> If testing locally without active Razorpay merchant API keys, you can simulate a successful test payment verification below:
                </p>
                <button type="button" id="btn-simulate-test-payment" class="btn btn-outline btn-sm">
                    ⚡ Simulate Test Payment Success & Verify Order
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Load Razorpay Checkout SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderData = {
        key: "<?= e($razorpayKey) ?>",
        amount: <?= (int) round(((float)$order['total_amount']) * 100) ?>,
        currency: "INR",
        name: "Plantpagli Online Nursery",
        description: "Payment for Order #<?= e($order['order_number']) ?>",
        image: "<?= asset('images/logo.png') ?>",
        order_id: "<?= e($rzpOrder['id']) ?>",
        handler: function (response) {
            // Post to backend for signature verification
            document.getElementById('form-rzp-order-id').value = response.razorpay_order_id;
            document.getElementById('form-rzp-payment-id').value = response.razorpay_payment_id;
            document.getElementById('form-rzp-signature').value = response.razorpay_signature;
            document.getElementById('rzp-verify-form').submit();
        },
        prefill: {
            name: "<?= e($order['customer_name']) ?>",
            email: "<?= e($order['customer_email']) ?>",
            contact: "<?= e($order['customer_phone']) ?>"
        },
        theme: {
            color: "#1b4332"
        }
    };

    const rzpBtn = document.getElementById('rzp-button');
    if (rzpBtn) {
        rzpBtn.onclick = function(e) {
            e.preventDefault();
            try {
                const rzp1 = new Razorpay(orderData);
                rzp1.on('payment.failed', function (response){
                    alert("Payment failed: " + response.error.description);
                });
                rzp1.open();
            } catch (err) {
                console.warn("Razorpay Checkout SDK notice: ", err);
                const simBtn = document.getElementById('btn-simulate-test-payment');
                if (simBtn) simBtn.click();
            }
        };
    }

    // Test simulation handler
    const simBtn = document.getElementById('btn-simulate-test-payment');
    if (simBtn) {
        simBtn.onclick = function() {
            document.getElementById('form-rzp-order-id').value = "<?= e($rzpOrder['id']) ?>";
            document.getElementById('form-rzp-payment-id').value = "pay_test_" + Math.random().toString(36).substr(2, 9);
            document.getElementById('form-rzp-signature').value = "simulated_test_signature_token";
            document.getElementById('rzp-verify-form').submit();
        };
    }
});
</script>
