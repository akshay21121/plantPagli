<?php
/**
 * Admin Products Management Listing
 * @var array $products
 * @var int $total
 * @var int $currentPage
 * @var int $totalPages
 * @var string $search
 * @var int|null $categoryId
 * @var array $categories
 */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700;">Products & Plant Catalog</h1>
        <span style="font-size: 13px; color: var(--text-muted);">Manage inventory, pricing, care guides, and images</span>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= url('/admin/products/export-csv') ?>" class="btn btn-secondary btn-sm">📥 Export CSV</a>
        <a href="<?= url('/admin/products/create') ?>" class="btn btn-primary btn-sm">+ Add New Product</a>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px;">
        <form action="<?= url('/admin/products') ?>" method="GET" style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" placeholder="Search by name, SKU..." value="<?= e($search) ?>" style="padding: 8px 14px; font-size: 13.5px;">
            </div>

            <div style="width: 220px;">
                <select name="category_id" style="padding: 8px 12px; font-size: 13.5px;">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId === (int)$cat['id']) ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if (!empty($search) || !empty($categoryId)): ?>
                <a href="<?= url('/admin/products') ?>" class="btn btn-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Products Table Card -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Name & SKU</th>
                        <th>Category</th>
                        <th>Price (₹)</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Rating</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" style="text-align: center; padding: 30px;">No products found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <div style="width: 44px; height: 44px; background: #f4efe4; border-radius: 6px; padding: 4px;">
                                        <img src="<?= productImage($p['primary_image'] ?? '') ?>" alt="<?= e($p['name']) ?>" style="width:100%;height:100%;object-fit:contain;" onerror="this.onerror=null;this.src='<?= asset('images/plant-placeholder.svg') ?>'">
                                    </div>
                                </td>
                                <td>
                                    <strong><?= e($p['name']) ?></strong>
                                    <div style="font-size: 11.5px; color: var(--text-muted);">SKU: <?= e($p['sku']) ?></div>
                                </td>
                                <td><?= e($p['category_name']) ?></td>
                                <td style="font-weight: 700;">
                                    <?= formatPrice($p['price']) ?>
                                    <?php if (!empty($p['compare_price'])): ?>
                                        <div style="font-size: 11px; text-decoration: line-through; color: var(--text-muted);"><?= formatPrice($p['compare_price']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: <?= ($p['stock_quantity'] <= 5) ? 'var(--danger)' : 'var(--success)' ?>;">
                                        <?= $p['stock_quantity'] ?> units
                                    </span>
                                </td>
                                <td>
                                    <?php if ($p['is_active']): ?>
                                        <span class="badge-status badge-delivered">Active</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-cancelled">Draft / Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>★ <?= $p['average_rating'] ?> (<?= $p['reviews_count'] ?>)</td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <a href="<?= url('/product/' . e($p['slug'])) ?>" target="_blank" class="btn btn-secondary btn-sm" title="View on Storefront">👁️</a>
                                        <a href="<?= url('/admin/products/edit/' . $p['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <form action="<?= url('/admin/products/delete/' . $p['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
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

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
        <?php for ($pg = 1; $pg <= $totalPages; $pg++): ?>
            <a href="<?= url('/admin/products?' . http_build_query(array_merge($_GET, ['page' => $pg]))) ?>" class="btn btn-sm <?= ($pg === $currentPage) ? 'btn-primary' : 'btn-secondary' ?>">
                <?= $pg ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
