<?php
/**
 * Customer Addresses Management View
 * @var array $user
 * @var array $addresses
 */
?>

<div class="container" style="margin-top: 30px; margin-bottom: 70px;">
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 36px; align-items: flex-start;">
        <!-- Account Sidebar -->
        <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 24px;">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 14px;">
                <li><a href="<?= url('/account') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">👤 Profile Overview</a></li>
                <li><a href="<?= url('/account/orders') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">📦 My Orders</a></li>
                <li><a href="<?= url('/account/addresses') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:700; background:var(--cream-bg); color:var(--primary);">📍 Delivery Addresses (<?= count($addresses) ?>)</a></li>
                <li><a href="<?= url('/wishlist') ?>" style="display:block; padding:8px 12px; border-radius:6px; font-weight:500;">🤍 Saved Wishlist</a></li>
                <li style="margin-top: 10px; border-top: 1px solid var(--cream-border); padding-top: 10px;">
                    <a href="<?= url('/logout') ?>" style="display:block; padding:8px 12px; border-radius:6px; color:var(--danger); font-weight:600;">Sign Out</a>
                </li>
            </ul>
        </div>

        <!-- Addresses Management -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h1 style="font-size: 26px;">Delivery Addresses</h1>
                <button type="button" onclick="document.getElementById('add-address-form-box').style.display='block';this.style.display='none';" class="btn btn-primary btn-sm">
                    + Add New Address
                </button>
            </div>

            <!-- Add Address Form (Hidden by default or toggled) -->
            <div id="add-address-form-box" style="display: none; background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 30px; margin-bottom: 30px;">
                <h3 style="font-size: 18px; margin-bottom: 18px;">Add New Indian Delivery Address</h3>
                <form action="<?= url('/account/addresses/add') ?>" method="POST">
                    <?= \Core\CSRF::field() ?>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Address Label</label>
                            <input type="text" name="label" value="Home" placeholder="e.g. Home, Office">
                        </div>
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" required value="<?= e($user['name']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" required value="<?= e($user['phone']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address Line 1 (Flat, Wing, Building, Street) *</label>
                        <input type="text" name="address_line1" required placeholder="e.g. Flat 402, Lotus Greens Apartment, HSR Layout">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Landmark</label>
                            <input type="text" name="landmark" placeholder="e.g. Near BDA Complex">
                        </div>
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" required placeholder="e.g. Bengaluru">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>State *</label>
                            <select name="state" required>
                                <option value="Karnataka">Karnataka</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Delhi (NCT)">Delhi (NCT)</option>
                                <option value="Tamil Nadu">Tamil Nadu</option>
                                <option value="Telangana">Telangana</option>
                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                <option value="West Bengal">West Bengal</option>
                                <option value="Gujarat">Gujarat</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>PIN Code *</label>
                            <input type="text" name="pincode" maxlength="6" pattern="[1-9][0-9]{5}" required placeholder="560102">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13.5px;cursor:pointer;">
                            <input type="checkbox" name="is_default" value="1">
                            Set as primary default delivery address
                        </label>
                    </div>

                    <div style="display:flex;gap:12px;">
                        <button type="submit" class="btn btn-primary">Save Address</button>
                        <button type="button" onclick="document.getElementById('add-address-form-box').style.display='none';" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Existing Addresses Cards -->
            <?php if (empty($addresses)): ?>
                <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-md); padding: 40px; text-align: center;">
                    <p style="color: var(--text-muted); font-size: 14px;">No addresses saved yet.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($addresses as $addr): ?>
                        <div style="background: var(--cream-surface); border: 1.5px solid <?= $addr['is_default'] ? 'var(--primary)' : 'var(--cream-border)' ?>; border-radius: var(--radius-md); padding: 22px; position: relative;">
                            <?php if ($addr['is_default']): ?>
                                <span class="badge" style="position:absolute;top:16px;right:16px;background:var(--accent-soft);color:var(--primary);font-size:11px;">Default</span>
                            <?php endif; ?>

                            <h3 style="font-size: 16px; margin-bottom: 8px;"><?= e($addr['label']) ?></h3>
                            <p style="font-size: 14px; line-height: 1.6; color: var(--text-main); margin-bottom: 16px;">
                                <strong><?= e($addr['full_name']) ?></strong><br>
                                <?= e($addr['address_line1']) ?><br>
                                <?php if (!empty($addr['landmark'])): ?>
                                    Landmark: <?= e($addr['landmark']) ?><br>
                                <?php endif; ?>
                                <?= e($addr['city']) ?>, <?= e($addr['state']) ?> - <strong><?= e($addr['pincode']) ?></strong><br>
                                📞 <?= e($addr['phone']) ?>
                            </p>

                            <form action="<?= url('/account/addresses/delete/' . $addr['id']) ?>" method="POST" onsubmit="return confirm('Delete this address?');">
                                <?= \Core\CSRF::field() ?>
                                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger);border-color:#fecaca;">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
