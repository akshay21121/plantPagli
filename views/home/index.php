<?php
/**
 * PlantPuglu Homepage View
 * @var array $heroBanners
 * @var array $featuredCategories
 * @var array $categories
 * @var array $bestsellers
 * @var array $featuredProducts
 * @var array $airPurifying
 * @var array $combos
 */
?>

<div class="container">
    <!-- Hero Banner -->
    <section class="hero-slider">
        <div class="hero-slide">
            <div class="hero-content">
                <div class="hero-badge">🌿 Nursery Fresh Guarantee</div>
                <h1>Bring Nature Inside Your Home</h1>
                <p>Curated healthy houseplants, fragrant traditional blooms, grafted terrace fruit trees, and organic plant care. Delivered securely across India with soil-lock protection.</p>
                <div class="hero-buttons">
                    <a href="<?= url('/products') ?>" class="btn btn-white">Shop All Plants &rarr;</a>
                    <a href="<?= url('/products?category=air-purifying-plants') ?>" class="btn btn-outline" style="border-color:#fff;color:#fff;">Air Purifiers</a>
                </div>
            </div>
            <div class="hero-visual">
                <img src="<?= uploadUrl('banners/hero-indoor-plants.jpg') ?>" alt="Fresh Indoor Plants Collection">
            </div>
        </div>
    </section>

    <!-- Trust & Value Proposition -->
    <section class="trust-banner">
        <div class="trust-item">
            <div class="trust-icon">📦</div>
            <div class="trust-text">
                <h4>Safe Transit Guarantee</h4>
                <p>Multi-layer shock-proof packaging</p>
            </div>
        </div>
        <div class="trust-item">
            <div class="trust-icon">🔄</div>
            <div class="trust-text">
                <h4>Free Replacement</h4>
                <p>If damaged during courier transit</p>
            </div>
        </div>
        <div class="trust-item">
            <div class="trust-icon">🚚</div>
            <div class="trust-text">
                <h4>Free Shipping</h4>
                <p>On all orders above ₹999 across India</p>
            </div>
        </div>
        <div class="trust-item">
            <div class="trust-icon">💬</div>
            <div class="trust-text">
                <h4>Free Plant Doctor Support</h4>
                <p>WhatsApp plant care advice for life</p>
            </div>
        </div>
    </section>

    <!-- Shop by Category Section -->
    <section class="categories-section" style="margin-bottom: 50px;">
        <div class="section-header">
            <div class="section-title">
                <h2>Explore Plant Categories</h2>
                <p>Everything you need to cultivate your personal green paradise</p>
            </div>
            <a href="<?= url('/products') ?>" class="view-all">All Categories &rarr;</a>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('/products?category=' . urlencode($cat['slug'])) ?>" class="category-card">
                    <div class="cat-icon-wrap">
                        <span style="font-size:28px;">
                            <?php
                            $iconMap = [
                                'indoor-plants' => '🪴',
                                'outdoor-plants' => '☀️',
                                'air-purifying-plants' => '🍃',
                                'flowering-plants' => '🌸',
                                'fruit-plants' => '🥭',
                                'vegetable-plants' => '🥕',
                                'seeds' => '🌱',
                                'pots-and-planters' => '🏺',
                                'soil-and-potting-mix' => '🪨',
                                'fertilizers-and-plant-nutrition' => '⚡',
                                'gardening-tools' => '✂️',
                                'garden-decor' => '🦉',
                                'plant-combos-and-gift-plants' => '🎁',
                            ];
                            echo $iconMap[$cat['slug']] ?? '🌿';
                            ?>
                        </span>
                    </div>
                    <h3><?= e($cat['name']) ?></h3>
                    <span class="count"><?= $cat['product_count'] ?? '10+' ?> items</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Bestseller Plants Section -->
    <section class="bestsellers-section" style="margin-bottom: 60px;">
        <div class="section-header">
            <div class="section-title">
                <h2>India's Bestselling Live Plants</h2>
                <p>Loved by thousands of urban gardeners for their resilience and elegance</p>
            </div>
            <a href="<?= url('/products?bestseller=1') ?>" class="view-all">View All Bestsellers &rarr;</a>
        </div>

        <div class="products-grid">
            <?php foreach ($bestsellers as $prod): ?>
                <div class="product-card">
                    <div class="product-badge-wrap">
                        <span class="badge badge-bestseller">Bestseller</span>
                        <?php if (!empty($prod['compare_price']) && $prod['compare_price'] > $prod['price']): ?>
                            <?php $pct = round((($prod['compare_price'] - $prod['price']) / $prod['compare_price']) * 100); ?>
                            <span class="badge badge-sale"><?= $pct ?>% OFF</span>
                        <?php endif; ?>
                    </div>

                    <button class="product-wishlist-btn btn-wishlist-toggle" data-product-id="<?= $prod['id'] ?>" title="Save to Wishlist">
                        🤍
                    </button>

                    <a href="<?= url('/product/' . e($prod['slug'])) ?>" class="product-thumb">
                        <img src="<?= productImage($prod['primary_image'] ?? '') ?>" alt="<?= e($prod['name']) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= asset('images/plant-placeholder.svg') ?>'">
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
    </section>

    <!-- Mid Promotional Banner -->
    <section style="background: linear-gradient(135deg, #f6f0e2 0%, #faedcd 100%); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; margin-bottom: 60px; display: flex; align-items: center; justify-content: space-between; gap: 30px;">
        <div style="max-width: 550px;">
            <span style="font-size:12px; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:1px; background:rgba(27,67,50,0.1); padding:4px 10px; border-radius:20px;">Desk & Workstation Greenery</span>
            <h2 style="font-size:32px; margin: 12px 0; color:var(--primary-dark);">Breathe Pure Air While You Work</h2>
            <p style="color:var(--text-muted); font-size:15px; margin-bottom: 22px;">Transform stressful work hours into serene moments. Our curated NASA-tested low-maintenance desk plants naturally absorb screen radiation, reduce carbon dioxide, and boost focus.</p>
            <a href="<?= url('/products?category=air-purifying-plants') ?>" class="btn btn-primary">Shop Air Purifiers &rarr;</a>
        </div>
        <div style="flex-shrink: 0; max-width: 320px;">
            <img src="<?= uploadUrl('banners/promo-combo.jpg') ?>" alt="Desk Plants Combo" style="max-height: 240px; object-fit: contain;">
        </div>
    </section>

    <!-- NASA Air-Purifying & Flowering Section -->
    <section class="air-purifying-section" style="margin-bottom: 60px;">
        <div class="section-header">
            <div class="section-title">
                <h2>Air-Purifying Champions</h2>
                <p>Natural oxygen factories validated by environmental science</p>
            </div>
            <a href="<?= url('/products?category=air-purifying-plants') ?>" class="view-all">See All Air Cleaners &rarr;</a>
        </div>

        <div class="products-grid">
            <?php foreach ($airPurifying as $prod): ?>
                <div class="product-card">
                    <div class="product-badge-wrap">
                        <span class="badge badge-featured">NASA Approved</span>
                    </div>

                    <button class="product-wishlist-btn btn-wishlist-toggle" data-product-id="<?= $prod['id'] ?>" title="Save to Wishlist">
                        🤍
                    </button>

                    <a href="<?= url('/product/' . e($prod['slug'])) ?>" class="product-thumb">
                        <img src="<?= productImage($prod['primary_image'] ?? '') ?>" alt="<?= e($prod['name']) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= asset('images/plant-placeholder.svg') ?>'">
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
    </section>

    <!-- Customer Testimonials Section -->
    <section style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 45px; margin-bottom: 50px;">
        <div class="section-header" style="justify-content: center; text-align: center; margin-bottom: 35px;">
            <div class="section-title">
                <h2>Verified Indian Plant Parents</h2>
                <p>Over 15,000+ happy plant lovers trust Plantpagli across Mumbai, Delhi, Bengaluru, Pune, Hyderabad & Kolkata</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <div style="background:var(--cream-bg); padding:24px; border-radius:var(--radius-md); border:1px solid var(--cream-border);">
                <div style="color:#ffb703; margin-bottom:10px;">★★★★★</div>
                <p style="font-style:italic; font-size:14px; margin-bottom:14px;">"The packaging is pure genius. Shipped all the way from nursery to Koramangala, Bengaluru without a leaf bruised. The Snake Plant arrived glowing green!"</p>
                <strong>— Ananya S., Bengaluru</strong>
            </div>

            <div style="background:var(--cream-bg); padding:24px; border-radius:var(--radius-md); border:1px solid var(--cream-border);">
                <div style="color:#ffb703; margin-bottom:10px;">★★★★★</div>
                <p style="font-style:italic; font-size:14px; margin-bottom:14px;">"Ordered the Alphonso Mango grafted sapling and Arabian Mogra. Both arrived with vigorous healthy root balls and started sprouting new leaves within a week."</p>
                <strong>— Rajesh K., Pune</strong>
            </div>

            <div style="background:var(--cream-bg); padding:24px; border-radius:var(--radius-md); border:1px solid var(--cream-border);">
                <div style="color:#ffb703; margin-bottom:10px;">★★★★★</div>
                <p style="font-style:italic; font-size:14px; margin-bottom:14px;">"Best customer care on WhatsApp! They guided me on the right watering schedule for my Peace Lily during the hot summer. 10/10 experience."</p>
                <strong>— Pooja M., New Delhi</strong>
            </div>
        </div>
    </section>
</div>
