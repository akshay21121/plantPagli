<?php
/**
 * Admin Banners & Announcements Management View
 * @var array $banners
 */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700;">Homepage Banners & Announcements</h1>
        <span style="font-size: 13px; color: var(--text-muted);">Manage hero carousels and top promotional announcement ticker</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 28px; align-items: flex-start;">
    <!-- Banners List -->
    <div class="card">
        <div class="card-header">
            <h3 style="font-size: 15px; font-weight: 700;">Active Promotional Elements</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Title / Text</th>
                            <th>Button & Link</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $b): ?>
                            <tr>
                                <td>
                                    <span class="badge" style="background:#e0f2fe;color:#0369a1;text-transform:uppercase;font-size:11px;">
                                        <?= e($b['type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= e($b['title']) ?></strong>
                                    <?php if (!empty($b['subtitle'])): ?>
                                        <div style="font-size:12px; color:var(--text-muted);"><?= e($b['subtitle']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($b['button_text'])): ?>
                                        <span style="font-size:12.5px;"><?= e($b['button_text']) ?> &rarr; <?= e($b['button_link']) ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-status badge-<?= $b['is_active'] ? 'delivered' : 'cancelled' ?>">
                                        <?= $b['is_active'] ? 'Live' : 'Hidden' ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <form action="<?= url('/admin/banners/delete/' . $b['id']) ?>" method="POST" onsubmit="return confirm('Remove this banner?');">
                                        <?= \Core\CSRF::field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Banner Form -->
    <div class="card">
        <div class="card-header">
            <h3 style="font-size: 15px; font-weight: 700;">+ Add Promotional Item</h3>
        </div>
        <div class="card-body">
            <form action="<?= url('/admin/banners/store') ?>" method="POST" enctype="multipart/form-data">
                <?= \Core\CSRF::field() ?>

                <div class="form-group">
                    <label>Placement Type *</label>
                    <select name="type" required>
                        <option value="hero">Hero Main Slider</option>
                        <option value="announcement">Top Announcement Ticker Bar</option>
                        <option value="promo_mid">Mid-Page Feature Banner</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Main Headline / Notice *</label>
                    <input type="text" name="title" required placeholder="e.g. Monsoon Green Fest 20% OFF">
                </div>

                <div class="form-group">
                    <label>Subtitle / Body Text</label>
                    <textarea name="subtitle" rows="2" placeholder="Descriptive text..."></textarea>
                </div>

                <div class="form-group">
                    <label>Badge Pill Text (Optional)</label>
                    <input type="text" name="badge_text" placeholder="e.g. Limited Time">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Button Label</label>
                        <input type="text" name="button_text" value="Shop Now">
                    </div>
                    <div class="form-group">
                        <label>Button Target URL</label>
                        <input type="text" name="button_link" value="/products">
                    </div>
                </div>

                <div class="form-group">
                    <label>Banner Graphic / Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Publish Promotional Item</button>
            </form>
        </div>
    </div>
</div>
