<?php
if (!defined('ABSPATH')) exit;

class AGC_Affiliate_Manager {
    public static function init() {
        // Hooks for affiliate registration, role checks, and link generation
        add_shortcode('agc_affiliate_signup', [self::class, 'signup_form_shortcode']);
        add_shortcode('agc_referral_link', [self::class, 'referral_link_shortcode']);
    }

    public static function signup_form_shortcode($atts) {
        ob_start();
        ?>
        <div>
            <h3>Affiliate Signup (Debug Mode)</h3>
            <p>This is where the affiliate signup form will appear.</p>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function referral_link_shortcode($atts) {
        ob_start();
        $current_user = wp_get_current_user();
        ?>
        <div>
            <h3>Referral Link Generator (Debug Mode)</h3>
            <p>
                <?php if ($current_user && $current_user->exists()): ?>
                    Your referral link for product ID 123:<br>
                    <code><?php echo esc_url(site_url('/product/ux-ui-design-consultation/?aff_ref=' . $current_user->user_login)); ?></code>
                <?php else: ?>
                    Please log in to see your affiliate referral link.
                <?php endif; ?>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
}