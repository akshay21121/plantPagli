<?php
/**
 * Admin Coupons Listing View
 * @var array $coupons
 */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700;">Discount Coupons & Promos</h1>
        <span style="font-size: 13px; color: var(--text-muted);">Manage percentage and flat rupee discounts for promotions</span>
    </div>
    <a href="<?= url('/admin/coupons/create') ?>" class="btn btn-primary btn-sm">+ Create New Coupon</a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Promo Code</th>
                        <th>Discount Value</th>
                        <th>Min Order Required</th>
                        <th>Max Cap (INR)</th>
                        <th>Usage Count</th>
                        <th>Expires On</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($coupons)): ?>
                        <tr><td colspan="8" style="text-align: center; padding: 30px;">No coupons found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $cp): ?>
                            <tr>
                                <td>
                                    <strong style="font-size:15px; letter-spacing:0.5px;"><code><?= e($cp['code']) ?></code></strong>
                                    <?php if (!empty($cp['description'])): ?>
                                        <div style="font-size:11.5px; color:var(--text-muted);"><?= e($cp['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color:var(--primary);">
                                        <?= ($cp['discount_type'] === 'percentage') ? ((float)$cp['discount_value'] . '% OFF') : formatPrice($cp['discount_value']) . ' OFF' ?>
                                    </strong>
                                </td>
                                <td><?= formatPrice($cp['min_order_amount']) ?></td>
                                <td><?= !empty($cp['max_discount_amount']) ? formatPrice($cp['max_discount_amount']) : 'No Limit' ?></td>
                                <td><?= $cp['used_count'] ?> / <?= $cp['usage_limit'] ?> used</td>
                                <td style="font-size: 12.5px;"><?= !empty($cp['expires_at']) ? date('d M Y', strtotime($cp['expires_at'])) : 'Never' ?></td>
                                <td>
                                    <span class="badge-status badge-<?= $cp['is_active'] ? 'delivered' : 'cancelled' ?>">
                                        <?= $cp['is_active'] ? 'Active' : 'Disabled' ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <form action="<?= url('/admin/coupons/delete/' . $cp['id']) ?>" method="POST" onsubmit="return confirm('Delete this coupon?');">
                                        <?= \Core\CSRF::field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
