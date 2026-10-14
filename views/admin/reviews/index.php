<?php
/**
 * Admin Product Reviews Moderation View
 * @var array $reviews
 */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700;">Customer Reviews & Ratings</h1>
        <span style="font-size: 13px; color: var(--text-muted);">Moderate product reviews submitted by customers</span>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Reviewer</th>
                        <th>Rating</th>
                        <th>Headline & Comment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Moderation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 30px;">No reviews submitted yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $r): ?>
                            <tr>
                                <td>
                                    <strong><?= e($r['product_name']) ?></strong>
                                </td>
                                <td>
                                    <?= e($r['customer_name']) ?><br>
                                    <small style="color:var(--text-muted);"><?= e($r['customer_email']) ?></small>
                                </td>
                                <td style="color:#ffb703; font-weight:700;">
                                    ★ <?= $r['rating'] ?>/5
                                </td>
                                <td style="max-width: 340px;">
                                    <?php if (!empty($r['title'])): ?>
                                        <strong><?= e($r['title']) ?></strong><br>
                                    <?php endif; ?>
                                    <span style="font-size: 13px; color: var(--text-main);"><?= e($r['comment']) ?></span>
                                </td>
                                <td style="font-size: 12px;"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                                <td>
                                    <span class="badge-status badge-<?= $r['status'] === 'approved' ? 'delivered' : ($r['status'] === 'rejected' ? 'cancelled' : 'pending') ?>">
                                        <?= ucfirst($r['status']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <?php if ($r['status'] !== 'approved'): ?>
                                            <form action="<?= url('/admin/reviews/approve/' . $r['id']) ?>" method="POST" style="display:inline;">
                                                <?= \Core\CSRF::field() ?>
                                                <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--success);">Approve</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($r['status'] !== 'rejected'): ?>
                                            <form action="<?= url('/admin/reviews/reject/' . $r['id']) ?>" method="POST" style="display:inline;">
                                                <?= \Core\CSRF::field() ?>
                                                <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--warning);">Reject</button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?= url('/admin/reviews/delete/' . $r['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this review permanently?');">
                                            <?= \Core\CSRF::field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm">✕</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
