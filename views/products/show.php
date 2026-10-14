<?php
/**
 * PlantPuglu Single Product Detail View
 * @var array $product
 * @var array $relatedProducts
 * @var array $reviews
 */
?>

<div class="container" style="margin-top: 24px;">
    <!-- Breadcrumb -->
    <nav style="font-size: 13.5px; margin-bottom: 24px; color: var(--text-muted);">
        <a href="<?= url('/') ?>">Home</a> &rsaquo;
        <a href="<?= url('/products') ?>">Plants</a> &rsaquo;
        <a href="<?= url('/products?category=' . urlencode($product['category_slug'])) ?>"><?= e($product['category_name']) ?></a> &rsaquo;
        <strong><?= e($product['name']) ?></strong>
    </nav>

    <!-- Product Detail Layout -->
    <div class="product-detail-layout">
        <!-- Gallery Column -->
        <div class="detail-gallery">
            <div class="main-image-frame">
                <img id="main-product-image" src="<?= uploadUrl('products/' . ($product['primary_image'] ?? 'plant-placeholder.svg')) ?>" alt="<?= e($product['name']) ?>">
            </div>

            <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                <div class="thumbs-row">
                    <?php foreach ($product['images'] as $idx => $img): ?>
                        <div class="thumb-item <?= ($idx === 0) ? 'active' : '' ?>" data-full-image="<?= uploadUrl('products/' . $img['image_path']) ?>">
                            <img src="<?= uploadUrl('products/' . $img['image_path']) ?>" alt="View <?= $idx + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Summary & Purchase Column -->
        <div class="detail-summary">
            <span style="font-size:12.5px; font-weight:700; color:var(--accent); text-transform:uppercase; letter-spacing:0.5px;">
                <?= e($product['category_name']) ?>
            </span>
            <h1><?= e($product['name']) ?></h1>
            <div class="detail-sku">SKU: <strong><?= e($product['sku']) ?></strong> | In Stock: <strong><?= $product['stock_quantity'] ?> units</strong></div>

            <!-- Star Rating -->
            <div class="product-ratings" style="margin-bottom: 14px;">
                <span class="stars" style="font-size: 16px;">★★★★★</span>
                <span style="font-weight: 700; font-size: 14px;"><?= $product['average_rating'] ?></span>
                <span class="reviews-num">(<?= $product['reviews_count'] ?> customer reviews)</span>
            </div>

            <!-- Price Box -->
            <div class="detail-price-box">
                <span class="price-now" id="product-price-display"><?= formatPrice($product['price']) ?></span>
                <?php if (!empty($product['compare_price'])): ?>
                    <span class="price-was"><?= formatPrice($product['compare_price']) ?></span>
                    <?php $pct = round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100); ?>
                    <span class="badge badge-sale" style="font-size: 12px;"><?= $pct ?>% OFF</span>
                <?php endif; ?>
            </div>

            <p style="font-size: 15px; color: var(--text-main); margin-bottom: 20px; line-height: 1.6;">
                <?= e($product['short_description'] ?? '') ?>
            </p>

            <!-- Variants Selection if present -->
            <?php if (!empty($product['variants'])): ?>
                <div class="variant-selector">
                    <label>Choose Pot / Size Option:</label>
                    <div class="variant-options">
                        <button type="button" class="variant-btn active" data-variant-id="" data-price="<?= formatPrice($product['price']) ?>">
                            Standard Plant
                        </button>
                        <?php foreach ($product['variants'] as $variant): ?>
                            <button type="button" class="variant-btn" data-variant-id="<?= $variant['id'] ?>" data-price="<?= formatPrice($variant['price']) ?>">
                                <?= e($variant['name']) ?> (+<?= formatPrice($variant['price']) ?>)
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Add to Cart / Purchase Row -->
            <form action="<?= url('/cart/add') ?>" method="POST" id="product-purchase-form">
                <?= \Core\CSRF::field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="variant_id" id="selected-variant-id" value="">

                <div class="purchase-row">
                    <div class="qty-control" style="border-radius: 30px;">
                        <button type="button" class="qty-btn qty-minus">−</button>
                        <input type="number" name="quantity" class="qty-input" id="product-qty-input" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                        <button type="button" class="qty-btn qty-plus">+</button>
                    </div>

                    <button type="button" class="btn btn-primary btn-add-to-cart" data-product-id="<?= $product['id'] ?>" style="flex: 1.5; padding: 14px 28px;">
                        <span>🛒 Add to Cart</span>
                    </button>

                    <button type="button" class="btn btn-outline btn-wishlist-toggle" data-product-id="<?= $product['id'] ?>" style="width: 48px; height: 48px; border-radius: 50%; padding: 0;" title="Save to Wishlist">
                        🤍
                    </button>
                </div>
            </form>

            <!-- Quick Specs Grid -->
            <div class="spec-grid">
                <?php if (!empty($product['plant_height'])): ?>
                    <div class="spec-item">
                        <span class="label">Plant Height</span>
                        <span class="val"><?= e($product['plant_height']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($product['pot_size'])): ?>
                    <div class="spec-item">
                        <span class="label">Pot Size</span>
                        <span class="val"><?= e($product['pot_size']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($product['sunlight'])): ?>
                    <div class="spec-item">
                        <span class="label">Sunlight</span>
                        <span class="val"><?= e($product['sunlight']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($product['watering'])): ?>
                    <div class="spec-item">
                        <span class="label">Watering</span>
                        <span class="val"><?= e($product['watering']) ?></span>
                    </div>
                <?php endif; ?>

                <div class="spec-item">
                    <span class="label">Maintenance</span>
                    <span class="val"><?= e($product['maintenance'] ?? 'Low') ?></span>
                </div>

                <div class="spec-item">
                    <span class="label">Pet Friendly</span>
                    <span class="val"><?= !empty($product['pet_friendly']) ? 'Yes (Safe)' : 'No (Keep Away)' ?></span>
                </div>
            </div>

            <!-- Plant Care Instruction Box -->
            <?php if (!empty($product['care_instructions'])): ?>
                <div class="care-box">
                    <h4><span>🌱</span> Horticulturist Care Guide</h4>
                    <p><?= nl2br(e($product['care_instructions'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Long Description & Details -->
    <section style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; margin-bottom: 50px;">
        <h3 style="font-size: 22px; margin-bottom: 16px;">About this Live Nursery Plant</h3>
        <div style="font-size: 15px; line-height: 1.8; color: var(--text-main); margin-bottom: 24px;">
            <?= nl2br(e($product['description'] ?? '')) ?>
        </div>

        <?php if (!empty($product['shipping_info'])): ?>
            <div style="background: var(--cream-bg); padding: 18px 24px; border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
                <strong style="color:var(--primary-dark);">Transit & Packaging Information:</strong>
                <p style="margin-top: 4px; font-size: 13.5px;"><?= e($product['shipping_info']) ?></p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Customer Reviews Section -->
    <section style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; margin-bottom: 60px;">
        <div class="section-header" style="align-items: center;">
            <div class="section-title">
                <h2>Verified Customer Reviews (<?= count($reviews) ?>)</h2>
                <p>Authentic feedback from fellow plant parents across India</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 20px;">
            <!-- Reviews List -->
            <div>
                <?php if (empty($reviews)): ?>
                    <p style="color: var(--text-muted); font-style: italic;">No reviews yet for this plant. Be the first to share your experience!</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach ($reviews as $rev): ?>
                            <div style="background: var(--cream-bg); padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--cream-border);">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;">
                                    <strong style="font-size: 15px;"><?= e($rev['customer_name']) ?></strong>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                                </div>
                                <div style="color: #ffb703; font-size: 14px; margin-bottom: 6px;">
                                    <?= str_repeat('★', (int)$rev['rating']) . str_repeat('☆', 5 - (int)$rev['rating']) ?>
                                    <span style="color: var(--success); font-size: 11.5px; margin-left: 6px; font-weight: 700;">✓ Verified Plant Parent</span>
                                </div>
                                <?php if (!empty($rev['title'])): ?>
                                    <h4 style="font-size: 14px; margin-bottom: 4px;"><?= e($rev['title']) ?></h4>
                                <?php endif; ?>
                                <p style="font-size: 13.5px; color: var(--text-main); line-height: 1.5;"><?= e($rev['comment']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Write a Review Form -->
            <div style="background: var(--cream-bg); padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--cream-border);">
                <h3 style="font-size: 18px; margin-bottom: 16px;">Write a Plant Review</h3>
                <form action="<?= url('/reviews/store') ?>" method="POST">
                    <?= \Core\CSRF::field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                    <?php if (!\Core\Auth::check()): ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Your Name</label>
                                <input type="text" name="customer_name" required placeholder="e.g. Aditi Sharma">
                            </div>
                            <div class="form-group">
                                <label>Your Email</label>
                                <input type="email" name="customer_email" required placeholder="name@example.com">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Star Rating</label>
                        <select name="rating" required style="width: 100%;">
                            <option value="5">★★★★★ (5 Stars - Extremely Healthy Plant)</option>
                            <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                            <option value="3">★★★☆☆ (3 Stars - Average)</option>
                            <option value="2">★★☆☆☆ (2 Stars - Disappointed)</option>
                            <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Review Headline</label>
                        <input type="text" name="title" placeholder="e.g. Arrived in pristine condition!">
                    </div>

                    <div class="form-group">
                        <label>Your Review & Care Feedback</label>
                        <textarea name="comment" rows="4" required placeholder="Tell us how the plant arrived and how it is thriving in your space..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Submit Verified Review</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <section style="margin-bottom: 60px;">
            <div class="section-header">
                <div class="section-title">
                    <h2>You May Also Like</h2>
                    <p>Companion plants that thrive in similar lighting conditions</p>
                </div>
            </div>

            <div class="products-grid">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="product-card">
                        <a href="<?= url('/product/' . e($rel['slug'])) ?>" class="product-thumb">
                            <img src="<?= uploadUrl('products/' . ($rel['primary_image'] ?? 'plant-placeholder.svg')) ?>" alt="<?= e($rel['name']) ?>" loading="lazy">
                        </a>

                        <div class="product-info">
                            <span class="product-category-meta"><?= e($rel['category_name']) ?></span>
                            <h3 class="product-title">
                                <a href="<?= url('/product/' . e($rel['slug'])) ?>"><?= e($rel['name']) ?></a>
                            </h3>

                            <div class="product-price-row">
                                <span class="current-price"><?= formatPrice($rel['price']) ?></span>
                                <?php if (!empty($rel['compare_price'])): ?>
                                    <span class="compare-price"><?= formatPrice($rel['compare_price']) ?></span>
                                <?php endif; ?>
                            </div>

                            <a href="<?= url('/product/' . e($rel['slug'])) ?>" class="btn btn-outline btn-sm btn-block">View Plant</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
