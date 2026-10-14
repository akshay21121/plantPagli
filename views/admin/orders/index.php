<?php
/**
 * Admin Orders Listing View
 * @var array $orders
 * @var int $total
 * @var int $currentPage
 * @var int $totalPages
 * @var string|null $status
 * @var string|null $paymentStatus
 * @var string $search
 */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700;">Customer Orders</h1>
        <span style="font-size: 13px; color: var(--text-muted);">View transactions, update courier tracking numbers, and manage shipments</span>
    </div>
    <a href="<?= url('/admin/orders/export-csv') ?>" class="btn btn-secondary btn-sm">📥 Export All Orders (CSV)</a>
</div>

<!-- Filters Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px;">
        <form action="<?= url('/admin/orders') ?>" method="GET" style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" placeholder="Search order number, customer name, email, phone..." value="<?= e($search) ?>" style="padding: 8px 14px; font-size: 13.5px;">
            </div>

            <div style="width: 180px;">
                <select name="status" style="padding: 8px 12px; font-size: 13.5px;">
                    <option value="">All Order Statuses</option>
                    <option value="pending" <?= ($status === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= ($status === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                    <option value="shipped" <?= ($status === 'shipped') ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= ($status === 'delivered') ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= ($status === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div style="width: 180px;">
                <select name="payment_status" style="padding: 8px 12px; font-size: 13.5px;">
                    <option value="">All Payment Statuses</option>
                    <option value="pending" <?= ($paymentStatus === 'pending') ? 'selected' : '' ?>>Payment Pending</option>
                    <option value="paid" <?= ($paymentStatus === 'paid') ? 'selected' : '' ?>>Paid (Captured)</option>
                    <option value="failed" <?= ($paymentStatus === 'failed') ? 'selected' : '' ?>>Failed</option>
                    <option value="refunded" <?= ($paymentStatus === 'refunded') ? 'selected' : '' ?>>Refunded</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if (!empty($search) || !empty($status) || !empty($paymentStatus)): ?>
                <a href="<?= url('/admin/orders') ?>" class="btn btn-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Items</th>
                        <th>Total (INR)</th>
                        <th>Payment</th>
                        <th>Shipment Status</th>
                        <th>Date</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="9" style="text-align: center; padding: 30px;">No matching orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><strong>#<?= e($o['order_number']) ?></strong></td>
                                <td>
                                    <strong><?= e($o['customer_name']) ?></strong><br>
                                    <small style="color:var(--text-muted);"><?= e($o['customer_phone']) ?></small>
                                </td>
                                <td><?= e($o['shipping_city']) ?>, <?= e($o['shipping_state']) ?></td>
                                <td><?= $o['total_items_count'] ?? 1 ?> items</td>
                                <td style="font-weight: 700; color: var(--primary-dark);"><?= formatPrice($o['total_amount']) ?></td>
                                <td>
                                    <span class="badge-status badge-<?= $o['payment_status'] ?>">
                                        <?= strtoupper($o['payment_method']) ?> • <?= ucfirst($o['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status badge-<?= $o['order_status'] ?>">
                                        <?= ucfirst($o['order_status']) ?>
                                    </span>
                                </td>
                                <td style="font-size: 12px;"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></td>
                                <td style="text-align: right;">
                                    <a href="<?= url('/admin/orders/' . $o['id']) ?>" class="btn btn-secondary btn-sm">View / Manage</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
        <?php for ($pg = 1; $pg <= $totalPages; $pg++): ?>
            <a href="<?= url('/admin/orders?' . http_build_query(array_merge($_GET, ['page' => $pg]))) ?>" class="btn btn-sm <?= ($pg === $currentPage) ? 'btn-primary' : 'btn-secondary' ?>">
                <?= $pg ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
