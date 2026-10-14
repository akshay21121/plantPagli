<?php
/**
 * Contact Us Page View
 * @var string $phone
 * @var string $email
 * @var string $address
 */
?>

<div class="container" style="max-width: 960px; margin: 40px auto 70px;">
    <div style="background: var(--cream-surface); border: 1px solid var(--cream-border); border-radius: var(--radius-lg); padding: 50px; box-shadow: var(--shadow-sm);">
        <h1 style="font-size: 32px; margin-bottom: 8px;">Contact Our Plant Care Team</h1>
        <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 36px;">Have a question about plant care, order status, or wholesale corporate gifting? We are here to help!</p>

        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 48px;">
            <!-- Contact Details -->
            <div>
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 44px; height: 44px; background: var(--accent-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            📞
                        </div>
                        <div>
                            <h4 style="font-size: 15px; margin-bottom: 2px;">Phone & WhatsApp</h4>
                            <p style="font-size: 14px; color: var(--text-muted);"><?= e($phone) ?></p>
                            <span style="font-size: 12px; color: var(--success); font-weight: 600;">Mon - Sat, 9:00 AM - 7:00 PM IST</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 44px; height: 44px; background: var(--accent-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            ✉️
                        </div>
                        <div>
                            <h4 style="font-size: 15px; margin-bottom: 2px;">Customer Support Email</h4>
                            <p style="font-size: 14px; color: var(--text-muted);"><?= e($email) ?></p>
                            <span style="font-size: 12px; color: var(--text-muted);">We typically respond within 12 hours.</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 44px; height: 44px; background: var(--accent-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            📍
                        </div>
                        <div>
                            <h4 style="font-size: 15px; margin-bottom: 2px;">Registered Nursery Facility</h4>
                            <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5;"><?= e($address) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Message Form -->
            <div style="background: var(--cream-bg); padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--cream-border);">
                <h3 style="font-size: 18px; margin-bottom: 16px;">Send Us a Message</h3>
                <form action="<?= url('/contact') ?>" method="POST">
                    <?= \Core\CSRF::field() ?>

                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Meera Nair">
                    </div>

                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required placeholder="meera@example.com">
                    </div>

                    <div class="form-group">
                        <label>Subject / Order Number (if applicable)</label>
                        <input type="text" name="subject" placeholder="e.g. Order #PP-2026-1001 or Plant Advice">
                    </div>

                    <div class="form-group">
                        <label>Your Message / Plant Query *</label>
                        <textarea name="message" rows="4" required placeholder="Describe what you need help with..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Send Message &rarr;</button>
                </form>
            </div>
        </div>
    </div>
</div>
