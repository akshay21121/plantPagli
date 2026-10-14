<?php
/**
 * Customer Order Detailed View & Status Tracker
 * @var array $order
 */
?>

<div class="container" style="max-width: 850px; margin: 30px auto 70px;">
    <div style="margin-bottom: 20px;">
        <a href="<?= url('/account/orders') ?>" style="color: var(--primary); font-weight: 700; font-size: 14px;">&larr; Back to Orders List</a>
    </div>

    <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid var(--cream-border); padding-bottom: 20px;">
            <div>
                <h1 style="font-size: 26px; margin-bottom: 4px;">Order #<?= e($order['order_number']) ?></h1>
                <span style="font-size: 13.5px; color: var(--text-muted);">Placed on <?= date('d F Y, h:i A', strtotime($order['created_at'])) ?></span>
            </div>
            <div style="text-align: right;">
                <button onclick="window.print()" class="btn btn-outline btn-sm">🖨️ Print Invoice</button>
            </div>
        </div>

        <!-- Order Tracking Timeline -->
        <div style="background: var(--cream-bg); border-radius: var(--radius-md); padding: 24px; margin-bottom: 30px;">
            <h3 style="font-size: 16px; margin-bottom: 16px;">Shipment Status Tracker</h3>
            
            <?php
            $statuses = ['pending', 'confirmed', 'shipped', 'delivered'];
            $currentIndex = array_search($order['order_status'], $statuses);
            if ($currentIndex === false) $currentIndex = 0;
            ?>

            <div style="display: flex; justify-content: space-between; position: relative; margin: 20px 10px;">
                <!-- Line -->
                <div style="position: absolute; top: 12px; left: 0; right: 0; height: 4px; background: #ccd8cf; z-index: 1;"></div>
                <div style="position: absolute; top: 12px; left: 0; width: <?= ($currentIndex / 3) * 100 ?>%; height: 4px; background: var(--primary); z-index: 1; transition: width 0.3s;"></div>

                <?php
                $stepLabels = ['Order Placed', 'Confirmed & Prepped', 'Shipped with Courier', 'Safely Delivered'];
                foreach ($statuses as $idx => $st):
                    $isActive = ($idx <= $currentIndex);
                ?>
                    <div style="position: relative; z-index: 2; text-align: center;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: <?= $isActive ? 'var(--primary)' : '#ccd8cf' ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin: 0 auto 6px;">
                            <?= $isActive ? '✓' : ($idx + 1) ?>
                        </div>
                        <span style="font-size: 11.5px; font-weight: <?= $isActive ? '700' : '500' ?>; color: <?= $isActive ? 'var(--primary-dark)' : 'var(--text-muted)' ?>;">
                            <?= $stepLabels[$idx] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($order['tracking_number'])): ?>
                <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed var(--cream-border); font-size: 13.5px;">
                    🚚 Courier Partner: <strong><?= e($order['courier_name'] ?? 'DTDC Express') ?></strong> | 
                    Tracking Number: <strong><?= e($order['tracking_number']) ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- Address and Payment Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px;">
            <div style="background: var(--cream-bg); padding: 20px; border-radius: var(--radius-sm);">
                <h4 style="font-size: 14px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px;">Delivery Address</h4>
                <p style="font-size: 14px; line-height: 1.6;">
                    <strong><?= e($order['customer_name']) ?></strong> (📞 <?= e($order['customer_phone']) ?>)<br>
                    <?= e($order['shipping_address']) ?><br>
                    <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?>
                </p>
            </div>

            <div style="background: var(--cream-bg); padding: 20px; border-radius: var(--radius-sm);">
                <h4 style="font-size: 14px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px;">Payment Information</h4>
                <p style="font-size: 14px; line-height: 1.6;">
                    Method: <strong><?= strtoupper($order['payment_method']) ?></strong><br>
                    Payment Status: <strong style="color:var(--success);"><?= ucfirst($order['payment_status']) ?></strong><br>
                    Currency: <strong>INR (₹)</strong>
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <h3 style="font-size: 18px; margin-bottom: 14px;">Items in this Order</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 24px;">
            <thead>
                <tr style="text-align: left; color: var(--text-muted); font-size: 12px; border-bottom: 1px solid var(--cream-border);">
                    <th style="padding-bottom: 10px;">Product Name</th>
                    <th style="padding-bottom: 10px;">Unit Price</th>
                    <th style="padding-bottom: 10px; text-align: center;">Qty</th>
                    <th style="padding-bottom: 10px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr style="border-bottom: 1px solid var(--cream-border);">
                        <td style="padding: 14px 0;">
                            <strong><?= e($item['product_name']) ?></strong>
                            <?php if (!empty($item['variant_name'])): ?>
                                <div style="font-size: 12px; color: var(--text-muted);"><?= e($item['variant_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 14px 0;"><?= formatPrice($item['price']) ?></td>
                        <td style="padding: 14px 0; text-align: center;"><?= $item['quantity'] ?></td>
                        <td style="padding: 14px 0; text-align: right; font-weight: 700;"><?= formatPrice($item['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div style="max-width: 320px; margin-left: auto;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                <span>Subtotal:</span>
                <strong><?= formatPrice($order['subtotal']) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                <span>Shipping Fee:</span>
                <strong><?= ($order['shipping_fee'] == 0) ? 'FREE' : formatPrice($order['shipping_fee']) ?></strong>
            </div>
            <?php if ($order['discount_amount'] > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: var(--success);">
                    <span>Discount (<?= e($order['coupon_code'] ?? '') ?>):</span>
                    <strong>-<?= formatPrice($order['discount_amount']) ?></strong>
                </div>
            <?php endif; ?>
            <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1.5px dashed var(--cream-border); font-size: 18px; font-weight: 800; color: var(--primary-dark);">
                <span>Total Amount:</span>
                <span><?= formatPrice($order['total_amount']) ?></span>
            </div>
        </div>
    </div>
</div>
