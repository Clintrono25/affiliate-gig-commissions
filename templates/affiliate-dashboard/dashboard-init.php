<?php
global $wpdb;
$agc_user_logged_in = is_user_logged_in();
if ($agc_user_logged_in) {
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    if (get_user_meta($user_id, 'agc_affiliate_deactivated', true)) {
        wp_redirect(site_url('/affiliate-login/?deactivated=1'));
        exit;
    }
    $agc_user = $current_user;
    $agc_table = $wpdb->prefix . 'agc_commissions';
    $agc_withdrawal_table = $wpdb->prefix . 'agc_withdrawals';
    $agc_total_commissions = (float) $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $agc_table WHERE affiliate_id = %d", $user_id));
    $agc_total_withdrawn = (float) $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $agc_withdrawal_table WHERE affiliate_id = %d AND status = 'paid'", $user_id));
    $agc_pending = (float) $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $agc_table WHERE affiliate_id = %d AND status = 'pending'", $user_id));
    $agc_available = $agc_total_commissions - $agc_total_withdrawn;
    if ($agc_available < 0) $agc_available = 0;
    $agc_min_withdrawal = 30;
    $agc_tab = isset($_GET['agc_tab']) ? sanitize_text_field($_GET['agc_tab']) : 'overview';
    $agc_payout_method = get_user_meta($user_id, 'agc_payout_method', true) ?: [];
    $agc_payout_msg = '';
    if (!empty($_POST['agc_update_payout'])) {
        check_admin_referer('agc_update_payout');
        $method = sanitize_text_field($_POST['payout_method']);
        $pay_data = [];
        if ($method === 'paypal') {
            $pay_data = ['type' => 'paypal', 'email' => sanitize_email($_POST['paypal_email'])];
        } elseif ($method === 'payoneer') {
            $pay_data = ['type' => 'payoneer', 'email' => sanitize_email($_POST['payoneer_email'])];
        }
        update_user_meta($user_id, 'agc_payout_method', $pay_data);
        $agc_payout_method = $pay_data;
        $agc_payout_msg = '<div class="alert alert-success mt-2">Payout method updated!</div>';
    }
    $agc_earnings_30d = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT DATE(created_at) as day, SUM(amount) as total FROM $agc_table WHERE affiliate_id = %d GROUP BY day ORDER BY day DESC LIMIT 30", $user_id
        )
    );
    $agc_commissions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $agc_table WHERE affiliate_id = %d ORDER BY created_at DESC LIMIT 50", $user_id
        )
    );
    $agc_withdrawals = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $agc_withdrawal_table WHERE affiliate_id = %d ORDER BY requested_at DESC LIMIT 10", $user_id
        )
    );
    $agc_withdrawal_msg = '';
    if (!empty($_POST['agc_withdrawal_request'])) {
        check_admin_referer('agc_withdrawal_request');
        $request_amount = $agc_available;
        if ($request_amount >= $agc_min_withdrawal && !empty($agc_payout_method['type'])) {
            $wpdb->insert($agc_withdrawal_table, [
                'affiliate_id' => $user_id,
                'amount' => $request_amount,
                'status' => 'pending',
                'requested_at' => current_time('mysql', 1),
                'method' => $agc_payout_method['type']
            ], ['%d','%f','%s','%s','%s']);
            $agc_withdrawal_msg = '<div class="alert alert-success mt-2">Withdrawal request submitted for ' . wc_price($request_amount) . '!</div>';
            $agc_available = 0;
        } elseif (empty($agc_payout_method['type'])) {
            $agc_withdrawal_msg = '<div class="alert alert-danger mt-2">Please set your payout method first.</div>';
        } else {
            $agc_withdrawal_msg = '<div class="alert alert-danger mt-2">Minimum withdrawal is ' . wc_price($agc_min_withdrawal) . '.</div>';
        }
    }
    $agc_user_type = get_user_meta($user_id, '_user_type', true);
    $agc_commission_rate_message = '';
    if ($agc_user_type === 'freelancers') {
        if (!empty($_POST['agc_update_commission_rate'])) {
            check_admin_referer('agc_commission_rate_update');
            $new_rate = isset($_POST['agc_affiliate_rate']) ? intval($_POST['agc_affiliate_rate']) : 0;
            if ($new_rate < 0) $new_rate = 0;
            if ($new_rate > 40) $new_rate = 40;
            update_user_meta($user_id, 'agc_affiliate_rate', $new_rate);
            $agc_commission_rate_message = '<div class="alert alert-success mt-2">Your commission rate has been updated!</div>';
        }
        $agc_current_rate = get_user_meta($user_id, 'agc_affiliate_rate', true);
        if ($agc_current_rate === '' || !is_numeric($agc_current_rate)) $agc_current_rate = 0;
    }
    if (isset($_POST['agc_deactivate_affiliate']) && check_admin_referer('agc_deactivate_affiliate')) {
        update_user_meta($user_id, 'agc_affiliate_deactivated', 1);
        wp_logout();
        wp_redirect(site_url('/affiliate-login/?deactivated=1'));
        exit;
    }
    $agc_is_deactivated = get_user_meta($user_id, 'agc_affiliate_deactivated', true);
}
?>