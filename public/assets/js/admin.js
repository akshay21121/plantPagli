/**
 * PlantPuglu Admin Control Panel Scripts
 */

document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete prompts
    document.querySelectorAll('.confirm-action').forEach(el => {
        el.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Add Dynamic Variant Row in Product Create/Edit
    const addVariantBtn = document.getElementById('btn-add-variant-row');
    const variantContainer = document.getElementById('variant-rows-container');

    if (addVariantBtn && variantContainer) {
        addVariantBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'variant-row form-grid';
            row.style.marginBottom = '12px';
            row.innerHTML = `
                <div class="form-group">
                    <label>Variant Title (e.g. 6 Inch Pot)</label>
                    <input type="text" name="variant_names[]" required placeholder="Variant Name">
                </div>
                <div class="form-group" style="display:flex;gap:10px;align-items:flex-end;">
                    <div style="flex:1;">
                        <label>Price (₹)</label>
                        <input type="number" step="0.01" name="variant_prices[]" required placeholder="299.00">
                    </div>
                    <div style="flex:1;">
                        <label>Stock</label>
                        <input type="number" name="variant_stocks[]" value="10" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.variant-row').remove()" style="height:42px;">✕</button>
                </div>
            `;
            variantContainer.appendChild(row);
        });
    }

    // Image file preview
    const imageInput = document.getElementById('product-image-input');
    const imagePreview = document.getElementById('product-image-preview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
