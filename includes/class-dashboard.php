<?php
if (!defined('ABSPATH')) exit;

class AGC_Dashboard {
    public static function init() {
        add_shortcode('agc_affiliate_dashboard', [self::class, 'dashboard_shortcode']);
    }

    public static function dashboard_shortcode($atts) {
        ob_start();
        ?>
        <div>
            <h3>Affiliate Dashboard (Debug Mode)</h3>
            <p>This is where the affiliate dashboard will show commission stats.</p>
        </div>
        <?php
        return ob_get_clean();
    }
}