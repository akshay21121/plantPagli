<?php
/**
 * Customer Profile Dashboard View
 * @var array $user
 * @var array $orders
 * @var array $addresses
 */
?>

<div class="container" style="margin-top: 30px; margin-bottom: 70px;">
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 36px; align-items: flex-start;">
        <!-- Account Navigation Sidebar -->
        <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 24px;">
            <div style="text-align: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--cream-border);">
                <div style="width: 60px; height: 60px; background: var(--accent-soft); border-radius: 50%; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin: 0 auto 10px;">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <h3 style="font-size: 16px;"><?= e($user['name']) ?></h3>
                <span style="font-size: 12px; color: var(--text-muted);"><?= e($user['email']) ?></span>
            </div>

            <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 14px;">
                <li><a href="<?= url('/account') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:700; background:var(--cream-bg); color:var(--primary);">👤 Profile Overview</a></li>
                <li><a href="<?= url('/account/orders') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">📦 My Orders (<?= count($orders) ?>)</a></li>
                <li><a href="<?= url('/account/addresses') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">📍 Delivery Addresses (<?= count($addresses) ?>)</a></li>
                <li><a href="<?= url('/wishlist') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">🤍 Saved Wishlist</a></li>
                <li style="margin-top: 10px; border-top: 1px solid var(--cream-border); padding-top: 10px;">
                    <a href="<?= url('/logout') ?>" style="display:block; padding:8px 12px; border-radius:6px; color:var(--danger); font-weight:600;">Sign Out</a>
                </li>
            </ul>
        </div>

        <!-- Main Profile Content -->
        <div>
            <!-- Overview KPI Cards -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 28px;">
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 20px;">
                    <span style="font-size: 12px; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Total Orders</span>
                    <h3 style="font-size: 26px; color: var(--primary); margin-top: 4px;"><?= count($orders) ?></h3>
                </div>
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 20px;">
                    <span style="font-size: 12px; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Saved Addresses</span>
                    <h3 style="font-size: 26px; color: var(--primary); margin-top: 4px;"><?= count($addresses) ?></h3>
                </div>
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 20px;">
                    <span style="font-size: 12px; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Plant Parent Since</span>
                    <h3 style="font-size: 18px; color: var(--primary); margin-top: 8px;"><?= date('M Y', strtotime($user['created_at'])) ?></h3>
                </div>
            </div>

            <!-- Profile Edit Form -->
            <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 30px; margin-bottom: 30px;">
                <h3 style="font-size: 18px; margin-bottom: 20px;">Update Profile Details</h3>
                <form action="<?= url('/account/profile/update') ?>" method="POST">
                    <?= \Core\CSRF::field() ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" required value="<?= e($user['name']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" required value="<?= e($user['phone']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" value="<?= e($user['email']) ?>" disabled style="background:#f4f4f4;">
                        <small style="color:var(--text-muted); font-size:11.5px;">Email cannot be modified directly for account security.</small>
                    </div>

                    <h4 style="font-size: 15px; margin: 24px 0 14px; border-top: 1px solid var(--cream-border); padding-top: 16px;">Change Password (Optional)</h4>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label>Confirm New</label>
                            <input type="password" name="confirm_password" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 11px 24px;">Save Changes</button>
                </form>
            </div>

            <!-- Recent Orders Preview -->
            <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                    <h3 style="font-size: 18px;">Recent Nursery Orders</h3>
                    <a href="<?= url('/account/orders') ?>" style="font-size: 13.5px; font-weight: 700; color: var(--primary);">View All &rarr;</a>
                </div>

                <?php if (empty($orders)): ?>
                    <p style="color: var(--text-muted); font-size: 14px;">You haven't placed any orders yet.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                        <thead>
                            <tr style="text-align: left; color: var(--text-muted); font-size: 12px; border-bottom: 1px solid var(--cream-border);">
                                <th style="padding-bottom: 8px;">Order #</th>
                                <th style="padding-bottom: 8px;">Date</th>
                                <th style="padding-bottom: 8px;">Status</th>
                                <th style="padding-bottom: 8px;">Total</th>
                                <th style="padding-bottom: 8px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($orders, 0, 4) as $o): ?>
                                <tr style="border-bottom: 1px solid var(--cream-border);">
                                    <td style="padding: 12px 0;"><strong>#<?= e($o['order_number']) ?></strong></td>
                                    <td style="padding: 12px 0;"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                                    <td style="padding: 12px 0;">
                                        <span class="badge" style="background:#e0f2fe;color:#0369a1;text-transform:capitalize;"><?= e($o['order_status']) ?></span>
                                    </td>
                                    <td style="padding: 12px 0; font-weight: 700;"><?= formatPrice($o['total_amount']) ?></td>
                                    <td style="padding: 12px 0; text-align: right;">
                                        <a href="<?= url('/account/orders/' . e($o['order_number'])) ?>" class="btn btn-outline btn-sm">Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
