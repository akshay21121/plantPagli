<?php
/**
 * PlantPuglu Order Confirmation / Success View
 * @var array $order
 * @var string $supportPhone
 * @var string $supportEmail
 */
?>

<div class="container" style="max-width: 780px; margin: 40px auto 70px;">
    <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-md);">
        <!-- Success Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 72px; height: 72px; background: var(--accent-soft); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 16px;">
                ✓
            </div>
            <h1 style="font-size: 30px; margin-bottom: 8px;">Order Confirmed!</h1>
            <p style="font-size: 15px; color: var(--text-muted);">Thank you, <strong><?= e($order['customer_name']) ?></strong>. Your live plants are being carefully prepped and packed at our nursery!</p>
            <div style="margin-top: 12px; display: inline-block; background: var(--cream-bg); padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 14px; color: var(--primary-dark);">
                Order Number: #<?= e($order['order_number']) ?>
            </div>
        </div>

        <!-- Order Meta Details -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; background: var(--cream-bg); padding: 20px; border-radius: var(--radius-md); margin-bottom: 30px; font-size: 13.5px;">
            <div>
                <span style="color:var(--text-muted); display:block; font-size:12px; text-transform:uppercase; font-weight:700;">Order Date</span>
                <strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong>
            </div>
            <div>
                <span style="color:var(--text-muted); display:block; font-size:12px; text-transform:uppercase; font-weight:700;">Payment Method</span>
                <strong><?= strtoupper($order['payment_method']) ?> (<?= ucfirst($order['payment_status']) ?>)</strong>
            </div>
            <div>
                <span style="color:var(--text-muted); display:block; font-size:12px; text-transform:uppercase; font-weight:700;">Estimated Delivery</span>
                <strong style="color:var(--success);">3 - 5 Business Days</strong>
            </div>
        </div>

        <!-- Shipping Destination -->
        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--cream-border);">
            <h3 style="font-size: 16px; margin-bottom: 10px;">📦 Shipping Destination</h3>
            <p style="font-size: 14px; line-height: 1.6; color: var(--text-main);">
                <strong><?= e($order['customer_name']) ?></strong> (📞 <?= e($order['customer_phone']) ?>)<br>
                <?= e($order['shipping_address']) ?><br>
                <?php if (!empty($order['shipping_landmark'])): ?>
                    Landmark: <?= e($order['shipping_landmark']) ?><br>
                <?php endif; ?>
                <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <strong><?= e($order['shipping_pincode']) ?></strong>
            </p>
        </div>

        <!-- Items Ordered -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; margin-bottom: 14px;">Ordered Plant Varieties</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--cream-border); text-align: left; color: var(--text-muted); font-size: 12px;">
                        <th style="padding-bottom: 8px;">Item</th>
                        <th style="padding-bottom: 8px; text-align: center;">Qty</th>
                        <th style="padding-bottom: 8px; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr style="border-bottom: 1px solid var(--cream-border);">
                            <td style="padding: 12px 0;">
                                <strong><?= e($item['product_name']) ?></strong>
                                <?php if (!empty($item['variant_name'])): ?>
                                    <div style="font-size: 12px; color: var(--text-muted);"><?= e($item['variant_name']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 0; text-align: center;"><?= $item['quantity'] ?></td>
                            <td style="padding: 12px 0; text-align: right; font-weight: 700;"><?= formatPrice($item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 16px; max-width: 300px; margin-left: auto;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13.5px;">
                    <span>Subtotal:</span>
                    <strong><?= formatPrice($order['subtotal']) ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13.5px;">
                    <span>Shipping Charges:</span>
                    <strong><?= ($order['shipping_fee'] == 0) ? 'FREE' : formatPrice($order['shipping_fee']) ?></strong>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13.5px; color:var(--success);">
                        <span>Coupon Savings:</span>
                        <strong>-<?= formatPrice($order['discount_amount']) ?></strong>
                    </div>
                <?php endif; ?>
                <div style="display:flex; justify-content:space-between; padding-top:8px; border-top:1.5px dashed var(--cream-border); font-size:16px; font-weight:800; color:var(--primary-dark);">
                    <span>Total Paid:</span>
                    <span><?= formatPrice($order['total_amount']) ?></span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 14px; justify-content: center; margin-top: 30px;">
            <a href="<?= url('/products') ?>" class="btn btn-primary">Continue Plant Shopping &rarr;</a>
            <button onclick="window.print()" class="btn btn-outline">🖨️ Print Receipt</button>
        </div>

        <div style="margin-top: 24px; text-align: center; font-size: 13px; color: var(--text-muted);">
            Questions about your order? WhatsApp us at <strong><?= e($supportPhone) ?></strong> or email <strong><?= e($supportEmail) ?></strong>.
        </div>
    </div>
</div>
