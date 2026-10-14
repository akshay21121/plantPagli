<?php
$errors = \Core\Session::get('_auth_errors', []);
\Core\Session::remove('_auth_errors');
?>

<div class="container" style="max-width: 480px; margin: 50px auto 80px;">
    <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-sm);">
        <h1 style="font-size: 26px; text-align: center; margin-bottom: 8px;">Welcome Back</h1>
        <p style="text-align: center; color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">Sign in to your PlantPuglu account to track orders and manage addresses.</p>

        <form action="<?= url('/login') ?>" method="POST">
            <?= \Core\CSRF::field() ?>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required value="<?= e(old('email')) ?>" placeholder="name@example.com">
                <?php if (!empty($errors['email'])): ?>
                    <small style="color:var(--danger);"><?= e($errors['email'][0]) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <div style="display:flex; justify-content:space-between; align-items:baseline;">
                    <label>Password</label>
                    <a href="<?= url('/forgot-password') ?>" style="font-size:12px; color:var(--primary); font-weight:600;">Forgot Password?</a>
                </div>
                <input type="password" name="password" required placeholder="••••••••">
                <?php if (!empty($errors['password'])): ?>
                    <small style="color:var(--danger);"><?= e($errors['password'][0]) ?></small>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 15px; margin-top: 10px;">
                Sign In
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 13.5px; color: var(--text-muted); border-top: 1px solid var(--cream-border); padding-top: 20px;">
            Don't have an account yet? <a href="<?= url('/register') ?>" style="color:var(--primary); font-weight:700;">Create One Here &rarr;</a>
        </div>
    </div>
</div>
