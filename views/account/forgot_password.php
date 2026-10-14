<div class="container" style="max-width: 480px; margin: 60px auto 90px;">
    <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-sm);">
        <h1 style="font-size: 26px; text-align: center; margin-bottom: 8px;">Reset Password</h1>
        <p style="text-align: center; color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">Enter your registered email address and we'll send you instructions to reset your password.</p>

        <form action="<?= url('/forgot-password') ?>" method="POST">
            <?= \Core\CSRF::field() ?>

            <div class="form-group">
                <label>Registered Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 15px; margin-top: 10px;">
                Send Reset Link
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 13.5px;">
            <a href="<?= url('/login') ?>" style="color:var(--primary); font-weight:600;">&larr; Back to Login</a>
        </div>
    </div>
</div>
