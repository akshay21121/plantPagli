<?php
declare(strict_types=1);

/**
 * PlantPuglu Routing Definitions
 * Maps HTTP methods and URI patterns to Controllers and Actions.
 *
 * @var Core\Router $router
 */

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\AccountController;
use App\Controllers\WishlistController;
use App\Controllers\ReviewController;
use App\Controllers\PageController;

// Admin Controllers
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Controllers\Admin\CouponController as AdminCouponController;
use App\Controllers\Admin\ReviewController as AdminReviewController;
use App\Controllers\Admin\BannerController as AdminBannerController;
use App\Controllers\Admin\SettingController as AdminSettingController;

// -----------------------------------------------------------------------------
// Public Frontend Routes
// -----------------------------------------------------------------------------
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/product/{slug}', [ProductController::class, 'show']);

// Shopping Cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/coupon/apply', [CartController::class, 'applyCoupon']);
$router->post('/cart/coupon/remove', [CartController::class, 'removeCoupon']);
$router->get('/cart/count', [CartController::class, 'count']);

// Checkout & Razorpay Payment Verification
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);
$router->post('/checkout/verify-payment', [CheckoutController::class, 'verifyPayment']);
$router->get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success']);

// Customer Authentication
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Customer Account Area (Requires Auth)
$router->get('/account', [AccountController::class, 'index'], ['auth']);
$router->post('/account/profile/update', [AccountController::class, 'updateProfile'], ['auth']);
$router->get('/account/orders', [AccountController::class, 'orders'], ['auth']);
$router->get('/account/orders/{orderNumber}', [AccountController::class, 'orderDetail'], ['auth']);
$router->get('/account/addresses', [AccountController::class, 'addresses'], ['auth']);
$router->post('/account/addresses/add', [AccountController::class, 'addAddress'], ['auth']);
$router->post('/account/addresses/delete/{id}', [AccountController::class, 'deleteAddress'], ['auth']);

// Customer Wishlist
$router->get('/wishlist', [WishlistController::class, 'index'], ['auth']);
$router->post('/wishlist/toggle', [WishlistController::class, 'toggle'], ['auth']);
$router->post('/wishlist/remove', [WishlistController::class, 'remove'], ['auth']);

// Product Reviews
$router->post('/reviews/store', [ReviewController::class, 'store']);

// Informational & Policy Pages
$router->get('/about', [PageController::class, 'about']);
$router->get('/contact', [PageController::class, 'contact']);
$router->post('/contact', [PageController::class, 'sendContactMessage']);
$router->get('/plant-care', [PageController::class, 'plantCare']);
$router->get('/shipping-policy', [PageController::class, 'shippingPolicy']);
$router->get('/refund-policy', [PageController::class, 'refundPolicy']);
$router->get('/privacy-policy', [PageController::class, 'privacyPolicy']);

// -----------------------------------------------------------------------------
// Admin Dashboard & Management Routes (Protected by 'admin' Middleware)
// -----------------------------------------------------------------------------
$router->get('/admin', [DashboardController::class, 'index'], ['admin']);

// Admin Products
$router->get('/admin/products', [AdminProductController::class, 'index'], ['admin']);
$router->get('/admin/products/create', [AdminProductController::class, 'create'], ['admin']);
$router->post('/admin/products/store', [AdminProductController::class, 'store'], ['admin']);
$router->get('/admin/products/edit/{id}', [AdminProductController::class, 'edit'], ['admin']);
$router->post('/admin/products/update/{id}', [AdminProductController::class, 'update'], ['admin']);
$router->post('/admin/products/delete/{id}', [AdminProductController::class, 'delete'], ['admin']);
$router->post('/admin/products/{productId}/images/delete/{imageId}', [AdminProductController::class, 'deleteImage'], ['admin']);
$router->post('/admin/products/{productId}/images/set-primary/{imageId}', [AdminProductController::class, 'setPrimaryImage'], ['admin']);
$router->get('/admin/products/export-csv', [AdminProductController::class, 'exportCsv'], ['admin']);

// Admin Categories
$router->get('/admin/categories', [AdminCategoryController::class, 'index'], ['admin']);
$router->get('/admin/categories/create', [AdminCategoryController::class, 'create'], ['admin']);
$router->post('/admin/categories/store', [AdminCategoryController::class, 'store'], ['admin']);
$router->get('/admin/categories/edit/{id}', [AdminCategoryController::class, 'edit'], ['admin']);
$router->post('/admin/categories/update/{id}', [AdminCategoryController::class, 'update'], ['admin']);
$router->post('/admin/categories/delete/{id}', [AdminCategoryController::class, 'delete'], ['admin']);

// Admin Orders
$router->get('/admin/orders', [AdminOrderController::class, 'index'], ['admin']);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show'], ['admin']);
$router->post('/admin/orders/status/{id}', [AdminOrderController::class, 'updateStatus'], ['admin']);
$router->post('/admin/orders/payment-status/{id}', [AdminOrderController::class, 'updatePaymentStatus'], ['admin']);
$router->get('/admin/orders/export-csv', [AdminOrderController::class, 'exportCsv'], ['admin']);

// Admin Customers
$router->get('/admin/customers', [AdminCustomerController::class, 'index'], ['admin']);

// Admin Coupons
$router->get('/admin/coupons', [AdminCouponController::class, 'index'], ['admin']);
$router->get('/admin/coupons/create', [AdminCouponController::class, 'create'], ['admin']);
$router->post('/admin/coupons/store', [AdminCouponController::class, 'store'], ['admin']);
$router->post('/admin/coupons/delete/{id}', [AdminCouponController::class, 'delete'], ['admin']);

// Admin Reviews
$router->get('/admin/reviews', [AdminReviewController::class, 'index'], ['admin']);
$router->post('/admin/reviews/approve/{id}', [AdminReviewController::class, 'approve'], ['admin']);
$router->post('/admin/reviews/reject/{id}', [AdminReviewController::class, 'reject'], ['admin']);
$router->post('/admin/reviews/delete/{id}', [AdminReviewController::class, 'delete'], ['admin']);

// Admin Banners
$router->get('/admin/banners', [AdminBannerController::class, 'index'], ['admin']);
$router->post('/admin/banners/store', [AdminBannerController::class, 'store'], ['admin']);
$router->post('/admin/banners/delete/{id}', [AdminBannerController::class, 'delete'], ['admin']);

// Admin Settings
$router->get('/admin/settings', [AdminSettingController::class, 'index'], ['admin']);
$router->post('/admin/settings/update', [AdminSettingController::class, 'update'], ['admin']);
