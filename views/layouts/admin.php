<?php
use Core\Auth;
use Core\Session;

$user = Auth::user();
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Plantpagli Admin Control Panel') ?></title>
    <?= \Core\CSRF::meta() ?>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <img src="<?= asset('images/logo.png') ?>" alt="Plantpagli Logo" style="height:42px; width:42px; border-radius:8px; object-fit:contain; border:1.5px solid rgba(16,185,129,0.3); background:#fff; flex-shrink:0;">
                <span>Plantpagli Admin</span>
            </div>

            <ul class="sidebar-nav">
                <li>
                    <a href="<?= url('/admin') ?>" class="<?= $currentUri === '/admin' ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/products') ?>" class="<?= str_starts_with($currentUri, '/admin/products') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M12 2l-5.5 9h11zM12 22l5.5-9h-11z"/></svg>
                        <span>Products</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/categories') ?>" class="<?= str_starts_with($currentUri, '/admin/categories') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/></svg>
                        <span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/orders') ?>" class="<?= str_starts_with($currentUri, '/admin/orders') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
                        <span>Orders</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/customers') ?>" class="<?= str_starts_with($currentUri, '/admin/customers') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        <span>Customers</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/coupons') ?>" class="<?= str_starts_with($currentUri, '/admin/coupons') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.65-.5-.65C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/></svg>
                        <span>Coupons</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/reviews') ?>" class="<?= str_starts_with($currentUri, '/admin/reviews') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <span>Reviews</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/banners') ?>" class="<?= str_starts_with($currentUri, '/admin/banners') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/></svg>
                        <span>Banners</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/admin/settings') ?>" class="<?= str_starts_with($currentUri, '/admin/settings') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                        <span>Store Settings</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="<?= url('/') ?>" target="_blank" style="color:#74c69d;font-weight:600;display:flex;align-items:center;gap:6px;">
                    <span>Visit Live Store &rarr;</span>
                </a>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="admin-main">
            <!-- Topbar -->
            <header class="admin-topbar">
                <h2><?= e($title ?? 'Control Center') ?></h2>
                <div class="topbar-actions">
                    <span style="font-size:13px;color:var(--text-muted);">
                        Admin: <strong><?= e($user['name'] ?? 'Super Admin') ?></strong>
                    </span>
                    <a href="<?= url('/logout') ?>" class="btn btn-secondary btn-sm">Sign Out</a>
                </div>
            </header>

            <!-- Content Area -->
            <main class="admin-content">
                <?php if ($success = Session::getFlash('success')): ?>
                    <div class="alert alert-success">
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error = Session::getFlash('error')): ?>
                    <div class="alert alert-error">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
