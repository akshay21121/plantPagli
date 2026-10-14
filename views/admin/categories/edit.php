<?php
/**
 * Admin Category Edit View
 * @var array $category
 * @var array $categories
 */
?>

<div style="margin-bottom: 24px;">
    <a href="<?= url('/admin/categories') ?>" style="color: var(--primary); font-weight: 700; font-size: 13.5px;">&larr; Back to Categories</a>
    <h1 style="font-size: 24px; font-weight: 700; margin-top: 8px;">Edit Category: <?= e($category['name']) ?></h1>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-body">
        <form action="<?= url('/admin/categories/update/' . $category['id']) ?>" method="POST">
            <?= \Core\CSRF::field() ?>

            <div class="form-group">
                <label>Category Name *</label>
                <input type="text" name="name" required value="<?= e($category['name']) ?>">
            </div>

            <div class="form-group">
                <label>URL Slug</label>
                <input type="text" name="slug" value="<?= e($category['slug']) ?>">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?= e($category['description'] ?? '') ?></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Sort Order Index</label>
                    <input type="number" name="sort_order" value="<?= $category['sort_order'] ?>">
                </div>
                <div class="form-group">
                    <label>Icon Keyword</label>
                    <input type="text" name="icon" value="<?= e($category['icon'] ?? 'leaf') ?>">
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" <?= $category['is_active'] ? 'checked' : '' ?>>
                    Active
                </label>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_featured" value="1" <?= $category['is_featured'] ? 'checked' : '' ?>>
                    Feature on Homepage
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Update Category</button>
        </form>
    </div>
</div>
