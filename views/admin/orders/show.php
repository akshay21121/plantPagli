<?php
/**
 * Admin Single Order Management View
 * @var array $order
 */
?>

<div style="margin-bottom: 24px;">
    <a href="<?= url('/admin/orders') ?>" style="color: var(--primary); font-weight: 700; font-size: 13.5px;">&larr; Back to Orders List</a>
    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 8px;">
        <h1 style="font-size: 24px; font-weight: 700;">Order #<?= e($order['order_number']) ?></h1>
        <span style="font-size: 13.5px; color: var(--text-muted);">Placed on <?= date('d F Y, h:i A', strtotime($order['created_at'])) ?></span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 28px; align-items: flex-start;">
    <!-- Left: Order Items & Delivery Info -->
    <div>
        <!-- Order Items -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 15px; font-weight: 700;">Ordered Plant Varieties</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Unit Price</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $it): ?>
                            <tr>
                                <td>
                                    <strong><?= e($it['product_name']) ?></strong>
                                    <?php if (!empty($it['variant_name'])): ?>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?= e($it['variant_name']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatPrice($it['price']) ?></td>
                                <td style="text-align: center;"><?= $it['quantity'] ?></td>
                                <td style="text-align: right; font-weight: 700;"><?= formatPrice($it['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding: 20px 24px; background: #f8fafc; border-top: 1px solid var(--border);">
                <div style="max-width: 300px; margin-left: auto;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13.5px;">
                        <span>Subtotal:</span>
                        <strong><?= formatPrice($order['subtotal']) ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13.5px;">
                        <span>Shipping Fee:</span>
                        <strong><?= ($order['shipping_fee'] == 0) ? 'FREE' : formatPrice($order['shipping_fee']) ?></strong>
                    </div>
                    <?php if ($order['discount_amount'] > 0): ?>
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13.5px; color:var(--success);">
                            <span>Coupon Savings (<?= e($order['coupon_code'] ?? '') ?>):</span>
                            <strong>-<?= formatPrice($order['discount_amount']) ?></strong>
                        </div>
                    <?php endif; ?>
                    <div style="display:flex; justify-content:space-between; padding-top:8px; border-top:1.5px dashed var(--border); font-size:16px; font-weight:800; color:var(--primary-dark);">
                        <span>Total Paid:</span>
                        <span><?= formatPrice($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & Shipping Card -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 15px; font-weight: 700;">Customer & Shipping Address</h3>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div>
                        <span style="font-size:12px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Customer Details</span>
                        <p style="margin-top:6px; line-height:1.6;">
                            <strong><?= e($order['customer_name']) ?></strong><br>
                            Email: <a href="mailto:<?= e($order['customer_email']) ?>"><?= e($order['customer_email']) ?></a><br>
                            Phone: <a href="tel:<?= e($order['customer_phone']) ?>"><?= e($order['customer_phone']) ?></a>
                        </p>
                    </div>

                    <div>
                        <span style="font-size:12px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Delivery Destination</span>
                        <p style="margin-top:6px; line-height:1.6;">
                            <?= e($order['shipping_address']) ?><br>
                            <?php if (!empty($order['shipping_landmark'])): ?>
                                Landmark: <?= e($order['shipping_landmark']) ?><br>
                            <?php endif; ?>
                            <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <strong><?= e($order['shipping_pincode']) ?></strong>
                        </p>
                    </div>
                </div>

                <?php if (!empty($order['notes'])): ?>
                    <div style="margin-top: 16px; padding: 12px; background: var(--admin-bg); border-radius: 6px; font-size: 13px;">
                        <strong>Customer Gate Notes:</strong> <?= e($order['notes']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Logs -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 15px; font-weight: 700;">Payment & Transaction References</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Razorpay Order ID</th>
                            <th>Payment ID</th>
                            <th>Recorded Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($order['payments'])): ?>
                            <tr><td colspan="5" style="padding:16px;">No transaction records.</td></tr>
                        <?php else: ?>
                            <?php foreach ($order['payments'] as $pay): ?>
                                <tr>
                                    <td><strong><?= strtoupper($pay['payment_method']) ?></strong></td>
                                    <td>
                                        <span class="badge-status badge-<?= $pay['status'] === 'captured' ? 'paid' : $pay['status'] ?>">
                                            <?= ucfirst($pay['status']) ?>
                                        </span>
                                    </td>
                                    <td><code><?= e($pay['razorpay_order_id'] ?? 'N/A') ?></code></td>
                                    <td><code><?= e($pay['razorpay_payment_id'] ?? 'N/A') ?></code></td>
                                    <td><?= formatPrice($pay['amount']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Order Management Actions -->
    <div>
        <!-- Update Order Status & Tracking -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 15px; font-weight: 700;">Update Shipment Status</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/orders/status/' . $order['id']) ?>" method="POST">
                    <?= \Core\CSRF::field() ?>

                    <div class="form-group">
                        <label>Order Status</label>
                        <select name="order_status" required>
                            <option value="pending" <?= ($order['order_status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= ($order['order_status'] === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                            <option value="processing" <?= ($order['order_status'] === 'processing') ? 'selected' : '' ?>>Processing / Packing</option>
                            <option value="shipped" <?= ($order['order_status'] === 'shipped') ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= ($order['order_status'] === 'delivered') ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= ($order['order_status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Courier Partner Name</label>
                        <input type="text" name="courier_name" value="<?= e($order['courier_name'] ?? '') ?>" placeholder="e.g. DTDC, Delhivery, BlueDart">
                    </div>

                    <div class="form-group">
                        <label>Tracking AWB / Number</label>
                        <input type="text" name="tracking_number" value="<?= e($order['tracking_number'] ?? '') ?>" placeholder="e.g. DTDC-BLR-883921">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Update Shipment Status</button>
                </form>
            </div>
        </div>

        <!-- Update Payment Status -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 15px; font-weight: 700;">Update Payment Status</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/orders/payment-status/' . $order['id']) ?>" method="POST">
                    <?= \Core\CSRF::field() ?>

                    <div class="form-group">
                        <label>Payment Status</label>
                        <select name="payment_status" required>
                            <option value="pending" <?= ($order['payment_status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= ($order['payment_status'] === 'paid') ? 'selected' : '' ?>>Paid (Confirmed)</option>
                            <option value="failed" <?= ($order['payment_status'] === 'failed') ? 'selected' : '' ?>>Failed</option>
                            <option value="refunded" <?= ($order['payment_status'] === 'refunded') ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-secondary btn-block">Update Payment Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
