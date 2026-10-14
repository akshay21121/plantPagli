<?php
/**
 * PlantPuglu Checkout View
 * @var array $cart
 * @var array|null $user
 * @var array $savedAddresses
 * @var string $razorpayKey
 * @var bool $codEnabled
 */

$errors = \Core\Session::get('_checkout_errors', []);
\Core\Session::remove('_checkout_errors');
?>

<div class="container" style="margin-top: 24px;">
    <h1 style="font-size: 28px; margin-bottom: 24px;">Secure Checkout</h1>

    <form action="<?= url('/checkout/place-order') ?>" method="POST" id="checkout-main-form">
        <?= \Core\CSRF::field() ?>

        <div class="checkout-layout">
            <!-- Left Column: Shipping & Payment Details -->
            <div>
                <!-- 1. Customer Delivery Details -->
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <span>📍</span> Shipping & Delivery Address (India)
                    </h3>

                    <?php if (!empty($savedAddresses)): ?>
                        <div style="margin-bottom: 20px; background: var(--cream-bg); padding: 14px; border-radius: var(--radius-sm);">
                            <label style="font-weight: 700; margin-bottom: 8px;">Use Saved Address:</label>
                            <select id="saved-address-selector" style="background:#fff;">
                                <option value="">-- Or enter a new delivery address below --</option>
                                <?php foreach ($savedAddresses as $addr): ?>
                                    <option value="<?= $addr['id'] ?>"
                                            data-name="<?= e($addr['full_name']) ?>"
                                            data-phone="<?= e($addr['phone']) ?>"
                                            data-address="<?= e($addr['address_line1']) ?>"
                                            data-landmark="<?= e($addr['landmark']) ?>"
                                            data-city="<?= e($addr['city']) ?>"
                                            data-state="<?= e($addr['state']) ?>"
                                            data-pincode="<?= e($addr['pincode']) ?>">
                                        <?= e($addr['label']) ?>: <?= e($addr['full_name']) ?>, <?= e($addr['address_line1']) ?>, <?= e($addr['city']) ?> (<?= e($addr['pincode']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="customer_name" id="ship-name" required value="<?= e(old('customer_name', $user['name'] ?? '')) ?>" placeholder="e.g. Rahul Sharma">
                        </div>
                        <div class="form-group">
                            <label>Mobile Number (For Courier Updates) *</label>
                            <input type="tel" name="customer_phone" id="ship-phone" required value="<?= e(old('customer_phone', $user['phone'] ?? '')) ?>" placeholder="10-digit Indian Mobile e.g. 9876543210">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address (For Order Confirmation & Invoice) *</label>
                        <input type="email" name="customer_email" required value="<?= e(old('customer_email', $user['email'] ?? '')) ?>" placeholder="name@example.com">
                    </div>

                    <div class="form-group">
                        <label>House / Flat No., Building, Street Name *</label>
                        <input type="text" name="shipping_address" id="ship-address" required value="<?= e(old('shipping_address')) ?>" placeholder="e.g. Flat 301, Palm Greens, 14th Main Road">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Landmark (Optional)</label>
                            <input type="text" name="shipping_landmark" id="ship-landmark" value="<?= e(old('shipping_landmark')) ?>" placeholder="e.g. Near HDFC Bank">
                        </div>
                        <div class="form-group">
                            <label>City / Town *</label>
                            <input type="text" name="shipping_city" id="ship-city" required value="<?= e(old('shipping_city')) ?>" placeholder="e.g. Bengaluru">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>State *</label>
                            <select name="shipping_state" id="ship-state" required>
                                <option value="">Select Indian State</option>
                                <?php
                                $indianStates = [
                                    "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat",
                                    "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh",
                                    "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab",
                                    "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh",
                                    "Uttarakhand", "West Bengal", "Delhi (NCT)", "Chandigarh"
                                ];
                                foreach ($indianStates as $st): ?>
                                    <option value="<?= e($st) ?>" <?= (old('shipping_state') === $st) ? 'selected' : '' ?>><?= e($st) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>PIN Code (6 Digits) *</label>
                            <input type="text" name="shipping_pincode" id="ship-pincode" maxlength="6" pattern="[1-9][0-9]{5}" required value="<?= e(old('shipping_pincode')) ?>" placeholder="e.g. 560034">
                        </div>
                    </div>

                    <?php if ($user): ?>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;cursor:pointer;">
                                <input type="checkbox" name="save_address" value="1" checked>
                                Save this address to my profile for future orders
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. Payment Method Selection -->
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 28px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <span>💳</span> Select Payment Method
                    </h3>

                    <label class="payment-method-box selected" id="method-card-razorpay">
                        <input type="radio" name="payment_method" value="razorpay" checked style="width:auto;">
                        <div style="flex:1;">
                            <div style="font-weight: 700; font-size: 15px;">Razorpay Online Payment (Recommended)</div>
                            <div style="font-size: 12.5px; color: var(--text-muted);">
                                Instant payment via Google Pay, PhonePe, Paytm, UPI QR, Any Credit/Debit Card, NetBanking.
                            </div>
                        </div>
                        <span style="font-size: 20px;">🛡️</span>
                    </label>

                    <?php if ($codEnabled): ?>
                        <label class="payment-method-box" id="method-card-cod">
                            <input type="radio" name="payment_method" value="cod" style="width:auto;">
                            <div style="flex:1;">
                                <div style="font-weight: 700; font-size: 15px;">Cash on Delivery (COD)</div>
                                <div style="font-size: 12.5px; color: var(--text-muted);">
                                    Pay cash or UPI directly to courier upon receiving your healthy plants.
                                </div>
                            </div>
                            <span style="font-size: 20px;">💵</span>
                        </label>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Order Review & Placement -->
            <div class="summary-card" style="position: sticky; top: 100px;">
                <h3 style="font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--cream-border); padding-bottom: 10px;">Review Order</h3>

                <!-- Order items quick summary -->
                <div style="max-height: 240px; overflow-y: auto; margin-bottom: 16px; display:flex; flex-direction:column; gap:10px;">
                    <?php foreach ($cart['items'] as $it): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:13.5px;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="background:var(--cream-bg); width:24px; height:24px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:700;">
                                    <?= $it['quantity'] ?>x
                                </span>
                                <div>
                                    <strong><?= e($it['product_name']) ?></strong>
                                    <?php if (!empty($it['variant_name'])): ?>
                                        <div style="font-size:11.5px; color:var(--text-muted);"><?= e($it['variant_name']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <strong><?= formatPrice($it['price'] * $it['quantity']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong><?= formatPrice($cart['subtotal']) ?></strong>
                </div>

                <div class="summary-row">
                    <span>Shipping Charges</span>
                    <strong><?= ($cart['shipping_fee'] == 0) ? '<span style="color:var(--success);">FREE</span>' : formatPrice($cart['shipping_fee']) ?></strong>
                </div>

                <?php if ($cart['discount_amount'] > 0): ?>
                    <div class="summary-row" style="color:var(--success);">
                        <span>Coupon Savings</span>
                        <strong>-<?= formatPrice($cart['discount_amount']) ?></strong>
                    </div>
                <?php endif; ?>

                <div class="summary-row total-row">
                    <span>Total Amount Payable</span>
                    <span><?= formatPrice($cart['grand_total']) ?></span>
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <label style="font-size: 12.5px;">Delivery Notes / Gate Instructions (Optional)</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Please leave with security if unavailable"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px; margin-top: 10px;">
                    Place Order Now &rarr;
                </button>

                <div style="margin-top: 14px; text-align: center; font-size: 11.5px; color: var(--text-muted);">
                    By placing your order, you agree to PlantPuglu's terms of service and transit replacement policy.
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('saved-address-selector');
    if (selector) {
        selector.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt.value) {
                document.getElementById('ship-name').value = opt.getAttribute('data-name');
                document.getElementById('ship-phone').value = opt.getAttribute('data-phone');
                document.getElementById('ship-address').value = opt.getAttribute('data-address');
                document.getElementById('ship-landmark').value = opt.getAttribute('data-landmark');
                document.getElementById('ship-city').value = opt.getAttribute('data-city');
                document.getElementById('ship-state').value = opt.getAttribute('data-state');
                document.getElementById('ship-pincode').value = opt.getAttribute('data-pincode');
            }
        });
    }

    // Toggle selected payment styling
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-method-box').forEach(b => b.classList.remove('selected'));
            this.closest('.payment-method-box').classList.add('selected');
        });
    });
});
</script>
