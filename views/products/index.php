<?php
/**
 * PlantPuglu Products Catalog View
 * @var array $products
 * @var int $totalProducts
 * @var int $currentPage
 * @var int $totalPages
 * @var array $filters
 * @var array $categories
 * @var array|null $currentCategory
 */
?>

<div class="container" style="margin-top: 24px;">
    <!-- Breadcrumb -->
    <nav style="font-size: 13.5px; margin-bottom: 20px; color: var(--text-muted);">
        <a href="<?= url('/') ?>">Home</a> &rsaquo;
        <a href="<?= url('/products') ?>">Plants & Supplies</a>
        <?php if ($currentCategory): ?>
            &rsaquo; <strong><?= e($currentCategory['name']) ?></strong>
        <?php elseif (!empty($filters['search'])): ?>
            &rsaquo; <strong>Search: "<?= e($filters['search']) ?>"</strong>
        <?php endif; ?>
    </nav>

    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 32px; align-items: flex-start; margin-bottom: 60px;">
        <!-- Sidebar Filters -->
        <aside style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 22px; position: sticky; top: 100px;">
            <h3 style="font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--cream-border); padding-bottom: 10px;">Filters</h3>

            <!-- Category Filter -->
            <div style="margin-bottom: 24px;">
                <label style="font-weight: 700; margin-bottom: 10px; display: block;">Categories</label>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 13.5px;">
                    <li>
                        <a href="<?= url('/products') ?>" style="display: flex; justify-content: space-between; font-weight: <?= empty($filters['category']) ? '700; color:var(--primary);' : 'normal' ?>;">
                            <span>All Plants</span>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?= url('/products?category=' . urlencode($cat['slug'])) ?>" style="display: flex; justify-content: space-between; font-weight: <?= ($filters['category'] === $cat['slug']) ? '700; color:var(--primary);' : 'normal' ?>;">
                                <span><?= e($cat['name']) ?></span>
                                <span style="color:var(--text-muted); font-size:12px;">(<?= $cat['product_count'] ?? 0 ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Price Range Filter Form -->
            <form action="<?= url('/products') ?>" method="GET">
                <?php if (!empty($filters['category'])): ?>
                    <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
                <?php endif; ?>
                <?php if (!empty($filters['search'])): ?>
                    <input type="hidden" name="search" value="<?= e($filters['search']) ?>">
                <?php endif; ?>

                <div style="margin-bottom: 24px; border-top: 1px solid var(--cream-border); padding-top: 18px;">
                    <label style="font-weight: 700; margin-bottom: 10px; display: block;">Price Range (₹)</label>
                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 12px;">
                        <input type="number" name="min_price" placeholder="Min" value="<?= e($filters['min_price'] ?? '') ?>" style="padding: 7px 10px; font-size: 13px;">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Max" value="<?= e($filters['max_price'] ?? '') ?>" style="padding: 7px 10px; font-size: 13px;">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="in_stock" value="1" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>>
                        In Stock Only
                    </label>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary btn-sm btn-block">Apply Filters</button>
                    <a href="<?= url('/products') ?>" class="btn btn-outline btn-sm" style="padding: 6px 12px;">Reset</a>
                </div>
            </form>
        </aside>

        <!-- Product Listings Column -->
        <section>
            <!-- Listing Controls Bar -->
            <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">
                    Showing <strong><?= count($products) ?></strong> of <strong><?= $totalProducts ?></strong> items
                </span>

                <!-- Sort Filter -->
                <form action="<?= url('/products') ?>" method="GET" style="display: flex; align-items: center; gap: 10px;">
                    <?php if (!empty($filters['category'])): ?>
                        <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($filters['search'])): ?>
                        <input type="hidden" name="search" value="<?= e($filters['search']) ?>">
                    <?php endif; ?>

                    <label style="margin: 0; font-size: 13.5px; color: var(--text-muted); font-weight: 600;">Sort By:</label>
                    <select name="sort" onchange="this.form.submit()" style="padding: 6px 12px; font-size: 13.5px; width: auto;">
                        <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Newest Arrivals</option>
                        <option value="popular" <?= ($filters['sort'] === 'popular') ? 'selected' : '' ?>>Bestsellers</option>
                        <option value="price_asc" <?= ($filters['sort'] === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= ($filters['sort'] === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="rating" <?= ($filters['sort'] === 'rating') ? 'selected' : '' ?>>Highest Rated</option>
                    </select>
                </form>
            </div>

            <?php if (empty($products)): ?>
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 50px; text-align: center;">
                    <div style="font-size: 40px; margin-bottom: 14px;">🌱</div>
                    <h3>No Plants Found Matching Your Criteria</h3>
                    <p style="color: var(--text-muted); margin: 8px 0 20px;">Try adjusting your price range or search terms.</p>
                    <a href="<?= url('/products') ?>" class="btn btn-primary">Browse All Plants</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $prod): ?>
                        <div class="product-card">
                            <div class="product-badge-wrap">
                                <?php if ($prod['is_bestseller']): ?>
                                    <span class="badge badge-bestseller">Bestseller</span>
                                <?php elseif ($prod['is_featured']): ?>
                                    <span class="badge badge-featured">Featured</span>
                                <?php endif; ?>

                                <?php if (!empty($prod['compare_price']) && $prod['compare_price'] > $prod['price']): ?>
                                    <?php $pct = round((($prod['compare_price'] - $prod['price']) / $prod['compare_price']) * 100); ?>
                                    <span class="badge badge-sale"><?= $pct ?>% OFF</span>
                                <?php endif; ?>
                            </div>

                            <button class="product-wishlist-btn btn-wishlist-toggle" data-product-id="<?= $prod['id'] ?>" title="Save to Wishlist">
                                🤍
                            </button>

                            <a href="<?= url('/product/' . e($prod['slug'])) ?>" class="product-thumb">
                                <img src="<?= uploadUrl('products/' . ($prod['primary_image'] ?? 'plant-placeholder.svg')) ?>" alt="<?= e($prod['name']) ?>" loading="lazy">
                            </a>

                            <div class="product-info">
                                <span class="product-category-meta"><?= e($prod['category_name']) ?></span>
                                <h3 class="product-title">
                                    <a href="<?= url('/product/' . e($prod['slug'])) ?>"><?= e($prod['name']) ?></a>
                                </h3>

                                <div class="product-ratings">
                                    <span class="stars">★★★★★</span>
                                    <span class="reviews-num">(<?= $prod['reviews_count'] ?>)</span>
                                </div>

                                <div class="product-price-row">
                                    <span class="current-price"><?= formatPrice($prod['price']) ?></span>
                                    <?php if (!empty($prod['compare_price'])): ?>
                                        <span class="compare-price"><?= formatPrice($prod['compare_price']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="product-card-actions">
                                    <button type="button" class="btn btn-primary btn-sm btn-block btn-add-to-cart" data-product-id="<?= $prod['id'] ?>">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 30px;">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= url('/products?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1]))) ?>" class="btn btn-outline btn-sm">&larr; Prev</a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <a href="<?= url('/products?' . http_build_query(array_merge($_GET, ['page' => $p]))) ?>" class="btn btn-sm <?= ($p === $currentPage) ? 'btn-primary' : 'btn-outline' ?>" style="min-width: 36px;">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= url('/products?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1]))) ?>" class="btn btn-outline btn-sm">Next &rarr;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</div>
