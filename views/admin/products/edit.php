<?php
/**
 * Admin Product Edit View
 * @var array $product
 * @var array $categories
 */
?>

<div style="margin-bottom: 24px;">
    <a href="<?= url('/admin/products') ?>" style="color: var(--primary); font-weight: 700; font-size: 13.5px;">&larr; Back to Products</a>
    <h1 style="font-size: 24px; font-weight: 700; margin-top: 8px;">Edit Product: <?= e($product['name']) ?></h1>
</div>

<form action="<?= url('/admin/products/update/' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
    <?= \Core\CSRF::field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 28px; align-items: flex-start;">
        <!-- Left: Primary Details -->
        <div>
            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">1. Product Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" required value="<?= e($product['name']) ?>">
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>SKU / Product Code *</label>
                            <input type="text" name="sku" required value="<?= e($product['sku']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Short Summary</label>
                        <textarea name="short_description" rows="2"><?= e($product['short_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Full Description</label>
                        <textarea name="description" rows="6"><?= e($product['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">2. Plant Specifications</h3>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Plant Height</label>
                            <input type="text" name="plant_height" value="<?= e($product['plant_height'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Pot Size</label>
                            <input type="text" name="pot_size" value="<?= e($product['pot_size'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sunlight</label>
                            <input type="text" name="sunlight" value="<?= e($product['sunlight'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Watering</label>
                            <input type="text" name="watering" value="<?= e($product['watering'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Maintenance</label>
                            <select name="maintenance">
                                <option value="Extremely Low" <?= ($product['maintenance'] === 'Extremely Low') ? 'selected' : '' ?>>Extremely Low</option>
                                <option value="Low" <?= ($product['maintenance'] === 'Low') ? 'selected' : '' ?>>Low</option>
                                <option value="Moderate" <?= ($product['maintenance'] === 'Moderate') ? 'selected' : '' ?>>Moderate</option>
                                <option value="High" <?= ($product['maintenance'] === 'High') ? 'selected' : '' ?>>High</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pet Friendly?</label>
                            <select name="pet_friendly">
                                <option value="0" <?= empty($product['pet_friendly']) ? 'selected' : '' ?>>No</option>
                                <option value="1" <?= !empty($product['pet_friendly']) ? 'selected' : '' ?>>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Care Instructions Text</label>
                        <textarea name="care_instructions" rows="3"><?= e($product['care_instructions'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Shipping Info</label>
                        <textarea name="shipping_info" rows="2"><?= e($product['shipping_info'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Pricing, Status, Media -->
        <div>
            <!-- Pricing & Stock -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">Pricing & Stock</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Price (INR ₹) *</label>
                        <input type="number" step="0.01" name="price" required value="<?= e($product['price']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Compare Price</label>
                        <input type="number" step="0.01" name="compare_price" value="<?= e($product['compare_price'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock_quantity" value="<?= e($product['stock_quantity']) ?>" required>
                    </div>
                </div>
            </div>

            <!-- Image Upload & Multi-Image Gallery -->
            <div class="card">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="font-size: 15px; font-weight: 700;">Product Photos & Gallery</h3>
                    <span class="badge" style="background:var(--cream-bg); color:var(--text); font-size:11.5px; font-weight:600; padding:3px 8px; border-radius:12px; border:1px solid var(--border);">
                        <?= !empty($product['images']) ? count($product['images']) : 0 ?> <?= (!empty($product['images']) && count($product['images']) === 1) ? 'Photo' : 'Photos' ?>
                    </span>
                </div>
                <div class="card-body">
                    <!-- Current Images Display -->
                    <?php if (!empty($product['images'])): ?>
                        <div style="margin-bottom: 18px;">
                            <label style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 8px;">Uploaded Photos:</label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(105px, 1fr)); gap: 10px;">
                                <?php foreach ($product['images'] as $img): ?>
                                    <div style="border: 1px solid <?= !empty($img['is_primary']) ? 'var(--primary)' : 'var(--border)' ?>; border-radius: 8px; padding: 6px; background: #fff; text-align: center; box-shadow: <?= !empty($img['is_primary']) ? '0 0 0 2px rgba(27,67,50,0.18)' : '0 1px 2px rgba(0,0,0,0.03)' ?>; display: flex; flex-direction: column; justify-content: space-between;">
                                        <div style="height: 85px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 6px; overflow: hidden; margin-bottom: 6px;">
                                            <img src="<?= uploadUrl('products/' . $img['image_path']) ?>" alt="Photo" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                        </div>
                                        
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <?php if (!empty($img['is_primary'])): ?>
                                                <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 3px 6px; border-radius: 4px; display: block;">★ Primary</span>
                                            <?php else: ?>
                                                <button type="submit" form="form-set-primary-<?= $img['id'] ?>" class="btn btn-outline btn-sm" style="font-size: 10px; padding: 3px 5px; width: 100%; line-height: 1.2;" title="Set as main store display photo">Make Primary</button>
                                            <?php endif; ?>

                                            <button type="submit" form="form-delete-img-<?= $img['id'] ?>" onclick="return confirm('Delete this image?');" class="btn btn-sm" style="font-size: 10px; padding: 3px 5px; width: 100%; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; line-height: 1.2;">Delete</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <hr style="border: 0; border-top: 1px solid var(--border); margin: 16px 0;">
                    <?php endif; ?>

                    <!-- Upload 2nd & Additional Images (Multi-File) -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                            <span>📸</span> Add 2nd & More Images (Gallery)
                        </label>
                        <input type="file" name="gallery_images[]" id="gallery-image-input" multiple accept="image/*" style="border: 2px dashed var(--primary); background: #f0fdf4; padding: 10px; border-radius: 8px; width: 100%; cursor: pointer;">
                        <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 5px; line-height: 1.4;">
                            Select <strong>1 or multiple photos</strong> from your device (hold Ctrl / Shift to pick multiple). These appear as 2nd, 3rd, and alternate view thumbnails on the product page.
                        </small>
                        <div id="gallery-preview-container" style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;"></div>
                    </div>

                    <!-- Replace Primary Image -->
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 13px;">Replace Primary Image</label>
                        <input type="file" name="image" id="primary-image-input" accept="image/*">
                        <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">
                            Upload a photo to immediately overwrite the main product image.
                        </small>
                        <img id="primary-image-preview" src="" alt="Preview" style="display:none; max-height: 120px; border-radius: 6px; margin-top: 8px; border: 1px solid var(--border);">
                    </div>
                </div>
            </div>

            <!-- Status Toggles -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">Status</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?>>
                            Active & Published
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_bestseller" value="1" <?= $product['is_bestseller'] ? 'checked' : '' ?>>
                            Bestseller Tag
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
                            Featured on Homepage
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                Update Product Details & Photos
            </button>
        </div>
    </div>
</form>

<?php if (!empty($product['images'])): ?>
    <?php foreach ($product['images'] as $img): ?>
        <form id="form-delete-img-<?= $img['id'] ?>" action="<?= url('/admin/products/' . $product['id'] . '/images/delete/' . $img['id']) ?>" method="POST" style="display:none;">
            <?= \Core\CSRF::field() ?>
        </form>
        <form id="form-set-primary-<?= $img['id'] ?>" action="<?= url('/admin/products/' . $product['id'] . '/images/set-primary/' . $img['id']) ?>" method="POST" style="display:none;">
            <?= \Core\CSRF::field() ?>
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<script>
// Image preview for additional gallery files
document.getElementById('gallery-image-input')?.addEventListener('change', function(e) {
    const container = document.getElementById('gallery-preview-container');
    container.innerHTML = '';
    if (this.files) {
        Array.from(this.files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const thumb = document.createElement('div');
                    thumb.style.width = '55px';
                    thumb.style.height = '55px';
                    thumb.style.borderRadius = '6px';
                    thumb.style.overflow = 'hidden';
                    thumb.style.border = '1px solid #52b788';
                    thumb.style.background = '#f8fafc';
                    thumb.style.display = 'flex';
                    thumb.style.alignItems = 'center';
                    thumb.style.justifyContent = 'center';
                    thumb.innerHTML = `<img src="${evt.target.result}" style="max-width:100%; max-height:100%; object-fit:contain;">`;
                    container.appendChild(thumb);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Image preview for replace primary image
document.getElementById('primary-image-input')?.addEventListener('change', function(e) {
    const preview = document.getElementById('primary-image-preview');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            preview.src = evt.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(this.files[0]);
    } else {
        preview.style.display = 'none';
    }
});
</script>
