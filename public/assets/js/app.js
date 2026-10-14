/**
 * PlantPuglu Frontend Interactivity
 */

document.addEventListener('DOMContentLoaded', function() {
    initAddToCart();
    initQuantityControls();
    initImageGallery();
    initWishlistToggles();
    initCouponHandler();
    initCategoryNavScroll();
});

// CSRF Token Helper
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.getAttribute('content');
    const input = document.querySelector('input[name="_csrf_token"]');
    return input ? input.value : '';
}

// Toast Notification
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const bg = type === 'success' ? '#1b4332' : (type === 'error' ? '#d90429' : '#0369a1');
    toast.style.cssText = `background:${bg};color:#fff;padding:12px 20px;border-radius:10px;font-size:14px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,0.18);display:flex;align-items:center;gap:10px;transition:all 0.3s ease;transform:translateY(20px);opacity:0;`;
    toast.innerHTML = `<span>${type === 'success' ? '🌱' : (type === 'error' ? '⚠️' : 'ℹ️')}</span> ${message}`;
    
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 10);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Add to Cart Handlers
function initAddToCart() {
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const qtyInput = document.querySelector(`input[name="quantity"][data-product-id="${productId}"]`) || document.getElementById('product-qty-input');
            const quantity = qtyInput ? qtyInput.value : 1;
            const variantInput = document.querySelector('input[name="selected_variant_id"]');
            const variantId = variantInput ? variantInput.value : null;

            const originalText = this.innerHTML;
            this.innerHTML = '<span>Adding...</span>';
            this.disabled = true;

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            if (variantId) formData.append('variant_id', variantId);
            formData.append('_csrf_token', getCsrfToken());

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                this.innerHTML = originalText;
                this.disabled = false;
                if (data.success) {
                    showToast(data.message, 'success');
                    updateCartCount(data.total_items);
                } else {
                    showToast(data.message || 'Could not add to cart.', 'error');
                }
            })
            .catch(() => {
                this.innerHTML = originalText;
                this.disabled = false;
                showToast('Failed to add item. Please try again.', 'error');
            });
        });
    });
}

function updateCartCount(count) {
    const badges = document.querySelectorAll('.cart-count-badge');
    badges.forEach(b => {
        b.textContent = count;
        b.style.display = count > 0 ? 'flex' : 'none';
    });
}

// Quantity Steppers
function initQuantityControls() {
    document.querySelectorAll('.qty-control').forEach(control => {
        const minus = control.querySelector('.qty-minus');
        const plus = control.querySelector('.qty-plus');
        const input = control.querySelector('.qty-input');
        const cartItemId = control.getAttribute('data-cart-item-id');

        if (minus && plus && input) {
            minus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                if (val > 1) {
                    input.value = val - 1;
                    if (cartItemId) triggerCartUpdate(cartItemId, input.value);
                }
            });

            plus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                input.value = val + 1;
                if (cartItemId) triggerCartUpdate(cartItemId, input.value);
            });

            input.addEventListener('change', () => {
                let val = parseInt(input.value) || 1;
                if (val < 1) val = 1;
                input.value = val;
                if (cartItemId) triggerCartUpdate(cartItemId, val);
            });
        }
    });
}

function triggerCartUpdate(cartItemId, quantity) {
    const formData = new FormData();
    formData.append('cart_item_id', cartItemId);
    formData.append('quantity', quantity);
    formData.append('_csrf_token', getCsrfToken());

    fetch('/cart/update', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.total_items);
            if (document.getElementById('cart-subtotal')) {
                document.getElementById('cart-subtotal').textContent = data.subtotal;
            }
            if (document.getElementById('cart-grand-total')) {
                document.getElementById('cart-grand-total').textContent = data.grand_total;
            }
            if (document.getElementById('cart-shipping-fee')) {
                document.getElementById('cart-shipping-fee').textContent = data.shipping_fee;
            }
            if (document.getElementById('free-shipping-progress')) {
                document.getElementById('free-shipping-progress').style.width = data.free_shipping_progress + '%';
            }
            showToast('Cart updated.', 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
}

// Product Thumbnail Switcher
function initImageGallery() {
    const mainImg = document.getElementById('main-product-image');
    document.querySelectorAll('.thumb-item').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const targetSrc = this.getAttribute('data-full-image');
            if (mainImg && targetSrc) {
                mainImg.src = targetSrc;
            }
        });
    });

    // Variant Options Click
    document.querySelectorAll('.variant-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const varId = this.getAttribute('data-variant-id');
            const price = this.getAttribute('data-price');
            const varInput = document.getElementById('selected-variant-id');
            if (varInput) varInput.value = varId;
            const priceDisplay = document.getElementById('product-price-display');
            if (priceDisplay && price) {
                priceDisplay.textContent = price;
            }
        });
    });
}

// Wishlist Toggle
function initWishlistToggles() {
    document.querySelectorAll('.btn-wishlist-toggle').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('_csrf_token', getCsrfToken());

            fetch('/wishlist/toggle', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => {
                if (res.status === 401 || res.redirected) {
                    window.location.href = '/login';
                    return;
                }
                return res.json();
            })
            .then(data => {
                if (!data) return;
                if (data.success) {
                    showToast(data.message, 'success');
                    if (data.added) {
                        this.classList.add('active');
                        this.innerHTML = '❤️';
                    } else {
                        this.classList.remove('active');
                        this.innerHTML = '🤍';
                    }
                }
            });
        });
    });
}

// Coupon Form
function initCouponHandler() {
    const couponForm = document.getElementById('coupon-apply-form');
    if (!couponForm) return;

    couponForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const codeInput = document.getElementById('coupon-code-input');
        const code = codeInput ? codeInput.value.trim() : '';
        if (!code) return;

        const formData = new FormData();
        formData.append('coupon_code', code);
        formData.append('_csrf_token', getCsrfToken());

        fetch('/cart/coupon/apply', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message, 'error');
            }
        });
    });
}

// Category Navigation Smooth Scroll & Arrows
function initCategoryNavScroll() {
    const scrollContainer = document.getElementById('catNavScroll');
    const prevBtn = document.getElementById('catNavPrev');
    const nextBtn = document.getElementById('catNavNext');

    if (!scrollContainer || !prevBtn || !nextBtn) return;

    function updateArrowVisibility() {
        const scrollLeft = scrollContainer.scrollLeft;
        const maxScrollLeft = scrollContainer.scrollWidth - scrollContainer.clientWidth;

        // Show arrows only if scrolling is actually possible (overflow exists)
        if (maxScrollLeft > 6) {
            if (scrollLeft > 6) {
                prevBtn.classList.add('visible');
            } else {
                prevBtn.classList.remove('visible');
            }

            if (scrollLeft < maxScrollLeft - 6) {
                nextBtn.classList.add('visible');
            } else {
                nextBtn.classList.remove('visible');
            }
        } else {
            prevBtn.classList.remove('visible');
            nextBtn.classList.remove('visible');
        }
    }

    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        scrollContainer.scrollBy({ left: -260, behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        scrollContainer.scrollBy({ left: 260, behavior: 'smooth' });
    });

    scrollContainer.addEventListener('scroll', updateArrowVisibility, { passive: true });
    window.addEventListener('resize', updateArrowVisibility);

    // Initial check and after paint/fonts
    updateArrowVisibility();
    setTimeout(updateArrowVisibility, 150);
    setTimeout(updateArrowVisibility, 600);
}
