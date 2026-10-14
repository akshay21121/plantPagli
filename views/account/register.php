<?php
$errors = \Core\Session::get('_auth_errors', []);
\Core\Session::remove('_auth_errors');
?>

<div class="container" style="max-width: 520px; margin: 50px auto 80px;">
    <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-sm);">
        <h1 style="font-size: 26px; text-align: center; margin-bottom: 8px;">Join the Green Family</h1>
        <p style="text-align: center; color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">Create your PlantPuglu account for seamless ordering, plant care logs, and exclusive promotions.</p>

        <form action="<?= url('/register') ?>" method="POST">
            <?= \Core\CSRF::field() ?>

            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required value="<?= e(old('name')) ?>" placeholder="e.g. Rahul Sharma">
                <?php if (!empty($errors['name'])): ?>
                    <small style="color:var(--danger);"><?= e($errors['name'][0]) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required value="<?= e(old('email')) ?>" placeholder="name@example.com">
                    <?php if (!empty($errors['email'])): ?>
                        <small style="color:var(--danger);"><?= e($errors['email'][0]) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Mobile Number *</label>
                    <input type="tel" name="phone" required value="<?= e(old('phone')) ?>" placeholder="9876543210">
                    <?php if (!empty($errors['phone'])): ?>
                        <small style="color:var(--danger);"><?= e($errors['phone'][0]) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password (min 6 chars) *</label>
                    <input type="password" name="password" required placeholder="••••••••">
                    <?php if (!empty($errors['password'])): ?>
                        <small style="color:var(--danger);"><?= e($errors['password'][0]) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 15px; margin-top: 10px;">
                Create My Account
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 13.5px; color: var(--text-muted); border-top: 1px solid var(--cream-border); padding-top: 20px;">
            Already have an account? <a href="<?= url('/login') ?>" style="color:var(--primary); font-weight:700;">Sign In Here &rarr;</a>
        </div>
    </div>
</div>
