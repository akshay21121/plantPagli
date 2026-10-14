<?php
/**
 * Admin Dashboard View
 * @var array $stats
 */
?>

<!-- Metric Stat Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">💰</div>
        <div class="stat-info">
            <span class="title">Total Revenue</span>
            <h3 class="value"><?= formatPrice($stats['total_sales']) ?></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">📦</div>
        <div class="stat-info">
            <span class="title">Total Orders</span>
            <h3 class="value"><?= $stats['total_orders'] ?></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">⏳</div>
        <div class="stat-info">
            <span class="title">Pending Orders</span>
            <h3 class="value"><?= $stats['pending_orders'] ?></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon rose">⚠️</div>
        <div class="stat-info">
            <span class="title">Low Stock (&le;5)</span>
            <h3 class="value"><?= $stats['low_stock_count'] ?></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">🪴</div>
        <div class="stat-info">
            <span class="title">Active Plants</span>
            <h3 class="value"><?= $stats['total_products'] ?></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div class="stat-info">
            <span class="title">Registered Customers</span>
            <h3 class="value"><?= $stats['total_customers'] ?></h3>
        </div>
    </div>
</div>

<!-- Quick Shortcuts -->
<div style="display: flex; gap: 12px; margin-bottom: 24px;">
    <a href="<?= url('/admin/products/create') ?>" class="btn btn-primary">+ Add New Plant / Product</a>
    <a href="<?= url('/admin/coupons/create') ?>" class="btn btn-secondary">+ Create Discount Coupon</a>
    <a href="<?= url('/admin/orders/export-csv') ?>" class="btn btn-secondary">📥 Export Orders CSV</a>
    <a href="<?= url('/admin/products/export-csv') ?>" class="btn btn-secondary">📥 Export Products CSV</a>
</div>

<!-- Recent Orders Table Card -->
<div class="card">
    <div class="card-header">
        <h3 style="font-size: 16px; font-weight: 700;">Recent Customer Orders</h3>
        <a href="<?= url('/admin/orders') ?>" style="font-size: 13px; font-weight: 700; color: var(--primary);">View All Orders &rarr;</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>City / State</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Order Status</th>
                        <th>Date</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stats['recent_orders'])): ?>
                        <tr><td colspan="8" style="text-align:center;padding:24px;">No orders found yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($stats['recent_orders'] as $ord): ?>
                            <tr>
                                <td><strong>#<?= e($ord['order_number']) ?></strong></td>
                                <td>
                                    <?= e($ord['customer_name']) ?><br>
                                    <small style="color:var(--text-muted);"><?= e($ord['customer_phone']) ?></small>
                                </td>
                                <td><?= e($ord['shipping_city']) ?>, <?= e($ord['shipping_state']) ?></td>
                                <td style="font-weight: 700;"><?= formatPrice($ord['total_amount']) ?></td>
                                <td>
                                    <span class="badge-status badge-<?= $ord['payment_status'] ?>">
                                        <?= strtoupper($ord['payment_method']) ?> • <?= ucfirst($ord['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status badge-<?= $ord['order_status'] ?>">
                                        <?= ucfirst($ord['order_status']) ?>
                                    </span>
                                </td>
                                <td style="font-size: 12.5px;"><?= date('d M, h:i A', strtotime($ord['created_at'])) ?></td>
                                <td style="text-align: right;">
                                    <a href="<?= url('/admin/orders/' . $ord['id']) ?>" class="btn btn-secondary btn-sm">Manage</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
