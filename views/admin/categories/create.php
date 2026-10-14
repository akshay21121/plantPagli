<?php
/**
 * Admin Category Creation View
 * @var array $categories
 */
?>

<div style="margin-bottom: 24px;">
    <a href="<?= url('/admin/categories') ?>" style="color: var(--primary); font-weight: 700; font-size: 13.5px;">&larr; Back to Categories</a>
    <h1 style="font-size: 24px; font-weight: 700; margin-top: 8px;">Add New Category</h1>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-body">
        <form action="<?= url('/admin/categories/store') ?>" method="POST">
            <?= \Core\CSRF::field() ?>

            <div class="form-group">
                <label>Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Bonsai Plants">
            </div>

            <div class="form-group">
                <label>Custom URL Slug (Leave blank to generate automatically)</label>
                <input type="text" name="slug" placeholder="e.g. bonsai-plants">
            </div>

            <div class="form-group">
                <label>Parent Category (Optional)</label>
                <select name="parent_id">
                    <option value="">None (Top-Level Category)</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Category summary..."></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Sort Order Index</label>
                    <input type="number" name="sort_order" value="0">
                </div>
                <div class="form-group">
                    <label>Icon Keyword</label>
                    <input type="text" name="icon" value="leaf" placeholder="e.g. leaf, sun, flower">
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_featured" value="1">
                    Feature on Homepage category carousel
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Save Category</button>
        </form>
    </div>
</div>
