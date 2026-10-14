<?php
/**
 * Admin Categories Index View
 * @var array $categories
 */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700;">Product Categories</h1>
        <span style="font-size: 13px; color: var(--text-muted);">Organize plants, tools, pots, seeds and fertilizers</span>
    </div>
    <a href="<?= url('/admin/categories/create') ?>" class="btn btn-primary btn-sm">+ Add New Category</a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Sort</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Associated Products</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 30px;">No categories found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $c): ?>
                            <tr>
                                <td><?= $c['sort_order'] ?></td>
                                <td><strong><?= e($c['name']) ?></strong></td>
                                <td><code><?= e($c['slug']) ?></code></td>
                                <td><?= $c['product_count'] ?? 0 ?> products</td>
                                <td>
                                    <?php if ($c['is_active']): ?>
                                        <span class="badge-status badge-delivered">Active</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-cancelled">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $c['is_featured'] ? '⭐ Yes' : 'No' ?></td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <a href="<?= url('/products?category=' . e($c['slug'])) ?>" target="_blank" class="btn btn-secondary btn-sm">👁️</a>
                                        <a href="<?= url('/admin/categories/edit/' . $c['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <form action="<?= url('/admin/categories/delete/' . $c['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this category? (Only possible if no products are assigned)');">
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
