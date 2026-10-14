<?php
/**
 * Customer Wishlist View
 * @var array $items
 */
?>

<div class="container" style="margin-top: 30px; margin-bottom: 70px;">
    <h1 style="font-size: 28px; margin-bottom: 8px;">My Saved Plants (<?= count($items) ?>)</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 28px;">Keep track of the green varieties you love and move them to cart whenever you are ready.</p>

    <?php if (empty($items)): ?>
        <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 60px; text-align: center;">
            <div style="font-size: 44px; margin-bottom: 14px;">🤍</div>
            <h2>Your Wishlist is Empty</h2>
            <p style="color: var(--text-muted); margin: 8px 0 24px;">Explore our lush indoor foliage, fruit trees, and gardening tools to save your favorites.</p>
            <a href="<?= url('/products') ?>" class="btn btn-primary">Discover Plants &rarr;</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($items as $prod): ?>
                <div class="product-card">
                    <form action="<?= url('/wishlist/remove') ?>" method="POST" style="position: absolute; top: 12px; right: 12px; z-index: 5;">
                        <?= \Core\CSRF::field() ?>
                        <input type="hidden" name="product_id" value="<?= $prod['product_id'] ?>">
                        <button type="submit" class="product-wishlist-btn" title="Remove from Wishlist" style="color: var(--danger);">
                            ❤️
                        </button>
                    </form>

                    <a href="<?= url('/product/' . e($prod['slug'])) ?>" class="product-thumb">
                        <img src="<?= uploadUrl('products/' . ($prod['primary_image'] ?? 'plant-placeholder.svg')) ?>" alt="<?= e($prod['name']) ?>" loading="lazy">
                    </a>

                    <div class="product-info">
                        <span class="product-category-meta"><?= e($prod['category_name']) ?></span>
                        <h3 class="product-title">
                            <a href="<?= url('/product/' . e($prod['slug'])) ?>"><?= e($prod['name']) ?></a>
                        </h3>

                        <div class="product-price-row">
                            <span class="current-price"><?= formatPrice($prod['price']) ?></span>
                            <?php if (!empty($prod['compare_price'])): ?>
                                <span class="compare-price"><?= formatPrice($prod['compare_price']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="product-card-actions">
                            <button type="button" class="btn btn-primary btn-sm btn-block btn-add-to-cart" data-product-id="<?= $prod['product_id'] ?>">
                                Move to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
