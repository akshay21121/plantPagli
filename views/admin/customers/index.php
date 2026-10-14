<?php
/**
 * Admin Customers Listing View
 * @var array $customers
 * @var int $total
 * @var int $currentPage
 * @var int $totalPages
 */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700;">Customer Directory (<?= $total ?>)</h1>
        <span style="font-size: 13px; color: var(--text-muted);">View registered plant parents and purchase histories</span>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Email Address</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Lifetime Spend (INR)</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 30px;">No registered customers found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><strong><?= e($c['name']) ?></strong></td>
                                <td><a href="mailto:<?= e($c['email']) ?>"><?= e($c['email']) ?></a></td>
                                <td><?= e($c['phone'] ?? 'N/A') ?></td>
                                <td><strong><?= $c['total_orders'] ?></strong> orders</td>
                                <td style="font-weight: 700; color: var(--primary);"><?= formatPrice($c['total_spent']) ?></td>
                                <td style="font-size: 12.5px;"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                                <td>
                                    <span class="badge-status badge-delivered"><?= ucfirst($c['status']) ?></span>
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
            <a href="<?= url('/admin/customers?page=' . $pg) ?>" class="btn btn-sm <?= ($pg === $currentPage) ? 'btn-primary' : 'btn-secondary' ?>">
                <?= $pg ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
