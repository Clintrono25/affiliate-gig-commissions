<?php
if (!defined('ABSPATH')) exit;

class AGC_Commission_Tracker {
    public static function init() {
        add_action('woocommerce_order_status_completed', [self::class, 'maybe_assign_commission'], 10, 1);
    }

    public static function maybe_assign_commission($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;

        $aff_ref = get_post_meta($order_id, '_agc_aff_ref', true);
        if (empty($aff_ref)) {
            error_log('[AGC] No affiliate ref found for order '.$order_id);
            return;
        }

        $affiliate_user = get_user_by('login', $aff_ref);
        if (!$affiliate_user) {
            error_log('[AGC] Affiliate user not found for ref: '.$aff_ref);
            return;
        }

        $affiliate_user_id = $affiliate_user->ID;
        global $wpdb;
        $table = $wpdb->prefix . 'agc_commissions';

        $commission_rate = 0.1; // 10% for now

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $line_total = $item->get_total();
            $commission = $line_total * $commission_rate;

            $wpdb->insert($table, [
                'user_id' => $affiliate_user_id,
                'order_id' => $order_id,
                'product_id' => $product_id,
                'commission' => $commission,
                'status' => 'pending',
                'created_at' => current_time('mysql', 1),
            ]);
        }

        error_log('[AGC] Commission recorded for affiliate '.$affiliate_user_id.' on order '.$order_id);
    }
}

// Store aff_ref in session if present in URL
add_action('init', function() {
    if (!session_id()) {
        session_start();
    }
    if (isset($_GET['aff_ref'])) {
        $_SESSION['agc_aff_ref'] = sanitize_text_field($_GET['aff_ref']);
    }
});

// Copy aff_ref from session to order meta on checkout
add_action('woocommerce_checkout_create_order', function($order, $data) {
    if (isset($_SESSION['agc_aff_ref'])) {
        $order->update_meta_data('_agc_aff_ref', $_SESSION['agc_aff_ref']);
    }
}, 10, 2);