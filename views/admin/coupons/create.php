<div style="margin-bottom: 24px;">
    <a href="<?= url('/admin/coupons') ?>" style="color: var(--primary); font-weight: 700; font-size: 13.5px;">&larr; Back to Coupons</a>
    <h1 style="font-size: 24px; font-weight: 700; margin-top: 8px;">Create Discount Coupon</h1>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-body">
        <form action="<?= url('/admin/coupons/store') ?>" method="POST">
            <?= \Core\CSRF::field() ?>

            <div class="form-group">
                <label>Coupon Code (e.g. MONSOON25) *</label>
                <input type="text" name="code" required placeholder="UPPERCASE CODE" style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label>Internal Description</label>
                <input type="text" name="description" placeholder="e.g. Special Festival 25% Off Promotion">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Discount Type *</label>
                    <select name="discount_type" required>
                        <option value="percentage">Percentage Discount (%)</option>
                        <option value="fixed">Fixed Rupee Discount (₹)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Discount Value *</label>
                    <input type="number" step="0.01" name="discount_value" required placeholder="e.g. 15 or 150.00">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Minimum Cart Subtotal (INR ₹)</label>
                    <input type="number" step="0.01" name="min_order_amount" value="499.00">
                </div>
                <div class="form-group">
                    <label>Maximum Discount Cap (INR ₹)</label>
                    <input type="number" step="0.01" name="max_discount_amount" placeholder="e.g. 300.00 (Optional)">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Total Usage Limit</label>
                    <input type="number" name="usage_limit" value="1000">
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="datetime-local" name="expires_at">
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    Activate Coupon Immediately
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Create Coupon</button>
        </form>
    </div>
</div>
