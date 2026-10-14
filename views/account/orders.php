<?php
/**
 * Customer Orders History View
 * @var array $user
 * @var array $orders
 */
?>

<div class="container" style="margin-top: 30px; margin-bottom: 70px;">
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 36px; align-items: flex-start;">
        <!-- Account Sidebar -->
        <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 24px;">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 14px;">
                <li><a href="<?= url('/account') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">👤 Profile Overview</a></li>
                <li><a href="<?= url('/account/orders') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:700; background:var(--cream-bg); color:var(--primary);">📦 My Orders (<?= count($orders) ?>)</a></li>
                <li><a href="<?= url('/account/addresses') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">📍 Delivery Addresses</a></li>
                <li><a href="<?= url('/wishlist') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">🤍 Saved Wishlist</a></li>
                <li style="margin-top: 10px; border-top: 1px solid var(--cream-border); padding-top: 10px;">
                    <a href="<?= url('/logout') ?>" style="display:block; padding:8px 12px; border-radius:6px; color:var(--danger); font-weight:600;">Sign Out</a>
                </li>
            </ul>
        </div>

        <!-- Orders List -->
        <div>
            <h1 style="font-size: 26px; margin-bottom: 20px;">Order History</h1>

            <?php if (empty($orders)): ?>
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 50px; text-align: center;">
                    <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 16px;">You haven't placed any plant orders yet.</p>
                    <a href="<?= url('/products') ?>" class="btn btn-primary">Start Shopping &rarr;</a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($orders as $order): ?>
                        <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 24px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--cream-border); padding-bottom: 14px; margin-bottom: 16px;">
                                <div>
                                    <h3 style="font-size: 17px; margin-bottom: 2px;">Order #<?= e($order['order_number']) ?></h3>
                                    <span style="font-size: 12.5px; color: var(--text-muted);">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
                                </div>
                                <div style="text-align: right;">
                                    <span class="badge" style="background:#e0f2fe;color:#0369a1;text-transform:capitalize;font-size:12px;">
                                        <?= e($order['order_status']) ?>
                                    </span>
                                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px;">
                                        Payment: <strong><?= ucfirst($order['payment_status']) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Items in Order -->
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 44px; height: 44px; background: #f4efe4; border-radius: 6px; padding: 4px;">
                                                <img src="<?= productImage($item['product_image'] ?? '') ?>" alt="<?= e($item['product_name']) ?>" style="width:100%;height:100%;object-fit:contain;" onerror="this.onerror=null;this.src='<?= asset('images/plant-placeholder.svg') ?>'">
                                            </div>
                                            <div>
                                                <strong><?= e($item['product_name']) ?></strong>
                                                <div style="font-size: 12px; color: var(--text-muted);"><?= $item['quantity'] ?> × <?= formatPrice($item['price']) ?></div>
                                            </div>
                                        </div>
                                        <strong style="color: var(--primary-dark);"><?= formatPrice($item['subtotal']) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--cream-border); padding-top: 14px;">
                                <div style="font-size: 15px;">
                                    Total: <strong><?= formatPrice($order['total_amount']) ?></strong>
                                </div>
                                <a href="<?= url('/account/orders/' . e($order['order_number'])) ?>" class="btn btn-outline btn-sm">
                                    View Full Details &rarr;
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
