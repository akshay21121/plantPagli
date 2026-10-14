<?php
/**
 * PlantPuglu Shopping Cart View
 * @var array $cart
 */
?>

<div class="container" style="margin-top: 24px;">
    <h1 style="font-size: 28px; margin-bottom: 24px;">Your Shopping Basket (<?= $cart['total_items'] ?> items)</h1>

    <?php if (empty($cart['items'])): ?>
        <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 60px; text-align: center; margin: 30px 0;">
            <div style="font-size: 48px; margin-bottom: 16px;">🪴</div>
            <h2>Your Plant Cart is Empty</h2>
            <p style="color: var(--text-muted); margin: 8px 0 24px;">Your space deserves a touch of living greenery. Explore our bestselling nursery varieties.</p>
            <a href="<?= url('/products') ?>" class="btn btn-primary">Start Plant Shopping &rarr;</a>
        </div>
    <?php else: ?>
        <!-- Free Shipping Progress Tracker -->
        <div class="shipping-progress-box">
            <?php if ($cart['amount_left_for_free_shipping'] > 0): ?>
                <span>🚚 Add <strong><?= formatPrice($cart['amount_left_for_free_shipping']) ?></strong> more to your cart for <strong>FREE Doorstep Delivery across India!</strong></span>
            <?php else: ?>
                <span>🎉 Congratulations! Your order qualifies for <strong>FREE Doorstep Delivery across India!</strong></span>
            <?php endif; ?>

            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" id="free-shipping-progress" style="width: <?= $cart['free_shipping_progress'] ?>%;"></div>
            </div>
        </div>

        <div class="cart-layout">
            <!-- Line Items Table Card -->
            <div class="cart-items-card">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Plant / Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th style="text-align: right;">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart['items'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="cart-prod-cell">
                                        <div class="cart-prod-thumb">
                                            <img src="<?= uploadUrl('products/' . ($item['product_image'] ?? 'plant-placeholder.svg')) ?>" alt="<?= e($item['product_name']) ?>">
                                        </div>
                                        <div>
                                            <a href="<?= url('/product/' . e($item['product_slug'])) ?>" style="font-weight: 700; font-size: 14.5px;">
                                                <?= e($item['product_name']) ?>
                                            </a>
                                            <?php if (!empty($item['variant_name'])): ?>
                                                <div style="font-size: 12px; color: var(--text-muted);"><?= e($item['variant_name']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= formatPrice($item['price']) ?></strong>
                                </td>
                                <td>
                                    <div class="qty-control" data-cart-item-id="<?= $item['id'] ?>" style="width: 110px;">
                                        <button type="button" class="qty-btn qty-minus">−</button>
                                        <input type="number" class="qty-input" value="<?= $item['quantity'] ?>" min="1">
                                        <button type="button" class="qty-btn qty-plus">+</button>
                                    </div>
                                </td>
                                <td style="text-align: right; font-weight: 800; font-size: 15px; color: var(--primary-dark);">
                                    <?= formatPrice($item['price'] * $item['quantity']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <form action="<?= url('/cart/remove') ?>" method="POST" style="display:inline;">
                                        <?= \Core\CSRF::field() ?>
                                        <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                                        <button type="submit" style="background:none;border:none;color:var(--text-light);cursor:pointer;font-size:16px;" title="Remove Item">✕</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
                    <a href="<?= url('/products') ?>" style="font-weight: 600; color: var(--primary); font-size: 14px;">
                        &larr; Continue Shopping for Plants
                    </a>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="summary-card">
                <h3 style="font-size: 18px; margin-bottom: 18px; border-bottom: 1px solid var(--cream-border); padding-bottom: 12px;">Order Summary</h3>

                <div class="summary-row">
                    <span>Items Subtotal</span>
                    <strong id="cart-subtotal"><?= formatPrice($cart['subtotal']) ?></strong>
                </div>

                <div class="summary-row">
                    <span>Standard Shipping</span>
                    <strong id="cart-shipping-fee">
                        <?= ($cart['shipping_fee'] == 0) ? '<span style="color:var(--success);">FREE</span>' : formatPrice($cart['shipping_fee']) ?>
                    </strong>
                </div>

                <?php if ($cart['discount_amount'] > 0): ?>
                    <div class="summary-row" style="color: var(--success);">
                        <span>Coupon Discount (<?= e($cart['coupon_code']) ?>)</span>
                        <strong>-<?= formatPrice($cart['discount_amount']) ?></strong>
                    </div>
                <?php endif; ?>

                <div class="summary-row total-row">
                    <span>Grand Total (INR)</span>
                    <span id="cart-grand-total"><?= formatPrice($cart['grand_total']) ?></span>
                </div>

                <!-- Coupon Form -->
                <div style="margin: 20px 0; border-top: 1px solid var(--cream-border); padding-top: 16px;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 6px; display: block;">Have a Promo Coupon?</label>
                    <?php if (!empty($cart['coupon_code'])): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: var(--accent-soft); padding: 8px 12px; border-radius: 6px; font-size: 13px;">
                            <span>Applied: <strong><?= e($cart['coupon_code']) ?></strong></span>
                            <form action="<?= url('/cart/coupon/remove') ?>" method="POST" style="margin:0;">
                                <?= \Core\CSRF::field() ?>
                                <button type="submit" style="background:none;border:none;color:var(--danger);font-weight:700;cursor:pointer;">Remove</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <form id="coupon-apply-form" class="coupon-form" action="<?= url('/cart/coupon/apply') ?>" method="POST">
                            <?= \Core\CSRF::field() ?>
                            <input type="text" name="coupon_code" id="coupon-code-input" placeholder="e.g. PLANTGREEN" required>
                            <button type="submit" class="btn btn-outline btn-sm">Apply</button>
                        </form>
                        <small style="color:var(--text-muted);font-size:11.5px;">Try coupons <strong>PLANTGREEN</strong> or <strong>WELCOME10</strong></small>
                    <?php endif; ?>
                </div>

                <a href="<?= url('/checkout') ?>" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                    Proceed to Secure Checkout &rarr;
                </a>

                <div style="margin-top: 16px; font-size: 12px; text-align: center; color: var(--text-muted);">
                    🔒 256-Bit SSL Secured Checkout • Safe Delivery
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
