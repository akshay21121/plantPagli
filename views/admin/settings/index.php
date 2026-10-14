<?php
/**
 * Admin Store Settings Configuration View
 * @var array $settingsGrouped
 */

$settingsMap = [];
foreach ($settingsGrouped as $group => $items) {
    foreach ($items as $item) {
        $settingsMap[$item['setting_key']] = $item['setting_value'];
    }
}
?>

<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 700;">Store Configuration & Payment Gateways</h1>
    <span style="font-size: 13px; color: var(--text-muted);">Configure contact information, shipping rules, Razorpay Indian gateway keys, and SEO</span>
</div>

<form action="<?= url('/admin/settings/update') ?>" method="POST">
    <?= \Core\CSRF::field() ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px; align-items: flex-start;">
        <!-- Left Column: General & Shipping & SEO -->
        <div>
            <!-- General Branding -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">1. Store Branding</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Store Brand Name</label>
                        <input type="text" name="settings[store_name]" value="<?= e($settingsMap['store_name'] ?? 'PlantPuglu') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Tagline</label>
                        <input type="text" name="settings[store_tagline]" value="<?= e($settingsMap['store_tagline'] ?? '') ?>">
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Currency Symbol</label>
                            <input type="text" name="settings[currency_symbol]" value="<?= e($settingsMap['currency_symbol'] ?? '₹') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Currency ISO Code</label>
                            <input type="text" name="settings[currency_code]" value="<?= e($settingsMap['currency_code'] ?? 'INR') ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Calculations -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">2. Shipping Charges & Free Threshold</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Standard Shipping Charge (INR ₹)</label>
                        <input type="number" step="0.01" name="settings[shipping_fee]" value="<?= e($settingsMap['shipping_fee'] ?? '99.00') ?>" required>
                        <small style="color:var(--text-muted);font-size:11.5px;">Applied to orders below the free shipping threshold.</small>
                    </div>

                    <div class="form-group">
                        <label>Free Shipping Cart Threshold (INR ₹)</label>
                        <input type="number" step="0.01" name="settings[free_shipping_threshold]" value="<?= e($settingsMap['free_shipping_threshold'] ?? '999.00') ?>" required>
                        <small style="color:var(--text-muted);font-size:11.5px;">Orders with subtotal equal or greater receive 100% free delivery across India.</small>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">3. Default SEO Meta</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Homepage SEO Meta Title</label>
                        <input type="text" name="settings[site_meta_title]" value="<?= e($settingsMap['site_meta_title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Homepage SEO Meta Description</label>
                        <textarea name="settings[site_meta_description]" rows="3"><?= e($settingsMap['site_meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Contact & Payment Gateways -->
        <div>
            <!-- Contact & Address -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">4. Contact & Support Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Customer Support Email</label>
                        <input type="email" name="settings[store_email]" value="<?= e($settingsMap['store_email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Support Phone Number</label>
                        <input type="text" name="settings[store_phone]" value="<?= e($settingsMap['store_phone'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>WhatsApp Support Line</label>
                        <input type="text" name="settings[store_whatsapp]" value="<?= e($settingsMap['store_whatsapp'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Registered Nursery Physical Address</label>
                        <textarea name="settings[store_address]" rows="3"><?= e($settingsMap['store_address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Razorpay & COD Payment Gateway -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">5. Razorpay & Indian Payment Gateway</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Razorpay Key ID</label>
                        <input type="text" name="settings[razorpay_key_id]" value="<?= e($settingsMap['razorpay_key_id'] ?? '') ?>" placeholder="rzp_test_YourKeyId">
                        <small style="color:var(--text-muted);font-size:11.5px;">Test key starts with <code>rzp_test_</code>. Production live key starts with <code>rzp_live_</code>.</small>
                    </div>

                    <div class="form-group">
                        <label>Razorpay Key Secret (Server Verification)</label>
                        <input type="password" name="settings[razorpay_key_secret]" value="<?= e($settingsMap['razorpay_key_secret'] ?? '') ?>" placeholder="••••••••••••••••">
                        <small style="color:var(--text-muted);font-size:11.5px;">Required for HMAC-SHA256 signature verification. Kept secure on server.</small>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="settings[cod_enabled]" value="1" <?= (!empty($settingsMap['cod_enabled']) && $settingsMap['cod_enabled'] == '1') ? 'checked' : '' ?>>
                            Enable Cash on Delivery (COD) Payment Option
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                Save All Store Configurations
            </button>
        </div>
    </div>
</form>
