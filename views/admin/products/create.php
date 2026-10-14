<?php
/**
 * Admin Product Creation View
 * @var array $categories
 */
?>

<div style="margin-bottom: 24px;">
    <a href="<?= url('/admin/products') ?>" style="color: var(--primary); font-weight: 700; font-size: 13.5px;">&larr; Back to Products</a>
    <h1 style="font-size: 24px; font-weight: 700; margin-top: 8px;">Add New Nursery Plant or Gardening Product</h1>
</div>

<form action="<?= url('/admin/products/store') ?>" method="POST" enctype="multipart/form-data">
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
                        <input type="text" name="name" required placeholder="e.g. Sansevieria Golden Hahnii (Snake Plant)">
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>SKU / Product Code *</label>
                            <input type="text" name="sku" required placeholder="e.g. PP-IN-001">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Short Summary (Shown in cards)</label>
                        <textarea name="short_description" rows="2" placeholder="Compact summary of plant features..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Full Botanical Description & Details</label>
                        <textarea name="description" rows="6" placeholder="Full care, growth habits, origin and aesthetics..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Botanical Specifications -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">2. Plant Specifications & Care Guide</h3>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Plant Height</label>
                            <input type="text" name="plant_height" placeholder="e.g. 6 - 9 inches">
                        </div>
                        <div class="form-group">
                            <label>Pot Size</label>
                            <input type="text" name="pot_size" placeholder="e.g. 4 inch nursery pot">
                        </div>
                        <div class="form-group">
                            <label>Sunlight Requirement</label>
                            <input type="text" name="sunlight" placeholder="e.g. Low to Bright Indirect">
                        </div>
                        <div class="form-group">
                            <label>Watering Schedule</label>
                            <input type="text" name="watering" placeholder="e.g. Once every 10-14 days">
                        </div>
                        <div class="form-group">
                            <label>Maintenance Difficulty</label>
                            <select name="maintenance">
                                <option value="Extremely Low">Extremely Low (Beginner Friendly)</option>
                                <option value="Low">Low</option>
                                <option value="Moderate">Moderate</option>
                                <option value="High">High (Demanding)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pet Friendly?</label>
                            <select name="pet_friendly">
                                <option value="0">No (Keep away from pets)</option>
                                <option value="1">Yes (Pet safe)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Care Instructions Text</label>
                        <textarea name="care_instructions" rows="3" placeholder="Light, watering, fertilization and pruning advice..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Shipping / Packaging Info</label>
                        <textarea name="shipping_info" rows="2" placeholder="Transit packaging details..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Dynamic Variants Box -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">3. Product Variants & Pot Options</h3>
                    <button type="button" id="btn-add-variant-row" class="btn btn-secondary btn-sm">+ Add Variant</button>
                </div>
                <div class="card-body">
                    <div id="variant-rows-container">
                        <!-- Added dynamically via admin.js -->
                    </div>
                    <small style="color:var(--text-muted);font-size:12px;">Add optional size or planter variants (e.g. "6 Inch Ceramic Pot" or "Pack of 2").</small>
                </div>
            </div>
        </div>

        <!-- Right: Pricing, Images, Badges -->
        <div>
            <!-- Pricing & Inventory -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">Pricing & Stock</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Selling Price (INR ₹) *</label>
                        <input type="number" step="0.01" name="price" required placeholder="299.00">
                    </div>

                    <div class="form-group">
                        <label>Compare Price (MRP Strike-through)</label>
                        <input type="number" step="0.01" name="compare_price" placeholder="399.00">
                    </div>

                    <div class="form-group">
                        <label>Stock Available *</label>
                        <input type="number" name="stock_quantity" value="25" required>
                    </div>
                </div>
            </div>

            <!-- Product Media -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">Product Images</h3>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-weight: 600;">Primary Main Image *</label>
                        <input type="file" name="image" id="product-image-input" accept="image/*">
                        <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">Main cover photo shown on store catalogs.</small>
                        <img id="product-image-preview" src="" alt="Preview" style="display:none; max-height:140px; margin-top:8px; border-radius:8px; border:1px solid var(--border);">
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                            <span>📸</span> Additional Gallery Photos (2nd, 3rd, etc.)
                        </label>
                        <input type="file" name="gallery_images[]" id="gallery-image-input" multiple accept="image/*" style="border: 2px dashed var(--primary); background: #f0fdf4; padding: 10px; border-radius: 8px; width: 100%; cursor: pointer;">
                        <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 5px; line-height: 1.4;">
                            Select <strong>1 or multiple photos</strong> (hold Ctrl / Shift to pick multiple). These appear as thumbnail views on the product page.
                        </small>
                        <div id="gallery-preview-container" style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;"></div>
                    </div>
                </div>
            </div>

            <!-- Publishing Flags -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 15px; font-weight: 700;">Visibility & Badges</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" checked>
                            Active & Available on Store
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_bestseller" value="1">
                            Tag as Bestseller
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_featured" value="1">
                            Feature on Homepage
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                Save & Publish Product
            </button>
        </div>
    </div>
</form>

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

// Image preview for primary image
document.getElementById('product-image-input')?.addEventListener('change', function(e) {
    const preview = document.getElementById('product-image-preview');
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
