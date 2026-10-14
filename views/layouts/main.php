<?php
use Core\Auth;
use Core\Session;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Setting;

$cart = new Cart();
$cartSummary = $cart->getSummary();
$categoryModel = new Category();
$navCategories = $categoryModel->getAll(true);
$settingModel = new Setting();

$user = Auth::user();
$storeName = $settingModel->get('store_name', 'Plantpagli');
$storePhone = $settingModel->get('store_phone', '+91 98765 43210');
$storeEmail = $settingModel->get('store_email', 'care@plantpagli.com');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? "{$storeName} | Buy Live Plants & Gardening Supplies Online") ?></title>
    <meta name="description" content="<?= e($meta_description ?? 'India’s trusted online nursery. Shop lush indoor plants, flowering plants, fruit saplings, designer pots and organic fertilizers.') ?>">
    <?= \Core\CSRF::meta() ?>
    
    <!-- Open Graph SEO -->
    <meta property="og:title" content="<?= e($title ?? $storeName) ?>">
    <meta property="og:description" content="<?= e($meta_description ?? 'Buy live healthy plants online across India with safe packaging guarantee.') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url($_SERVER['REQUEST_URI'] ?? '')) ?>">
    <meta property="og:site_name" content="<?= e($storeName) ?>">

    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

    <!-- Promotional Announcement Bar -->
    <div class="announcement-bar">
        <span class="badge-promo">SPECIAL OFFER</span>
        <span>🌱 Monsoon Green Sale: Use Code <strong>PLANTGREEN</strong> for ₹150 OFF on ₹999+ | Free Shipping Across India</span>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container header-inner">
            <!-- Brand Logo -->
            <a href="<?= url('/') ?>" class="brand-logo" title="PlantPagli - For the love of plants">
                <img src="<?= asset('images/logo.png') ?>" alt="PlantPagli Logo" class="brand-logo-img">
                <div class="brand-logo-text-wrap">
                    <span class="brand-logo-text">PlantPagli<span class="dot">.</span></span>
                    <span class="brand-logo-tagline">For the love of plants 🌱</span>
                </div>
            </a>

            <!-- Search Bar -->
            <div class="header-search">
                <form action="<?= url('/products') ?>" method="GET">
                    <input type="text" name="search" placeholder="Search plants, pots, fertilizers, seeds..." value="<?= e($_GET['search'] ?? '') ?>" autocomplete="off" required>
                    <button type="submit" aria-label="Search">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </form>
            </div>

            <!-- Customer Actions -->
            <div class="header-actions">
                <?php if ($user): ?>
                    <a href="<?= url('/account') ?>" class="action-link" title="My Account">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span><?= e(explode(' ', $user['name'])[0]) ?></span>
                    </a>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="<?= url('/admin') ?>" class="btn btn-sm btn-outline" style="border-radius:20px;padding:4px 10px;font-size:11px;">Admin Panel</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="action-link">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Sign In</span>
                    </a>
                <?php endif; ?>

                <!-- Wishlist -->
                <a href="<?= url('/wishlist') ?>" class="action-link" title="My Wishlist">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </a>

                <!-- Cart -->
                <a href="<?= url('/cart') ?>" class="action-link" title="Shopping Cart">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <span class="action-badge cart-count-badge" style="<?= $cartSummary['total_items'] > 0 ? '' : 'display:none;' ?>">
                        <?= $cartSummary['total_items'] ?>
                    </span>
                </a>
            </div>
        </div>

        <!-- Category Navigation Menu with Smooth Scroll Arrows -->
        <nav class="category-nav">
            <div class="container category-nav-container">
                <button type="button" class="cat-nav-arrow cat-nav-prev" id="catNavPrev" aria-label="Scroll categories backward" title="Previous categories">
                    <span class="arrow-text">&laquo;&laquo;</span>
                </button>
                <div class="category-nav-scroll" id="catNavScroll">
                    <ul id="catNavList">
                        <li><a href="<?= url('/products') ?>" class="<?= empty($_GET['category']) && str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/products') ? 'active' : '' ?>">🌿 All Plants & Supplies</a></li>
                        <?php foreach ($navCategories as $cat): ?>
                            <li>
                                <a href="<?= url('/products?category=' . urlencode($cat['slug'])) ?>" class="<?= (($_GET['category'] ?? '') === $cat['slug']) ? 'active' : '' ?>">
                                    <?= e($cat['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="cat-nav-arrow cat-nav-next" id="catNavNext" aria-label="Scroll categories forward" title="Next categories">
                    <span class="arrow-text">&raquo;&raquo;</span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Main Content Container with Flash Notifications -->
    <main class="main-body-content">
        <div class="container" style="margin-top: 20px;">
            <?php if ($successMsg = Session::getFlash('success')): ?>
                <div class="alert alert-success">
                    <span>✅</span> <?= e($successMsg) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg = Session::getFlash('error')): ?>
                <div class="alert alert-error">
                    <span>⚠️</span> <?= e($errorMsg) ?>
                </div>
            <?php endif; ?>

            <?php if ($infoMsg = Session::getFlash('info')): ?>
                <div class="alert alert-info">
                    <span>ℹ️</span> <?= e($infoMsg) ?>
                </div>
            <?php endif; ?>
        </div>

        <?= $content ?>
    </main>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="<?= url('/') ?>" class="brand-logo brand-logo-footer" title="PlantPagli - For the love of plants" style="margin-bottom:14px;text-decoration:none;display:inline-flex;align-items:center;gap:12px;">
                        <img src="<?= asset('images/logo.png') ?>" alt="PlantPagli Logo" class="brand-logo-img" style="height:62px;width:auto;border-radius:12px;border-color:#52b788;background:#fff;">
                        <div class="brand-logo-text-wrap">
                            <span class="brand-logo-text" style="color:#ffffff;">PlantPagli<span class="dot" style="color:var(--accent);">.</span></span>
                            <span class="brand-logo-tagline" style="color:#a7f3d0;">For the love of plants 🌱</span>
                        </div>
                    </a>
                    <p>India’s most trusted online nursery & gardening haven. We deliver healthy, nursery-grown live plants directly to your doorstep in specially ventilated transit packaging.</p>
                    <p style="font-size:13px;color:#a3b8ab;">
                        📍 <?= e($settingModel->get('store_address', 'Bengaluru, India')) ?><br>
                        📞 Support: <?= e($storePhone) ?><br>
                        ✉️ Email: <?= e($storeEmail) ?>
                    </p>
                </div>

                <div class="footer-col">
                    <h4>Top Categories</h4>
                    <ul class="footer-links">
                        <li><a href="<?= url('/products?category=indoor-plants') ?>">Indoor Plants</a></li>
                        <li><a href="<?= url('/products?category=air-purifying-plants') ?>">Air-Purifying Plants</a></li>
                        <li><a href="<?= url('/products?category=flowering-plants') ?>">Flowering Plants</a></li>
                        <li><a href="<?= url('/products?category=fruit-plants') ?>">Grafted Fruit Plants</a></li>
                        <li><a href="<?= url('/products?category=pots-and-planters') ?>">Pots & Planters</a></li>
                        <li><a href="<?= url('/products?category=fertilizers-and-plant-nutrition') ?>">Organic Fertilizers</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Customer Care</h4>
                    <ul class="footer-links">
                        <li><a href="<?= url('/plant-care') ?>">Plant Care & Troubleshooting</a></li>
                        <li><a href="<?= url('/shipping-policy') ?>">Safe Shipping Policy</a></li>
                        <li><a href="<?= url('/refund-policy') ?>">Damage Replacement Policy</a></li>
                        <li><a href="<?= url('/contact') ?>">Contact Horticulturist Team</a></li>
                        <li><a href="<?= url('/about') ?>">About Plantpagli</a></li>
                        <li><a href="<?= url('/privacy-policy') ?>">Privacy & Terms</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>100% Safe Delivery</h4>
                    <p>Every plant travels in customized shock-absorbent corrugated packaging with moisture-sealed soil locks.</p>
                    <div style="background:rgba(255,255,255,0.08);padding:14px;border-radius:10px;font-size:12.5px;">
                        <strong style="color:var(--accent-soft);">🌱 Plant Guarantee:</strong> If your plant arrives damaged during courier transit, we replace it completely free of charge!
                    </div>
                    <p style="margin-top:12px;font-size:12px;color:#8e9e92;">Secured by Razorpay • UPI • Cards • NetBanking • COD</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= e($storeName) ?> India. All rights reserved. Original gardening e-commerce platform.</p>
                <p>Designed with care for Indian plant lovers 🌿</p>
            </div>
        </div>
    </footer>

    <!-- Interactive JavaScript -->
    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
    (function() {
        function initCategoryArrows() {
            var scroll = document.getElementById('catNavScroll');
            var prevBtn = document.getElementById('catNavPrev');
            var nextBtn = document.getElementById('catNavNext');
            if (!scroll || !prevBtn || !nextBtn) return;

            function updateArrows() {
                var scrollLeft = scroll.scrollLeft;
                var maxScrollLeft = scroll.scrollWidth - scroll.clientWidth;

                // Show arrows only when scrolling is actually possible (overflow exists)
                if (maxScrollLeft > 6) {
                    if (scrollLeft > 6) {
                        prevBtn.classList.add('visible');
                    } else {
                        prevBtn.classList.remove('visible');
                    }

                    if (scrollLeft < maxScrollLeft - 6) {
                        nextBtn.classList.add('visible');
                    } else {
                        nextBtn.classList.remove('visible');
                    }
                } else {
                    prevBtn.classList.remove('visible');
                    nextBtn.classList.remove('visible');
                }
            }

            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                scroll.scrollBy({ left: -260, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                scroll.scrollBy({ left: 260, behavior: 'smooth' });
            });

            scroll.addEventListener('scroll', updateArrows, { passive: true });
            window.addEventListener('resize', updateArrows);

            // Check immediately and after fonts/DOM paint
            updateArrows();
            setTimeout(updateArrows, 150);
            setTimeout(updateArrows, 600);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCategoryArrows);
        } else {
            initCategoryArrows();
        }
    })();
    </script>
</body>
</html>
